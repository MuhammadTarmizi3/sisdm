-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 15, 2026 at 12:49 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sisdminicoba`
--

-- --------------------------------------------------------

--
-- Table structure for table `approval_log`
--

CREATE TABLE `approval_log` (
  `ID_APPROVAL_LOG` int NOT NULL,
  `ID_PENGAJUAN` int NOT NULL,
  `ID_TAHAPAN` int NOT NULL,
  `ID_USER` int NOT NULL,
  `KEPUTUSAN` varchar(192) DEFAULT NULL,
  `CATATAN` varchar(255) DEFAULT NULL,
  `WAKTU` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_berkas`
--

CREATE TABLE `detail_berkas` (
  `ID_PENGAJUAN` int NOT NULL,
  `ID_PERSYARATAN` int NOT NULL,
  `ID_DETAIL_BERKAS` int NOT NULL,
  `FILE_PATH` varchar(255) DEFAULT NULL,
  `STATUS_VERIFIKASI` varchar(32) DEFAULT NULL,
  `CATATAN` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_kgb`
--

CREATE TABLE `detail_kgb` (
  `ID_PENGAJUAN` int NOT NULL,
  `GAJI_POKOK_LAMA` int DEFAULT NULL,
  `GAJI_POKOK_BARU` int DEFAULT NULL,
  `TMT_KGB_BERIKUTNYA` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jabatan`
--

CREATE TABLE `jabatan` (
  `ID_JABATAN` int NOT NULL,
  `NAMA_JABATAN` varchar(128) DEFAULT NULL,
  `JENIS_JABATAN` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jenis_layanan`
--

CREATE TABLE `jenis_layanan` (
  `ID_LAYANAN` int NOT NULL,
  `KODE_LAYANAN` char(8) DEFAULT NULL,
  `NAMA_LAYANAN` varchar(64) DEFAULT NULL,
  `KATEGORI` varchar(128) DEFAULT NULL,
  `STATUS_LAYANAN` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `ID_LOG` int NOT NULL,
  `ID_USER` int NOT NULL,
  `AKTIVITAS` varchar(255) DEFAULT NULL,
  `ENTITY_TYPE` varchar(50) DEFAULT NULL,
  `ENTITY_ID` int DEFAULT NULL,
  `WAKTU` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `ID_NOTIFIKASI` int NOT NULL,
  `USER_ID` int NOT NULL,
  `JUDUL` varchar(128) DEFAULT NULL,
  `PESAN` varchar(255) DEFAULT NULL,
  `IS_READ` tinyint(1) DEFAULT NULL,
  `CREATED_AT` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pangkat_golongan`
--

CREATE TABLE `pangkat_golongan` (
  `ID_PANGKAT` int NOT NULL,
  `NAMA_PANGKAT` varchar(128) DEFAULT NULL,
  `GOLONGAN_RUANG` varchar(128) DEFAULT NULL,
  `URUTAN_TINGKAT` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pegawai`
--

CREATE TABLE `pegawai` (
  `ID_PEGAWAI` int NOT NULL,
  `ID_UNIT` int DEFAULT NULL,
  `ID_JABATAN` int DEFAULT NULL,
  `ID_PANGKAT` int DEFAULT NULL,
  `NIP_NRP` char(18) NOT NULL,
  `NAMA_PEGAWAI` varchar(128) NOT NULL,
  `TEMPAT_LAHIR` varchar(50) DEFAULT NULL,
  `TGL_LAHIR` date DEFAULT NULL,
  `JENIS_KELAMIN` varchar(20) NOT NULL,
  `NOMER_HP` varchar(13) DEFAULT NULL,
  `STATUS_KEPEGAWAIAN` varchar(20) DEFAULT NULL,
  `TMT_CPNS` date DEFAULT NULL,
  `TMT_PNS` date DEFAULT NULL,
  `PENDIDIKAN_TERAKHIR` varchar(50) DEFAULT NULL,
  `FOTO` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan`
--

CREATE TABLE `pengajuan` (
  `ID_PENGAJUAN` int NOT NULL,
  `ID_PEGAWAI` int NOT NULL,
  `ID_LAYANAN` int DEFAULT NULL,
  `TANGGAL_PENGAJUAN` date DEFAULT NULL,
  `STATUS_PENGAJUAN` varchar(32) DEFAULT NULL,
  `CATATAN_VERIFIKATOR` varchar(255) DEFAULT NULL,
  `NOMER_SK` varchar(32) DEFAULT NULL,
  `TANGGAL_SK` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `persyaratan_master`
--

CREATE TABLE `persyaratan_master` (
  `ID_PERSYARATAN` int NOT NULL,
  `ID_LAYANAN` int DEFAULT NULL,
  `NAMA_PERSYARATAN` varchar(128) DEFAULT NULL,
  `WAJIB` tinyint(1) DEFAULT NULL,
  `URUTAN` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_jabatan`
--

CREATE TABLE `riwayat_jabatan` (
  `ID_RIWAYAT` int NOT NULL,
  `ID_PEGAWAI` int NOT NULL,
  `ID_JABATAN` int NOT NULL,
  `TMT_JABATAN` date DEFAULT NULL,
  `NOMER_SK` varchar(32) DEFAULT NULL,
  `TANGGAL_SK` date DEFAULT NULL,
  `FILE_SK` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_pangkat`
--

CREATE TABLE `riwayat_pangkat` (
  `ID_RIWAYAT_PANGKAT` int NOT NULL,
  `ID_PEGAWAI` int NOT NULL,
  `ID_PANGKAT` int NOT NULL,
  `TMT_PANGKAT` date DEFAULT NULL,
  `NOMER_SK` varchar(32) DEFAULT NULL,
  `TANGGAL_SK` date DEFAULT NULL,
  `FILE_SK` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `ID_ROLE` int NOT NULL,
  `KODE_ROLE` char(4) DEFAULT NULL,
  `NAMA_ROLE` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tahapan_approval`
--

CREATE TABLE `tahapan_approval` (
  `ID_APPROVAL` int NOT NULL,
  `ID_LAYANAN` int DEFAULT NULL,
  `URUTAN` int DEFAULT NULL,
  `NAMA_TAHAP` varchar(255) DEFAULT NULL,
  `ID_ROLE_BERWENANG` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `unit_kerja`
--

CREATE TABLE `unit_kerja` (
  `ID_UNIT` int NOT NULL,
  `NAMA_UNIT` varchar(128) DEFAULT NULL,
  `TIPE_UNIT` char(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `USER_ID` int NOT NULL,
  `ID_PEGAWAI` int DEFAULT NULL,
  `ID_ROLE` int NOT NULL,
  `USERNAME` varchar(50) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_berkas`
--
ALTER TABLE `detail_berkas`
  ADD PRIMARY KEY (`ID_DETAIL_BERKAS`),
  ADD KEY `FK_MEMPUNYAI_DETAIL_BERKAS` (`ID_PENGAJUAN`),
  ADD KEY `FK_DETAIL_BERKAS_PERSYARATAN` (`ID_PERSYARATAN`);

--
-- Indexes for table `detail_kgb`
--
ALTER TABLE `detail_kgb`
  ADD PRIMARY KEY (`ID_PENGAJUAN`);

--
-- Indexes for table `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`ID_JABATAN`);

--
-- Indexes for table `jenis_layanan`
--
ALTER TABLE `jenis_layanan`
  ADD PRIMARY KEY (`ID_LAYANAN`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`ID_LOG`),
  ADD KEY `FK_LOG_USER` (`ID_USER`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`ID_NOTIFIKASI`),
  ADD KEY `FK_MENDAPAT` (`USER_ID`);

--
-- Indexes for table `pangkat_golongan`
--
ALTER TABLE `pangkat_golongan`
  ADD PRIMARY KEY (`ID_PANGKAT`);

--
-- Indexes for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`ID_PEGAWAI`),
  ADD UNIQUE KEY `uq_nip` (`NIP_NRP`),
  ADD KEY `FK_MEMPUNYAI` (`ID_PANGKAT`),
  ADD KEY `FK_MENJABAT` (`ID_JABATAN`),
  ADD KEY `FK_TERGABUNG` (`ID_UNIT`);

--
-- Indexes for table `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD PRIMARY KEY (`ID_PENGAJUAN`),
  ADD KEY `FK_DAPAT_MENGAJUKAN` (`ID_PEGAWAI`),
  ADD KEY `FK_TERMASUK` (`ID_LAYANAN`);

--
-- Indexes for table `persyaratan_master`
--
ALTER TABLE `persyaratan_master`
  ADD PRIMARY KEY (`ID_PERSYARATAN`),
  ADD KEY `FK_DIMILIKI` (`ID_LAYANAN`);

--
-- Indexes for table `riwayat_jabatan`
--
ALTER TABLE `riwayat_jabatan`
  ADD PRIMARY KEY (`ID_RIWAYAT`),
  ADD KEY `FK_MEMPUNYAI_RIWAYAT_JABATAN` (`ID_PEGAWAI`),
  ADD KEY `FK_RIWAYAT_JABATAN_JABATAN` (`ID_JABATAN`);

--
-- Indexes for table `riwayat_pangkat`
--
ALTER TABLE `riwayat_pangkat`
  ADD PRIMARY KEY (`ID_RIWAYAT_PANGKAT`),
  ADD KEY `FK_MEMILIKI` (`ID_PEGAWAI`),
  ADD KEY `FK_RIWAYAT_PANGKAT_PANGKAT` (`ID_PANGKAT`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`ID_ROLE`);

--
-- Indexes for table `tahapan_approval`
--
ALTER TABLE `tahapan_approval`
  ADD PRIMARY KEY (`ID_APPROVAL`),
  ADD KEY `FK_TERDAPAT` (`ID_LAYANAN`),
  ADD KEY `FK_TAHAPAN_ROLE` (`ID_ROLE_BERWENANG`);

--
-- Indexes for table `unit_kerja`
--
ALTER TABLE `unit_kerja`
  ADD PRIMARY KEY (`ID_UNIT`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`USER_ID`),
  ADD UNIQUE KEY `uq_username` (`USERNAME`),
  ADD KEY `FK_PEGAWAI_MEMBUAT` (`ID_PEGAWAI`),
  ADD KEY `FK_USER_MEMILIKI` (`ID_ROLE`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `ID_LOG` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_berkas`
--
ALTER TABLE `detail_berkas`
  ADD CONSTRAINT `FK_DETAIL_BERKAS_PERSYARATAN` FOREIGN KEY (`ID_PERSYARATAN`) REFERENCES `persyaratan_master` (`ID_PERSYARATAN`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_MEMPUNYAI_DETAIL_BERKAS` FOREIGN KEY (`ID_PENGAJUAN`) REFERENCES `pengajuan` (`ID_PENGAJUAN`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_kgb`
--
ALTER TABLE `detail_kgb`
  ADD CONSTRAINT `FK_MEMPUNYAI_DETAIL` FOREIGN KEY (`ID_PENGAJUAN`) REFERENCES `pengajuan` (`ID_PENGAJUAN`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `FK_LOG_USER` FOREIGN KEY (`ID_USER`) REFERENCES `users` (`USER_ID`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `FK_MENDAPAT` FOREIGN KEY (`USER_ID`) REFERENCES `users` (`USER_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD CONSTRAINT `FK_MEMPUNYAI` FOREIGN KEY (`ID_PANGKAT`) REFERENCES `pangkat_golongan` (`ID_PANGKAT`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_MENJABAT` FOREIGN KEY (`ID_JABATAN`) REFERENCES `jabatan` (`ID_JABATAN`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_TERGABUNG` FOREIGN KEY (`ID_UNIT`) REFERENCES `unit_kerja` (`ID_UNIT`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD CONSTRAINT `FK_DAPAT_MENGAJUKAN` FOREIGN KEY (`ID_PEGAWAI`) REFERENCES `pegawai` (`ID_PEGAWAI`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_TERMASUK` FOREIGN KEY (`ID_LAYANAN`) REFERENCES `jenis_layanan` (`ID_LAYANAN`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `persyaratan_master`
--
ALTER TABLE `persyaratan_master`
  ADD CONSTRAINT `FK_DIMILIKI` FOREIGN KEY (`ID_LAYANAN`) REFERENCES `jenis_layanan` (`ID_LAYANAN`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `riwayat_jabatan`
--
ALTER TABLE `riwayat_jabatan`
  ADD CONSTRAINT `FK_MEMPUNYAI_RIWAYAT_JABATAN` FOREIGN KEY (`ID_PEGAWAI`) REFERENCES `pegawai` (`ID_PEGAWAI`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_RIWAYAT_JABATAN_JABATAN` FOREIGN KEY (`ID_JABATAN`) REFERENCES `jabatan` (`ID_JABATAN`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `riwayat_pangkat`
--
ALTER TABLE `riwayat_pangkat`
  ADD CONSTRAINT `FK_MEMILIKI` FOREIGN KEY (`ID_PEGAWAI`) REFERENCES `pegawai` (`ID_PEGAWAI`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_RIWAYAT_PANGKAT_PANGKAT` FOREIGN KEY (`ID_PANGKAT`) REFERENCES `pangkat_golongan` (`ID_PANGKAT`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `tahapan_approval`
--
ALTER TABLE `tahapan_approval`
  ADD CONSTRAINT `FK_TAHAPAN_ROLE` FOREIGN KEY (`ID_ROLE_BERWENANG`) REFERENCES `roles` (`ID_ROLE`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_TERDAPAT` FOREIGN KEY (`ID_LAYANAN`) REFERENCES `jenis_layanan` (`ID_LAYANAN`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_PEGAWAI_MEMBUAT` FOREIGN KEY (`ID_PEGAWAI`) REFERENCES `pegawai` (`ID_PEGAWAI`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_USER_MEMILIKI` FOREIGN KEY (`ID_ROLE`) REFERENCES `roles` (`ID_ROLE`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
