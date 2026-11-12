# Dokumentasi Fungsi, Prosedur, dan Method

## A. Objek Database (MySQL/MariaDB)

File: `database/sql/database_objects.sql`

### 1. Function `fn_tool_available_stock(p_tool_id BIGINT) RETURNS INT`
Menghitung stok yang benar-benar tersedia: `stock_total` dikurangi total kuantitas pada peminjaman aktif (`disetujui`, `dipinjam`, `terlambat`).

```sql
SELECT fn_tool_available_stock(1);
```

Kegunaan: verifikasi silang antara kolom `stock_available` dan data transaksi.

### 2. Stored Procedure `sp_approve_borrowing(p_borrowing_id, p_approver_id, OUT p_result)`
Menyetujui peminjaman secara atomik:
1. `START TRANSACTION` + `SELECT ... FOR UPDATE` pada baris peminjaman.
2. Validasi status masih `menunggu`.
3. Validasi stok mencukupi.
4. Kurangi `stock_available` tiap alat.
5. Ubah status menjadi `disetujui`, isi `approved_by` dan `approved_at`.
6. Catat activity log.
7. `COMMIT` bila sukses; `ROLLBACK` melalui `EXIT HANDLER` bila terjadi error.

```sql
CALL sp_approve_borrowing(3, 2, @result);
SELECT @result;   -- 'OK' atau pesan kegagalan
```

### 3. Trigger `trg_tools_check_stock_before_update`
`BEFORE UPDATE ON tools` — menolak (`SIGNAL SQLSTATE '45000'`) bila:
- `stock_available < 0`
- `stock_available > stock_total`

Menjaga integritas stok pada level database.

### 4. Trigger `trg_activity_logs_prevent_update` & `trg_activity_logs_prevent_delete`
`BEFORE UPDATE` / `BEFORE DELETE ON activity_logs` — menolak perubahan/penghapusan log aktivitas. Log bersifat **immutable** dan tidak dapat dimanipulasi.

## B. Service Layer (App\Services)

### `BorrowingService`
| Method | Fungsi |
|---|---|
| `create(User $user, array $data, ?User $actor)` | Membuat pengajuan + detail dalam transaksi, validasi stok & status alat, notifikasi petugas, log. |
| `approve(Borrowing, User)` | Menyetujui: lock baris, cek stok, kurangi `stock_available`, set status `disetujui`, notifikasi peminjam. |
| `reject(Borrowing, User, string $reason)` | Menolak dengan alasan, notifikasi peminjam. |
| `markPickedUp(Borrowing, User)` | Menandai alat diserahkan (`dipinjam`, `picked_up_at`). |
| `refreshLateStatuses()` | Mengubah peminjaman aktif yang melewati `due_date` menjadi `terlambat`. |
| `generateCode()` | Membuat kode unik `PJM-YYYY-####`. |

### `ReturnService`
| Method | Fungsi |
|---|---|
| `submit(Borrowing, User, array $data, ?User $actor)` | Mengajukan pengembalian + detail kondisi, hitung keterlambatan & denda, notifikasi petugas. |
| `verify(ReturnRecord, User, array $data)` | Verifikasi: tambah kembali stok (dibatasi `stock_total`), perbarui kondisi alat, status `diverifikasi`, peminjaman `selesai`. |
| `calculateLateFee(Borrowing, Carbon $date)` | Menghitung `late_days` dan denda berdasarkan `settings`. |
| `generateCode()` | Membuat kode `PGB-YYYY-####`. |

### `ReportService`
| Method | Fungsi |
|---|---|
| `build(string $type, array $filters)` | Menyusun data laporan: `tools`, `borrowings`, `returns`, `late`, `activity` berdasarkan filter tanggal/kategori/status. |

### `ActivityLogger`
| Method | Fungsi |
|---|---|
| `log($action, $description, ?Model $subject, ?User $user)` | Mencatat aktivitas (pengguna, aksi, deskripsi, IP, user agent, waktu). |

## C. Controller Utama

| Controller | Tanggung jawab |
|---|---|
| `LandingController` | Halaman publik, statistik dinamis, katalog & filter. |
| `Auth\LoginController` / `RegisterController` | Login (email/username), logout, registrasi peminjam. |
| `Admin\*` | CRUD pengguna, alat, kategori, peminjaman, pengembalian, log, pengaturan. |
| `Petugas\*` | Persetujuan, monitoring, verifikasi pengembalian, laporan & export. |
| `Peminjam\*` | Katalog, pengajuan peminjaman, pengembalian, riwayat. |
| `ProfileController`, `NotificationController` | Profil, ubah kata sandi, notifikasi. |

## D. Middleware & Keamanan

- `CheckRole` (`role:admin`, `role:petugas`, `role:peminjam`) melindungi setiap route.
- Form Request memvalidasi seluruh input di backend (`StoreBorrowingRequest`, `StoreToolRequest`, dll.).
- CSRF token pada semua form, password bcrypt, upload foto dibatasi tipe & ukuran (maks 2 MB).
- Query menggunakan Eloquent/parameter binding (mencegah SQL injection); output Blade di-escape (mencegah XSS).