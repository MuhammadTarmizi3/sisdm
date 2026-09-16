# Analisis Gap: Skema SQL Aktual vs. Desain (SDD) — SISDM Modul KGB

| | |
|---|---|
| **Sumber pembanding** | `SISDM.sql` (dump phpMyAdmin, 15 Sep 2026) vs. `SDD_SISDM_Modul_KGB.md` v1.0 |
| **Tujuan dokumen** | Menandai apa yang **kurang/beda** antara skema yang sudah dieksekusi dan desain, supaya siap diserahkan ke AI/developer untuk pembuatan backend |

---

## 1. Ringkasan Temuan

`.sql` yang kamu kirim **secara struktur (nama tabel, relasi, FK, aturan ON DELETE) sudah sangat sesuai** dengan desain di SDD — bahkan lebih detail di beberapa bagian (mis. `pegawai` punya field kepegawaian yang lebih lengkap, `tahapan_approval` punya `ID_ROLE_BERWENANG`). Tapi ada **4 gap kritis** yang sebaiknya diputuskan dulu, dan beberapa gap teknis Laravel-spesifik yang wajib diperbaiki sebelum dijadikan migration.

---

## 2. Gap Kritis (Perlu Keputusan Kamu)

### 🔴 GAP-1: Tidak ada `AUTO_INCREMENT` di hampir semua Primary Key
Dari seluruh 17 tabel, **hanya `log_aktivitas.ID_LOG`** yang di-set `AUTO_INCREMENT`. Tabel lain (`users.USER_ID`, `pegawai.ID_PEGAWAI`, `pengajuan.ID_PENGAJUAN`, dst.) PK-nya polos `int NOT NULL` tanpa auto-increment.

**Dampak:** Laravel Eloquent secara default mengasumsikan PK auto-increment. Kalau ini dibiarkan, setiap `INSERT` harus menyertakan ID manual (developer/AI harus generate ID sendiri) — rawan bentrok dan tidak sesuai konvensi Laravel (`$table->id()`).

**Pertanyaan:** Apakah `.sql` ini export dari database yang datanya sudah diisi manual (makanya AUTO_INCREMENT belum di-set), atau memang belum sempat ditambahkan? Kalau kamu setuju, saya siapkan patch `ALTER TABLE ... MODIFY ... AUTO_INCREMENT` untuk semua PK.

### 🔴 GAP-2: Kolom `deleted_at` (soft delete) tidak ada di tabel `pegawai`
SRS/keputusan desain sebelumnya menetapkan **`pegawai` harus soft delete**, tapi tabel `pegawai` di `.sql` **tidak punya kolom `deleted_at`** sama sekali.

**Dampak:** Tanpa kolom ini, `SoftDeletes` trait Laravel tidak bisa dipakai; penghapusan pegawai akan selalu hard delete — bertentangan dengan aturan bisnis di SRS RQ-ADM-02.

**Pertanyaan:** Mau saya buatkan `ALTER TABLE pegawai ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;`?

### 🔴 GAP-3: Kolom `IS_ACTIVE` tidak ada di `persyaratan_master`
SRS RQ-ADM-09 menetapkan persyaratan yang sudah pernah dipakai **tidak boleh dihapus**, hanya **dinonaktifkan** (soft-disable). Tabel `persyaratan_master` di `.sql` tidak punya kolom status aktif/nonaktif.

**Pertanyaan:** Mau saya buatkan `ALTER TABLE persyaratan_master ADD COLUMN IS_ACTIVE TINYINT(1) NOT NULL DEFAULT 1;`?

### 🔴 GAP-4: Kolom `JENIS` tidak ada di `notifikasi`
Keputusan desain sebelumnya (tercatat di memori proyek): kolom `jenis` di `notifikasi` untuk membedakan pengingat KGB vs. notifikasi submisi/persetujuan/penolakan. Tabel `notifikasi` di `.sql` tidak punya kolom ini.

**Pertanyaan:** Mau saya buatkan `ALTER TABLE notifikasi ADD COLUMN JENIS VARCHAR(32) NULL AFTER PESAN;` (nilai: `submisi_kgb`, `perlu_perbaikan`, `disetujui`, `ditolak`, `pengingat_kgb`)?

---

## 3. Gap Teknis (Konvensi Laravel — Rekomendasi, Bukan Pertanyaan)

### 🟡 Tidak ada `created_at` / `updated_at` di sebagian besar tabel
Hanya `notifikasi.CREATED_AT` dan `log_aktivitas.WAKTU` yang punya timestamp. Tabel seperti `pengajuan`, `detail_berkas`, `approval_log`, `riwayat_jabatan`, `riwayat_pangkat`, `pegawai`, `users` tidak punya `created_at`/`updated_at`.

**Rekomendasi:** Laravel Eloquent secara default membaca/menulis kedua kolom ini. Dua opsi:
- (a) Tambahkan `created_at`/`updated_at TIMESTAMP NULL` ke semua tabel transaksional (disarankan, untuk audit).
- (b) Set `public $timestamps = false;` di tiap Model yang tabelnya tidak punya kolom ini (lebih cepat, tapi kehilangan histori otomatis).

### 🟡 `STATUS_LAYANAN` di `jenis_layanan` bertipe `varchar(16)`, bukan boolean
SDD sebelumnya mengasumsikan boolean. Karena aktual `varchar`, perlu disepakati **nilai string standarnya** (mis. `'aktif'` / `'nonaktif'`) supaya konsisten antara migration, seeder, dan validasi backend.

### 🟡 `JENIS_KELAMIN` di `pegawai` bertipe `varchar(20)`, bebas
Tidak ada constraint nilai. Rekomendasi: enforce di level aplikasi (Form Request `in:L,P` atau `in:Laki-laki,Perempuan`) — tentukan salah satu standar nilai penyimpanan.

### 🟡 `approval_log` punya `ID_TAHAPAN` (FK ke `tahapan_approval`)
Ini **lebih detail** dari yang tertulis di SDD (SDD hanya menyebut `id_verifikator`). Artinya setiap baris `approval_log` juga mencatat *tahap approval* mana yang sedang berjalan — cocok untuk modul KGB yang cuma 1 tahap (Verifikator), tapi **penting untuk direfleksikan ke SDD** karena struktur ini sudah disiapkan untuk alur berjenjang (multi-tahap) di layanan lain nanti.

### 🟡 `detail_kgb.ID_PENGAJUAN` adalah PK itu sendiri (bukan PK terpisah + FK unik)
Ini justru **lebih tepat** daripada asumsi SDD (`id_detail_kgb` sendiri + FK unik) — desain aktual sudah benar-benar 1-1 murni. **SDD perlu diperbarui** mengikuti implementasi aktual ini (lebih sederhana, tidak perlu diubah balik).

---

## 4. Yang Sudah Benar / Tidak Perlu Diubah
- Semua aturan `ON DELETE RESTRICT` untuk FK ke data master (`jabatan`, `unit_kerja`, `pangkat_golongan`, `jenis_layanan`, `roles`, `persyaratan_master`) ✅ sesuai SRS §7.
- `detail_berkas` `ON DELETE CASCADE` dari `pengajuan` ✅ sesuai.
- `notifikasi` `ON DELETE CASCADE` dari `users` — konsisten dengan asumsi "ikut hilang jika akun dihapus fisik" ✅.
- `users` tidak punya kolom `email` ✅ sesuai keputusan "login via NIP/NRP, bukan email".
- `pegawai.NIP_NRP` unik ✅.
- `riwayat_jabatan`/`riwayat_pangkat` FK ke `pegawai` dengan `ON DELETE CASCADE` — **catatan:** ini beda dari SRS §7 yang bilang RESTRICT (supaya riwayat tetap ada meski pegawai di-soft-delete). Karena `pegawai` seharusnya **soft delete** (GAP-2), delete fisik semestinya jarang terjadi, jadi CASCADE di sini kemungkinan tidak akan pernah ter-trigger dalam praktik — namun tetap perlu dicatat sebagai inkonsistensi kecil dengan dokumen SRS.

---

## 5. Rekomendasi Patch SQL (jika kamu setuju semua Gap Kritis di atas)

```sql
-- GAP-1: AUTO_INCREMENT untuk seluruh PK (contoh beberapa tabel utama)
ALTER TABLE `users` MODIFY `USER_ID` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `pegawai` MODIFY `ID_PEGAWAI` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `pengajuan` MODIFY `ID_PENGAJUAN` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `detail_berkas` MODIFY `ID_DETAIL_BERKAS` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `approval_log` MODIFY `ID_APPROVAL_LOG` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `roles` MODIFY `ID_ROLE` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `jabatan` MODIFY `ID_JABATAN` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `unit_kerja` MODIFY `ID_UNIT` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `pangkat_golongan` MODIFY `ID_PANGKAT` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `jenis_layanan` MODIFY `ID_LAYANAN` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `persyaratan_master` MODIFY `ID_PERSYARATAN` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `tahapan_approval` MODIFY `ID_APPROVAL` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `riwayat_jabatan` MODIFY `ID_RIWAYAT` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `riwayat_pangkat` MODIFY `ID_RIWAYAT_PANGKAT` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `notifikasi` MODIFY `ID_NOTIFIKASI` INT NOT NULL AUTO_INCREMENT;
-- detail_kgb TIDAK di-AUTO_INCREMENT karena PK-nya = FK ke pengajuan (1-1 murni)

-- GAP-2: soft delete pegawai
ALTER TABLE `pegawai` ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL;

-- GAP-3: status aktif persyaratan
ALTER TABLE `persyaratan_master` ADD COLUMN `IS_ACTIVE` TINYINT(1) NOT NULL DEFAULT 1;

-- GAP-4: jenis notifikasi
ALTER TABLE `notifikasi` ADD COLUMN `JENIS` VARCHAR(32) NULL AFTER `PESAN`;

-- Rekomendasi timestamps (opsional, lihat §3)
ALTER TABLE `pengajuan` ADD COLUMN `created_at` TIMESTAMP NULL, ADD COLUMN `updated_at` TIMESTAMP NULL;
ALTER TABLE `detail_berkas` ADD COLUMN `created_at` TIMESTAMP NULL, ADD COLUMN `updated_at` TIMESTAMP NULL;
ALTER TABLE `approval_log` ADD COLUMN `created_at` TIMESTAMP NULL;
ALTER TABLE `riwayat_jabatan` ADD COLUMN `created_at` TIMESTAMP NULL;
ALTER TABLE `riwayat_pangkat` ADD COLUMN `created_at` TIMESTAMP NULL;
ALTER TABLE `pegawai` ADD COLUMN `created_at` TIMESTAMP NULL, ADD COLUMN `updated_at` TIMESTAMP NULL;
ALTER TABLE `users` ADD COLUMN `created_at` TIMESTAMP NULL, ADD COLUMN `updated_at` TIMESTAMP NULL;
```

> ⚠️ Patch di atas **belum saya jalankan** — ini draf untuk kamu review. Konfirmasi dulu jawaban 4 pertanyaan Gap Kritis di §2, baru saya finalisasi (atau sesuaikan bila ada koreksi).

---

## 6. Yang Masih Kurang di Luar Skema Database (dari daftar sebelumnya)

Dengan `.sql` ini, **gap #1 (migration/DDL) di jawaban saya sebelumnya sudah terisi**. Yang **masih kurang** untuk "siap eksekusi AI":

| # | Item | Status |
|---|---|---|
| 1 | Migration/DDL literal | ✅ Terisi oleh `.sql` ini (setelah patch Gap Kritis) |
| 2 | Contoh request/response JSON per endpoint | ❌ Belum ada |
| 3 | Data seed (`roles`, `jenis_layanan='KGB'`, daftar `persyaratan_master` KGB) | ❌ Belum ada — nilai row-nya belum ditentukan |
| 4 | Diagram sumber (ERD/use case/activity) sebagai referensi visual bagi AI | ⚠️ Ada tapi hanya sebagai gambar terpisah, tidak dalam folder proyek `.md` |
| 5 | Konfigurasi environment (`.env`, CORS/Sanctum domain React) | ❌ Belum konkret |
| 6 | Task Breakdown terurut | ❌ Belum dibuat |

---

## 7. Pertanyaan yang Perlu Dijawab

1. Setujukah dengan patch **GAP-1 (AUTO_INCREMENT)** untuk seluruh PK?
2. Setujukah menambahkan **`deleted_at`** ke `pegawai` (GAP-2)?
3. Setujukah menambahkan **`IS_ACTIVE`** ke `persyaratan_master` (GAP-3)?
4. Setujukah menambahkan **`JENIS`** ke `notifikasi` (GAP-4)?
5. Untuk timestamps (`created_at`/`updated_at`) di tabel yang belum punya — mau ditambahkan ke skema, atau cukup dimatikan di level Model Laravel (`$timestamps = false`)?
6. Nilai standar `STATUS_LAYANAN` (`jenis_layanan`) dan `JENIS_KELAMIN` (`pegawai`) — mau pakai istilah apa persis (mis. `'aktif'/'nonaktif'` dan `'L'/'P'`)?

---
*Setelah pertanyaan di atas dijawab, saya akan (a) finalisasi patch SQL, (b) perbarui SDD agar 100% sinkron dengan skema aktual, dan bisa lanjut menyiapkan contoh request/response JSON + seed data + Task Breakdown.*
