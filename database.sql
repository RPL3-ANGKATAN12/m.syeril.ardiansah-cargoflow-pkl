-- =======================================================
-- SISTEM MANAJEMEN PENGIRIMAN KARGO & KONTAINER (LOGIX PRO)
-- Studi Kasus IDUKA PKL: J3LOG & Indobox Cargo Forwarding
-- Basis Data: PHP & MySQL (MariaDB)
-- Disusun untuk Tugas Projek Kelas XII RPL / Persiapan UKK
-- =======================================================

CREATE DATABASE IF NOT EXISTS `db_logistik_pkl` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_logistik_pkl`;

-- 1. Tabel Pengguna (Admin & Operator)
DROP TABLE IF EXISTS `riwayat_status`;
DROP TABLE IF EXISTS `transaksi_pengiriman`;
DROP TABLE IF EXISTS `kontainer`;
DROP TABLE IF EXISTS `pelanggan`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'operator') DEFAULT 'operator',
  `foto` VARCHAR(255) DEFAULT 'default-avatar.png',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password hash default (password: admin123 dan operator123)
-- Hash kompatibel dengan password_verify() PHP
INSERT INTO `users` (`id`, `nama_lengkap`, `username`, `email`, `password`, `role`) VALUES
(1, 'Administrator Logistik', 'admin', 'admin@j3log.co.id', '$2y$10$E6t8jFm9502WwQJc53H7feJ2uO/LpWJ1fXb6M4zW7Z7R5z3ZfFqKy', 'admin'),
(2, 'Staf Operasional PKL', 'operator', 'operator@j3log.co.id', '$2y$10$E6t8jFm9502WwQJc53H7feJ2uO/LpWJ1fXb6M4zW7Z7R5z3ZfFqKy', 'operator');

-- 2. Data Master: Kontainer / Kargo
CREATE TABLE `kontainer` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nomor_kontainer` VARCHAR(30) NOT NULL UNIQUE,
  `tipe_kontainer` VARCHAR(50) NOT NULL,
  `kapasitas_ton` DECIMAL(8,2) NOT NULL,
  `lokasi_depo` VARCHAR(100) NOT NULL,
  `kondisi` VARCHAR(50) NOT NULL DEFAULT 'Layak Muat',
  `status` ENUM('Tersedia', 'Sedang Digunakan', 'Dalam Perjalanan', 'Perawatan') DEFAULT 'Tersedia',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `kontainer` (`id`, `nomor_kontainer`, `tipe_kontainer`, `kapasitas_ton`, `lokasi_depo`, `kondisi`, `status`) VALUES
(1, 'JLOG-U260101', 'Dry FCL 20ft', 21.50, 'Depo Tanjung Priok, Jakarta', 'Layak Muat (Grade A)', 'Tersedia'),
(2, 'JLOG-U260102', 'Dry FCL 40ft High Cube', 28.00, 'Depo Tanjung Priok, Jakarta', 'Layak Muat (Grade A)', 'Sedang Digunakan'),
(3, 'INDO-C882190', 'Reefer Container 20ft (Pendingin)', 18.00, 'Depo Tanjung Perak, Surabaya', 'Layak Muat (Grade A)', 'Tersedia'),
(4, 'JLOG-U260104', 'LCL Cargo Container', 15.00, 'Depo Belawan, Medan', 'Layak Muat (Grade B)', 'Tersedia'),
(5, 'MSKU-4410982', 'Dry FCL 40ft Standar', 26.50, 'Depo Batam Island', 'Perlu Pengecekan Pintu', 'Perawatan');

-- 3. Data Master: Pelanggan / Shipper
CREATE TABLE `pelanggan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_pelanggan` VARCHAR(20) NOT NULL UNIQUE,
  `nama_perusahaan` VARCHAR(150) NOT NULL,
  `penanggung_jawab` VARCHAR(100) NOT NULL,
  `no_telp` VARCHAR(25) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `alamat` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pelanggan` (`id`, `kode_pelanggan`, `nama_perusahaan`, `penanggung_jawab`, `no_telp`, `email`, `alamat`) VALUES
(1, 'CUST-001', 'PT Nusantara Elektronik Sentosa', 'Bambang Sudarsono', '081288991122', 'logistik@nusantaraelektronik.com', 'Kawasan Industri MM2100 Cikarang Barat, Bekasi'),
(2, 'CUST-002', 'CV Samudera Agro Perkasa', 'Dewi Rahmawati', '081377884455', 'agro@samuderaprkasa.co.id', 'Jl. Pergudangan Margomulyo Permai Kav. 14, Surabaya'),
(3, 'CUST-003', 'PT Global Tekstil Indonesia', 'Hendri Wijaya', '085711223344', 'supply@globaltekstil.com', 'Kawasan Industri Rancaekek KM 23, Bandung'),
(4, 'CUST-004', 'PT Berkah Pangan Makmur', 'Hj. Siti Fatimah', '082199008811', 'order@berkahpangan.co.id', 'Kawasan Pelabuhan Belawan, Medan');

-- 4. Data Transaksi: Pengiriman Kargo (Shipment)
CREATE TABLE `transaksi_pengiriman` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `no_resi` VARCHAR(50) NOT NULL UNIQUE,
  `id_pelanggan` INT NOT NULL,
  `id_kontainer` INT NOT NULL,
  `pelabuhan_asal` VARCHAR(100) NOT NULL,
  `pelabuhan_tujuan` VARCHAR(100) NOT NULL,
  `jenis_muatan` VARCHAR(150) NOT NULL,
  `berat_muatan_ton` DECIMAL(8,2) NOT NULL,
  `tanggal_booking` DATE NOT NULL,
  `tanggal_muat` DATE NOT NULL,
  `estimasi_tiba` DATE NOT NULL,
  `tarif_freight` DECIMAL(14,2) NOT NULL,
  `biaya_handling` DECIMAL(14,2) NOT NULL,
  `total_biaya` DECIMAL(14,2) NOT NULL,
  `status_pengiriman` ENUM('Draft / Booking', 'Loading Kontainer', 'Sailing / Dalam Perjalanan', 'Tiba di Tujuan', 'Selesai') DEFAULT 'Draft / Booking',
  `catatan` TEXT,
  `created_by` INT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_kontainer`) REFERENCES `kontainer`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `transaksi_pengiriman` (`id`, `no_resi`, `id_pelanggan`, `id_kontainer`, `pelabuhan_asal`, `pelabuhan_tujuan`, `jenis_muatan`, `berat_muatan_ton`, `tanggal_booking`, `tanggal_muat`, `estimasi_tiba`, `tarif_freight`, `biaya_handling`, `total_biaya`, `status_pengiriman`, `catatan`, `created_by`) VALUES
(1, 'J3LOG-2610-0001', 1, 2, 'Pelabuhan Tanjung Priok, Jakarta', 'Pelabuhan Tanjung Perak, Surabaya', 'Komponen Elektronik & PCB Board', 19.50, '2026-10-25', '2026-10-27', '2026-10-31', 14500000.00, 1500000.00, 16000000.00, 'Sailing / Dalam Perjalanan', 'Pengiriman ekspres jalur laut via KM Nusantara Indah', 1),
(2, 'J3LOG-2610-0002', 2, 3, 'Pelabuhan Tanjung Perak, Surabaya', 'Pelabuhan Belawan, Medan', 'Komoditas Buah & Sayur Segar (Cold Chain)', 14.00, '2026-10-28', '2026-10-29', '2026-11-03', 18000000.00, 2200000.00, 20200000.00, 'Loading Kontainer', 'Suhu reefer container wajib dijaga stabil pada -2°C', 1),
(3, 'J3LOG-2610-0003', 3, 1, 'Pelabuhan Tanjung Priok, Jakarta', 'Jurong Port, Singapore', 'Tekstil Katun Siap Ekspor', 12.80, '2026-10-29', '2026-11-01', '2026-11-06', 22500000.00, 3000000.00, 25500000.00, 'Draft / Booking', 'Menunggu finalisasi dokumen custom clearance PEB', 1);

-- 5. Tabel Riwayat Status / Tracking Pengiriman
CREATE TABLE `riwayat_status` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_transaksi` INT NOT NULL,
  `status` VARCHAR(100) NOT NULL,
  `lokasi` VARCHAR(150) NOT NULL,
  `keterangan` TEXT,
  `waktu` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi_pengiriman`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `riwayat_status` (`id`, `id_transaksi`, `status`, `lokasi`, `keterangan`) VALUES
(1, 1, 'Booking Diterima', 'Kantor Operasional Jakarta', 'Booking pengiriman dibuat oleh admin logistik.'),
(2, 1, 'Loading Selesai', 'Terminal Kontainer Tanjung Priok', 'Kontainer dimuat ke atas kapal KM Nusantara Indah.'),
(3, 1, 'Sailing / Berlayar', 'Laut Jawa', 'Kapal bertolak menuju Pelabuhan Tanjung Perak Surabaya.'),
(4, 2, 'Booking Diterima', 'Kantor Operasional Surabaya', 'Pengajuan reefer container disetujui.'),
(5, 2, 'Loading Kontainer', 'Depo Reefer Tanjung Perak', 'Muatan sedang proses stuffing ke dalam kontainer berpendingin.');
