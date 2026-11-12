# Test Case — SISTEMA

Pengujian otomatis menggunakan **Laravel Feature Test** (PHPUnit) dengan database uji terpisah `sistema_db_test` (tidak mengganggu data utama).

## Cara menjalankan

```bash
php artisan test
```

## Hasil aktual

```
Tests: 33 passed
Time:  ~6.5s
```

Seluruh skenario di bawah **BERHASIL (PASS)**.

## Skenario pengujian

| No | Skenario | Input utama | Hasil yang diharapkan | Hasil aktual | Status |
|----|----------|-------------|------------------------|--------------|--------|
| 1 | Login dengan username valid | username `admin_test`, password `password` | Redirect ke dashboard admin, user terautentikasi | Sesuai | PASS |
| 2 | Login dengan email valid | email petugas, password benar | Redirect ke dashboard petugas | Sesuai | PASS |
| 3 | Login password salah | password salah | Kembali ke login, tetap guest | Sesuai | PASS |
| 4 | Login akun nonaktif | user `is_active=false` | Ditolak, tetap guest | Sesuai | PASS |
| 5 | Registrasi peminjam | nama, username, email, password | User dibuat dengan role peminjam | Sesuai | PASS |
| 6 | Guest akses dashboard | tanpa login | Redirect ke `/login` | Sesuai | PASS |
| 7 | Peminjam akses area admin/petugas | role peminjam | HTTP 403 | Sesuai | PASS |
| 8 | Petugas akses area admin | role petugas | HTTP 403 | Sesuai | PASS |
| 9 | Admin akses area peminjam | role admin | HTTP 403 | Sesuai | PASS |
| 10 | Tiap role akses dashboard sendiri | admin/petugas/peminjam | HTTP 200 | Sesuai | PASS |
| 11 | Tambah alat | kode, nama, kategori, stok | Alat tersimpan, stok tersedia = stok total | Sesuai | PASS |
| 12 | Kode alat duplikat | kode sama | Error validasi `code` | Sesuai | PASS |
| 13 | Hapus kategori yang dipakai | kategori berisi alat | Ditolak, kategori tetap ada | Sesuai | PASS |
| 14 | Stok total < jumlah dipinjam | total 5 (reserved 2) → jadi 1 | Error validasi, data tidak berubah | Sesuai | PASS |
| 15 | Ajukan peminjaman | 2 unit alat stok 5 | Status `menunggu`, stok tidak berubah | Sesuai | PASS |
| 16 | Jumlah melebihi stok | minta 5 dari stok 1 | Gagal validasi, tidak ada peminjaman | Sesuai | PASS |
| 17 | Setujui peminjaman | pengajuan 2 unit | Status `disetujui`, stok turun 5→3 | Sesuai | PASS |
| 18 | Serahkan alat | status disetujui | Status `dipinjam` | Sesuai | PASS |
| 19 | Setujui saat stok kurang | stok 1, butuh 3 | Error `stock`, status tetap menunggu | Sesuai | PASS |
| 20 | Peminjam menyetujui sendiri | role peminjam | HTTP 403 | Sesuai | PASS |
| 21 | Ajukan pengembalian | 2 unit, kondisi baik | Status `diajukan`, stok belum bertambah | Sesuai | PASS |
| 22 | Verifikasi pengembalian | petugas verifikasi | Status `diverifikasi`, peminjaman `selesai`, stok pulih 3→5 | Sesuai | PASS |
| 23 | Denda keterlambatan | telat 2 hari, denda 5000/hari | `late_days=2`, denda 10000 | Sesuai | PASS |
| 24 | Kembalikan milik orang lain | borrowing user lain | HTTP 403 | Sesuai | PASS |
| 25 | Render seluruh halaman admin | 20 halaman | HTTP 200 | Sesuai | PASS |
| 26 | Render seluruh halaman petugas | 11 halaman | HTTP 200 | Sesuai | PASS |
| 27 | Render seluruh halaman peminjam | 8 halaman | HTTP 200 | Sesuai | PASS |
| 28 | Export PDF laporan | route laporan PDF | HTTP 200, `application/pdf` | Sesuai | PASS |
| 29 | Export Excel laporan | route laporan Excel | HTTP 200, spreadsheet | Sesuai | PASS |
| 30 | Halaman profil & notifikasi | user login | HTTP 200 | Sesuai | PASS |

## Pengujian koneksi database manual

| No | Pengujian | Hasil |
|----|-----------|-------|
| 1 | MySQL XAMPP aktif | OK (port 3306 terbuka) |
| 2 | Database `sistema_db` tersedia | OK |
| 3 | Laravel terkoneksi | OK |
| 4 | Migration berhasil | OK (12 migration) |
| 5 | Data dapat ditambahkan | OK |
| 6 | Data dapat diedit | OK |
| 7 | Data dapat dihapus sesuai aturan | OK |
| 8 | Relasi antar tabel berfungsi | OK |
| 9 | Transaksi peminjaman & pengembalian berjalan | OK |
| 10 | Perubahan terlihat di phpMyAdmin | OK |

> Catatan: file pengujian berada di `tests/Feature/` (AuthTest, RoleAccessTest, ToolManagementTest, BorrowingFlowTest, ReturnFlowTest, PagesSmokeTest, ExampleTest).