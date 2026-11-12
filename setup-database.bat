@echo off
title SISTEMA - Setup Database
cd /d %~dp0
echo ============================================
echo   SISTEMA - Setup Database
echo ============================================
echo.
echo Pastikan MySQL (XAMPP/Laragon) sudah menyala.
echo.

echo [1/4] Membuat database sistema_db (jika belum ada)...
"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS sistema_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo [2/4] Menjalankan migration dan data awal...
php artisan migrate --seed --force

echo [3/4] Membuat function, procedure, dan trigger...
"C:\xampp\mysql\bin\mysql.exe" -u root sistema_db < "database\sql\database_objects.sql"

echo [4/4] Membuat link storage...
php artisan storage:link

echo.
echo Selesai. Database sistema_db siap digunakan.
echo Akun demo: admin/admin123, petugas/petugas123, peminjam/peminjam123
echo.
pause