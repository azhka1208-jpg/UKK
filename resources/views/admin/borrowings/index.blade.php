@extends('layouts.dashboard')

@section('title', 'Data Peminjaman')

@section('content')
    <x-page-header title="Data Peminjaman" description="Seluruh transaksi peminjaman alat beserta statusnya.">
        <x-slot:actions>
            <a href="{{ route('admin.borrowings.create') }}" class="btn-primary btn-sm">
                <i data-lucide="plus" class="h-4 w-4"></i> Tambah Peminjaman
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 flex flex-wrap gap-2">
        @foreach ([
            'all' => 'Semua',
            'menunggu' => 'Menunggu',
            'disetujui' => 'Disetujui',
            'dipinjam' => 'Dipinjam',
            'terlambat' => 'Terlambat',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ] as $key => $label)
            <a href="{{ route('admin.borrowings.index', $key === 'all' ? [] : ['status' => $key]) }}"
               class="rounded-full border px-3 py-1.5 text-xs font-medium transition-colors {{ ($key === 'all' && ! request('status')) || request('status') === $key ? 'border-navy bg-navy text-white' : 'border-line bg-surface text-ink-secondary hover:border-navy/30 hover:text-heading' }}">
                {{ $label }}
                @if ($key !== 'all')
                    <span class="ml-1 opacity-70">{{ $statusCounts[$key] ?? 0 }}</span>
                @endif
            </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('admin.borrowings.index') }}" class="mt-4 card card-pad">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_auto]">
            <div>
                <label class="label" for="q">Pencarian</label>
                <div class="relative">
                    <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-secondary"></i>
                    <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="Kode atau nama peminjam" class="input pl-9">
                </div>
            </div>
            <div>
                <label class="label" for="from">Dari Tanggal</label>
                <input id="from" type="date" name="from" value="{{ request('from') }}" class="input">
            </div>
            <div>
                <label class="label" for="to">Sampai Tanggal</label>
                <input id="to" type="date" name="to" value="{{ request('to') }}" class="input">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary flex-1">Terapkan</button>
                <a href="{{ route('admin.borrowings.index') }}" class="btn-secondary">Reset</a>
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
                            <td>
                                <a href="{{ route('admin.borrowings.show', $borrowing) }}" class="font-medium text-heading hover:underline">{{ $borrowing->code }}</a>
                            </td>
                            <td>
                                <p class="text-ink">{{ $borrowing->user?->name ?? '-' }}</p>
                                <p class="text-xs text-ink-secondary">{{ $borrowing->user?->username }}</p>
                            </td>
                            <td class="whitespace-nowrap text-ink-secondary">{{ $borrowing->borrow_date?->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap text-ink-secondary">
                                {{ $borrowing->due_date?->format('d/m/Y') }}
                                @if ($borrowing->is_late)
                                    <span class="ml-1 text-xs text-danger">+{{ $borrowing->late_days }} hari</span>
                                @endif
                            </td>
                            <td>{{ $borrowing->total_items }}</td>
                            <td><x-status-badge :label="$borrowing->status_label" :tone="$borrowing->status_badge" dot /></td>
                            <td>
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.borrowings.show', $borrowing) }}" class="btn-icon" title="Lihat">
                                        <i data-lucide="eye" class="h-4 w-4"></i>
                                    </a>
                                    @if ($borrowing->status === 'menunggu')
                                        <a href="{{ route('admin.borrowings.edit', $borrowing) }}" class="btn-icon" title="Edit">
                                            <i data-lucide="pencil" class="h-4 w-4"></i>
                                        </a>
                                    @endif
                                    @if (in_array($borrowing->status, ['menunggu', 'ditolak']))
                                        <x-confirm-form :action="route('admin.borrowings.destroy', $borrowing)" title="Hapus peminjaman"
                                            :message="'Peminjaman ' . $borrowing->code . ' akan dihapus.'" label="Hapus" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-empty-state title="Belum ada data peminjaman" description="Data peminjaman akan muncul setelah ada pengajuan." icon="clipboard-list" />
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