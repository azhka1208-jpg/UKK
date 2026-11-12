@echo off
title SISTEMA - Server
cd /d %~dp0
echo ============================================
echo   SISTEMA - Menjalankan server lokal
echo ============================================
echo.
echo Pastikan MySQL (XAMPP/Laragon) sudah menyala.
echo Aplikasi akan dibuka di: http://127.0.0.1:8000
echo.
start "" http://127.0.0.1:8000
php artisan serve --host=127.0.0.1 --port=8000
pause