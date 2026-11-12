# Laporan Evaluasi Sistem — SISTEMA

## 1. Fitur yang Berjalan dengan Baik

- **Autentikasi & otorisasi**: login (email/username), remember me, logout, registrasi peminjam; pembatasan akses per role menggunakan middleware `CheckRole` dan proteksi pada setiap route.
- **Tiga dashboard berbeda**: Admin, Petugas, Peminjam dengan statistik dinamis dari database.
- **Landing page publik**: hero, statistik aktual, katalog alat dengan pencarian & filter kategori/status, panduan, pengumuman, dan footer kontak.
- **Manajemen pengguna, alat, kategori**: CRUD lengkap, pencarian, filter, pagination, upload foto alat, validasi backend.
- **Alur peminjaman**: pengajuan → persetujuan/penolakan → penyerahan alat → selesai, dengan pengurangan stok yang konsisten.
- **Alur pengembalian**: pengajuan → verifikasi petugas → pengembalian stok → status selesai, termasuk perhitungan keterlambatan dan denda berdasarkan pengaturan.
- **Integritas data**: `DB::transaction()` + `lockForUpdate()`; trigger mencegah stok negatif/melebihi total; log aktivitas immutable.
- **Objek database**: function `fn_tool_available_stock`, procedure `sp_approve_borrowing` (COMMIT/ROLLBACK), tiga trigger — terverifikasi dapat dijalankan di MariaDB 10.4.
- **Laporan**: rekap data alat, peminjaman, pengembalian, keterlambatan, dan aktivitas; filter tanggal/kategori/status; cetak, export PDF, dan export Excel.
- **Notifikasi & log aktivitas** untuk peristiwa penting.
- **Pengaturan sistem**: identitas aplikasi, kontak, kebijakan lama peminjaman, nominal & status denda, pengumuman beranda.
- **Pengujian otomatis**: 33 test lulus (autentikasi, hak akses, CRUD, alur peminjaman, alur pengembalian, denda, render halaman, export PDF/Excel).
- **Tampilan responsif** (desktop, tablet, mobile) dengan sidebar yang berubah menjadi drawer pada layar kecil.

## 2. Bug / Keterbatasan yang Belum Diperbaiki

- **Versi paket lama**: `dompdf` dan `phpspreadsheet` memakai versi yang kompatibel dengan PHP 8.0 sehingga masih tercatat pada security advisory Composer. Disarankan upgrade saat PHP dinaikkan ke 8.1+.
- **Status terlambat tidak otomatis terjadwal**: pembaruan status `terlambat` dilakukan saat halaman dashboard/monitoring diakses (`refreshLateStatuses`), belum memakai scheduler/cron.
- **Pengembalian bersifat penuh**: seluruh kuantitas dikembalikan sekaligus; belum mendukung pengembalian sebagian per unit.
- **Belum ada reset password via email / verifikasi email**: fitur email belum dikonfigurasi.
- **Foto alat tidak dioptimasi**: file disimpan apa adanya (tanpa resize/thumbnail).
- **Pendaftaran terbuka**: siapa pun dapat mendaftar sebagai peminjam tanpa verifikasi email/admin.
- **Laporan berukuran sangat besar**: export memuat seluruh baris pada periode terpilih (belum dipaginasi/streaming).

## 3. Rencana Pengembangan Selanjutnya

1. Upgrade ke Laravel 10/11 + PHP 8.1+ dan perbarui seluruh dependensi.
2. Menambahkan **scheduled task** untuk menandai keterlambatan otomatis dan pengingat jatuh tempo.
3. **Notifikasi email/WhatsApp** untuk persetujuan, penolakan, dan pengingat pengembalian.
4. **QR Code/Barcode** pada alat dan peminjaman untuk mempercepat proses serah terima.
5. Dukungan **pengembalian sebagian** dan pencatatan kerusakan per unit.
6. **Optimasi gambar** (resize/compress) dan penyimpanan berbasis cloud.
7. **Dashboard analitik** lebih lanjut (tren peminjaman per kategori, utilisasi alat).
8. Penambahan **audit log terperinci** untuk perubahan pengaturan dan hak akses.
9. Integrasi **backup otomatis** database terjadwal.

---

**Kesimpulan:** aplikasi berfungsi sesuai kebutuhan utama UKK, seluruh fitur wajib tersedia dan terhubung ke database MySQL nyata, serta telah diverifikasi melalui pengujian otomatis. Keterbatasan yang ada bersifat pengembangan lanjutan dan tidak menghalangi penggunaan inti sistem.