@extends('layouts.dashboard')

@section('title', 'Peminjaman Saya')

@section('content')
    <x-page-header title="Peminjaman Saya" description="Daftar seluruh transaksi peminjaman alat Anda.">
        <x-slot:actions>
            <a href="{{ route('peminjam.borrowings.create') }}" class="btn-primary btn-sm">
                <i data-lucide="plus" class="h-4 w-4"></i> Ajukan Peminjaman
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" action="{{ route('peminjam.borrowings.index') }}" class="mt-6 card card-pad">
        <div class="grid gap-3 sm:grid-cols-[1fr_auto]">
            <div>
                <label class="label" for="status">Status</label>
                <select id="status" name="status" class="input">
                    <option value="">Semua status</option>
                    @foreach (['menunggu' => 'Menunggu Persetujuan', 'disetujui' => 'Disetujui', 'dipinjam' => 'Sedang Dipinjam', 'terlambat' => 'Terlambat', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary">Terapkan</button>
                <a href="{{ route('peminjam.borrowings.index') }}" class="btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="mt-5 card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Item</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($borrowings as $borrowing)
                        <tr>
                            <td><a href="{{ route('peminjam.borrowings.show', $borrowing) }}" class="font-medium text-heading hover:underline">{{ $borrowing->code }}</a></td>
                            <td class="whitespace-nowrap text-ink-secondary">{{ $borrowing->borrow_date?->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap">
                                <span class="{{ $borrowing->is_late ? 'font-medium text-danger' : 'text-ink-secondary' }}">{{ $borrowing->due_date?->format('d/m/Y') }}</span>
                                @if ($borrowing->is_late)
                                    <span class="ml-1 text-xs text-danger">+{{ $borrowing->late_days }} hari</span>
                                @endif
                            </td>
                            <td>{{ $borrowing->total_items }}</td>
                            <td><x-status-badge :label="$borrowing->status_label" :tone="$borrowing->status_badge" dot /></td>
                            <td>
                                <a href="{{ route('peminjam.borrowings.show', $borrowing) }}" class="btn-secondary btn-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state title="Belum ada peminjaman" description="Ajukan peminjaman pertama Anda melalui menu Ajukan Peminjaman." icon="clipboard-list">
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
        <div class="border-t border-line px-4 py-3">
            {{ $borrowings->links() }}
        </div>
    </div>
@endsection