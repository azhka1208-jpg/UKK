@extends('layouts.dashboard')

@section('title', 'Pengembalian')

@section('content')
    <x-page-header title="Pengembalian" description="Riwayat pengajuan pengembalian alat Anda." />

    <div class="mt-5 card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Peminjaman</th>
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
                            <td><a href="{{ route('peminjam.returns.show', $return) }}" class="font-medium text-heading hover:underline">{{ $return->code }}</a></td>
                            <td class="font-mono text-xs text-ink-secondary">{{ $return->borrowing?->code ?? '-' }}</td>
                            <td class="whitespace-nowrap text-ink-secondary">{{ $return->return_date?->format('d/m/Y') }}</td>
                            <td>{{ $return->late_days }} hari</td>
                            <td class="whitespace-nowrap">Rp {{ number_format((float) $return->fine_amount, 0, ',', '.') }}</td>
                            <td><x-status-badge :label="$return->status_label" :tone="$return->status_badge" dot /></td>
                            <td>
                                <div class="flex justify-end">
                                    <a href="{{ route('peminjam.returns.show', $return) }}" class="btn-secondary btn-sm">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-empty-state title="Belum ada pengembalian" description="Pengajuan pengembalian akan muncul di sini setelah Anda mengembalikan alat." icon="undo-2" />
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