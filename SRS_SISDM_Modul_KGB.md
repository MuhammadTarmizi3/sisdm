# Software Requirements Specification (SRS)
## Sistem Informasi Sumber Daya Manusia (SISDM) — Modul Kenaikan Gaji Berkala (KGB)
### BNNP Kalimantan Selatan

| | |
|---|---|
| **Versi** | 1.0 |
| **Tanggal** | 15 September 2026 |
| **Rujukan** | PRD SISDM Modul KGB v1.0; Kebutuhan Fungsional SISDM v1.2; ERD SISDM (17 tabel); Activity Diagram AD-01 s.d. AD-09; Use Case Diagram Sea/Fish Level |
| **Ruang Lingkup** | **KGB saja.** Layanan lain (6 jenis Kenaikan Pangkat + 5 layanan lain) di luar cakupan dokumen ini. |

---

## 1. Pendahuluan

### 1.1 Tujuan Dokumen
Dokumen ini merinci **aturan validasi, perilaku sistem (behavior), dan aturan aplikasi (business rules)** untuk setiap kebutuhan fungsional Modul KGB, sebagai acuan implementasi backend (Laravel), desain form, dan pengujian. SRS ini adalah turunan teknis dari PRD; setiap requirement di sini tertelusur ke kode KF/UC pada Dokumen Kebutuhan Fungsional v1.2.

### 1.2 Konvensi Penomoran
- **RQ-[MODUL]-[NN]** — kode requirement teknis baru di dokumen ini.
- Setiap RQ mencantumkan referensi ke KF/UC terkait dan tabel ERD yang terdampak.

### 1.3 Entitas Data Inti (rujukan cepat ERD)
`users`, `roles`, `notifikasi`, `pegawai`, `jabatan`, `unit_kerja`, `pangkat_golongan`, `riwayat_jabatan`, `riwayat_pangkat`, `jenis_layanan`, `persyaratan_master`, `tahapan_approval`, `pengajuan`, `detail_berkas`, `approval_log`, `detail_kgb`.

---

## 2. Requirement Umum (Autentikasi & Akses)

### RQ-UM-01 — Login (KF-UM-01, KF-UM-02 / UC-UM-01)
**Behavior:**
- Login menggunakan **NIP/NRP** (disimpan pada kolom `username` tabel `users`) + `password`. Kolom `email` (jika terisi) bersifat opsional dan **tidak digunakan sebagai kredensial login**.
- Sesi terbentuk hanya setelah kredensial tervalidasi dan role (`roles.kode_role`) diketahui.

**Validasi:**
- `username` wajib diisi, format sesuai pola NIP (18 digit numerik) atau NRP yang berlaku.
- `password` wajib diisi; dibandingkan dalam bentuk hash (bcrypt/argon2), tidak pernah dibandingkan plaintext.
- Maksimal percobaan gagal berturut-turut: disarankan 5x sebelum akun dikunci sementara (lockout) — mencegah brute force.
- Jika akun berstatus nonaktif (`is_active = false`, dikelola Admin via KF-ADM-01), login **ditolak** dengan pesan generik "akun tidak aktif, hubungi Admin".

**Aturan Aplikasi:**
- Satu `users` hanya terhubung ke satu `pegawai` (relasi 1-1 `dibuat`); satu `pegawai` hanya boleh punya satu akun aktif.
- Role menentukan menu dan endpoint yang bisa diakses (RBAC via `spatie/laravel-permission`); percobaan akses endpoint di luar role menghasilkan HTTP 403, bukan redirect diam-diam.

### RQ-UM-02 — Logout (KF-UM-03 / UC-UM-02)
**Behavior:** Sesi (token/session Laravel) dihapus/invalidasi saat logout; token lama tidak bisa dipakai ulang.
**Validasi:** Endpoint logout hanya bisa dipanggil oleh sesi yang sedang aktif (memerlukan token valid).

---

## 3. Requirement Pegawai

### RQ-PEG-01 — Melihat Data Kepegawaian & Riwayat (KF-PEG-01, KF-PEG-02 / UC-PEG-01)
**Behavior:** Menampilkan data `pegawai` milik akun yang sedang login beserta seluruh baris `riwayat_jabatan` dan `riwayat_pangkat` miliknya, diurutkan dari `tmt_jabatan`/`tmt_pangkat` terbaru ke terlama.
**Aturan Aplikasi:** Pegawai **hanya bisa melihat datanya sendiri** — filter WHERE berdasarkan `pegawai.user_id = auth()->id()` diterapkan di setiap query, tidak boleh mengandalkan parameter ID dari client (mencegah IDOR).

### RQ-PEG-02 — Pengingat KGB (KF-PEG-03, KF-SYS-01–03 / UC-PEG-02)
**Behavior:**
- Sistem menghitung `perkiraan_tmt_kgb_berikutnya` = `tmt_kgb_terakhir` (dari `detail_kgb`/data KGB terakhir yang dikelola Admin) + interval siklus KGB (umumnya 2 tahun, dikonfigurasi bukan di-hardcode).
- Setiap kali Pegawai membuka dashboard, sistem membandingkan tanggal hari ini dengan `perkiraan_tmt_kgb_berikutnya - 3 bulan`. Jika tanggal hari ini ≥ ambang tersebut **dan** belum ada pengajuan KGB aktif (status bukan Diajukan/Disetujui) untuk siklus tersebut, pengingat ditampilkan.
**Validasi:** Perhitungan tidak dijalankan jika `detail_kgb`/data KGB terakhir Pegawai kosong — sistem menampilkan status "data KGB terakhir belum tersedia, hubungi Admin" alih-alih pengingat.
**Aturan Aplikasi:** Pengingat bersifat **informatif**, tidak memblokir Pegawai untuk tetap mengajukan KGB di luar jendela pengingat (mis. pengajuan susulan).

### RQ-PEG-03 — Melihat Persyaratan KGB (KF-PEG-04, KF-SYS-04 / UC-PEG-03)
**Behavior:** Checklist ditarik secara dinamis dari `persyaratan_master` WHERE `jenis_layanan = KGB` AND status aktif = true, diurutkan berdasarkan `urutan`.
**Validasi:** Jika tidak ada persyaratan aktif terdaftar, sistem menampilkan peringatan ke Pegawai bahwa pengajuan belum bisa dibuat (bukan checklist kosong yang menyesatkan).

### RQ-PEG-04 — Mengajukan KGB (KF-PEG-05, KF-PEG-06 / UC-PEG-04, AD-01)
**Behavior (alur AD-01):**
1. Sistem menampilkan data pegawai + checklist persyaratan aktif.
2. Pegawai melengkapi data & mengunggah berkas per item persyaratan (loop hingga lengkap).
3. Saat "Mengirim pengajuan" ditekan, sistem menjalankan pemeriksaan kelengkapan.
4. Jika lengkap → insert baris baru `pengajuan` dengan `status_pengajuan = 'Diajukan'`, `tanggal_pengajuan = now()`; insert baris `detail_berkas` untuk tiap file dengan `status_verifikasi = 'belum diperiksa'`; kirim notifikasi ke Verifikator.
5. Jika tidak lengkap → tampilkan daftar item yang kurang, tidak membuat baris `pengajuan`.

**Validasi:**
- Setiap item persyaratan bertanda `wajib = true` pada `persyaratan_master` **harus** memiliki berkas terunggah sebelum pengajuan bisa dikirim.
- Format file dibatasi (mis. PDF/JPG/PNG), ukuran maksimum per file (mis. 5 MB) — ditolak di sisi server, bukan hanya validasi client-side.
- **Satu Pegawai tidak boleh memiliki lebih dari satu pengajuan KGB aktif secara bersamaan** (status Diajukan/Perlu perbaikan) — dicegah dengan unique constraint logis (cek query) sebelum insert, agar tidak ada duplikasi antrean di Verifikator.
- `tmt_kgb_berikutnya` yang tersimpan di `detail_kgb` pada pengajuan diisi otomatis dari hasil perhitungan RQ-PEG-02, bukan input manual Pegawai, untuk mencegah manipulasi tanggal.

**Aturan Aplikasi:** Status awal pengajuan selalu **"Diajukan"**, tidak pernah "Draf" tersimpan di database (jika UI menyediakan draf, itu disimpan sisi client/local, bukan sebagai baris `pengajuan` — untuk menjaga tabel `pengajuan` tetap bersih sebagai catatan resmi/audit).

### RQ-PEG-05 — Mengunggah Berkas (KF-PEG-06 / UC-PEG-05)
**Validasi:**
- Field `file_path` di `detail_berkas` wajib merujuk ke penyimpanan server (bukan URL eksternal).
- Setiap unggah ulang pada item yang sama **mengganti** referensi file lama (versi terbaru yang aktif), namun file lama tidak langsung dihapus fisik selama masih relevan untuk audit (soft-replace).
- Nama file disanitasi (hindari path traversal, karakter khusus).

### RQ-PEG-06 — Mengirim Pengajuan (KF-PEG-05 / UC-PEG-06)
Sudah tercakup dalam RQ-PEG-04 (langkah 3–5). Tidak ada requirement tambahan di luar itu.

### RQ-PEG-07 — Memantau Status Pengajuan (KF-PEG-07, KF-PEG-09–10 / UC-PEG-07, AD-02)
**Behavior:** Menampilkan daftar `pengajuan` milik Pegawai + `status_pengajuan` + `catatan_verifikator` + status tiap `detail_berkas`.
**Validasi:** Jika belum ada pengajuan sama sekali, tampilkan pesan "belum ada pengajuan" (bukan halaman kosong tanpa keterangan) — sesuai AD-02.
**Aturan Aplikasi:** Status yang mungkin muncul: `Diajukan`, `Perlu perbaikan`, `Disetujui`, `Ditolak`. Tidak ada status lain yang valid ditampilkan ke Pegawai.

### RQ-PEG-08 — Memperbaiki Berkas (KF-PEG-08, KF-PEG-10 / UC-PEG-08, AD-08)
**Behavior (alur AD-08):**
1. Hanya bisa diakses jika `status_pengajuan = 'Perlu perbaikan'`.
2. Pegawai melihat catatan per berkas yang tidak valid, memperbaiki data/unggah ulang.
3. Saat kirim ulang, sistem memeriksa kelengkapan berkas wajib lagi (sama seperti RQ-PEG-04).
4. Jika lengkap → `status_pengajuan` kembali menjadi **`Diajukan`** (bukan status baru "Diajukan Ulang") dan notifikasi terkirim ke Verifikator; pengajuan **masuk kembali ke antrean AD-06** menggunakan baris `pengajuan` yang sama (tidak membuat baris baru).
**Validasi:** Pegawai **tidak bisa** mengakses fitur ini untuk pengajuan berstatus `Diajukan`, `Disetujui`, atau `Ditolak` — akses ditolak (403) jika dicoba.
**Aturan Aplikasi:** Riwayat perbaikan (versi berkas sebelumnya, catatan lama) tetap tersimpan sebagai jejak audit di `approval_log`, tidak ditimpa/dihapus.

---

## 4. Requirement Admin Kepegawaian

### RQ-ADM-01 — Kelola Akun Pengguna (KF-ADM-01 / UC-ADM-01)
**Validasi:**
- `username` (NIP/NRP) unik di seluruh tabel `users`.
- Password baru (saat create/reset) memenuhi kebijakan minimum (panjang ≥ 8, kombinasi huruf-angka) dan langsung di-hash.
- Role wajib dipilih dari daftar `roles` yang tersedia (`Pegawai`, `Admin Kepegawaian`, `Verifikator`) — tidak boleh kosong.
- Menonaktifkan akun (`is_active = false`) **tidak menghapus** baris `users`/`pegawai` (soft, bukan delete) — akun bisa diaktifkan kembali.
**Aturan Aplikasi:** Admin tidak dapat menonaktifkan akunnya sendiri sebagai pencegahan lockout tak sengaja (opsional, tergantung kebijakan; jika diterapkan, harus ada minimal satu Admin aktif di sistem).

### RQ-ADM-02 — Kelola Data Induk Pegawai (KF-ADM-02 / UC-ADM-02)
**Validasi:**
- `nip` unik dan wajib 18 digit numerik.
- `tanggal_lahir` tidak boleh di masa depan.
- Penghapusan data pegawai **selalu soft delete** (`deleted_at`), tidak pernah hard delete — karena pegawai memiliki riwayat jabatan/pangkat dan pengajuan yang harus tetap tertelusur.
- Pegawai yang di-soft-delete otomatis **tidak muncul** di daftar aktif manapun (form pengajuan, dashboard, dsb.) namun datanya tetap ada untuk kebutuhan audit/historis.
**Aturan Aplikasi:** Field `jabatan`, `unit_kerja`, `pangkat_golongan` pada `pegawai` merujuk (FK) ke tabel master — RESTRICT: baris master **tidak bisa dihapus** selama masih dirujuk oleh minimal satu `pegawai`.

### RQ-ADM-03–05 — Kelola Data Master (Unit Kerja, Jabatan, Pangkat/Golongan) (KF-ADM-03–05 / UC-ADM-03–05)
**Validasi:**
- Nama master (unik per jenis, mis. `nama_unit`, `nama_jabatan`, kombinasi `urutan_tingkat`+`golongan_ruang` untuk pangkat) tidak boleh duplikat.
- **RESTRICT on delete:** jika master data (unit kerja/jabatan/pangkat) masih dipakai oleh minimal satu `pegawai` atau baris riwayat, penghapusan **ditolak** dengan pesan jelas — Admin harus memindahkan/menonaktifkan referensi dulu.
**Aturan Aplikasi:** Perubahan nama master tidak memengaruhi riwayat historis (`riwayat_jabatan`/`riwayat_pangkat` menyimpan snapshot melalui FK, tampilan riwayat lama tetap konsisten dengan FK yang sama, bukan duplikasi data nama).

### RQ-ADM-06–07 — Kelola Riwayat Jabatan & Pangkat/Golongan (KF-ADM-06–07 / UC-ADM-06–07)
**Behavior:**
- Baris riwayat dapat diinput **manual oleh Admin** (mis. migrasi data lama) **atau otomatis** oleh sistem saat Admin menginput nomor & tanggal SK pada pengajuan KGB berstatus Disetujui (lihat RQ-ADM-13 / AD-09).
**Validasi:**
- `tmt_jabatan`/`tmt_pangkat` wajib diisi dan tidak boleh mendahului tanggal riwayat sebelumnya milik pegawai yang sama (urutan kronologis).
- `nomer_sk` dan `file_sk` wajib diisi bersamaan (tidak boleh salah satu kosong jika yang lain terisi).
**Aturan Aplikasi:** Riwayat bersifat **append-only** dari sisi bisnis — baris riwayat yang sudah tercatat sebaiknya tidak dihapus, hanya bisa dikoreksi (edit) oleh Admin bila terjadi kesalahan input, dengan tercatat di log aktivitas.

### RQ-ADM-08 — Kelola Data KGB Terakhir (KF-ADM-08, KF-SYS-01 / UC-ADM-08)
**Validasi:** `tmt_kgb_berikutnya`/data dasar KGB terakhir wajib berupa tanggal valid dan tidak boleh di masa depan (karena ini data historis, bukan proyeksi).
**Aturan Aplikasi:** Data ini adalah **satu-satunya sumber** perhitungan RQ-PEG-02 (perkiraan TMT KGB berikutnya) — jika kosong, pengingat otomatis tidak dapat dihasilkan untuk pegawai tersebut.

### RQ-ADM-09 — Kelola Persyaratan KGB (KF-ADM-09, KF-SYS-04 / UC-ADM-09)
**Validasi:**
- `nama_persyaratan` unik dalam `jenis_layanan = KGB`.
- Field `wajib` (boolean) dan `urutan` (integer, unik per jenis layanan) wajib diisi.
**Aturan Aplikasi:**
- Mengubah status `wajib` suatu persyaratan **tidak mengubah** riwayat pengajuan yang sudah ada (perubahan hanya berlaku untuk pengajuan baru setelah perubahan disimpan) — mencegah pengajuan lama dinyatakan tidak lengkap secara retroaktif.
- Menghapus persyaratan yang **sudah pernah dipakai** pada `detail_berkas` di pengajuan manapun **tidak diperbolehkan** (RESTRICT) — sebagai gantinya, Admin menonaktifkan (soft) persyaratan tersebut.

### RQ-ADM-10 — Dashboard Rekap Kepegawaian (KF-ADM-10 / UC-ADM-10, AD-05)
**Behavior:** Ringkasan dihitung real-time (atau cache berjangka pendek) dari `pegawai` aktif, dikelompokkan per `unit_kerja`/`pangkat_golongan` sesuai filter yang dipilih.
**Validasi:** Filter kosong = tampilkan seluruh data aktif (bukan error).

### RQ-ADM-11 — Ekspor Laporan Rekap (KF-ADM-11 / UC-ADM-11, AD-05)
**Behavior:** Admin dapat memilih format **Excel atau PDF**; sistem membuat berkas laporan sesuai filter yang sedang aktif di layar saat ekspor diminta.
**Validasi:** Format harus salah satu dari {Excel, PDF} — pilihan lain ditolak.
**Aturan Aplikasi:** *(Catatan: ini berbeda dari fitur ekspor Verifikator — lihat RQ-VER-11 — yang **hanya Excel**, tidak ada opsi PDF.)*

### RQ-ADM-12 — Log Aktivitas (KF-ADM-12, KF-SYS-08 / UC-ADM-12)
**Behavior:** Setiap aksi penting (tambah/ubah/hapus data master & pegawai, perubahan akun, keputusan Verifikator, input SK) dicatat ke `log_aktivitas` dengan aktor, waktu, dan ringkasan aksi.
**Aturan Aplikasi:** `log_aktivitas` bersifat **append-only, tidak pernah cascade delete atau bisa diedit/dihapus** oleh siapa pun melalui UI — demi integritas audit.

### RQ-ADM-13 — Input Nomor & Tanggal SK KGB (AD-09)
**Behavior:**
1. Hanya berlaku untuk `pengajuan` dengan `status_pengajuan = 'Disetujui'`.
2. Admin mengisi `nomor_sk` dan `tanggal_sk` pada baris `pengajuan` tersebut.
3. Sistem menyimpan data tanpa mengubah `status_pengajuan` (tetap "Disetujui").
4. Penyimpanan ini **memicu insert otomatis** satu baris baru ke `riwayat_jabatan` dan/atau `riwayat_pangkat` (tergantung jenis dampak KGB — untuk KGB murni umumnya hanya menyentuh riwayat gaji/`detail_kgb`, bukan riwayat jabatan/pangkat; **klarifikasi:** insert otomatis berlaku bila kenaikan gaji berkala tersebut turut mengubah pangkat/golongan sesuai aturan kepegawaian yang berlaku — jika tidak, cukup `detail_kgb` yang diperbarui).

**Validasi:**
- `nomor_sk` dan `tanggal_sk` **tidak bisa diisi** untuk pengajuan yang belum berstatus Disetujui — tombol/endpoint dinonaktifkan di sisi backend, bukan hanya UI.
- `nomor_sk` unik per pengajuan (tidak boleh dua pengajuan berbeda memakai nomor SK yang sama).
- `tanggal_sk` tidak boleh mendahului `tanggal_pengajuan`.
- Field ini **hanya bisa diisi sekali** secara normal; perubahan berikutnya (koreksi) harus tercatat di log aktivitas sebagai revisi, bukan penimpaan diam-diam.

**Aturan Aplikasi:** Ini adalah **efek samping (side-effect trigger), bukan transisi status** — perbedaan ini krusial untuk desain UI (tidak boleh ada tombol "Ubah status jadi SK Diterbitkan") dan untuk data model (status pengajuan tetap satu status akhir "Disetujui" selamanya).

---

## 5. Requirement Verifikator

### RQ-VER-01–03 — Melihat Pengajuan Masuk, Detail Pegawai, Berkas (KF-VER-01–03 / UC-VER-02–04)
**Behavior:** Daftar hanya menampilkan `pengajuan` dengan `status_pengajuan = 'Diajukan'` (termasuk yang kembali dari perbaikan). Detail pegawai & berkas ditampilkan read-only bagi Verifikator (Verifikator tidak dapat mengubah data induk pegawai — itu wewenang Admin).
**Validasi:** Verifikator tidak dapat membuka detail pengajuan yang sudah bukan miliknya untuk diproses (mis. sudah diproses Verifikator lain jika multi-Verifikator) — cegah race condition dengan pengecekan status sebelum submit keputusan (optimistic locking sederhana: submit ditolak jika status sudah berubah sejak halaman dimuat).

### RQ-VER-04–05 — Mencatat Hasil Verifikasi & Catatan (KF-VER-04–05 / UC-VER-05, AD-06)
**Validasi:**
- Setiap baris `detail_berkas` yang diperiksa harus diberi salah satu dari status: `valid`, `tidak valid`, `perlu perbaikan` — tidak boleh dibiarkan kosong/"belum diperiksa" saat Verifikator mengambil keputusan akhir (setuju/tolak/kembalikan).
- Catatan (`catatan_verifikator`) **wajib diisi** ketika keputusan adalah "Tolak" atau "Perlu perbaikan" (agar Pegawai tahu apa yang harus diperbaiki) — validasi menolak submit kosong.

### RQ-VER-06 — Mengembalikan Pengajuan (KF-VER-06 / UC-VER-06, AD-06)
**Behavior:** `status_pengajuan` → `'Perlu perbaikan'`; `catatan_verifikator` tersimpan; notifikasi ke Pegawai; baris `approval_log` baru dengan `keputusan = 'Perlu perbaikan'`.
**Validasi:** Hanya bisa dipilih jika minimal satu `detail_berkas` berstatus `tidak valid`/`perlu perbaikan` (konsisten secara logis — tidak masuk akal mengembalikan pengajuan yang semua berkasnya valid).

### RQ-VER-07 — Menyetujui Pengajuan (KF-VER-07, KF-SYS-05–06 / UC-VER-07, AD-06)
**Behavior:** `status_pengajuan` → `'Disetujui'` (status akhir); otomatis masuk ke daftar memenuhi syarat (query, bukan tabel duplikat terpisah — daftar memenuhi syarat = view/query `pengajuan WHERE status = 'Disetujui'`); notifikasi ke Pegawai; baris `approval_log` baru.
**Validasi (KF-SYS-05 — aturan paling kritis di modul ini):**
- Sistem **wajib memblokir** persetujuan (mengembalikan error, bukan hanya peringatan UI) apabila terdapat **minimal satu** `detail_berkas` dengan `persyaratan_master.wajib = true` yang `status_verifikasi != 'valid'`.
- Validasi ini dilakukan di **backend** (server-side), tidak boleh hanya mengandalkan disable-button di frontend.
**Aturan Aplikasi:** Setelah `Disetujui`, **tidak ada aksi apa pun** (termasuk oleh Verifikator sendiri) yang dapat mengubah status ini kembali — jika terjadi kesalahan, koreksi dilakukan melalui prosedur manual di luar sistem dengan pencatatan di log aktivitas, bukan lewat fitur "batalkan persetujuan" pada UI standar.

### RQ-VER-08 — Menolak Pengajuan (KF-VER-08 / UC-VER-08)
**Behavior:** `status_pengajuan` → `'Ditolak'` (status akhir); `catatan_verifikator`/alasan penolakan wajib diisi; notifikasi ke Pegawai.
**Validasi:** Sama seperti persetujuan — begitu `Ditolak`, tidak bisa diubah lagi melalui alur normal.

### RQ-VER-09–10 — Melihat & Memfilter Daftar Memenuhi Syarat (KF-VER-09–10 / UC-VER-09–10, AD-07)
**Behavior:** Query dasar: `pengajuan WHERE jenis_layanan = KGB AND status_pengajuan = 'Disetujui'`. Filter tambahan (unit kerja, pangkat/golongan, rentang tanggal disetujui) bersifat opsional dan tidak mengubah query dasar.
**Validasi:** Jika hasil filter kosong, tampilkan pesan "data tidak ditemukan" (sesuai AD-07), bukan halaman kosong tanpa keterangan.

### RQ-VER-11 — Ekspor Daftar ke Excel (KF-VER-11 / UC-VER-11, AD-07)
**Behavior:** Menghasilkan berkas **Excel (.xlsx) saja** — sesuai hasil pencarian/filter yang sedang ditampilkan di layar saat ekspor diminta.
**Validasi:**
- **Tidak ada opsi format lain (PDF, CSV, dsb.) untuk fitur ini** — berbeda dengan ekspor rekap Admin (RQ-ADM-11) yang mendukung Excel dan PDF.
- Kolom minimum dalam file Excel: NIP, nama pegawai, unit kerja, pangkat/golongan, tanggal disetujui, nomor pengajuan — cukup untuk proses administrasi SK di luar sistem.
**Aturan Aplikasi:** File yang diekspor adalah **keluaran akhir** sistem untuk modul KGB; tidak ada proses lanjutan (penerbitan SK, dsb.) yang terjadi di dalam SISDM setelah ekspor ini.

---

## 6. Requirement Proses Otomatis Sistem

### RQ-SYS-01 — Hitung Perkiraan TMT KGB (KF-SYS-01)
Lihat detail perhitungan di RQ-PEG-02. Requirement tambahan: perhitungan harus **idempotent** (dijalankan ulang kapan pun menghasilkan angka yang sama selama data KGB terakhir tidak berubah) — tidak disimpan sebagai nilai statis yang bisa basi, melainkan dihitung saat dibutuhkan atau di-refresh terjadwal (mis. job harian).

### RQ-SYS-02–03 — Deteksi & Kirim Pengingat (KF-SYS-02–03)
**Behavior:** Job terjadwal (harian) memindai seluruh `pegawai` aktif, membandingkan tanggal ke ambang H-3 bulan, membuat baris `notifikasi` dengan `jenis = 'pengingat_kgb'` bila memenuhi kriteria.
**Validasi:** Pengingat **tidak dikirim berulang setiap hari** untuk pegawai yang sama dalam satu siklus — sistem mengecek apakah pengingat untuk siklus KGB tersebut sudah pernah dibuat sebelum insert baru (mencegah spam notifikasi).

### RQ-SYS-04 — Checklist Dinamis (KF-SYS-04)
Lihat RQ-PEG-03/RQ-ADM-09. Requirement tambahan: checklist yang ditampilkan ke Pegawai saat mengajukan **wajib** merupakan snapshot `persyaratan_master` aktif **pada saat pengajuan dibuat** — perubahan konfigurasi Admin setelahnya tidak memengaruhi pengajuan yang statusnya sudah bukan draf (lihat catatan di RQ-ADM-09).

### RQ-SYS-05 — Validasi Syarat Persetujuan (KF-SYS-05)
Detail penuh di RQ-VER-07. Ini adalah **aturan aplikasi paling kritis** di seluruh modul KGB — kegagalan menerapkannya di backend adalah cacat serius (celah agar pengajuan tidak lengkap bisa disetujui).

### RQ-SYS-06 — Perbarui Daftar Memenuhi Syarat (KF-SYS-06)
**Behavior:** Tidak ada tabel/proses ETL terpisah — "daftar memenuhi syarat" **selalu** merupakan hasil query langsung terhadap `pengajuan` berstatus `Disetujui` saat diminta (RQ-VER-09), sehingga selalu real-time tanpa risiko data usang.

### RQ-SYS-07 — Kirim Notifikasi (KF-SYS-07)
**Behavior:** Setiap perubahan `status_pengajuan` atau `status_verifikasi` per berkas memicu insert baris `notifikasi` dengan `jenis` yang sesuai (mis. `submisi_kgb`, `perlu_perbaikan`, `disetujui`, `ditolak`, `pengingat_kgb`) agar Pegawai/Verifikator dapat membedakan jenis notifikasi di UI.
**Validasi:** `jenis` wajib salah satu dari daftar nilai yang telah ditetapkan (enum/lookup), tidak bebas teks.

### RQ-SYS-08 — Catat Audit Trail (KF-SYS-08)
Lihat RQ-ADM-12. Tambahan: log mencatat minimal **aktor (user_id), aksi, entitas & ID yang terdampak, waktu, dan nilai sebelum/sesudah** untuk perubahan data penting (khususnya perubahan status pengajuan dan input SK).

---

## 7. Aturan Integritas Data (Ringkasan Lintas Modul)

| Aturan | Penerapan |
|---|---|
| RESTRICT pada FK data master | `jabatan`, `unit_kerja`, `pangkat_golongan`, `persyaratan_master` tidak bisa dihapus selama masih dirujuk |
| CASCADE pada anak transaksional non-audit | Mis. `detail_berkas` ikut terhapus jika `pengajuan` induitnya dihapus (kasus sangat jarang/hanya untuk data uji) |
| **Tidak pernah cascade delete** | `pengajuan`, `approval_log`, `log_aktivitas` — demi audit & legal, baris ini tidak boleh hilang meski entitas terkait berubah |
| Soft delete | `pegawai` — dihapus secara logis (`deleted_at`), bukan fisik |
| Append-only | `riwayat_jabatan`, `riwayat_pangkat`, `approval_log`, `log_aktivitas`, `notifikasi` |
| Login | Berbasis `username` (NIP/NRP), bukan `email` |
| Status akhir tunggal | `pengajuan.status_pengajuan = 'Disetujui'` atau `'Ditolak'` tidak dapat diubah lagi melalui alur normal aplikasi |

## 8. Requirement Non-Fungsional Terkait (Ringkas)

- **Keamanan:** Semua endpoint tervalidasi role (RBAC); validasi kritis (RQ-SYS-05, RQ-VER-07) **wajib** di server-side, tidak boleh hanya client-side.
- **Auditability:** Setiap perubahan status dan input SK harus dapat ditelusuri kembali (siapa, kapan, perubahan apa) melalui `approval_log`/`log_aktivitas`.
- **Konsistensi:** Daftar memenuhi syarat KGB (Verifikator) dan dashboard rekap (Admin) harus selalu mencerminkan data terkini (query real-time, bukan cache basi berjangka panjang).
- **Ketertelusuran:** Setiap requirement pada dokumen ini tertaut ke kode KF/UC pada Dokumen Kebutuhan Fungsional v1.2 dan ke Activity Diagram terkait (AD-01 s.d. AD-09).

---
*Dokumen ini melengkapi PRD SISDM Modul KGB v1.0 dengan detail teknis validasi, behavior, dan aturan aplikasi. Perubahan lingkup (mis. penambahan layanan Kenaikan Pangkat) memerlukan revisi SRS ini secara eksplisit.*
