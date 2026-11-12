# Debugging — Catatan Masalah & Perbaikan

Dokumen ini mencatat masalah nyata yang ditemui selama pengembangan/pemasangan dan cara memperbaikinya.

## 1. MySQL gagal start (InnoDB crash recovery)
- **Gejala:** `mysqld` berhenti, log: `InnoDB: Failing assertion: slot`, `corruption in the InnoDB tablespace`.
- **Penyebab:** Shutdown tidak bersih sehingga redo log tidak konsisten.
- **Perbaikan:** Jalankan sekali dengan `--innodb-force-recovery=1`, lakukan `mysqladmin shutdown`, lalu start normal. Setelah redo log dipulihkan, MySQL dapat start normal.
- **Verifikasi:** Port 3306 terbuka, `SELECT VERSION();` berhasil.

## 2. Composer menolak instalasi paket (security advisory)
- **Gejala:** `phpoffice/phpspreadsheet` dan `barryvdh/laravel-dompdf` diblokir karena advisory.
- **Penyebab:** Konfigurasi `audit.block-insecure` aktif pada Composer baru.
- **Perbaikan:** `composer config audit.block-insecure false` lalu install. (Catatan: untuk produksi, gunakan versi paket terbaru.)
- **Tambahan:** Ekstensi `gd` dibutuhkan PhpSpreadsheet namun belum aktif → aktifkan `extension=gd` di `php.ini` (`zip` sudah aktif).

## 3. Webpack build gagal: `Cannot find module 'webpack/lib/SizeFormatHelpers'`
- **Gejala:** `npm run production` error pada `laravel-mix/src/webpackPlugins/BuildOutputPlugin.js`.
- **Penyebab:** npm memasang webpack 5.112.2 yang telah menghapus `SizeFormatHelpers`, tidak kompatibel dengan laravel-mix 6.0.49.
- **Perbaikan:** Pin webpack kompatibel: `npm install webpack@5.76.0 --save-dev --save-exact`.
- **Verifikasi:** Build menghasilkan `✔ Compiled Successfully`.

## 4. Tailwind error `@apply bg-white/12`
- **Gejala:** Build gagal pada `resources/css/app.css` (utility tidak valid).
- **Penyebab:** Nilai opacity `12` bukan bagian skala default Tailwind dan tidak bisa dipakai di `@apply`.
- **Perbaikan:** Ganti menjadi `bg-white/[0.12]`.
- **Verifikasi:** Build sukses.

## 5. `SQLSTATE[HY000] [1049] Unknown database 'sistema_db'`
- **Gejala:** Aplikasi menampilkan error unknown database.
- **Penyebab:** Database belum/ tidak lagi ada pada instance MySQL yang berjalan.
- **Perbaikan:** `CREATE DATABASE sistema_db`, lalu `php artisan migrate --seed`, lalu jalankan `database/sql/database_objects.sql`.
- **Verifikasi:** Beranda dan seluruh halaman mengembalikan HTTP 200.

## 6. Asset CSS/JS tidak ditemukan
- **Gejala:** Halaman tampil tanpa styling.
- **Penyebab:** `public/css/app.css` dan `public/js/app.js` belum dibangun; `public/storage` belum ada.
- **Perbaikan:** `npm run production` dan `php artisan storage:link`.
- **Verifikasi:** Kedua file ada dan halaman tampil normal.

## 7. View tidak ditemukan (contoh: `peminjam.dashboard`, `peminjam.borrowings.create`)
- **Gejala:** `View [peminjam.dashboard] not found` saat membuka halaman atau menjalankan test.
- **Penyebab:** Beberapa file Blade belum dibuat.
- **Perbaikan:** Lengkapi view yang hilang; tambahkan `PagesSmokeTest` untuk merender seluruh halaman agar kekurangan terdeteksi otomatis.
- **Verifikasi:** `php artisan test` → 33 passed.

## 8. Factory User gagal (kolom `username` wajib)
- **Gejala:** Test gagal karena `username` NOT NULL tanpa default.
- **Penyebab:** `UserFactory` bawaan belum mengisi `username`/`role`.
- **Perbaikan:** Tambahkan `username`, `role`, `is_active` serta state `admin()`, `petugas()`, `peminjam()`, `inactive()`.
- **Verifikasi:** Seluruh test autentikasi & role lulus.

## 9. Pencarian `COUNT(*)` di phpMyAdmin
- **Gejala:** Query dianggap error.
- **Penyebab:** Penulisan/penempelan query tidak lengkap (mis. tabel tanpa backtick ganda atau syntax terpotong).
- **Perbaikan:** `SELECT COUNT(*) AS total_alat FROM tools;`
- **Verifikasi:** Menampilkan satu baris jumlah alat.