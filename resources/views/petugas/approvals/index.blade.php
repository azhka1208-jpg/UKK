@extends('layouts.dashboard')

@section('title', 'Persetujuan Peminjaman')

@section('content')
    <x-page-header title="Persetujuan Peminjaman" description="Tinjau dan proses pengajuan peminjaman alat." />

    <div class="mt-6 grid gap-3 sm:grid-cols-3">
        @foreach ([
            ['menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'],
        ] as $group)
            @foreach ($group as $key => $label)
                <a href="{{ route('petugas.approvals.index', ['status' => $key]) }}"
                   class="card flex items-center justify-between px-4 py-3 transition-colors hover:border-navy/30 {{ $status === $key ? 'border-navy/40' : '' }}">
                    <span class="text-sm font-medium text-heading">{{ $label }}</span>
                    <span class="badge-neutral">{{ $counts[$key] ?? 0 }}</span>
                </a>
            @endforeach
        @endforeach
    </div>

    <form method="GET" action="{{ route('petugas.approvals.index') }}" class="mt-4 card card-pad">
        <div class="grid gap-3 sm:grid-cols-[1fr_1fr_auto]">
            <div>
                <label class="label" for="q">Pencarian</label>
                <div class="relative">
                    <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-secondary"></i>
                    <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="Kode atau peminjam" class="input pl-9">
                </div>
            </div>
            <div>
                <label class="label" for="status">Status</label>
                <select id="status" name="status" class="input">
                    <option value="menunggu" @selected($status === 'menunggu')>Menunggu</option>
                    <option value="disetujui" @selected($status === 'disetujui')>Disetujui</option>
                    <option value="ditolak" @selected($status === 'ditolak')>Ditolak</option>
                    <option value="all" @selected($status === 'all')>Semua</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary">Terapkan</button>
                <a href="{{ route('petugas.approvals.index') }}" class="btn-secondary">Reset</a>
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
                            <td><a href="{{ route('petugas.approvals.show', $borrowing) }}" class="font-medium text-heading hover:underline">{{ $borrowing->code }}</a></td>
                            <td>
                                <p class="text-ink">{{ $borrowing->user?->name }}</p>
                                <p class="text-xs text-ink-secondary">{{ $borrowing->created_at?->diffForHumans() }}</p>
                            </td>
                            <td class="whitespace-nowrap text-ink-secondary">{{ $borrowing->borrow_date?->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap text-ink-secondary">{{ $borrowing->due_date?->format('d/m/Y') }}</td>
                            <td>{{ $borrowing->total_items }}</td>
                            <td><x-status-badge :label="$borrowing->status_label" :tone="$borrowing->status_badge" dot /></td>
                            <td>
                                <div class="flex justify-end">
                                    <a href="{{ route('petugas.approvals.show', $borrowing) }}" class="btn-secondary btn-sm">Tinjau</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-empty-state title="Tidak ada data" description="Belum ada pengajuan pada status ini." icon="check-circle-2" />
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