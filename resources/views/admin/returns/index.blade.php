@extends('layouts.dashboard')

@section('title', 'Data Pengembalian')

@section('content')
    <x-page-header title="Data Pengembalian" description="Pantau pengembalian alat, keterlambatan, dan denda.">
        <x-slot:actions>
            <a href="{{ route('admin.returns.create') }}" class="btn-primary btn-sm">
                <i data-lucide="plus" class="h-4 w-4"></i> Catat Pengembalian
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-3 sm:grid-cols-3">
        <a href="{{ route('admin.returns.index') }}" class="card flex items-center justify-between px-4 py-3 hover:border-navy/30">
            <span class="text-sm font-medium text-heading">Semua</span><span class="badge-neutral">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('admin.returns.index', ['status' => 'diajukan']) }}" class="card flex items-center justify-between px-4 py-3 hover:border-navy/30">
            <span class="text-sm font-medium text-heading">Menunggu Verifikasi</span><span class="badge-warning">{{ $counts['diajukan'] }}</span>
        </a>
        <a href="{{ route('admin.returns.index', ['status' => 'diverifikasi']) }}" class="card flex items-center justify-between px-4 py-3 hover:border-navy/30">
            <span class="text-sm font-medium text-heading">Diverifikasi</span><span class="badge-success">{{ $counts['diverifikasi'] }}</span>
        </a>
    </div>

    <form method="GET" action="{{ route('admin.returns.index') }}" class="mt-4 card card-pad">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[2fr_1fr_auto]">
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
                <button type="submit" class="btn-primary flex-1">Terapkan</button>
                <a href="{{ route('admin.returns.index') }}" class="btn-secondary">Reset</a>
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
                            <td><a href="{{ route('admin.returns.show', $return) }}" class="font-medium text-heading hover:underline">{{ $return->code }}</a></td>
                            <td class="font-mono text-xs text-ink-secondary">{{ $return->borrowing?->code ?? '-' }}</td>
                            <td class="text-ink">{{ $return->borrowing?->user?->name ?? '-' }}</td>
                            <td class="whitespace-nowrap text-ink-secondary">{{ $return->return_date?->format('d/m/Y') }}</td>
                            <td>{{ $return->late_days }} hari</td>
                            <td class="whitespace-nowrap">Rp {{ number_format((float) $return->fine_amount, 0, ',', '.') }}</td>
                            <td><x-status-badge :label="$return->status_label" :tone="$return->status_badge" dot /></td>
                            <td>
                                <div class="flex justify-end">
                                    <a href="{{ route('admin.returns.show', $return) }}" class="btn-icon" title="Lihat">
                                        <i data-lucide="eye" class="h-4 w-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-empty-state title="Belum ada data pengembalian" description="Data pengembalian muncul setelah peminjam mengajukan pengembalian." icon="undo-2" />
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