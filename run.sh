#!/bin/bash
# ==========================================================
# Script Menjalankan Server Lokal LOGIX PRO
# PHP & MySQL / SQLite Automatic Fallback Engine
# ==========================================================

PORT=8000
echo "=========================================================="
echo "  LOGIX PRO - Sistem Manajemen Kargo & Kontainer IDUKA    "
echo "  Tugas Projek PKL Kelas XII RPL - Persiapan UKK          "
echo "=========================================================="
echo ""
echo "Menjalankan server PHP lokal pada: http://localhost:$PORT"
echo "Buka browser Anda dan akses: http://localhost:$PORT"
echo ""
echo "Tekan CTRL + C untuk menghentikan server."
echo "=========================================================="

php -d extension=pdo_sqlite.so -S localhost:$PORT
