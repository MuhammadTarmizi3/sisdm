# System Design Document (SDD)
## Sistem Informasi Sumber Daya Manusia (SISDM) — Modul Kenaikan Gaji Berkala (KGB)
### BNNP Kalimantan Selatan

| | |
|---|---|
| **Versi** | 1.0 |
| **Tanggal** | 15 September 2026 |
| **Rujukan** | PRD v1.0, SRS v1.0, ERD SISDM (17 tabel), Kebutuhan Fungsional v1.2, Activity Diagram AD-01–AD-09 |
| **Ruang Lingkup** | **KGB saja.** Desain ini tidak mencakup layanan Kenaikan Pangkat atau lima layanan lain — namun struktur `pengajuan` generik sengaja disiapkan agar dapat diperluas nanti tanpa migrasi ulang skema inti. |
| **Stack** | **Backend: Laravel (PHP, API-only) · Frontend: React JS (SPA)**, MySQL/MariaDB, `spatie/laravel-permission` (RBAC), `maatwebsite/excel`, DomPDF, autentikasi berbasis session (Laravel Sanctum SPA mode) |

---

## 1. Arsitektur Sistem

### 1.1 Gaya Arsitektur
SISDM menggunakan arsitektur **decoupled SPA + REST API**: **backend Laravel bersifat API-only** (tidak merender halaman Blade untuk fitur bisnis), dan **frontend adalah aplikasi React JS terpisah** yang mengonsumsi API tersebut. Keduanya tetap dijalankan dalam satu **jaringan intranet** BNNP Kalsel — tidak ada komponen yang diekspos ke internet publik, dan tidak ada integrasi keluar (SIMPEG BKN, dsb.).

```
┌─────────────────────────────────────────────────────────────┐
│              KLIEN — React JS SPA (Browser Intranet)         │
│        Pegawai · Admin Kepegawaian · Verifikator              │
│   Routing client-side (React Router), state management        │
│   (mis. React Query/Redux), pemanggilan API via Axios/fetch    │
└───────────────────────────┬───────────────────────────────────┘
                            │ HTTPS (intranet), session cookie
                            │ (Sanctum SPA: CSRF cookie + XSRF token)
┌───────────────────────────▼───────────────────────────────────┐
│              LAPISAN PRESENTASI — Laravel API-only            │
│   routes/api.php saja (tidak ada view Blade untuk fitur bisnis)│
│   Middleware: auth:sanctum, role (spatie), CORS, CSRF           │
└───────────────────────────┬───────────────────────────────────┘
                            │
┌───────────────────────────▼───────────────────────────────────┐
│                LAPISAN APLIKASI (Controllers)                 │
│  Http/Controllers/{Auth, Pegawai, Admin, Verifikator, Sistem}  │
│  Http/Requests (Form Request validation)                      │
│  Http/Resources (transformasi response API)                   │
└───────────────────────────┬───────────────────────────────────┘
                            │
┌───────────────────────────▼───────────────────────────────────┐
│                  LAPISAN LOGIKA BISNIS (Services)              │
│  PengajuanService · VerifikasiService · KgbCalculatorService   │
│  NotifikasiService · EksporService · AuditService              │
│  (menampung seluruh aturan bisnis dari SRS §2–7)               │
└───────────────────────────┬───────────────────────────────────┘
                            │
┌───────────────────────────▼───────────────────────────────────┐
│                LAPISAN AKSES DATA (Eloquent ORM)               │
│  Models: User, Pegawai, Pengajuan, DetailBerkas, ...           │
│  Policies: PengajuanPolicy, PegawaiPolicy, ...                 │
└───────────────────────────┬───────────────────────────────────┘
                            │
┌───────────────────────────▼───────────────────────────────────┐
│              MySQL/MariaDB (17 tabel, lihat §2)                │
└─────────────────────────────────────────────────────────────┘

Komponen pendukung lintas-lapisan:
- Storage lokal (disk server) untuk berkas unggahan (`file_path`, `file_sk`)
- Laravel Scheduler (cron) → job harian: deteksi & kirim pengingat KGB (RQ-SYS-02–03)
- Queue (opsional, database/sync driver) → pengiriman notifikasi asinkron
- maatwebsite/excel → ekspor Excel (Admin & Verifikator)
- DomPDF → ekspor PDF (Admin saja)
```

### 1.2 Prinsip Arsitektur
1. **Server-side authority** — seluruh validasi kritis (RQ-SYS-05, status akhir, RBAC) dilakukan di lapisan Service/Controller backend, tidak pernah hanya di client.
2. **Tipis di controller, tebal di service** — Controller hanya menangani HTTP request/response; seluruh aturan bisnis (SRS §2–7) berada di kelas Service agar dapat diuji unit secara terpisah dari HTTP layer.
3. **Satu pola generik untuk `pengajuan`** — modul KGB dibangun di atas struktur yang sama yang nantinya dipakai 6 layanan Kenaikan Pangkat, sehingga controller/service KGB menjadi referensi pola untuk modul berikutnya (tanpa mengaktifkan modul tersebut sekarang).
4. **Auditability by design** — setiap perubahan status/​data penting melewati `AuditService` agar tercatat di `log_aktivitas` secara konsisten, tidak diserahkan ke masing-masing controller.
5. **Tidak ada logika bisnis di lapisan Model** — Model (Eloquent) hanya mendefinisikan relasi, cast, dan scope query; perhitungan (mis. TMT KGB) berada di Service.

### 1.3 Autentikasi & Otorisasi
- **Autentikasi:** session-based via **Laravel Sanctum (mode SPA)** — React di-treat sebagai *first-party SPA*, bukan klien token API pihak ketiga. Alur: React memanggil `GET /sanctum/csrf-cookie` sekali di awal, lalu login dengan `username` (NIP/NRP) + `password` mengirim header `X-XSRF-TOKEN`; sesi disimpan sebagai cookie HttpOnly.
- **CORS:** karena React (build statis atau dev server) dan Laravel API bisa berjalan di origin/port berbeda dalam intranet yang sama, `config/cors.php` dan `SANCTUM_STATEFUL_DOMAINS` wajib dikonfigurasi eksplisit menyertakan origin React; `supports_credentials = true` agar cookie sesi ikut terkirim.
- **Otorisasi:** `spatie/laravel-permission` — tiga role (`pegawai`, `admin_kepegawaian`, `verifikator`), dipetakan 1:1 ke tabel `roles`. Middleware `role:` diterapkan di setiap grup route.
- **Policy** Laravel digunakan untuk otorisasi berbasis kepemilikan objek (mis. Pegawai hanya boleh mengakses `pengajuan` miliknya sendiri — lihat SRS RQ-PEG-01/RQ-PEG-08).
- **Frontend (React):** semua response API mengikuti format JSON konsisten (§3.5) agar mudah dikonsumsi oleh layer data-fetching React (mis. React Query/SWR) tanpa parsing HTML.

---

## 2. Desain Database

Skema fisik MySQL/MariaDB, 17 tabel, dirancang di PowerDesigner (CDM/PDM) dan dieksekusi via phpMyAdmin. Konvensi: `snake_case`, PK `id_<entitas>` (kecuali `users.user_id`), FK mengikuti nama PK yang dirujuk, timestamp standar Laravel (`created_at`, `updated_at`) ditambahkan di semua tabel transaksional (tidak dituliskan ulang di tiap tabel di bawah demi keringkasan), dan `deleted_at` khusus untuk soft delete.

### 2.1 Kelompok Identitas & Akses

**`roles`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_role (PK) | INT UNSIGNED AI | |
| kode_role | VARCHAR(30) UNIQUE | `pegawai`, `admin_kepegawaian`, `verifikator` |
| nama_role | VARCHAR(50) | |

**`users`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| user_id (PK) | INT UNSIGNED AI | |
| username | VARCHAR(30) UNIQUE NOT NULL | Diisi NIP/NRP — **dipakai untuk login** |
| password | VARCHAR(255) NOT NULL | Hash (bcrypt) |
| email | VARCHAR(100) NULL | Opsional, **tidak dipakai untuk login** |
| id_role (FK → roles) | INT UNSIGNED NOT NULL | RESTRICT on delete |
| is_active | BOOLEAN DEFAULT true | Dikelola Admin (KF-ADM-01) |

**`notifikasi`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_notifikasi (PK) | INT UNSIGNED AI | |
| user_id (FK → users) | INT UNSIGNED NOT NULL | CASCADE (notifikasi ikut hilang jika akun benar-benar dihapus fisik — kasus jarang) |
| judul | VARCHAR(150) | |
| pesan | TEXT | |
| jenis | ENUM('submisi_kgb','perlu_perbaikan','disetujui','ditolak','pengingat_kgb') NOT NULL | Membedakan pengingat KGB vs. notifikasi pengajuan (RQ-SYS-07) |
| is_read | BOOLEAN DEFAULT false | |
| created_at | TIMESTAMP | Append-only, tidak diedit |

### 2.2 Kelompok Data Kepegawaian

**`unit_kerja`**, **`jabatan`**, **`pangkat_golongan`** — tabel master, masing-masing:
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_unit / id_jabatan / id_pangkat (PK) | INT UNSIGNED AI | |
| nama_unit / nama_jabatan / nama_pangkat | VARCHAR | UNIQUE per tabel |
| tipe_unit / jenis_jabatan | VARCHAR | |
| urutan_tingkat, golongan_ruang | (khusus `pangkat_golongan`) | Menentukan urutan hierarki pangkat |

> **Aturan RESTRICT:** ketiga tabel master ini **tidak dapat dihapus** selama masih dirujuk oleh `pegawai` atau baris riwayat manapun (SRS §7).

**`pegawai`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_pegawai (PK) | INT UNSIGNED AI | |
| user_id (FK → users, UNIQUE) | INT UNSIGNED NOT NULL | Relasi 1-1 `dibuat` |
| nip | VARCHAR(18) UNIQUE NOT NULL | |
| nama_pegawai | VARCHAR(150) NOT NULL | |
| tempat_lahir, tanggal_lahir | VARCHAR / DATE | |
| jenis_kelamin | ENUM('L','P') | |
| nomer_hp | VARCHAR(20) | |
| id_jabatan (FK → jabatan) | INT UNSIGNED | RESTRICT |
| id_unit (FK → unit_kerja) | INT UNSIGNED | RESTRICT |
| id_pangkat (FK → pangkat_golongan) | INT UNSIGNED | RESTRICT |
| deleted_at | TIMESTAMP NULL | **Soft delete** |

**`riwayat_jabatan`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_riwayat (PK) | INT UNSIGNED AI | |
| id_pegawai (FK → pegawai) | INT UNSIGNED NOT NULL | RESTRICT (bukan cascade — riwayat harus tetap ada meski pegawai soft-deleted) |
| id_jabatan (FK → jabatan) | INT UNSIGNED NOT NULL | RESTRICT |
| tmt_jabatan | DATE NOT NULL | |
| nomer_sk, tanggal_sk, file_sk | VARCHAR / DATE / VARCHAR | |
| sumber | ENUM('manual_admin','otomatis_kgb') DEFAULT 'manual_admin' | Menandai baris hasil trigger AD-09 |
| created_at | TIMESTAMP | **Append-only** |

**`riwayat_pangkat`** — struktur identik dengan `riwayat_jabatan`, mengganti `id_jabatan`→`id_pangkat`, `tmt_jabatan`→`tmt_pangkat`.

### 2.3 Kelompok Layanan & Persyaratan (Generik, Siap Multi-Layanan)

**`jenis_layanan`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_layanan (PK) | INT UNSIGNED AI | |
| kode_layanan | VARCHAR(20) UNIQUE | `KGB` untuk tahap ini; kode lain disiapkan tapi **tidak diaktifkan** (`status_layanan = false`) |
| nama_layanan | VARCHAR(100) | |
| status_layanan | BOOLEAN | Hanya `KGB` yang `true` pada tahap ini |
| kategori | VARCHAR(50) | |

**`persyaratan_master`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_persyaratan (PK) | INT UNSIGNED AI | |
| id_layanan (FK → jenis_layanan) | INT UNSIGNED NOT NULL | RESTRICT |
| nama_persyaratan | VARCHAR(150) NOT NULL | |
| wajib | BOOLEAN NOT NULL | Kunci utama validasi persetujuan (RQ-SYS-05) |
| urutan | INT | UNIQUE per `id_layanan` |
| is_active | BOOLEAN DEFAULT true | Nonaktif ≠ hapus (RQ-ADM-09) |

**`tahapan_approval`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_apprval (PK) | INT UNSIGNED AI | |
| id_layanan (FK → jenis_layanan) | INT UNSIGNED NOT NULL | RESTRICT |
| nama_tahap | VARCHAR(100) | Untuk KGB: hanya 1 tahap — `Verifikator` (bukan berjenjang) |
| urutan | INT | |

### 2.4 Kelompok Transaksi Pengajuan

**`pengajuan`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_pengajuan (PK) | INT UNSIGNED AI | |
| id_pegawai (FK → pegawai) | INT UNSIGNED NOT NULL | RESTRICT — **tidak pernah cascade delete** |
| id_layanan (FK → jenis_layanan) | INT UNSIGNED NOT NULL | RESTRICT |
| tanggal_pengajuan | DATETIME NOT NULL | |
| status_pengajuan | ENUM('Diajukan','Perlu perbaikan','Disetujui','Ditolak') NOT NULL DEFAULT 'Diajukan' | Lihat peta status PRD §6.10 |
| catatan_verifikator | TEXT NULL | Wajib diisi saat status Ditolak/Perlu perbaikan (SRS RQ-VER-04–05) |
| nomer_sk | VARCHAR(50) NULL UNIQUE | Hanya diisi setelah `Disetujui` (RQ-ADM-13) |
| tanggal_sk | DATE NULL | idem |

> **Constraint aplikasi (bukan constraint SQL murni, ditegakkan di Service):** `nomer_sk`/`tanggal_sk` hanya bisa NOT NULL jika `status_pengajuan = 'Disetujui'` — divalidasi di `PengajuanService::inputSk()`.

**`detail_berkas`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_detail_berkas (PK) | INT UNSIGNED AI | |
| id_pengajuan (FK → pengajuan) | INT UNSIGNED NOT NULL | CASCADE (berkas memang bagian tak terpisahkan dari satu pengajuan) |
| id_persyaratan (FK → persyaratan_master) | INT UNSIGNED NOT NULL | RESTRICT |
| file_path | VARCHAR(255) NOT NULL | |
| status_verifikasi | ENUM('belum diperiksa','valid','tidak valid','perlu perbaikan') DEFAULT 'belum diperiksa' | |

**`approval_log`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_approval_log (PK) | INT UNSIGNED AI | |
| id_pengajuan (FK → pengajuan) | INT UNSIGNED NOT NULL | RESTRICT — **append-only, tidak pernah cascade delete** |
| id_verifikator (FK → users) | INT UNSIGNED NOT NULL | RESTRICT |
| keputusan | ENUM('Disetujui','Ditolak','Perlu perbaikan') NOT NULL | |
| catatan | TEXT | |
| waktu | DATETIME NOT NULL | |

**`detail_kgb`** *(ekstensi 1-1 dari `pengajuan`, khusus data KGB)*
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_detail_kgb (PK) | INT UNSIGNED AI | |
| id_pengajuan (FK → pengajuan, UNIQUE) | INT UNSIGNED NOT NULL | Relasi **1-1**, bukan 1-M (koreksi dari label diagram awal — sesuai keputusan desain final) |
| gaji_pokok_lama | DECIMAL(12,2) | |
| gaji_pokok_baru | DECIMAL(12,2) | |
| tmt_kgb_berikutnya | DATE | Dasar perhitungan pengingat siklus selanjutnya |

### 2.5 Ringkasan Aturan Delete per Tabel

| Tabel | Perilaku delete |
|---|---|
| `roles`, `unit_kerja`, `jabatan`, `pangkat_golongan`, `persyaratan_master`, `jenis_layanan`, `tahapan_approval` | **RESTRICT** — ditolak jika masih dirujuk |
| `pegawai` | **Soft delete** (`deleted_at`) |
| `pengajuan`, `approval_log`, `log_aktivitas` | **Tidak pernah cascade delete** — dianggap append-only demi audit/legal |
| `riwayat_jabatan`, `riwayat_pangkat`, `notifikasi` | **Append-only** secara aplikasi; FK ke `pegawai`/`jabatan`/`pangkat_golongan` bersifat RESTRICT |
| `detail_berkas` | **CASCADE** dari `pengajuan` induknya |

### 2.6 Indeks Utama (untuk performa query yang sering dipakai)
- `pengajuan(status_pengajuan, id_layanan)` — untuk query daftar Diajukan (Verifikator) dan daftar Disetujui (daftar memenuhi syarat).
- `pengajuan(id_pegawai, status_pengajuan)` — untuk cek "pengajuan aktif" saat validasi RQ-PEG-04.
- `users(username)` UNIQUE — login.
- `pegawai(nip)` UNIQUE, `pegawai(deleted_at)` — filter data aktif.
- `notifikasi(user_id, is_read)` — badge notifikasi belum dibaca.

---

## 3. Desain API

Base path: `/api/v1`. Autentikasi: session cookie (Laravel Sanctum SPA-mode, cocok untuk skenario intranet single-domain) + middleware `role:`.

### 3.1 Autentikasi
| Method | Endpoint | Role | Deskripsi |
|---|---|---|---|
| POST | `/auth/login` | Publik | Login via `username` (NIP/NRP) + `password` |
| POST | `/auth/logout` | Semua (login) | Invalidasi sesi |
| GET | `/auth/me` | Semua (login) | Info user & role aktif |

### 3.2 Pegawai
| Method | Endpoint | Deskripsi | Ref. SRS |
|---|---|---|---|
| GET | `/pegawai/profil` | Data diri + riwayat jabatan/pangkat | RQ-PEG-01 |
| GET | `/pegawai/pengingat-kgb` | Status pengingat 3 bulan sebelum jatuh tempo | RQ-PEG-02 |
| GET | `/pegawai/persyaratan-kgb` | Checklist persyaratan KGB aktif | RQ-PEG-03 |
| POST | `/pegawai/pengajuan-kgb` | Buat & kirim pengajuan KGB baru | RQ-PEG-04 |
| POST | `/pegawai/pengajuan-kgb/{id}/berkas` | Unggah/ganti berkas per item persyaratan | RQ-PEG-05 |
| GET | `/pegawai/pengajuan-kgb` | Daftar pengajuan milik sendiri | RQ-PEG-07 |
| GET | `/pegawai/pengajuan-kgb/{id}` | Detail status, berkas, catatan Verifikator | RQ-PEG-07 |
| PUT | `/pegawai/pengajuan-kgb/{id}/perbaikan` | Kirim ulang setelah perbaikan berkas | RQ-PEG-08 |

### 3.3 Admin Kepegawaian
| Method | Endpoint | Deskripsi | Ref. SRS |
|---|---|---|---|
| GET/POST/PUT/PATCH | `/admin/akun` , `/admin/akun/{id}` | CRUD & aktif/nonaktifkan akun | RQ-ADM-01 |
| GET/POST/PUT/DELETE | `/admin/pegawai` , `/admin/pegawai/{id}` | CRUD data induk pegawai (delete = soft) | RQ-ADM-02 |
| GET/POST/PUT/DELETE | `/admin/unit-kerja`, `/admin/jabatan`, `/admin/pangkat-golongan` (+ `/{id}`) | CRUD data master | RQ-ADM-03–05 |
| GET/POST/PUT | `/admin/pegawai/{id}/riwayat-jabatan`, `/admin/pegawai/{id}/riwayat-pangkat` | Kelola riwayat | RQ-ADM-06–07 |
| GET/PUT | `/admin/pegawai/{id}/data-kgb-terakhir` | Kelola data dasar TMT KGB | RQ-ADM-08 |
| GET/POST/PUT/DELETE | `/admin/persyaratan-kgb` (+ `/{id}`) | Kelola checklist persyaratan | RQ-ADM-09 |
| GET | `/admin/dashboard/rekap` | Ringkasan + filter unit/pangkat | RQ-ADM-10 |
| GET | `/admin/dashboard/rekap/ekspor?format=excel\|pdf` | Ekspor laporan (Excel **atau** PDF) | RQ-ADM-11 |
| GET | `/admin/log-aktivitas` | Audit trail | RQ-ADM-12 |
| PUT | `/admin/pengajuan-kgb/{id}/sk` | Input `nomer_sk`/`tanggal_sk` (hanya jika status Disetujui) | RQ-ADM-13 |

### 3.4 Verifikator
| Method | Endpoint | Deskripsi | Ref. SRS |
|---|---|---|---|
| GET | `/verifikator/pengajuan-kgb` | Daftar berstatus Diajukan | RQ-VER-01 |
| GET | `/verifikator/pengajuan-kgb/{id}` | Detail pegawai + berkas | RQ-VER-02–03 |
| PUT | `/verifikator/pengajuan-kgb/{id}/berkas/{idBerkas}` | Set status valid/tidak valid/perlu perbaikan per berkas | RQ-VER-04–05 |
| POST | `/verifikator/pengajuan-kgb/{id}/kembalikan` | Kembalikan dengan catatan | RQ-VER-06 |
| POST | `/verifikator/pengajuan-kgb/{id}/setujui` | Setujui (server memvalidasi RQ-SYS-05) | RQ-VER-07 |
| POST | `/verifikator/pengajuan-kgb/{id}/tolak` | Tolak dengan alasan | RQ-VER-08 |
| GET | `/verifikator/memenuhi-syarat` | Daftar Disetujui + filter/pencarian | RQ-VER-09–10 |
| GET | `/verifikator/memenuhi-syarat/ekspor` | **Ekspor Excel saja** (tidak ada parameter `format` selain xlsx) | RQ-VER-11 |

### 3.5 Konvensi Response & Error
- Sukses: `{ "data": {...}, "message": "..." }` — HTTP 200/201.
- Validasi gagal: HTTP 422, `{ "errors": { "field": ["pesan"] } }` (format standar Laravel Form Request).
- Aturan bisnis dilanggar (mis. RQ-SYS-05 — masih ada berkas wajib belum valid): HTTP **409 Conflict**, `{ "message": "Masih terdapat berkas wajib yang belum valid" }` — dibedakan dari 422 agar frontend bisa menampilkan pesan bisnis, bukan error field form.
- Akses ditolak (role/kepemilikan salah): HTTP 403.
- Transisi status tidak valid (mis. input SK pada pengajuan belum Disetujui): HTTP 409.

---

## 4. Struktur Backend (Laravel)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/AuthController.php
│   │   ├── Pegawai/{ProfilController, PengajuanKgbController}.php
│   │   ├── Admin/{AkunController, PegawaiController, UnitKerjaController,
│   │   │          JabatanController, PangkatGolonganController,
│   │   │          RiwayatController, PersyaratanKgbController,
│   │   │          DashboardController, LogAktivitasController,
│   │   │          SkController}.php
│   │   └── Verifikator/{PengajuanMasukController, MemenuhiSyaratController}.php
│   ├── Requests/
│   │   ├── Pegawai/{StorePengajuanKgbRequest, UploadBerkasRequest}.php
│   │   ├── Admin/{StorePegawaiRequest, StorePersyaratanRequest, InputSkRequest}.php
│   │   └── Verifikator/{VerifikasiBerkasRequest, SetujuiPengajuanRequest,
│   │                    TolakPengajuanRequest}.php
│   ├── Resources/  (transformasi JSON: PengajuanResource, PegawaiResource, ...)
│   ├── Middleware/  (role check dari spatie sudah bawaan; tambahan CheckOwnership bila perlu)
│   └── Policies/  (PengajuanPolicy, PegawaiPolicy)
│
├── Models/
│   ├── User.php, Role.php, Notifikasi.php
│   ├── Pegawai.php, Jabatan.php, UnitKerja.php, PangkatGolongan.php
│   ├── RiwayatJabatan.php, RiwayatPangkat.php
│   ├── JenisLayanan.php, PersyaratanMaster.php, TahapanApproval.php
│   ├── Pengajuan.php, DetailBerkas.php, ApprovalLog.php, DetailKgb.php
│   └── LogAktivitas.php
│
├── Services/
│   ├── PengajuanService.php        (RQ-PEG-04, RQ-PEG-08, RQ-ADM-13)
│   ├── VerifikasiService.php       (RQ-VER-04–08, RQ-SYS-05)
│   ├── KgbCalculatorService.php    (RQ-SYS-01, RQ-PEG-02)
│   ├── NotifikasiService.php       (RQ-SYS-07)
│   ├── EksporService.php           (RQ-ADM-11, RQ-VER-11 — Excel-only untuk Verifikator)
│   └── AuditService.php            (RQ-SYS-08 / RQ-ADM-12)
│
├── Jobs/
│   └── DeteksiPengingatKgbJob.php  (dijalankan Scheduler harian — RQ-SYS-02–03)
│
├── Exports/
│   ├── RekapKepegawaianExport.php  (Admin, Excel)
│   └── MemenuhiSyaratKgbExport.php (Verifikator, **Excel saja**)
│
├── Notifications/
│   └── (opsional, bila memakai channel Laravel Notification selain tabel notifikasi custom)
│
└── Console/Kernel.php  (registrasi jadwal DeteksiPengingatKgbJob)

routes/
└── api.php   (satu-satunya route bisnis: grup prefix /api/v1, middleware auth:sanctum + role:)
    (routes/web.php dibiarkan default Laravel — hanya untuk endpoint CSRF cookie
     Sanctum `/sanctum/csrf-cookie`, tidak ada view Blade untuk fitur SISDM)

database/
├── migrations/   (17 tabel + kolom soft delete/append-only sesuai §2)
└── seeders/      (RoleSeeder, JenisLayananSeeder — seed 'KGB' aktif, PersyaratanMasterSeeder)
```

### 4.1 Pemetaan Tanggung Jawab per Layer
| Layer | Tanggung Jawab | Larangan |
|---|---|---|
| Controller | Terima request, panggil Form Request untuk validasi format, delegasikan ke Service, kembalikan Resource | Tidak boleh berisi query Eloquent langsung atau logika bisnis |
| Form Request | Validasi format/tipe data (RQ per field pada SRS §2–6) | Tidak memvalidasi aturan lintas-entitas kompleks (itu tugas Service) |
| Service | Aturan bisnis (RQ-SYS-05, transisi status, side-effect AD-09, dsb.), transaksi DB | Tidak menangani HTTP request/response langsung |
| Policy | Otorisasi kepemilikan (Pegawai hanya lihat miliknya) | Tidak menduplikasi pengecekan role (itu tugas middleware) |
| Model | Relasi Eloquent, cast, scope query sederhana | Tidak boleh memuat logika bisnis kompleks |
| Job/Scheduler | Tugas terjadwal (pengingat KGB) | Tidak dipanggil langsung dari Controller |

### 4.2 Modularitas untuk Perluasan Masa Depan
Struktur `Services/PengajuanService`, `VerifikasiService`, tabel generik `pengajuan`/`detail_berkas`/`approval_log`, dan `jenis_layanan` sengaja dibuat **layanan-agnostik** (parameter `id_layanan`) sehingga saat 6 layanan Kenaikan Pangkat mulai dikembangkan, pola yang sama dapat dipakai ulang — cukup menambah baris baru di `jenis_layanan` + `persyaratan_master` + controller/route baru, **tanpa mengubah skema inti**. Namun sesuai batasan ruang lingkup saat ini, implementasi layanan lain tersebut **tidak dikerjakan** pada tahap ini.

---

## 5. Ringkasan Keputusan Desain Kunci

| Keputusan | Alasan |
|---|---|
| Monolitik Laravel, bukan microservices | Skala organisasi (satu instansi, intranet) tidak membutuhkan kompleksitas microservices |
| Session-based auth, bukan token JWT murni | Cocok untuk skenario intranet single-domain, lebih sederhana dikelola |
| Login via `username` (NIP/NRP), bukan `email` | Konsisten dengan identitas ASN yang resmi dan tidak semua pegawai punya email aktif |
| `detail_kgb` 1-1 terhadap `pengajuan` | KGB hanya satu siklus per pengajuan, bukan banyak-ke-satu |
| Validasi RQ-SYS-05 di Service, bukan trigger DB | Memudahkan pesan error yang informatif ke frontend & unit test, dibanding trigger SQL yang sulit di-debug |
| `nomer_sk`/`tanggal_sk` sebagai kolom di `pengajuan`, bukan tabel `sk` terpisah | Karena hanya mencatat referensi (bukan menerbitkan SK), tidak perlu entitas terpisah |
| Ekspor Verifikator hanya Excel | Ditetapkan eksplisit oleh Yizi — beda dengan ekspor Admin yang mendukung Excel & PDF |
| Backend Laravel API-only + Frontend React JS (SPA terpisah) | Ditetapkan eksplisit oleh Yizi — memisahkan siklus rilis backend/frontend, memudahkan React menjadi UI utama tanpa Blade |

---
*Dokumen ini melengkapi PRD v1.0 dan SRS v1.0. Perubahan besar pada arsitektur atau skema (mis. saat modul Kenaikan Pangkat mulai dikembangkan) memerlukan revisi SDD ini secara eksplisit.*
