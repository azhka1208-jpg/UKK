@extends('layouts.dashboard')

@section('title', 'Data Peminjaman')

@section('content')
    <x-page-header title="Data Peminjaman" description="Riwayat seluruh transaksi peminjaman alat." />

    <form method="GET" action="{{ route('petugas.borrowings.index') }}" class="mt-6 card card-pad">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_1fr_auto]">
            <div>
                <label class="label" for="q">Pencarian</label>
                <div class="relative">
                    <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-secondary"></i>
                    <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="Kode atau nama peminjam" class="input pl-9">
                </div>
            </div>
            <div>
                <label class="label" for="status">Status</label>
                <select id="status" name="status" class="input">
                    <option value="">Semua</option>
                    @foreach (['menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'dipinjam' => 'Dipinjam', 'terlambat' => 'Terlambat', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="from">Dari</label>
                <input id="from" type="date" name="from" value="{{ request('from') }}" class="input">
            </div>
            <div>
                <label class="label" for="to">Sampai</label>
                <input id="to" type="date" name="to" value="{{ request('to') }}" class="input">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary">Terapkan</button>
                <a href="{{ route('petugas.borrowings.index') }}" class="btn-secondary">Reset</a>
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
                        <th>Tanggal</th>
                        <th>Jatuh Tempo</th>
                        <th>Item</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($borrowings as $borrowing)
                        <tr>
                            <td><a href="{{ route('petugas.borrowings.show', $borrowing) }}" class="font-medium text-heading hover:underline">{{ $borrowing->code }}</a></td>
                            <td>{{ $borrowing->user?->name }}</td>
                            <td class="whitespace-nowrap text-ink-secondary">{{ $borrowing->borrow_date?->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap text-ink-secondary">{{ $borrowing->due_date?->format('d/m/Y') }}</td>
                            <td>{{ $borrowing->total_items }}</td>
                            <td><x-status-badge :label="$borrowing->status_label" :tone="$borrowing->status_badge" dot /></td>
                            <td>
                                <div class="flex justify-end">
                                    <a href="{{ route('petugas.borrowings.show', $borrowing) }}" class="btn-icon" title="Detail">
                                        <i data-lucide="eye" class="h-4 w-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-empty-state title="Belum ada data peminjaman" description="Belum ada transaksi peminjaman tercatat." icon="clipboard-list" />
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
@endsection