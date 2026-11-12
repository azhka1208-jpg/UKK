@extends('layouts.dashboard')

@section('title', 'Detail Peminjaman')

@section('content')
    <x-page-header :title="'Peminjaman ' . $borrowing->code" description="Informasi lengkap transaksi peminjaman.">
        <x-slot:actions>
            <a href="{{ route('petugas.borrowings.index') }}" class="btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-5 lg:grid-cols-[1.6fr_1fr]">
        <div class="space-y-5">
            <div class="card card-pad">
                <div class="flex items-center justify-between">
                    <x-status-badge :label="$borrowing->status_label" :tone="$borrowing->status_badge" dot />
                    <span class="text-sm text-ink-secondary">{{ $borrowing->total_items }} item</span>
                </div>

                <dl class="mt-5 grid gap-4 border-t border-line pt-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Peminjam</dt>
                        <dd class="mt-0.5 font-medium text-heading">{{ $borrowing->user?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Diajukan</dt>
                        <dd class="mt-0.5 text-heading">{{ $borrowing->created_at?->translatedFormat('d M Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Tanggal Pinjam</dt>
                        <dd class="mt-0.5 text-heading">{{ $borrowing->borrow_date?->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Jatuh Tempo</dt>
                        <dd class="mt-0.5 text-heading">{{ $borrowing->due_date?->format('d/m/Y') }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Keperluan</dt>
                        <dd class="mt-0.5 text-ink">{{ $borrowing->purpose }}</dd>
                    </div>
                </dl>
            </div>

            <div class="card">
                <div class="border-b border-line px-5 py-4">
                    <h2 class="font-display text-base font-semibold text-heading">Daftar Alat</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr><th>Alat</th><th>Kode</th><th>Jumlah</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($borrowing->details as $detail)
                                <tr>
                                    <td class="font-medium text-heading">{{ $detail->tool?->name }}</td>
                                    <td class="font-mono text-xs text-ink-secondary">{{ $detail->tool?->code }}</td>
                                    <td>{{ $detail->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-5">
            <div class="card card-pad">
                <h2 class="font-display text-base font-semibold text-heading">Pengembalian</h2>
                @if ($borrowing->returnRecord)
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-sm text-ink-secondary">{{ $borrowing->returnRecord->code }}</span>
                        <x-status-badge :label="$borrowing->returnRecord->status_label" :tone="$borrowing->returnRecord->status_badge" dot />
                    </div>
                    <a href="{{ route('petugas.returns.show', $borrowing->returnRecord) }}" class="btn-secondary btn-sm mt-3 w-full">Detail pengembalian</a>
                @else
                    <p class="mt-2 text-sm text-ink-secondary">Belum ada pengembalian untuk transaksi ini.</p>
                @endif
            </div>

            <div class="card card-pad">
                <h2 class="font-display text-base font-semibold text-heading">Petugas</h2>
                <p class="mt-2 text-sm text-ink-secondary">Diproses oleh {{ $borrowing->approver?->name ?? '-' }}</p>
                <p class="text-xs text-ink-secondary">{{ $borrowing->approved_at?->translatedFormat('d M Y H:i') ?? '-' }}</p>
            </div>
        </div>
    </div>
@endsection