# PANDUAN LENGKAP PENGUMPULAN TUGAS PROJEK UMMI (UKK KELAS XII RPL)

## 📌 Identitas Projek
* **Judul Studi Kasus**: *Sistem Manajemen Pengiriman Kargo & Kontainer (LOGIX PRO)*
* **Tempat PKL / IDUKA**: J3LOG & Indobox Cargo Forwarding (Logistik Laut & Ekspedisi)
* **Teknologi**: PHP & MySQL (PDO) + Modern CSS Design System
* **Pengembang**: Muhammad Syeril Ardiansah (Kelas XII RPL - Persiapan UKK)

---

## 🚀 Cara Menjalankan Aplikasi di Komputer

Cukup buka terminal di folder projek ini, lalu jalankan perintah berikut:
```bash
./run.sh
```
Atau jika menggunakan perintah langsung:
```bash
php -d extension=pdo_sqlite.so -S localhost:8000
```
Buka browser dan akses: **`http://localhost:8000`**

### Kredensial Login:
* **Username**: `admin`
* **Password**: `admin123`
*(Tersedia tombol 1-Klik Isi Otomatis di halaman login)*

---

## 📸 Checklist Screenshot (SS) yang Diminta Ummi

Pastikan kamu mengambil tangkapan layar (screenshot) sesuai daftar berikut:

| No | Kebutuhan Form dari Ummi | Halaman & URL di Browser | Keterangan untuk Laporan |
| :---: | :--- | :--- | :--- |
| **1** | **Beranda** | `http://localhost:8000/dashboard.php`<br>`http://localhost:8000/index.php` | Tampilkan Dashboard Utama: Kartu Metrik Kontainer, Total Pengiriman, Nilai Kargo, dan Tabel 5 Pengiriman Terbaru. |
| **2** | **Form Login** | `http://localhost:8000/login.php` | Tampilkan form input Username, Password, tombol Masuk, dan kotak Kredensial Demo. |
| **3** | **Data Master** | `http://localhost:8000/kontainer_form.php`<br>`http://localhost:8000/kontainer.php`<br>`http://localhost:8000/pelanggan_form.php` | • Screenshot Form Input Kontainer (Nomor, Tipe, Kapasitas Ton, Depo, Status).<br>• Screenshot Tabel Data Master Kontainer & Pelanggan Shipper. |
| **4** | **Data Transaksi** | `http://localhost:8000/transaksi_form.php` | Screenshot Formulir Pemesanan Kargo Baru: Pilih Shipper, Unit Kontainer, Rute Pelabuhan Asal-Tujuan, Muatan, Jadwal, dan Total Biaya Otomatis. |
| **5** | **Konfirmasi Berhasil** | `http://localhost:8000/konfirmasi_berhasil.php`<br>`http://localhost:8000/transaksi_detail.php?id=1` | Screenshot banner sukses *"Surat Jalan Berhasil Diterbitkan"* lengkap dengan rincian data tersimpan & lembar Surat Jalan / Waybill siap cetak. |
| **6** | **Konfirmasi Gagal** | `http://localhost:8000/konfirmasi_gagal.php`<br>`http://localhost:8000/login.php` (masukkan pass salah) | Screenshot banner merah *"Validasi Transaksi Gagal (Overload Tonase / Duplikasi Data)"* yang membuktikan sistem memiliki validasi bisnis yang kuat. |
| **7** | **Form Lainnya** | `http://localhost:8000/profil.php`<br>`http://localhost:8000/transaksi_detail.php` | • Form Profil & Keamanan Ubah Password.<br>• Form Update Status Perjalanan Kargo & Log Tracking. |
| **8** | **Bukti GitHub (Bonus)** | Halaman GitHub Repo Kamu | Screenshot repository GitHub setelah di-push (folder projek & riwayat commit). |

---

## 🎬 Naskah / Skrip Rekaman Video Running Program (Durasi 2–3 Menit)

Gunakan panduan alur bicara ini saat merekam layar (pakai OBS Studio atau SimpleScreenRecorder):

1. **Pembukaan (0:00 - 0:30)**:
   > *"Assalamualaikum warahmatullahi wabarakatuh, Ummi dan teman-teman. Nama saya Muhammad Syeril Ardiansah dari kelas XII RPL. Pada kesempatan ini saya mempresentasikan tugas projek bekal UKK dengan mengangkat studi kasus tempat PKL saya di bidang logistik dan cargo forwarding, yaitu LOGIX PRO - Sistem Manajemen Pengiriman Kargo & Kontainer berbasis PHP dan MySQL."*
2. **Demo Beranda Publik & Tracking (0:30 - 0:50)**:
   > *"Pertama, ini adalah halaman Beranda publik. Di sini pelanggan dapat melacak pengiriman kargo secara langsung dengan memasukkan nomor resi, misalnya J3LOG-2610-0001, dan melihat riwayat perjalanan kontainer."*
3. **Demo Form Login & Konfirmasi Gagal (0:50 - 1:15)**:
   > *"Selanjutnya, kita masuk ke halaman Form Login petugas. Jika kita memasukkan password yang salah, sistem akan menampilkan notifikasi Konfirmasi Gagal berwarna merah. Sekarang saya login menggunakan akun administrator."*
4. **Demo Dashboard & Data Master (1:15 - 1:45)**:
   > *"Setelah login, kita diarahkan ke Dashboard Beranda Internal. Di sini ada ringkasan kontainer, shipper, dan omset kargo. Pada menu Data Master, kita bisa mengelola Data Kontainer dan Data Pelanggan. Mari kita coba tambah kontainer baru..."*
5. **Demo Data Transaksi & Konfirmasi Berhasil (1:45 - 2:30)**:
   > *"Sekarang masuk ke inti sistem, yaitu Data Transaksi Pengiriman. Di formulir ini nomor resi ter-generate otomatis J3LOG-YYMM-XXXX. Kita pilih shipper, pilih armada kontainer, tentukan rute pelabuhan, dan berat muatan. Biaya freight dan handling dihitung secara otomatis. Ketika tombol simpan ditekan, sistem menampilkan Konfirmasi Berhasil dan menerbitkan Lembar Surat Jalan resmi yang siap dicetak."*
6. **Penutup (2:30 - 2:50)**:
   > *"Projek ini juga dilengkapi formulir profil dan manajemen status logistik. Kode projek ini telah saya unggah ke repository GitHub. Sekian presentasi projek saya, terima kasih Ummi. Wassalamualaikum warahmatullahi wabarakatuh."*

---

## 🌐 Cara Upload ke GitHub (Mendapatkan Nilai Bonus)

Jalankan perintah ini di dalam folder `TUGAS UMMIE`:
```bash
git init
git add .
git commit -m "feat: inisialisasi projek tugas UKK logistik PHP MySQL"
git branch -M main
```
Lalu buat repository baru di akun GitHub kamu (misal nama repo: `tugas-pkl-logistik`), kemudian hubungkan:
```bash
git remote add origin https://github.com/USERNAME_KAMU/tugas-pkl-logistik.git
git push -u origin main
```
Setelah itu screenshot tampilan repo di browser sebagai bukti pengiriman GitHub bonus!
