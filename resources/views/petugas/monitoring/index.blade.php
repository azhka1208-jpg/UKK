@extends('layouts.dashboard')

@section('title', 'Monitoring Pengembalian')

@section('content')
    <x-page-header title="Monitoring Pengembalian" description="Daftar alat yang sedang dipinjam dan batas pengembaliannya." />

    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <x-stat-card label="Sedang Dipinjam" :value="$stats['active']" icon="package-open" tone="navy" />
        <x-stat-card label="Terlambat" :value="$stats['overdue']" icon="alarm-clock" tone="danger" />
        <x-stat-card label="Pengembalian Baru" :value="$stats['return_pending']" icon="undo-2" tone="success" :href="route('petugas.returns.index', ['status' => 'diajukan'])" />
    </div>

    <form method="GET" action="{{ route('petugas.monitoring.index') }}" class="mt-5 card card-pad">
        <div class="grid gap-3 sm:grid-cols-[1fr_1fr_auto]">
            <div>
                <label class="label" for="q">Pencarian</label>
                <div class="relative">
                    <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-secondary"></i>
                    <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="Kode atau peminjam" class="input pl-9">
                </div>
            </div>
            <div>
                <label class="label" for="status">Filter</label>
                <select id="status" name="status" class="input">
                    <option value="">Semua</option>
                    <option value="terlambat" @selected(request('status') === 'terlambat')>Hanya terlambat</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary">Terapkan</button>
                <a href="{{ route('petugas.monitoring.index') }}" class="btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="mt-5 card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Peminjam</th>
                        <th>Alat</th>
                        <th>Tanggal Pinjam</th>
                        <th>Batas Kembali</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($borrowings as $borrowing)
                        <tr>
                            <td><a href="{{ route('petugas.monitoring.show', $borrowing) }}" class="font-medium text-heading hover:underline">{{ $borrowing->code }}</a></td>
                            <td>
                                <p class="text-ink">{{ $borrowing->user?->name }}</p>
                                <p class="text-xs text-ink-secondary">{{ $borrowing->user?->phone ?: $borrowing->user?->email }}</p>
                            </td>
                            <td class="max-w-xs text-ink-secondary">{{ $borrowing->details->map(fn ($d) => ($d->tool?->name ?? '-') . ' (' . $d->quantity . ')')->join(', ') }}</td>
                            <td class="whitespace-nowrap text-ink-secondary">{{ $borrowing->borrow_date?->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap">
                                <span class="{{ $borrowing->is_late ? 'font-medium text-danger' : 'text-ink-secondary' }}">{{ $borrowing->due_date?->format('d/m/Y') }}</span>
                                @if ($borrowing->is_late)
                                    <span class="ml-1 text-xs text-danger">+{{ $borrowing->late_days }} hari</span>
                                @endif
                            </td>
                            <td><x-status-badge :label="$borrowing->status_label" :tone="$borrowing->status_badge" dot /></td>
                            <td>
                                <div class="flex items-center justify-end gap-1.5">
                                    @if ($borrowing->status === 'disetujui' && ! $borrowing->picked_up_at)
                                        <form action="{{ route('petugas.monitoring.pickup', $borrowing) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-soft btn-sm">Serahkan</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('petugas.monitoring.show', $borrowing) }}" class="btn-icon" title="Detail">
                                        <i data-lucide="eye" class="h-4 w-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-empty-state title="Tidak ada peminjaman aktif" description="Belum ada alat yang sedang dipinjam." icon="package" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-line px-4 py-3">
            {{ $borrowings->links() }}
        </div>
    </div>

    <div class="mt-6 card">
        <div class="flex items-center justify-between border-b border-line px-5 py-4">
            <h2 class="font-display text-base font-semibold text-heading">Pengembalian Terbaru Menunggu Verifikasi</h2>
            <a href="{{ route('petugas.returns.index') }}" class="link text-sm">Semua pengembalian</a>
        </div>
        <div class="divide-y divide-line">
            @forelse ($pendingReturns as $return)
                <div class="flex items-center justify-between gap-3 px-5 py-3.5">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-heading">{{ $return->code }} &middot; {{ $return->borrowing?->user?->name }}</p>
                        <p class="text-xs text-ink-secondary">
                            Dikembalikan {{ $return->return_date?->format('d/m/Y') }}
                            @if ($return->late_days > 0)
                                <span class="text-danger">&middot; terlambat {{ $return->late_days }} hari</span>
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('petugas.returns.show', $return) }}" class="btn-secondary btn-sm">Verifikasi</a>
                </div>
            @empty
                <x-empty-state title="Tidak ada pengembalian baru" description="Semua pengembalian telah diverifikasi." icon="check-circle-2" />
            @endforelse
        </div>
    </div>
@endsection