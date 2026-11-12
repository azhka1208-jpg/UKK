@extends('layouts.dashboard')

@section('title', 'Detail Peminjaman')

@section('content')
    <x-page-header :title="'Peminjaman ' . $borrowing->code" description="Detail peminjaman aktif dan tindakan petugas.">
        <x-slot:actions>
            <a href="{{ route('petugas.monitoring.index') }}" class="btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-5 lg:grid-cols-[1.6fr_1fr]">
        <div class="space-y-5">
            <div class="card card-pad">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <x-status-badge :label="$borrowing->status_label" :tone="$borrowing->status_badge" dot />
                    <div class="text-right">
                        <p class="text-xs uppercase tracking-wide text-ink-secondary">Batas Kembali</p>
                        <p class="font-display text-lg font-semibold {{ $borrowing->is_late ? 'text-danger' : 'text-heading' }}">
                            {{ $borrowing->due_date?->format('d/m/Y') }}
                            @if ($borrowing->is_late) <span class="text-sm">(+{{ $borrowing->late_days }} hari)</span> @endif
                        </p>
                    </div>
                </div>

                <dl class="mt-5 grid gap-4 border-t border-line pt-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Peminjam</dt>
                        <dd class="mt-0.5 font-medium text-heading">{{ $borrowing->user?->name }}</dd>
                        <dd class="text-xs text-ink-secondary">{{ $borrowing->user?->phone ?: $borrowing->user?->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Tanggal Pinjam</dt>
                        <dd class="mt-0.5 text-heading">{{ $borrowing->borrow_date?->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Diserahkan</dt>
                        <dd class="mt-0.5 text-heading">{{ $borrowing->picked_up_at?->translatedFormat('d M Y H:i') ?? 'Belum diserahkan' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Keperluan</dt>
                        <dd class="mt-0.5 text-ink">{{ $borrowing->purpose }}</dd>
                    </div>
                </dl>
            </div>

            <div class="card">
                <div class="border-b border-line px-5 py-4">
                    <h2 class="font-display text-base font-semibold text-heading">Alat yang Dipinjam</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr><th>Alat</th><th>Kode</th><th>Jumlah</th><th>Stok Tersedia</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($borrowing->details as $detail)
                                <tr>
                                    <td class="font-medium text-heading">{{ $detail->tool?->name }}</td>
                                    <td class="font-mono text-xs text-ink-secondary">{{ $detail->tool?->code }}</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>{{ $detail->tool?->stock_available }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($borrowing->returnRecord)
                <div class="card card-pad">
                    <div class="flex items-center justify-between">
                        <h2 class="font-display text-base font-semibold text-heading">Pengembalian</h2>
                        <x-status-badge :label="$borrowing->returnRecord->status_label" :tone="$borrowing->returnRecord->status_badge" dot />
                    </div>
                    <a href="{{ route('petugas.returns.show', $borrowing->returnRecord) }}" class="btn-secondary btn-sm mt-3">Buka detail pengembalian</a>
                </div>
            @endif
        </div>

        <div class="space-y-5">
            @if ($borrowing->status === 'disetujui')
                <div class="card card-pad">
                    <h2 class="font-display text-base font-semibold text-heading">Penyerahan Alat</h2>
                    <p class="mt-1 text-sm text-ink-secondary">Pastikan peminjam menunjukkan identitas sebelum menyerahkan alat.</p>
                    <form action="{{ route('petugas.monitoring.pickup', $borrowing) }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="btn-primary w-full">
                            <i data-lucide="package-open" class="h-4 w-4"></i> Tandai Alat Diserahkan
                        </button>
                    </form>
                </div>
            @else
                <div class="card card-pad">
                    <h2 class="font-display text-base font-semibold text-heading">Informasi</h2>
                    <p class="mt-2 text-sm text-ink-secondary">
                        @if ($borrowing->returnRecord)
                            Peminjam telah mengajukan pengembalian. Verifikasi melalui halaman pengembalian.
                        @else
                            Menunggu pengembalian dari peminjam.
                        @endif
                    </p>
                </div>
            @endif

            <div class="card card-pad">
                <h2 class="font-display text-base font-semibold text-heading">Riwayat Proses</h2>
                <ul class="mt-4 space-y-3 text-sm">
                    <li class="flex gap-3">
                        <i data-lucide="check" class="mt-0.5 h-4 w-4 shrink-0 text-success"></i>
                        <span>Pengajuan dibuat &middot; {{ $borrowing->created_at?->translatedFormat('d M Y H:i') }}</span>
                    </li>
                    <li class="flex gap-3">
                        <i data-lucide="{{ $borrowing->approved_at ? 'check' : 'circle' }}" class="mt-0.5 h-4 w-4 shrink-0 {{ $borrowing->approved_at ? 'text-success' : 'text-slate-300' }}"></i>
                        <span>Disetujui petugas &middot; {{ $borrowing->approved_at?->translatedFormat('d M Y H:i') ?? '-' }}</span>
                    </li>
                    <li class="flex gap-3">
                        <i data-lucide="{{ $borrowing->picked_up_at ? 'check' : 'circle' }}" class="mt-0.5 h-4 w-4 shrink-0 {{ $borrowing->picked_up_at ? 'text-success' : 'text-slate-300' }}"></i>
                        <span>Alat diserahkan &middot; {{ $borrowing->picked_up_at?->translatedFormat('d M Y H:i') ?? '-' }}</span>
                    </li>
                    <li class="flex gap-3">
                        <i data-lucide="{{ $borrowing->returnRecord?->verified_at ? 'check' : 'circle' }}" class="mt-0.5 h-4 w-4 shrink-0 {{ $borrowing->returnRecord?->verified_at ? 'text-success' : 'text-slate-300' }}"></i>
                        <span>Pengembalian selesai &middot; {{ $borrowing->returnRecord?->verified_at?->translatedFormat('d M Y H:i') ?? '-' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection