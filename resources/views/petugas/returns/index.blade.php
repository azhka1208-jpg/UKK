@extends('layouts.dashboard')

@section('title', 'Data Pengembalian')

@section('content')
    <x-page-header title="Data Pengembalian" description="Verifikasi pengembalian alat dari peminjam." />

    <form method="GET" action="{{ route('petugas.returns.index') }}" class="mt-6 card card-pad">
        <div class="grid gap-3 sm:grid-cols-[1fr_1fr_auto]">
            <div>
                <label class="label" for="q">Pencarian</label>
                <div class="relative">
                    <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-secondary"></i>
                    <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="Kode pengembalian atau peminjaman" class="input pl-9">
                </div>
            </div>
            <div>
                <label class="label" for="status">Status</label>
                <select id="status" name="status" class="input">
                    <option value="">Semua status</option>
                    <option value="diajukan" @selected(request('status') === 'diajukan')>Menunggu Verifikasi</option>
                    <option value="diverifikasi" @selected(request('status') === 'diverifikasi')>Diverifikasi</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary">Terapkan</button>
                <a href="{{ route('petugas.returns.index') }}" class="btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="mt-5 card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Peminjaman</th>
                        <th>Peminjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Terlambat</th>
                        <th>Denda</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($returns as $return)
                        <tr>
                            <td><a href="{{ route('petugas.returns.show', $return) }}" class="font-medium text-heading hover:underline">{{ $return->code }}</a></td>
                            <td class="font-mono text-xs text-ink-secondary">{{ $return->borrowing?->code ?? '-' }}</td>
                            <td class="text-ink">{{ $return->borrowing?->user?->name ?? '-' }}</td>
                            <td class="whitespace-nowrap text-ink-secondary">{{ $return->return_date?->format('d/m/Y') }}</td>
                            <td>{{ $return->late_days }} hari</td>
                            <td class="whitespace-nowrap">Rp {{ number_format((float) $return->fine_amount, 0, ',', '.') }}</td>
                            <td><x-status-badge :label="$return->status_label" :tone="$return->status_badge" dot /></td>
                            <td>
                                <div class="flex justify-end">
                                    <a href="{{ route('petugas.returns.show', $return) }}" class="btn-secondary btn-sm">
                                        {{ $return->status === 'diajukan' ? 'Verifikasi' : 'Detail' }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-empty-state title="Belum ada data pengembalian" description="Pengembalian yang diajukan peminjam akan muncul di sini." icon="undo-2" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-line px-4 py-3">
            {{ $returns->links() }}
        </div>
    </div>
@endsection