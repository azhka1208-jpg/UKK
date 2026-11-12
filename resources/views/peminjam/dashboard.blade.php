@extends('layouts.dashboard')

@section('title', 'Dashboard Peminjam')

@section('content')
    <x-page-header title="Dashboard Peminjam" description="Ringkasan peminjaman dan pengembalian alat Anda.">
        <x-slot:actions>
            <a href="{{ route('peminjam.tools.index') }}" class="btn-secondary btn-sm">
                <i data-lucide="package-search" class="h-4 w-4"></i> Daftar Alat
            </a>
            <a href="{{ route('peminjam.borrowings.create') }}" class="btn-primary btn-sm">
                <i data-lucide="plus" class="h-4 w-4"></i> Ajukan Peminjaman
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total Peminjaman" :value="$stats['total']" icon="clipboard-list" tone="navy" hint="Seluruh pengajuan" />
        <x-stat-card label="Peminjaman Aktif" :value="$stats['active']" icon="package-open" tone="warning" hint="Disetujui & berjalan" :href="route('peminjam.borrowings.index')" />
        <x-stat-card label="Menunggu Persetujuan" :value="$stats['pending']" icon="hourglass" tone="slate" hint="Belum diproses petugas" :href="route('peminjam.borrowings.index', ['status' => 'menunggu'])" />
        <x-stat-card label="Perlu Dikembalikan" :value="$stats['to_return']" icon="undo-2" tone="danger" hint="Segera kembalikan" :href="route('peminjam.returns.index')" />
    </div>

    <div class="mt-6 grid gap-5 lg:grid-cols-[1.6fr_1fr]">
        <div class="card">
            <div class="flex items-center justify-between border-b border-line px-5 py-4">
                <h2 class="font-display text-base font-semibold text-heading">Riwayat Peminjaman Terbaru</h2>
                <a href="{{ route('peminjam.borrowings.index') }}" class="link text-sm">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recent as $borrowing)
                            <tr>
                                <td>
                                    <a href="{{ route('peminjam.borrowings.show', $borrowing) }}" class="font-medium text-heading hover:underline">{{ $borrowing->code }}</a>
                                    <p class="text-xs text-ink-secondary">{{ $borrowing->details->map(fn ($d) => $d->tool?->name)->filter()->join(', ') }}</p>
                                </td>
                                <td class="whitespace-nowrap text-ink-secondary">{{ $borrowing->borrow_date?->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap {{ $borrowing->is_late ? 'font-medium text-danger' : 'text-ink-secondary' }}">{{ $borrowing->due_date?->format('d/m/Y') }}</td>
                                <td><x-status-badge :label="$borrowing->status_label" :tone="$borrowing->status_badge" dot /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-empty-state title="Belum ada peminjaman" description="Ajukan peminjaman alat untuk memulai." icon="clipboard-list">
                                        <x-slot:action>
                                            <a href="{{ route('peminjam.borrowings.create') }}" class="btn-primary btn-sm">Ajukan Peminjaman</a>
                                        </x-slot:action>
                                    </x-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-5">
            <div class="card card-pad">
                <h2 class="font-display text-base font-semibold text-heading">Alat Sedang Dipinjam</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($activeBorrowings as $borrowing)
                        <div class="rounded-lg border border-line bg-canvas p-3">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-xs text-ink-secondary">{{ $borrowing->code }}</span>
                                <x-status-badge :label="$borrowing->status_label" :tone="$borrowing->status_badge" dot />
                            </div>
                            <p class="mt-1 text-sm text-ink">{{ $borrowing->details->map(fn ($d) => ($d->tool?->name ?? '-') . ' (' . $d->quantity . ')')->join(', ') }}</p>
                            <p class="mt-1 text-xs {{ $borrowing->is_late ? 'text-danger' : 'text-ink-secondary' }}">
                                Batas kembali: {{ $borrowing->due_date?->format('d/m/Y') }}
                                @if ($borrowing->is_late) &middot; terlambat {{ $borrowing->late_days }} hari @endif
                            </p>
                            @if ($borrowing->returnRecord)
                                <p class="mt-2 text-xs text-ink-secondary">Pengembalian sudah diajukan, menunggu verifikasi.</p>
                            @else
                                <a href="{{ route('peminjam.returns.create', $borrowing) }}" class="btn-soft btn-sm mt-2 w-full">Ajukan Pengembalian</a>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-ink-secondary">Tidak ada alat yang sedang dipinjam.</p>
                    @endforelse
                </div>
            </div>

            <div class="card card-pad">
                <div class="flex items-center justify-between">
                    <h2 class="font-display text-base font-semibold text-heading">Notifikasi</h2>
                    <a href="{{ route('notifications.index') }}" class="link text-sm">Semua</a>
                </div>
                <ul class="mt-4 space-y-3">
                    @forelse ($notifications as $notification)
                        <li class="border-b border-line pb-3 last:border-0 last:pb-0">
                            <p class="text-sm font-medium text-heading">{{ $notification->title }}</p>
                            <p class="mt-0.5 text-xs text-ink-secondary">{{ $notification->message }}</p>
                        </li>
                    @empty
                        <li class="text-sm text-ink-secondary">Belum ada notifikasi.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection