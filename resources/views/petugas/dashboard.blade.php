@extends('layouts.dashboard')

@section('title', 'Dashboard Petugas')

@section('content')
    <x-page-header title="Dashboard Petugas" description="Pantau pengajuan, peminjaman aktif, dan pengembalian alat.">
        <x-slot:actions>
            <a href="{{ route('petugas.approvals.index') }}" class="btn-primary btn-sm">
                <i data-lucide="check-circle-2" class="h-4 w-4"></i> Persetujuan
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Menunggu Persetujuan" :value="$stats['pending']" icon="hourglass" tone="warning" hint="Pengajuan baru" :href="route('petugas.approvals.index')" />
        <x-stat-card label="Peminjaman Aktif" :value="$stats['active']" icon="clipboard-check" tone="navy" hint="Sedang berjalan" :href="route('petugas.monitoring.index')" />
        <x-stat-card label="Terlambat" :value="$stats['overdue']" icon="alarm-clock" tone="danger" hint="Melewati batas" :href="route('petugas.monitoring.index', ['status' => 'terlambat'])" />
        <x-stat-card label="Pengembalian Baru" :value="$stats['return_pending']" icon="undo-2" tone="success" hint="Menunggu verifikasi" :href="route('petugas.returns.index', ['status' => 'diajukan'])" />
    </div>

    <div class="mt-6 grid gap-5 lg:grid-cols-2">
        <div class="card">
            <div class="flex items-center justify-between border-b border-line px-5 py-4">
                <h2 class="font-display text-base font-semibold text-heading">Pengajuan Terbaru</h2>
                <a href="{{ route('petugas.approvals.index') }}" class="link text-sm">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr><th>Kode</th><th>Peminjam</th><th>Jatuh Tempo</th><th class="text-right">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingBorrowings as $borrowing)
                            <tr>
                                <td class="font-medium text-heading">{{ $borrowing->code }}</td>
                                <td>{{ $borrowing->user?->name }}</td>
                                <td class="whitespace-nowrap text-ink-secondary">{{ $borrowing->due_date?->format('d/m/Y') }}</td>
                                <td class="text-right">
                                    <a href="{{ route('petugas.approvals.show', $borrowing) }}" class="btn-secondary btn-sm">Tinjau</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><x-empty-state title="Tidak ada pengajuan" description="Semua pengajuan sudah diproses." icon="check-circle-2" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center justify-between border-b border-line px-5 py-4">
                <h2 class="font-display text-base font-semibold text-heading">Pengembalian Menunggu Verifikasi</h2>
                <a href="{{ route('petugas.returns.index') }}" class="link text-sm">Lihat semua</a>
            </div>
            <div class="divide-y divide-line">
                @forelse ($pendingReturns as $return)
                    <div class="flex items-center justify-between gap-3 px-5 py-3.5">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-heading">{{ $return->code }}</p>
                            <p class="text-xs text-ink-secondary">{{ $return->borrowing?->user?->name }} &middot; {{ $return->return_date?->format('d/m/Y') }}</p>
                        </div>
                        <a href="{{ route('petugas.returns.show', $return) }}" class="btn-secondary btn-sm">Verifikasi</a>
                    </div>
                @empty
                    <x-empty-state title="Tidak ada pengembalian baru" description="Pengembalian yang diajukan peminjam akan muncul di sini." icon="inbox" />
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 card">
        <div class="flex items-center justify-between border-b border-line px-5 py-4">
            <h2 class="font-display text-base font-semibold text-heading">Jatuh Tempo Dekat</h2>
            <a href="{{ route('petugas.monitoring.index') }}" class="link text-sm">Monitoring</a>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr><th>Kode</th><th>Peminjam</th><th>Alat</th><th>Jatuh Tempo</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse ($dueSoon as $borrowing)
                        <tr>
                            <td><a href="{{ route('petugas.monitoring.show', $borrowing) }}" class="font-medium text-heading hover:underline">{{ $borrowing->code }}</a></td>
                            <td>{{ $borrowing->user?->name }}</td>
                            <td class="max-w-xs truncate text-ink-secondary">{{ $borrowing->details->map(fn ($d) => $d->tool?->name)->join(', ') }}</td>
                            <td class="whitespace-nowrap {{ $borrowing->is_late ? 'text-danger' : 'text-ink-secondary' }}">{{ $borrowing->due_date?->format('d/m/Y') }}</td>
                            <td><x-status-badge :label="$borrowing->status_label" :tone="$borrowing->status_badge" dot /></td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-empty-state title="Tidak ada jatuh tempo dekat" description="Tidak ada peminjaman yang mendekati batas pengembalian." icon="calendar-check" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection