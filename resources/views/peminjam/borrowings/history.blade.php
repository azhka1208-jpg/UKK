@extends('layouts.dashboard')

@section('title', 'Riwayat Peminjaman')

@section('content')
    <x-page-header title="Riwayat Peminjaman" description="Peminjaman yang telah selesai atau ditolak." />

    <div class="mt-5 card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Alat</th>
                        <th>Tanggal Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($borrowings as $borrowing)
                        <tr>
                            <td><a href="{{ route('peminjam.borrowings.show', $borrowing) }}" class="font-medium text-heading hover:underline">{{ $borrowing->code }}</a></td>
                            <td class="max-w-xs text-ink-secondary">{{ $borrowing->details->map(fn ($d) => $d->tool?->name)->filter()->join(', ') }}</td>
                            <td class="whitespace-nowrap text-ink-secondary">{{ $borrowing->borrow_date?->format('d/m/Y') }}</td>
                            <td class="whitespace-nowrap text-ink-secondary">{{ $borrowing->due_date?->format('d/m/Y') }}</td>
                            <td><x-status-badge :label="$borrowing->status_label" :tone="$borrowing->status_badge" dot /></td>
                            <td>
                                <div class="flex justify-end">
                                    <a href="{{ route('peminjam.borrowings.show', $borrowing) }}" class="btn-secondary btn-sm">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state title="Belum ada riwayat" description="Peminjaman yang selesai atau ditolak akan muncul di sini." icon="history" />
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