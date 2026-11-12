@extends('layouts.dashboard')

@section('title', 'Tinjau Peminjaman')

@section('content')
    <x-page-header :title="'Tinjau ' . $borrowing->code" description="Periksa detail pengajuan sebelum memberikan keputusan.">
        <x-slot:actions>
            <a href="{{ route('petugas.approvals.index') }}" class="btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-5 lg:grid-cols-[1.6fr_1fr]">
        <div class="space-y-5">
            <div class="card card-pad">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-ink-secondary">Status</p>
                        <div class="mt-1"><x-status-badge :label="$borrowing->status_label" :tone="$borrowing->status_badge" dot /></div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs uppercase tracking-wide text-ink-secondary">Total Item</p>
                        <p class="font-display text-lg font-semibold text-heading">{{ $borrowing->total_items }}</p>
                    </div>
                </div>

                <dl class="mt-5 grid gap-4 border-t border-line pt-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Peminjam</dt>
                        <dd class="mt-0.5 font-medium text-heading">{{ $borrowing->user?->name }}</dd>
                        <dd class="text-xs text-ink-secondary">{{ $borrowing->user?->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Diajukan</dt>
                        <dd class="mt-0.5 font-medium text-heading">{{ $borrowing->created_at?->translatedFormat('d M Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Tanggal Peminjaman</dt>
                        <dd class="mt-0.5 text-heading">{{ $borrowing->borrow_date?->translatedFormat('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Jadwal Pengembalian</dt>
                        <dd class="mt-0.5 text-heading">{{ $borrowing->due_date?->translatedFormat('d M Y') }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Keperluan</dt>
                        <dd class="mt-0.5 text-ink">{{ $borrowing->purpose }}</dd>
                    </div>
                    @if ($borrowing->notes)
                        <div class="sm:col-span-2">
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Catatan Peminjam</dt>
                            <dd class="mt-0.5 text-ink-secondary">{{ $borrowing->notes }}</dd>
                        </div>
                    @endif
                    @if ($borrowing->rejection_reason)
                        <div class="sm:col-span-2">
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Alasan Penolakan</dt>
                            <dd class="mt-1 rounded-lg border border-danger/20 bg-danger/5 px-3 py-2 text-sm text-danger">{{ $borrowing->rejection_reason }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="card">
                <div class="border-b border-line px-5 py-4">
                    <h2 class="font-display text-base font-semibold text-heading">Alat yang Diajukan</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr><th>Alat</th><th>Kode</th><th>Kategori</th><th>Stok Tersedia</th><th>Jumlah</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($borrowing->details as $detail)
                                <tr>
                                    <td class="font-medium text-heading">{{ $detail->tool?->name ?? '-' }}</td>
                                    <td class="font-mono text-xs text-ink-secondary">{{ $detail->tool?->code }}</td>
                                    <td class="text-ink-secondary">{{ $detail->tool?->category?->name ?? '-' }}</td>
                                    <td>{{ $detail->tool?->stock_available ?? 0 }}</td>
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
                <h2 class="font-display text-base font-semibold text-heading">Keputusan</h2>

                @if ($borrowing->status === 'menunggu')
                    <form action="{{ route('petugas.approvals.approve', $borrowing) }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="btn-success w-full">
                            <i data-lucide="check" class="h-4 w-4"></i> Setujui
                        </button>
                    </form>

                    <div x-data="{ open: false }" class="mt-2">
                        <button type="button" class="btn-secondary w-full text-danger" x-on:click="open = true">
                            <i data-lucide="x" class="h-4 w-4"></i> Tolak
                        </button>
                        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                            <div x-show="open" x-transition.opacity class="absolute inset-0 bg-ink/45" x-on:click="open = false"></div>
                            <div x-show="open" x-transition class="relative w-full max-w-md rounded-xl bg-surface p-5 shadow-pop">
                                <h3 class="font-display text-base font-semibold text-heading">Tolak peminjaman</h3>
                                <form action="{{ route('petugas.approvals.reject', $borrowing) }}" method="POST" class="mt-3">
                                    @csrf
                                    <textarea name="rejection_reason" rows="3" required class="input" placeholder="Alasan penolakan...">{{ old('rejection_reason') }}</textarea>
                                    @error('rejection_reason')<p class="error-text">{{ $message }}</p>@enderror
                                    <div class="mt-4 flex justify-end gap-2">
                                        <button type="button" class="btn-secondary" x-on:click="open = false">Batal</button>
                                        <button type="submit" class="btn-danger">Tolak</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @elseif ($borrowing->status === 'disetujui')
                    <p class="mt-2 text-sm text-ink-secondary">Peminjaman telah disetujui. Tandai alat sebagai diserahkan saat peminjam mengambil alat.</p>
                    <a href="{{ route('petugas.monitoring.show', $borrowing) }}" class="btn-primary mt-4 w-full">
                        <i data-lucide="package-open" class="h-4 w-4"></i> Serahkan Alat
                    </a>
                @else
                    <p class="mt-2 text-sm text-ink-secondary">Pengajuan ini sudah diproses.</p>
                    @if ($borrowing->approver)
                        <p class="mt-2 text-xs text-ink-secondary">Diproses oleh {{ $borrowing->approver->name }}</p>
                    @endif
                @endif
            </div>

            @if ($borrowing->returnRecord)
                <div class="card card-pad">
                    <h2 class="font-display text-base font-semibold text-heading">Pengembalian</h2>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="text-sm text-ink-secondary">{{ $borrowing->returnRecord->code }}</span>
                        <x-status-badge :label="$borrowing->returnRecord->status_label" :tone="$borrowing->returnRecord->status_badge" dot />
                    </div>
                    <a href="{{ route('petugas.returns.show', $borrowing->returnRecord) }}" class="btn-secondary btn-sm mt-3 w-full">Lihat pengembalian</a>
                </div>
            @endif
        </div>
    </div>
@endsection