<?php
/**
 * Konfigurasi Database & Helper Global
 * LOGIX PRO - Sistem Manajemen Pengiriman Kargo & Kontainer
 * Studi Kasus PKL IDUKA: J3LOG & Indobox Logistics
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'LOGIX PRO');
define('APP_SUBTITLE', 'Sistem Manajemen Pengiriman Kargo & Kontainer');
define('APP_COMPANY', 'J3LOG & Indobox Freight Logistics IDUKA');

// Parameter Koneksi MySQL / MariaDB (Untuk XAMPP / Server Sekolah)
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'db_logistik_pkl');
define('DB_USER', 'root');
define('DB_PASS', '');

function getDB() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dbPath = __DIR__ . '/../storage/db_logistik.sqlite';
    $storageDir = __DIR__ . '/../storage';
    if (!is_dir($storageDir)) {
        mkdir($storageDir, 0777, true);
    }

    // 1. Coba koneksi ke MySQL terlebih dahulu (Prioritas Utama sesuai tugas Ummi)
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $pdo->setAttribute(PDO::ATTR_DRIVER_NAME, 'mysql');
        return $pdo;
    } catch (PDOException $e) {
        // MySQL gagal (misal belum diimport atau akses lokal dibatasi),
        // gunakan fallback otomatis engine SQLite agar aplikasi langsung running tanpa hambatan!
    }

    // 2. Fallback cerdas ke SQLite
    try {
        $pdo = new PDO("sqlite:" . $dbPath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        // Inisialisasi tabel jika database SQLite baru dibuat
        initSQLiteSchema($pdo);
        return $pdo;
    } catch (Exception $ex) {
        die("<h3>Koneksi Database Gagal:</h3><p>" . htmlspecialchars($ex->getMessage()) . "</p>");
    }
}

/**
 * Inisialisasi struktur dan data awal pada SQLite (Otomatis & Siap Pakai)
 */
function initSQLiteSchema($pdo) {
    $check = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='transaksi_pengiriman'")->fetch();
    if ($check) {
        return; // Sudah terinisialisasi
    }

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nama_lengkap TEXT NOT NULL,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            role TEXT DEFAULT 'operator',
            foto TEXT DEFAULT 'default-avatar.png',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS kontainer (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nomor_kontainer TEXT NOT NULL UNIQUE,
            tipe_kontainer TEXT NOT NULL,
            kapasitas_ton REAL NOT NULL,
            lokasi_depo TEXT NOT NULL,
            kondisi TEXT NOT NULL DEFAULT 'Layak Muat',
            status TEXT DEFAULT 'Tersedia',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS pelanggan (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            kode_pelanggan TEXT NOT NULL UNIQUE,
            nama_perusahaan TEXT NOT NULL,
            penanggung_jawab TEXT NOT NULL,
            no_telp TEXT NOT NULL,
            email TEXT NOT NULL,
            alamat TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS transaksi_pengiriman (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            no_resi TEXT NOT NULL UNIQUE,
            id_pelanggan INTEGER NOT NULL,
            id_kontainer INTEGER NOT NULL,
            pelabuhan_asal TEXT NOT NULL,
            pelabuhan_tujuan TEXT NOT NULL,
            jenis_muatan TEXT NOT NULL,
            berat_muatan_ton REAL NOT NULL,
            tanggal_booking DATE NOT NULL,
            tanggal_muat DATE NOT NULL,
            estimasi_tiba DATE NOT NULL,
            tarif_freight REAL NOT NULL,
            biaya_handling REAL NOT NULL,
            total_biaya REAL NOT NULL,
            status_pengiriman TEXT DEFAULT 'Draft / Booking',
            catatan TEXT,
            created_by INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS riwayat_status (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            id_transaksi INTEGER NOT NULL,
            status TEXT NOT NULL,
            lokasi TEXT NOT NULL,
            keterangan TEXT,
            waktu DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Seed User
    $passHash = password_hash('admin123', PASSWORD_BCRYPT);
    $passOp = password_hash('operator123', PASSWORD_BCRYPT);
    $pdo->exec("
        INSERT OR IGNORE INTO users (id, nama_lengkap, username, email, password, role) VALUES 
        (1, 'Administrator Logistik', 'admin', 'admin@j3log.co.id', '{$passHash}', 'admin'),
        (2, 'Staf Operasional PKL', 'operator', 'operator@j3log.co.id', '{$passOp}', 'operator');
    ");

    // Seed Kontainer
    $pdo->exec("
        INSERT OR IGNORE INTO kontainer (id, nomor_kontainer, tipe_kontainer, kapasitas_ton, lokasi_depo, kondisi, status) VALUES 
        (1, 'JLOG-U260101', 'Dry FCL 20ft', 21.50, 'Depo Tanjung Priok, Jakarta', 'Layak Muat (Grade A)', 'Tersedia'),
        (2, 'JLOG-U260102', 'Dry FCL 40ft High Cube', 28.00, 'Depo Tanjung Priok, Jakarta', 'Layak Muat (Grade A)', 'Sedang Digunakan'),
        (3, 'INDO-C882190', 'Reefer Container 20ft (Pendingin)', 18.00, 'Depo Tanjung Perak, Surabaya', 'Layak Muat (Grade A)', 'Tersedia'),
        (4, 'JLOG-U260104', 'LCL Cargo Container', 15.00, 'Depo Belawan, Medan', 'Layak Muat (Grade B)', 'Tersedia'),
        (5, 'MSKU-4410982', 'Dry FCL 40ft Standar', 26.50, 'Depo Batam Island', 'Perlu Pengecekan Pintu', 'Perawatan');
    ");

    // Seed Pelanggan
    $pdo->exec("
        INSERT OR IGNORE INTO pelanggan (id, kode_pelanggan, nama_perusahaan, penanggung_jawab, no_telp, email, alamat) VALUES 
        (1, 'CUST-001', 'PT Nusantara Elektronik Sentosa', 'Bambang Sudarsono', '081288991122', 'logistik@nusantaraelektronik.com', 'Kawasan Industri MM2100 Cikarang Barat, Bekasi'),
        (2, 'CUST-002', 'CV Samudera Agro Perkasa', 'Dewi Rahmawati', '081377884455', 'agro@samuderaprkasa.co.id', 'Jl. Pergudangan Margomulyo Permai Kav. 14, Surabaya'),
        (3, 'CUST-003', 'PT Global Tekstil Indonesia', 'Hendri Wijaya', '085711223344', 'supply@globaltekstil.com', 'Kawasan Industri Rancaekek KM 23, Bandung'),
        (4, 'CUST-004', 'PT Berkah Pangan Makmur', 'Hj. Siti Fatimah', '082199008811', 'order@berkahpangan.co.id', 'Kawasan Pelabuhan Belawan, Medan');
    ");

    // Seed Transaksi
    $pdo->exec("
        INSERT OR IGNORE INTO transaksi_pengiriman (id, no_resi, id_pelanggan, id_kontainer, pelabuhan_asal, pelabuhan_tujuan, jenis_muatan, berat_muatan_ton, tanggal_booking, tanggal_muat, estimasi_tiba, tarif_freight, biaya_handling, total_biaya, status_pengiriman, catatan, created_by) VALUES 
        (1, 'J3LOG-2610-0001', 1, 2, 'Pelabuhan Tanjung Priok, Jakarta', 'Pelabuhan Tanjung Perak, Surabaya', 'Komponen Elektronik & PCB Board', 19.50, '2026-10-25', '2026-10-27', '2026-10-31', 14500000.00, 1500000.00, 16000000.00, 'Sailing / Dalam Perjalanan', 'Pengiriman ekspres jalur laut via KM Nusantara Indah', 1),
        (2, 'J3LOG-2610-0002', 2, 3, 'Pelabuhan Tanjung Perak, Surabaya', 'Pelabuhan Belawan, Medan', 'Komoditas Buah & Sayur Segar (Cold Chain)', 14.00, '2026-10-28', '2026-10-29', '2026-11-03', 18000000.00, 2200000.00, 20200000.00, 'Loading Kontainer', 'Suhu reefer container wajib dijaga stabil pada -2°C', 1),
        (3, 'J3LOG-2610-0003', 3, 1, 'Pelabuhan Tanjung Priok, Jakarta', 'Jurong Port, Singapore', 'Tekstil Katun Siap Ekspor', 12.80, '2026-10-29', '2026-11-01', '2026-11-06', 22500000.00, 3000000.00, 25500000.00, 'Draft / Booking', 'Menunggu finalisasi dokumen custom clearance PEB', 1);
    ");

    // Seed Tracking
    $pdo->exec("
        INSERT OR IGNORE INTO riwayat_status (id, id_transaksi, status, lokasi, keterangan) VALUES 
        (1, 1, 'Booking Diterima', 'Kantor Operasional Jakarta', 'Booking pengiriman dibuat oleh admin logistik.'),
        (2, 1, 'Loading Selesai', 'Terminal Kontainer Tanjung Priok', 'Kontainer dimuat ke atas kapal KM Nusantara Indah.'),
        (3, 1, 'Sailing / Berlayar', 'Laut Jawa', 'Kapal bertolak menuju Pelabuhan Tanjung Perak Surabaya.'),
        (4, 2, 'Booking Diterima', 'Kantor Operasional Surabaya', 'Pengajuan reefer container disetujui.'),
        (5, 2, 'Loading Kontainer', 'Depo Reefer Tanjung Perak', 'Muatan sedang proses stuffing ke dalam kontainer berpendingin.');
    ");
}

function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function tanggalIndo($tanggal) {
    if (!$tanggal || $tanggal === '0000-00-00') return '-';
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $pecahkan = explode('-', date('Y-m-d', strtotime($tanggal)));
    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}

function generateNoResi($db) {
    $prefix = 'J3LOG-' . date('ym') . '-';
    $stmt = $db->query("SELECT no_resi FROM transaksi_pengiriman WHERE no_resi LIKE '{$prefix}%' ORDER BY id DESC LIMIT 1");
    $last = $stmt->fetch();
    if ($last) {
        $parts = explode('-', $last['no_resi']);
        $lastNumber = (int)end($parts);
        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $nextNumber = '0001';
    }
    return $prefix . $nextNumber;
}

function generateKodePelanggan($db) {
    $stmt = $db->query("SELECT kode_pelanggan FROM pelanggan ORDER BY id DESC LIMIT 1");
    $last = $stmt->fetch();
    if ($last && preg_match('/CUST-(\d+)/', $last['kode_pelanggan'], $matches)) {
        $next = (int)$matches[1] + 1;
        return 'CUST-' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }
    return 'CUST-001';
}

function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // 'success' atau 'danger' / 'warning'
        'message' => $message
    ];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
