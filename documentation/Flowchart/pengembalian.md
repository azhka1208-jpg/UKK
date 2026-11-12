# Flowchart — Pengembalian Alat

```mermaid
flowchart TD
    A([Mulai]) --> B[Peminjam buka Peminjaman Saya]
    B --> C[Pilih peminjaman berstatus Dipinjam/Terlambat]
    C --> D[Isi tanggal kembali + kondisi alat]
    D --> E{Validasi: status & belum ada pengembalian?}
    E -- Tidak --> F[Tampilkan pesan kesalahan]
    F --> C
    E -- Ya --> G[DB::transaction: buat returns + return_details]
    G --> H[Hitung late_days & denda dari pengaturan]
    H --> I[Status pengembalian = Diajukan]
    I --> J[Notifikasi petugas]
    J --> K[Petugas memeriksa kondisi fisik alat]
    K --> L{Petugas verifikasi?}
    L -- Tidak --> M[Tetap menunggu]
    L -- Ya --> N[DB::transaction + lock]
    N --> O[Tambah stock_available sesuai jumlah kembali]
    O --> P[Perbarui kondisi alat ke kondisi terburuk]
    P --> Q[Status pengembalian = Diverifikasi]
    Q --> R[Status peminjaman = Selesai]
    R --> S[Notifikasi peminjam + log aktivitas]
    S --> T([Selesai])
```

## Perhitungan denda
- `late_days = max(0, return_date - due_date)`
- `fine_amount = late_days × fine_per_day` (hanya jika `fine_enabled = 1`)
- Nominal dan status aktif denda diatur Admin melalui menu **Pengaturan**.