# LOGIX PRO - Sistem Manajemen Pengiriman Kargo & Kontainer

> **Tugas Projek Bekal UKK Kelas XII RPL**  
> Mengangkat Studi Kasus Tempat PKL: **J3LOG & Indobox Cargo Forwarding (Logistik & Ekspedisi)**  
> Basis Teknologi: **PHP, MySQL (MariaDB) / SQLite Fallback, HTML5, Vanilla CSS Design System, JavaScript**

---

## 📋 Fitur Utama Sistem

1. **Beranda Publik & Tracking Resi Cepat (`index.php`)**:
   - Showcase profil logistik kargo IDUKA.
   - Fitur pelacakan real-time berdasarkan Nomor Resi (misal `J3LOG-2610-0001`).
   - Riwayat perjalanan kontainer (Timeline Tracking).

2. **Form Login Petugas (`login.php`)**:
   - Autentikasi staf operasional dan administrator.
   - Penanganan validasi error / konfirmasi gagal jika password tidak sesuai.
   - Tombol 1-klik pengisian akun demo pengujian.

3. **Dashboard Beranda Internal (`dashboard.php`)**:
   - Kartu metrik: Total Pengiriman, Kontainer Siap Muat, Mitra Shipper, Total Nilai Kargo.
   - Tabel ringkasan 5 pengiriman terbaru dengan status kargo real-time.

4. **Data Master (CRUD)**:
   - **Data Kontainer (`kontainer.php`, `kontainer_form.php`)**: Nomor Kontainer, Tipe (Dry FCL, LCL, Reefer Cold Chain), Kapasitas Beban Ton, Depo Asal, Kondisi, Status Ketersediaan.
   - **Data Pelanggan / Shipper (`pelanggan.php`, `pelanggan_form.php`)**: ID Shipper, Nama PT/Perusahaan, PIC, Telepon, Email, Alamat Gudang.

5. **Data Transaksi Pengiriman (`transaksi.php`, `transaksi_form.php`)**:
   - Generator nomor project / surat jalan otomatis (`J3LOG-YYMM-XXXX`).
   - Relasi dinamis ke Data Master Pelanggan & Kontainer.
   - Validasi batas muatan kontainer (*Anti-Overload Protection*).
   - Perhitungan otomatis Tarif Freight + Biaya Handling Dermaga.

6. **Konfirmasi Berhasil & Surat Jalan (`konfirmasi_berhasil.php`, `transaksi_detail.php`)**:
   - Halaman konfirmasi sukses transaksi dengan ringkasan lengkap.
   - Lembar Surat Jalan resmi (*Bill of Lading / Waybill*) siap cetak (Print-to-PDF).

7. **Konfirmasi Gagal & Validasi (`konfirmasi_gagal.php`)**:
   - Demonstrasi error handling: Overload Muatan Tonase, Duplikasi Data Master, dan Login Invalid.

8. **Form Lainnya (`profil.php`, `transaksi_detail.php`)**:
   - Form pembaruan profil pengguna & ubah kata sandi.
   - Form pembaruan status logistik dan log pergerakan kontainer.

---

## 💻 Cara Menjalankan

1. Buka terminal di folder ini:
   ```bash
   ./run.sh
   ```
   Atau jalankan:
   ```bash
   php -d extension=pdo_sqlite.so -S localhost:8000
   ```
2. Buka browser: **`http://localhost:8000`**
3. Login menggunakan:
   - **Username**: `admin`
   - **Password**: `admin123`

Untuk import basis data ke phpMyAdmin / MySQL XAMPP:
- Gunakan file **`database.sql`** yang tersedia di root direktori.

Panduan lengkap screenshot dan naskah video dapat dibaca di: **`PANDUAN_TUGAS_UMMI.md`**.
