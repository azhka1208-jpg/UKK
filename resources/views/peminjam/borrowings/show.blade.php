@extends('layouts.dashboard')

@section('title', 'Detail Peminjaman')

@section('content')
    <x-page-header :title="'Peminjaman ' . $borrowing->code" description="Detail pengajuan peminjaman Anda.">
        <x-slot:actions>
            <a href="{{ route('peminjam.borrowings.index') }}" class="btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-5 lg:grid-cols-[1.6fr_1fr]">
        <div class="space-y-5">
            <div class="card card-pad">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <x-status-badge :label="$borrowing->status_label" :tone="$borrowing->status_badge" dot />
                    <div class="text-right">
                        <p class="text-xs uppercase tracking-wide text-ink-secondary">Total Item</p>
                        <p class="font-display text-lg font-semibold text-heading">{{ $borrowing->total_items }}</p>
                    </div>
                </div>

                <dl class="mt-5 grid gap-4 border-t border-line pt-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Tanggal Pengajuan</dt>
                        <dd class="mt-0.5 font-medium text-heading">{{ $borrowing->created_at?->translatedFormat('d M Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Tanggal Peminjaman</dt>
                        <dd class="mt-0.5 text-heading">{{ $borrowing->borrow_date?->translatedFormat('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Batas Pengembalian</dt>
                        <dd class="mt-0.5 {{ $borrowing->is_late ? 'font-medium text-danger' : 'text-heading' }}">
                            {{ $borrowing->due_date?->translatedFormat('d M Y') }}
                            @if ($borrowing->is_late) (terlambat {{ $borrowing->late_days }} hari) @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Diproses oleh</dt>
                        <dd class="mt-0.5 text-heading">{{ $borrowing->approver?->name ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Keperluan</dt>
                        <dd class="mt-0.5 text-ink">{{ $borrowing->purpose }}</dd>
                    </div>
                    @if ($borrowing->notes)
                        <div class="sm:col-span-2">
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Catatan</dt>
                            <dd class="mt-0.5 text-ink-secondary">{{ $borrowing->notes }}</dd>
                        </div>
                    @endif
                    @if ($borrowing->rejection_reason)
                        <div class="sm:col-span-2">
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Alasan Penolakan</dt>
                            <dd class="mt-1 rounded-lg border border-danger/20 bg-danger/5 px-3 py-2 text-sm text-danger">{{ $borrowing->rejection_reason }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="card">
                <div class="border-b border-line px-5 py-4">
                    <h2 class="font-display text-base font-semibold text-heading">Alat yang Dipinjam</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr><th>Alat</th><th>Kode</th><th>Jumlah</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($borrowing->details as $detail)
                                <tr>
                                    <td class="font-medium text-heading">{{ $detail->tool?->name ?? '-' }}</td>
                                    <td class="font-mono text-xs text-ink-secondary">{{ $detail->tool?->code }}</td>
                                    <td>{{ $detail->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-5">
            <div class="card card-pad">
                <h2 class="font-display text-base font-semibold text-heading">Status</h2>
                <p class="mt-2 text-sm text-ink-secondary">
                    @switch($borrowing->status)
                        @case('menunggu')
                            Pengajuan Anda sedang menunggu persetujuan petugas.
                            @break
                        @case('disetujui')
                            Pengajuan disetujui. Silakan ambil alat sesuai jadwal.
                            @break
                        @case('dipinjam')
                            Alat sedang dipinjam. Kembalikan sebelum batas waktu.
                            @break
                        @case('terlambat')
                            Pengembalian melewati batas waktu. Segera kembalikan alat.
                            @break
                        @case('selesai')
                            Peminjaman telah selesai. Terima kasih.
                            @break
                        @case('ditolak')
                            Pengajuan ditolak. Silakan ajukan peminjaman lain bila diperlukan.
                            @break
                    @endswitch
                </p>

                @if (in_array($borrowing->status, ['dipinjam', 'terlambat']) && ! $borrowing->returnRecord)
                    <a href="{{ route('peminjam.returns.create', $borrowing) }}" class="btn-primary mt-4 w-full">
                        <i data-lucide="undo-2" class="h-4 w-4"></i> Ajukan Pengembalian
                    </a>
                @endif
            </div>

            @if ($borrowing->returnRecord)
                <div class="card card-pad">
                    <h2 class="font-display text-base font-semibold text-heading">Pengembalian</h2>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-sm text-ink-secondary">{{ $borrowing->returnRecord->code }}</span>
                        <x-status-badge :label="$borrowing->returnRecord->status_label" :tone="$borrowing->returnRecord->status_badge" dot />
                    </div>
                    <a href="{{ route('peminjam.returns.show', $borrowing->returnRecord) }}" class="btn-secondary btn-sm mt-3 w-full">Lihat pengembalian</a>
                </div>
            @endif

            <div class="card card-pad">
                <h2 class="font-display text-base font-semibold text-heading">Alur Peminjaman</h2>
                @php
                    $order = ['menunggu' => 0, 'disetujui' => 1, 'dipinjam' => 2, 'terlambat' => 2, 'selesai' => 3, 'ditolak' => -1];
                    $current = $order[$borrowing->status] ?? 0;
                    $steps = ['Pengajuan dibuat', 'Disetujui petugas', 'Alat diserahkan', 'Pengembalian selesai'];
                @endphp
                <ol class="mt-4 space-y-3 text-sm">
                    @foreach ($steps as $index => $label)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full {{ $current >= $index && $borrowing->status !== 'ditolak' ? 'bg-success text-white' : 'bg-ink/5 text-ink-secondary' }}">
                                <i data-lucide="{{ $current >= $index && $borrowing->status !== 'ditolak' ? 'check' : 'circle' }}" class="h-3.5 w-3.5"></i>
                            </span>
                            <span class="{{ $current >= $index && $borrowing->status !== 'ditolak' ? 'font-medium text-heading' : 'text-ink-secondary' }}">{{ $label }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
@endsection