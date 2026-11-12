# SISTEMA — Sistem Peminjaman Alat

Aplikasi manajemen inventaris dan peminjaman alat berbasis **Laravel 9**, **MySQL/MariaDB**, **Tailwind CSS**, dan **Alpine.js**. Dibuat untuk Uji Kompetensi Keahlian (UKK) Rekayasa Perangkat Lunak 2025/2026.

---

## 1. Cara Menjalankan

Prasyarat: PHP >= 8.0, Composer, Node.js, MySQL (XAMPP/Laragon).

```bash
# 1. Siapkan database
#    Buat database bernama sistema_db (lihat bagian Database)

# 2. Install dependency (jika belum)
composer install
npm install

# 3. Salin konfigurasi environment
copy .env.example .env      # Windows
php artisan key:generate

# 4. Atur koneksi database di .env
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=sistema_db
#    DB_USERNAME=root
#    DB_PASSWORD=

# 5. Migrasi + data awal
php artisan migrate --seed

# 6. Objek database (function, procedure, trigger)
mysql -u root sistema_db < database/sql/database_objects.sql

# 7. Link storage (foto alat)
php artisan storage:link

# 8. Build aset frontend
npm run production

# 9. Jalankan aplikasi
php artisan serve
```

Buka **http://127.0.0.1:8000**.

---

## 2. Akun Demo

| Role     | Username   | Password     |
|----------|------------|--------------|
| Admin    | `admin`    | `admin123`   |
| Petugas  | `petugas`  | `petugas123` |
| Peminjam | `peminjam` | `peminjam123` |
| Peminjam | `budi`     | `peminjam123` |

> Kredensial hanya untuk pengembangan lokal, tidak ditampilkan di halaman publik.

---

## 3. Fitur Utama

- **Landing page publik**: hero, statistik dinamis, katalog alat (search/filter), panduan, pengumuman, kontak.
- **Autentikasi & role**: Admin, Petugas, Peminjam dengan middleware dan proteksi route.
- **Admin**: CRUD pengguna, alat, kategori, peminjaman, pengembalian; log aktivitas; pengaturan (denda & pengumuman).
- **Petugas**: persetujuan peminjaman, monitoring pengembalian, verifikasi pengembalian, laporan (cetak/PDF/Excel).
- **Peminjam**: katalog alat, ajukan peminjaman, pengembalian, riwayat, notifikasi.
- **Business logic**: validasi stok, transaksi database + locking, perhitungan denda otomatis.
- **Database objects**: function `fn_tool_available_stock`, procedure `sp_approve_borrowing`, trigger integritas stok & log.

---

## 4. Database

- Nama: `sistema_db` (MySQL/MariaDB).
- Struktur dibuat dengan **Laravel Migration**.
- Data awal dengan **Seeder**.
- Backup/restore: lihat `documentation/Database/`.
- File objek database: `database/sql/database_objects.sql`.
- Dump lengkap (struktur + data + routine + trigger): `documentation/Database/database.sql`.

### Export / Import via phpMyAdmin
- **Export**: pilih `sistema_db` → tab Export → Go → simpan `.sql`.
- **Import**: buat database `sistema_db` → tab Import → pilih file `.sql` → Go.

---

## 5. Pengujian

```bash
php artisan test
```

Hasil: **33 test lulus** (login, CRUD alat, alur peminjaman, alur pengembalian, hak akses, render halaman, export PDF/Excel). Detail skenario ada di `documentation/Testing/test-case.md`.

---

## 6. Struktur Singkat

```
app/
├── Http/Controllers        # Admin, Petugas, Peminjam, Auth
├── Http/Middleware         # CheckRole
├── Http/Requests           # Validasi form
├── Models                  # Eloquent + relasi
└── Services                # BorrowingService, ReturnService, ReportService, ActivityLogger
database/
├── migrations              # Struktur tabel
├── seeders                 # Data awal
└── sql/database_objects.sql# Function, procedure, trigger
resources/views/            # Blade (layouts, components, admin, petugas, peminjam)
documentation/              # ERD, flowchart, testing, fungsi, debugging, evaluasi
```

---

## 7. Catatan

- Jangan menjalankan `migrate:fresh` pada database berisi data penting.
- Password disimpan dengan hashing Laravel (bcrypt).
- Seluruh data tersimpan di MySQL, bukan localStorage/session.