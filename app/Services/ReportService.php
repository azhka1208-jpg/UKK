<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Borrowing;
use App\Models\ReturnRecord;
use App\Models\Tool;
use Carbon\Carbon;

class ReportService
{
    public const TYPES = [
        'tools' => 'Laporan Data Alat',
        'borrowings' => 'Laporan Peminjaman',
        'returns' => 'Laporan Pengembalian',
        'late' => 'Laporan Keterlambatan',
        'activity' => 'Laporan Aktivitas Peminjaman',
    ];

    public function build(string $type, array $filters): array
    {
        $from = ! empty($filters['from']) ? Carbon::parse($filters['from'])->startOfDay() : now()->startOfMonth();
        $to = ! empty($filters['to']) ? Carbon::parse($filters['to'])->endOfDay() : now()->endOfDay();

        $meta = [
            'type' => $type,
            'title' => self::TYPES[$type] ?? 'Laporan',
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'period' => $from->translatedFormat('d M Y') . ' - ' . $to->translatedFormat('d M Y'),
            'generated_at' => now()->translatedFormat('d M Y H:i'),
            'filters' => $filters,
        ];

        [$columns, $rows] = match ($type) {
            'tools' => $this->tools($filters),
            'returns' => $this->returns($from, $to, $filters),
            'late' => $this->late($from, $to, $filters),
            'activity' => $this->activity($from, $to, $filters),
            default => $this->borrowings($from, $to, $filters),
        };

        return [
            'meta' => $meta,
            'columns' => $columns,
            'rows' => $rows,
            'total' => count($rows),
        ];
    }

    private function tools(array $filters): array
    {
        $query = Tool::with('category');

        if (! empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $rows = $query->orderBy('name')->get()->map(fn (Tool $tool) => [
            $tool->code,
            $tool->name,
            $tool->category?->name ?? '-',
            $tool->stock_total,
            $tool->stock_available,
            $tool->condition_label,
            $tool->status_label,
        ])->all();

        return [
            ['Kode', 'Nama Alat', 'Kategori', 'Stok Total', 'Stok Tersedia', 'Kondisi', 'Status'],
            $rows,
        ];
    }

    private function borrowings(Carbon $from, Carbon $to, array $filters): array
    {
        $query = Borrowing::with(['user', 'details.tool'])
            ->whereBetween('borrow_date', [$from->toDateString(), $to->toDateString()]);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['category'])) {
            $query->whereHas('details.tool', fn ($q) => $q->where('category_id', $filters['category']));
        }

        $rows = $query->latest('borrow_date')->get()->map(fn (Borrowing $borrowing) => [
            $borrowing->code,
            $borrowing->user?->name ?? '-',
            $borrowing->borrow_date?->format('d/m/Y'),
            $borrowing->due_date?->format('d/m/Y'),
            $borrowing->total_items,
            $borrowing->status_label,
        ])->all();

        return [
            ['Kode', 'Peminjam', 'Tanggal Pinjam', 'Jatuh Tempo', 'Jumlah Item', 'Status'],
            $rows,
        ];
    }

    private function returns(Carbon $from, Carbon $to, array $filters): array
    {
        $query = ReturnRecord::with(['borrowing.user'])
            ->whereBetween('return_date', [$from->toDateString(), $to->toDateString()]);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['category'])) {
            $query->whereHas('borrowing.details.tool', fn ($q) => $q->where('category_id', $filters['category']));
        }

        $rows = $query->latest('return_date')->get()->map(fn (ReturnRecord $return) => [
            $return->code,
            $return->borrowing?->code ?? '-',
            $return->borrowing?->user?->name ?? '-',
            $return->return_date?->format('d/m/Y'),
            $return->late_days,
            'Rp ' . number_format((float) $return->fine_amount, 0, ',', '.'),
            $return->status_label,
        ])->all();

        return [
            ['Kode Pengembalian', 'Kode Peminjaman', 'Peminjam', 'Tanggal Kembali', 'Terlambat (hari)', 'Denda', 'Status'],
            $rows,
        ];
    }

    private function late(Carbon $from, Carbon $to, array $filters): array
    {
        $query = Borrowing::with(['user', 'returnRecord'])
            ->whereDate('due_date', '<', today())
            ->whereIn('status', [
                Borrowing::STATUS_DIPINJAM,
                Borrowing::STATUS_TERLAMBAT,
                Borrowing::STATUS_SELESAI,
            ])
            ->whereBetween('due_date', [$from->toDateString(), $to->toDateString()]);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['category'])) {
            $query->whereHas('details.tool', fn ($q) => $q->where('category_id', $filters['category']));
        }

        $rows = $query->orderBy('due_date')->get()->map(function (Borrowing $borrowing) {
            $reference = $borrowing->returnRecord?->return_date ?? today();
            $days = $reference->greaterThan($borrowing->due_date)
                ? (int) $borrowing->due_date->diffInDays($reference)
                : 0;

            return [
                $borrowing->code,
                $borrowing->user?->name ?? '-',
                $borrowing->due_date?->format('d/m/Y'),
                $borrowing->returnRecord?->return_date?->format('d/m/Y') ?? '-',
                $days,
                $borrowing->status_label,
            ];
        })->all();

        return [
            ['Kode', 'Peminjam', 'Jatuh Tempo', 'Tanggal Kembali', 'Terlambat (hari)', 'Status'],
            $rows,
        ];
    }

    private function activity(Carbon $from, Carbon $to, array $filters): array
    {
        $query = ActivityLog::with('user')
            ->whereBetween('created_at', [$from, $to]);

        if (! empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        $rows = $query->orderByDesc('created_at')->limit(1000)->get()->map(fn (ActivityLog $log) => [
            $log->created_at?->format('d/m/Y H:i'),
            $log->user?->name ?? 'Sistem',
            $log->action,
            $log->description,
            $log->ip_address ?? '-',
        ])->all();

        return [
            ['Waktu', 'Pengguna', 'Aksi', 'Deskripsi', 'IP'],
            $rows,
        ];
    }
}