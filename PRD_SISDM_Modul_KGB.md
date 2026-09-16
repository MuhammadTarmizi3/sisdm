# Product Requirements Document (PRD)
## Sistem Informasi Sumber Daya Manusia (SISDM) — Modul Kenaikan Gaji Berkala (KGB)
### BNNP Kalimantan Selatan

| | |
|---|---|
| **Versi** | 1.0 |
| **Tanggal** | 15 September 2026 |
| **Disusun oleh** | Yizi (PKL — Perencanaan, Desain Basis Data, dan Dokumentasi SISDM) |
| **Status** | Final untuk tahap pengembangan Modul KGB |
| **Rujukan** | Laporan Perencanaan Sistem SISDM BNNP Kalsel; Dokumen Kebutuhan Fungsional SISDM v1.2 (4 September 2026); ERD SISDM; Use Case Diagram (Sea Level, Fish Level Pegawai/Admin/Verifikator); Activity Diagram AD-01 s.d. AD-08 |

> **⚠️ PENETAPAN RUANG LINGKUP (BERLAKU UNTUK SELURUH TAHAP PENGEMBANGAN SELANJUTNYA)**
> Mulai dokumen ini dan seterusnya, **pengembangan SISDM difokuskan hanya pada satu layanan: Kenaikan Gaji Berkala (KGB)**. Enam jenis layanan Kenaikan Pangkat (KP Reguler, UKP PI, UKP UDKP, UKP Jabatan Fungsional, UKP Struktural Reguler, UKP Struktural Pilihan) dan lima layanan lain (Tugas Belajar, Tugas Belajar Mandiri, Pencantuman Gelar, Ujian Dinas Tk I/II, Alih Status) **ditunda sepenuhnya** dan tidak dibahas, dirancang, atau diimplementasikan pada tahap ini. Keputusan ini bersifat tetap sampai ada pernyataan eksplisit untuk memperluas ruang lingkup.

---

## 1. Latar Belakang

BNNP Kalimantan Selatan saat ini belum memiliki sistem informasi kepegawaian internal yang mendigitalkan proses layanan administrasi SDM. Proses pengajuan dan verifikasi Kenaikan Gaji Berkala (KGB) — salah satu layanan kepegawaian rutin dengan volume tinggi — masih dilakukan secara manual, sehingga:

- Pegawai tidak memiliki visibilitas atas status pengajuannya secara real-time.
- Admin Kepegawaian kesulitan memantau rekap dan riwayat data kepegawaian secara terpusat.
- Proses verifikasi berkas persyaratan rawan tercecer dan sulit ditelusuri (tidak ada audit trail).
- Tidak ada mekanisme pengingat otomatis menjelang jatuh tempo KGB seorang pegawai.

SISDM dirancang sebagai **sistem intranet, berdiri sendiri (standalone)**, tanpa integrasi ke SIMPEG BKN, untuk mendigitalkan proses ini secara bertahap. **Tahap pertama dan satu-satunya fokus saat ini adalah modul KGB.**

## 2. Tujuan Produk

1. **Mendigitalkan alur pengajuan KGB** dari sisi Pegawai — mulai dari pengecekan persyaratan, pengunggahan berkas, pengiriman pengajuan, hingga pemantauan status — sehingga tidak lagi bergantung pada proses manual/kertas.
2. **Menyediakan mekanisme verifikasi terstruktur** bagi Verifikator untuk memeriksa kelengkapan dan keabsahan berkas, memberikan catatan, serta menetapkan keputusan akhir (disetujui/ditolak/perlu perbaikan) atas setiap pengajuan KGB.
3. **Menyediakan alat kelola data kepegawaian terpusat** bagi Admin Kepegawaian — mencakup data induk pegawai, data master (unit kerja, jabatan, pangkat/golongan), riwayat jabatan/pangkat, data KGB terakhir, serta konfigurasi persyaratan KGB.
4. **Menghasilkan keluaran administratif** berupa daftar pegawai yang telah dinyatakan memenuhi syarat KGB (hasil persetujuan Verifikator) dalam format Excel, sebagai dasar proses penerbitan SK dan pembaruan gaji yang tetap dilakukan **di luar sistem**.
5. **Meningkatkan keandalan proses** melalui notifikasi otomatis, pengingat jatuh tempo KGB, checklist persyaratan yang konsisten (data-driven), dan pencatatan log aktivitas untuk keperluan audit.

## 3. Ruang Lingkup

### 3.1 Termasuk dalam ruang lingkup (Modul KGB)
- Pengajuan KGB oleh Pegawai beserta pengunggahan dan perbaikan berkas persyaratan.
- Verifikasi berkas dan penetapan keputusan (disetujui / perlu perbaikan / ditolak) oleh Verifikator.
- Pengelolaan data kepegawaian, data master, riwayat jabatan/pangkat, data KGB terakhir, dan persyaratan KGB oleh Admin Kepegawaian.
- Dashboard rekap kepegawaian dan ekspor laporan (Excel/PDF) oleh Admin.
- Ekspor daftar pegawai memenuhi syarat KGB (Excel) oleh Verifikator.
- Notifikasi dalam aplikasi, pengingat otomatis jatuh tempo KGB, dan log aktivitas/audit trail.

### 3.2 Di luar ruang lingkup (ditegaskan, bukan kelalaian)
- **Persetujuan berjenjang setelah Verifikator** — alur berhenti di level Verifikator; Kasatker berada di luar batas sistem.
- Penerbitan, penomoran, atau pencatatan resmi Surat Keputusan (SK) KGB — sistem hanya merekam nomor dan tanggal SK yang diinput Admin sebagai data, bukan menerbitkannya.
- Perhitungan nominal gaji baru atau besaran kenaikan gaji.
- Pembaruan data pada sistem penggajian/payroll, serta pencairan/pembayaran gaji.
- **Enam layanan Kenaikan Pangkat** dan **lima layanan lain** (Tugas Belajar, Tugas Belajar Mandiri, Pencantuman Gelar, Ujian Dinas Tk I/II, Alih Status) — seluruhnya ditunda ke tahap pengembangan berikutnya (lihat kotak peringatan di atas).
- Integrasi dengan SIMPEG BKN atau sistem eksternal lain.

## 4. Pengguna / Aktor Sistem

Sistem memiliki tiga aktor manusia, ditambah satu "aktor" proses otomatis internal.

| Aktor | Deskripsi | Tanggung Jawab Utama |
|---|---|---|
| **Pegawai** | Aparatur Sipil Negara (ASN) BNNP Kalsel yang berhak mengajukan KGB | Mengajukan KGB, mengunggah berkas persyaratan, memperbaiki berkas sesuai catatan Verifikator, memantau status dan hasil verifikasi pengajuannya sendiri |
| **Admin Kepegawaian** | Petugas pengelola data dan konfigurasi sistem | Mengelola akun pengguna, data induk pegawai, data master (unit kerja, jabatan, pangkat/golongan), riwayat jabatan/pangkat, data KGB terakhir, persyaratan KGB, dashboard rekap, ekspor laporan, dan log aktivitas |
| **Verifikator** | Petugas yang memeriksa dan memutuskan kelayakan pengajuan | Memeriksa berkas, mencatat hasil verifikasi per berkas, menyetujui/menolak/mengembalikan pengajuan, melihat & mengekspor daftar pegawai memenuhi syarat KGB |
| **Sistem (Proses Otomatis)** | Bukan aktor manusia — berjalan otomatis di balik layar | Menghitung perkiraan TMT KGB berikutnya, mendeteksi & mengirim pengingat, menampilkan checklist dinamis, mencegah persetujuan jika berkas wajib belum valid, memperbarui daftar memenuhi syarat, mengirim notifikasi, mencatat audit trail |

**Catatan penting:** Kasatker **tidak** menjadi aktor sistem. Alur persetujuan KGB berakhir di level Verifikator; ini adalah penyederhanaan yang disengaja dari alur dua tahap pada dokumen perencanaan awal (Laporan Perencanaan Sistem, bab 9).

## 5. Fitur Produk

Fitur dikelompokkan per aktor, mengacu pada kode kebutuhan fungsional (KF) dan use case (UC) yang telah divalidasi.

### 5.1 Fitur Umum (Semua Pengguna)
| Fitur | Deskripsi | Kode Terkait |
|---|---|---|
| Login | Autentikasi menggunakan NIP/NRP (bukan email) dan password; hak akses ditentukan oleh role | KF-UM-01, KF-UM-02 / UC-UM-01 |
| Logout | Mengakhiri sesi secara aman | KF-UM-03 / UC-UM-02 |

### 5.2 Fitur Pegawai
| Fitur | Deskripsi | Kode Terkait |
|---|---|---|
| Melihat Data Kepegawaian Sendiri | Menampilkan data pribadi, riwayat jabatan, dan riwayat pangkat/golongan | KF-PEG-01, KF-PEG-02 / UC-PEG-01 |
| Pengingat KGB | Notifikasi otomatis 3 bulan sebelum perkiraan jatuh tempo KGB | KF-PEG-03 / UC-PEG-02 |
| Melihat Persyaratan KGB | Checklist persyaratan aktif sesuai konfigurasi Admin | KF-PEG-04 / UC-PEG-03 |
| Mengajukan KGB | Menyiapkan dan mengirim pengajuan beserta berkas | KF-PEG-05, KF-PEG-06 / UC-PEG-04 |
| Mengunggah Berkas | Unggah dokumen per item persyaratan | KF-PEG-06 / UC-PEG-05 |
| Mengirim Pengajuan | Validasi kelengkapan lalu mengubah status menjadi "Diajukan" | KF-PEG-05 / UC-PEG-06 |
| Memantau Status Pengajuan | Melihat status per berkas, status pengajuan, dan catatan Verifikator | KF-PEG-07, KF-PEG-09, KF-PEG-10 / UC-PEG-07 |
| Memperbaiki Berkas | Mengunggah ulang/memperbaiki data sesuai catatan perbaikan, lalu mengirim ulang | KF-PEG-08, KF-PEG-10 / UC-PEG-08 |

### 5.3 Fitur Admin Kepegawaian
| Fitur | Deskripsi | Kode Terkait |
|---|---|---|
| Kelola Akun Pengguna | Tambah/lihat/ubah/aktifkan/nonaktifkan akun & role | KF-ADM-01 / UC-ADM-01 |
| Kelola Data Induk Pegawai | CRUD data pegawai (soft delete) | KF-ADM-02 / UC-ADM-02 |
| Kelola Data Master (Unit Kerja, Jabatan, Pangkat/Golongan) | CRUD data master yang menjadi rujukan sistem | KF-ADM-03–05 / UC-ADM-03–05 |
| Kelola Riwayat Jabatan & Pangkat/Golongan | Mencatat riwayat perubahan; sebagian insert otomatis dipicu input SK | KF-ADM-06–07 / UC-ADM-06–07 |
| Kelola Data KGB Terakhir | Dasar perhitungan perkiraan TMT KGB berikutnya | KF-ADM-08, KF-SYS-01 / UC-ADM-08 |
| Kelola Persyaratan KGB | Menentukan daftar persyaratan aktif (data-driven checklist) | KF-ADM-09, KF-SYS-04 / UC-ADM-09 |
| Dashboard Rekap Kepegawaian | Ringkasan data kepegawaian, filter unit/pangkat | KF-ADM-10 / UC-ADM-10 |
| Ekspor Laporan Rekap | Ekspor Excel/PDF sesuai filter | KF-ADM-11 / UC-ADM-11 |
| Log Aktivitas | Menampilkan audit trail aktivitas pengguna | KF-ADM-12, KF-SYS-08 / UC-ADM-12 |
| Input Nomor & Tanggal SK KGB | Mencatat data SK (bukan menerbitkan) — memicu insert otomatis ke riwayat_jabatan/riwayat_pangkat, **bukan** perubahan status pengajuan | (AD-09, lihat §6) |

### 5.4 Fitur Verifikator
| Fitur | Deskripsi | Kode Terkait |
|---|---|---|
| Melihat Pengajuan Masuk | Daftar pengajuan berstatus "Diajukan" | KF-VER-01 / UC-VER-02 |
| Melihat Detail Pegawai | Data pegawai pemohon sebagai bahan pemeriksaan | KF-VER-02 / UC-VER-03 |
| Memeriksa Berkas | Membuka/mengunduh berkas persyaratan | KF-VER-03 / UC-VER-04 |
| Mencatat Hasil Verifikasi | Status valid/tidak valid/perlu perbaikan per berkas + catatan | KF-VER-04–05 / UC-VER-05 |
| Mengembalikan Pengajuan | Kirim balik ke Pegawai untuk diperbaiki | KF-VER-06 / UC-VER-06 |
| Menyetujui Pengajuan | Hanya jika seluruh berkas wajib valid | KF-VER-07, KF-SYS-05–06 / UC-VER-07 |
| Menolak Pengajuan | Disertai alasan penolakan | KF-VER-08 / UC-VER-08 |
| Melihat Daftar Memenuhi Syarat | Daftar bersumber dari pengajuan berstatus "Disetujui" | KF-VER-09, KF-SYS-06 / UC-VER-09 |
| Cari & Filter Daftar | Pencarian/filter pada daftar memenuhi syarat | KF-VER-10 / UC-VER-10 |
| Ekspor ke Excel | Output akhir untuk proses administrasi SK di luar sistem | KF-VER-11 / UC-VER-11 |

### 5.5 Fitur Proses Otomatis Sistem
| Fitur | Deskripsi | Kode Terkait |
|---|---|---|
| Hitung Perkiraan TMT KGB | Berdasarkan data KGB terakhir | KF-SYS-01 / UC-SYS-01 |
| Deteksi & Kirim Pengingat | 3 bulan sebelum jatuh tempo | KF-SYS-02–03 / UC-SYS-02–03 |
| Checklist Dinamis | Mengikuti konfigurasi `persyaratan_master` aktif | KF-SYS-04 / UC-SYS-04 |
| Validasi Syarat Persetujuan | Mencegah persetujuan jika ada berkas wajib belum valid | KF-SYS-05 / UC-SYS-05 |
| Perbarui Daftar Memenuhi Syarat | Otomatis saat status berubah menjadi "Disetujui" | KF-SYS-06 / UC-SYS-06 |
| Kirim Notifikasi | Setiap perubahan status berkas/pengajuan | KF-SYS-07 / UC-SYS-07 |
| Catat Audit Trail | Aktivitas penting seluruh pengguna | KF-SYS-08 / UC-SYS-08 |

## 6. Alur Utama (Flow)

Delapan alur utama telah divalidasi dalam bentuk Activity Diagram (AD-01 s.d. AD-08), ditambah satu alur pendukung (AD-09).

### 6.1 AD-01 — Mengajukan KGB (Pegawai)
Pegawai membuka menu pengajuan → sistem menampilkan data & persyaratan KGB aktif → Pegawai melengkapi data dan mengunggah berkas (loop hingga lengkap) → mengirim pengajuan → sistem memeriksa kelengkapan berkas wajib → jika lengkap, status disimpan sebagai **Diajukan** dan notifikasi dikirim ke Verifikator; jika tidak, sistem menampilkan kekurangan dan Pegawai melengkapi kembali.

### 6.2 AD-02 — Memantau Pengajuan KGB (Pegawai)
Pegawai membuka daftar pengajuan miliknya → sistem mengambil data → jika ada, tampil daftar & status; jika tidak, tampil pesan kosong → Pegawai memilih satu pengajuan → sistem menampilkan status, berkas, dan catatan Verifikator → Pegawai membaca hasil pemeriksaan. Jika status **Perlu perbaikan**, alur berlanjut ke AD-08.

### 6.3 AD-03 — Mengelola Data Kepegawaian (Admin)
Admin memilih jenis data dan tindakan (tambah/ubah/hapus) → sistem menampilkan formulir/konfirmasi → Admin mengisi/konfirmasi → mengirim perubahan → sistem memvalidasi isian dan keterkaitan data → jika valid, perubahan disimpan, data terkait diperbarui, dan aktivitas dicatat; jika tidak, alasan ketidakvalidan ditampilkan. Cakupan: akun, pegawai, unit, jabatan, pangkat/golongan, riwayat, dan data KGB terakhir.

### 6.4 AD-04 — Mengelola Persyaratan KGB (Admin)
Admin membuka pengelolaan persyaratan → sistem menampilkan daftar aktif → Admin menambah/mengubah/menghapus → menyimpan perubahan → sistem memvalidasi → jika valid, daftar aktif diperbarui dan tercatat sebagai acuan Pegawai saat mengajukan KGB berikutnya.

### 6.5 AD-05 — Memantau Rekap Kepegawaian (Admin)
Admin membuka dashboard → sistem menampilkan ringkasan → Admin memilih filter unit kerja/pangkat → sistem menghitung & menampilkan rekap sesuai filter → Admin dapat memilih ekspor (Excel/PDF) → sistem membuat berkas laporan → Admin mengunduh.

### 6.6 AD-06 — Memverifikasi Pengajuan KGB (Verifikator)
Verifikator memilih pengajuan berstatus Diajukan → sistem menampilkan data pegawai, persyaratan, dan berkas → Verifikator memeriksa dan mencatat validitas tiap persyaratan → mengambil keputusan:
- **Setujui** (jika semua berkas wajib valid) → status **Disetujui**, masuk daftar memenuhi syarat, notifikasi terkirim;
- **Perlu perbaikan** → status **Perlu perbaikan** + catatan, notifikasi ke Pegawai (lanjut ke AD-08);
- **Tolak** → status **Ditolak** + alasan, notifikasi ke Pegawai.

Keputusan sepenuhnya ditetapkan oleh Verifikator, bukan otomatis oleh sistem.

### 6.7 AD-07 — Memperoleh Daftar Pegawai Memenuhi Syarat KGB (Verifikator)
Verifikator membuka daftar memenuhi syarat → sistem menampilkan daftar dari pengajuan **Disetujui** → Verifikator mengisi pencarian/filter (opsional) → sistem mengambil data sesuai kriteria → jika ada data, ditampilkan; jika tidak, tampil pesan kosong → Verifikator meminta ekspor Excel → sistem membuat berkas → Verifikator mengunduh. *(Penerbitan SK dan pelaksanaan kenaikan gaji berada di luar sistem.)*

### 6.8 AD-08 — Memperbaiki Berkas KGB (Pegawai)
Pegawai membuka pengajuan berstatus **Perlu perbaikan** → sistem menampilkan catatan & status tiap berkas → Pegawai memperbaiki data/mengunggah ulang (loop hingga lengkap) → mengirim ulang pengajuan → sistem memeriksa kelengkapan → jika lengkap, status kembali menjadi **Diajukan** dan notifikasi terkirim ke Verifikator (kembali ke antrean AD-06); jika tidak, kekurangan ditampilkan kembali.

### 6.9 AD-09 — Menginput Nomor dan Tanggal SK KGB (Admin) *(pendukung)*
Setelah pengajuan berstatus **Disetujui**, Admin menginput `nomor_sk` dan `tanggal_sk` pada data pengajuan → sistem menyimpan data SK tersebut **tanpa mengubah status pengajuan** (status tetap "Disetujui") → penyimpanan ini **memicu insert otomatis** ke tabel `riwayat_jabatan` dan/atau `riwayat_pangkat`. Ini adalah efek samping (side-effect), bukan transisi status.

### 6.10 Peta Status Pengajuan (`pengajuan.status_pengajuan`)
```
Diajukan → (Verifikator memeriksa)
   ├── Disetujui   [status akhir tunggal — tidak berubah lagi setelah ini]
   ├── Perlu perbaikan → (Pegawai perbaiki, AD-08) → Diajukan (kembali ke antrean)
   └── Ditolak     [status akhir]
```

## 7. Aturan Bisnis Utama

1. Kelayakan KGB seorang Pegawai **ditetapkan oleh Verifikator**, bukan diputuskan otomatis oleh sistem.
2. Pengajuan hanya dapat disetujui setelah **seluruh berkas wajib** berstatus valid.
3. Pengajuan yang memerlukan perbaikan dikembalikan ke Pegawai beserta catatan Verifikator.
4. Daftar pegawai memenuhi syarat hanya memuat pengajuan berstatus **Disetujui**.
5. File Excel adalah keluaran akhir sistem untuk proses administrasi KGB (penerbitan SK, dsb.) **di luar SISDM**.
6. Status "Disetujui" adalah status akhir tunggal; input nomor/tanggal SK oleh Admin tidak mengubah status, hanya memicu pencatatan riwayat.

## 8. Batasan Sistem

- Intranet-only, standalone — tidak terhubung ke SIMPEG BKN atau sistem eksternal lain.
- Tidak ada persetujuan berjenjang setelah Verifikator (Kasatker di luar batas sistem).
- Tidak menerbitkan/menomori SK secara resmi — hanya mencatat nomor & tanggal yang diinput manual oleh Admin.
- Tidak menghitung nominal gaji baru atau memperbarui sistem payroll.
- **Layanan Kenaikan Pangkat (6 jenis) dan lima layanan lain ditunda** — di luar cakupan pengembangan saat ini (lihat §3.2 dan catatan ruang lingkup di awal dokumen).

## 9. Kriteria Keberhasilan (Indikator Kualitatif)

- Pegawai dapat mengajukan KGB dan memantau statusnya sepenuhnya secara digital tanpa proses kertas.
- Verifikator dapat menuntaskan verifikasi satu pengajuan (memeriksa berkas → mencatat hasil → memutuskan) dalam satu alur berkelanjutan di sistem.
- Admin memiliki satu sumber data kepegawaian terpusat (single source of truth) untuk unit kerja, jabatan, pangkat/golongan, dan riwayatnya.
- Setiap pengajuan yang disetujui otomatis dan konsisten muncul di daftar memenuhi syarat tanpa entri ganda/manual.
- Seluruh perubahan status dan data penting tercatat di log aktivitas untuk keperluan audit.

## 10. Lampiran / Referensi

- ERD SISDM (17 tabel — `users`, `roles`, `notifikasi`, `pegawai`, `jabatan`, `unit_kerja`, `pangkat_golongan`, `riwayat_jabatan`, `riwayat_pangkat`, `jenis_layanan`, `persyaratan_master`, `tahapan_approval`, `pengajuan`, `detail_berkas`, `approval_log`, `detail_kgb`, dan pendukung lain)
- Use Case Diagram: Sea Level (SISDM keseluruhan), Fish Level Pegawai, Fish Level Admin, Fish Level Verifikator
- Activity Diagram AD-01 s.d. AD-08 (Modul KGB) + AD-09 (pendukung, input SK)
- Dokumen Kebutuhan Fungsional dan Deskripsi Use Case SISDM v1.2 (4 September 2026)
- Laporan Perencanaan Sistem SISDM BNNP Kalsel (`perencanaansisdmbnnpkalsel.pdf`)

---
*Dokumen ini menjadi acuan utama untuk penyusunan SRS, SDD, dan implementasi Modul KGB. Perubahan ruang lingkup di masa mendatang (mis. penambahan layanan Kenaikan Pangkat) memerlukan revisi PRD ini secara eksplisit.*
