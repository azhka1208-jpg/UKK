# ERD — SISTEMA

Entity Relationship Diagram database `sistema_db`.

## Diagram (Mermaid)

```mermaid
erDiagram
    USERS ||--o{ BORROWINGS : "mengajukan (user_id)"
    USERS ||--o{ BORROWINGS : "menyetujui (approved_by)"
    USERS ||--o{ ACTIVITY_LOGS : "melakukan"
    USERS ||--o{ NOTIFICATIONS : "menerima"

    CATEGORIES ||--o{ TOOLS : "memiliki"

    TOOLS ||--o{ BORROWING_DETAILS : "dipinjam pada"
    BORROWINGS ||--o{ BORROWING_DETAILS : "berisi"
    BORROWINGS ||--|| RETURNS : "memiliki"
    RETURNS ||--o{ RETURN_DETAILS : "berisi"
    TOOLS ||--o{ RETURN_DETAILS : "dikembalikan pada"

    USERS {
        bigint id PK
        string name
        string username UK
        string email UK
        string password
        string phone
        text address
        enum role "admin|petugas|peminjam"
        boolean is_active
        timestamps
    }
    CATEGORIES {
        bigint id PK
        string name UK
        text description
        timestamps
    }
    TOOLS {
        bigint id PK
        string code UK
        string name
        bigint category_id FK
        text description
        int stock_total
        int stock_available
        enum condition "baik|rusak_ringan|rusak_berat"
        string location
        string photo
        enum status "tersedia|perbaikan|tidak_tersedia"
        timestamps
    }
    BORROWINGS {
        bigint id PK
        string code UK
        bigint user_id FK
        bigint approved_by FK
        enum status "menunggu|disetujui|ditolak|dipinjam|selesai|terlambat"
        date borrow_date
        date due_date
        text purpose
        text notes
        text rejection_reason
        timestamp approved_at
        timestamp picked_up_at
        int total_items
        timestamps
    }
    BORROWING_DETAILS {
        bigint id PK
        bigint borrowing_id FK
        bigint tool_id FK
        int quantity
        string note
        timestamps
    }
    RETURNS {
        bigint id PK
        string code UK
        bigint borrowing_id FK,UK
        bigint received_by FK
        date return_date
        enum status "diajukan|diverifikasi"
        int late_days
        decimal fine_amount
        boolean fine_paid
        text notes
        timestamp verified_at
        timestamps
    }
    RETURN_DETAILS {
        bigint id PK
        bigint return_id FK
        bigint tool_id FK
        int quantity
        enum condition "baik|rusak_ringan|rusak_berat"
        text note
        timestamps
    }
    ACTIVITY_LOGS {
        bigint id PK
        bigint user_id FK
        string action
        string description
        string subject_type
        bigint subject_id
        string ip_address
        string user_agent
        timestamp created_at
    }
    NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        string title
        text message
        string type
        boolean is_read
        string url
        timestamps
    }
    SETTINGS {
        bigint id PK
        string key UK
        text value
        string type
        string group
        string label
        timestamps
    }
```

## Relasi

| Relasi | Tipe | Keterangan |
|---|---|---|
| users → borrowings (user_id) | 1 : N | Pemilik peminjaman |
| users → borrowings (approved_by) | 1 : N | Petugas yang menyetujui |
| users → activity_logs | 1 : N | Log aktivitas pengguna |
| users → notifications | 1 : N | Notifikasi pengguna |
| categories → tools | 1 : N | Kategori alat (restrict delete) |
| borrowings → borrowing_details | 1 : N | Detail alat per peminjaman |
| tools → borrowing_details | 1 : N | Alat yang dipinjam |
| borrowings → returns | 1 : 1 | Satu pengembalian per peminjaman |
| returns → return_details | 1 : N | Detail kondisi alat kembali |
| tools → return_details | 1 : N | Alat yang dikembalikan |

> Catatan: `borrowing_details` dan `return_details` berperan sebagai tabel penghubung (relasi many-to-many antara transaksi dan alat, dengan atribut tambahan seperti jumlah dan kondisi).