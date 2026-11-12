# Flowchart — Peminjaman Alat

```mermaid
flowchart TD
    A([Mulai]) --> B[Peminjam login]
    B --> C[Buka Daftar Alat]
    C --> D[Pilih alat & jumlah]
    D --> E[Isi tanggal pinjam, tanggal kembali, keperluan]
    E --> F{Validasi backend}
    F -- Gagal --> G[Tampilkan pesan kesalahan]
    G --> E
    F -- Lolos --> H{Stok tersedia mencukupi?}
    H -- Tidak --> G
    H -- Ya --> I[DB::transaction: simpan borrowing + detail]
    I --> J[Status = Menunggu Persetujuan]
    J --> K[Notifikasi ke petugas + log aktivitas]
    K --> L[Petugas membuka pengajuan]
    L --> M{Keputusan petugas}
    M -- Tolak --> N[Status = Ditolak + alasan]
    N --> O[Notifikasi ke peminjam]
    M -- Setujui --> P[DB::transaction + lock: cek stok]
    P --> Q{Stok masih cukup?}
    Q -- Tidak --> R[Tampilkan error, rollback]
    Q -- Ya --> S[Kurangi stock_available]
    S --> T[Status = Disetujui]
    T --> U[Notifikasi peminjam]
    U --> V[Petugas menyerahkan alat]
    V --> W[Status = Sedang Dipinjam]
    W --> X([Selesai - lanjut alur pengembalian])
    O --> X
```

## Aturan stok
- Stok dikurangi saat **persetujuan** (reservasi), bukan saat pengajuan.
- Menggunakan `DB::transaction()` + `lockForUpdate()` agar tidak terjadi peminjaman melebihi stok (race condition).
- Trigger `trg_tools_check_stock_before_update` mencegah stok negatif / melebihi total.