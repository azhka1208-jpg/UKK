# Database — Backup & Restore

Database: `sistema_db` (MySQL/MariaDB).

## Isi folder ini
- `database.sql` — dump lengkap: struktur tabel, data awal, **stored procedure, function, trigger**.

## Export (Backup) via phpMyAdmin
1. Buka http://localhost/phpmyadmin
2. Pilih database `sistema_db`
3. Tab **Export** → pilih **Quick** / **Custom**
4. Pastikan opsi **Structure and data**, centang **Add CREATE PROCEDURE / FUNCTION** dan **Add triggers**
5. Klik **Go**, simpan file `.sql`

## Import (Restore) via phpMyAdmin
1. Buat database baru `sistema_db` (jika belum ada)
2. Pilih database tersebut
3. Tab **Import** → pilih file `.sql`
4. Klik **Go**

## Backup/Restore via command line
```bash
# Backup
mysqldump -u root --routines --triggers --single-transaction --databases sistema_db > backup.sql

# Restore
mysql -u root < backup.sql
```

## Mengaktifkan objek database
Jika hanya ingin membuat function/procedure/trigger pada database yang sudah ada:
```bash
mysql -u root sistema_db < database/sql/database_objects.sql
```

## Catatan
- Jangan menjalankan `php artisan migrate:fresh` pada database berisi data penting.
- Setelah restore, jalankan `php artisan storage:link` bila foto alat disimpan di `storage/app/public`.