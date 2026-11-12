@extends('layouts.dashboard')

@section('title', 'Detail Peminjaman')

@section('content')
    <x-page-header :title="'Peminjaman ' . $borrowing->code" description="Detail transaksi peminjaman dan tindakan yang tersedia.">
        <x-slot:actions>
            <a href="{{ route('admin.borrowings.index') }}" class="btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
            @if ($borrowing->status === 'menunggu')
                <a href="{{ route('admin.borrowings.edit', $borrowing) }}" class="btn-secondary btn-sm">
                    <i data-lucide="pencil" class="h-4 w-4"></i> Edit
                </a>
            @endif
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
                        <dd class="mt-0.5 font-medium text-heading">{{ $borrowing->user?->name ?? '-' }}</dd>
                        <dd class="text-xs text-ink-secondary">{{ $borrowing->user?->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Tanggal Pengajuan</dt>
                        <dd class="mt-0.5 font-medium text-heading">{{ $borrowing->created_at?->translatedFormat('d M Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Tanggal Peminjaman</dt>
                        <dd class="mt-0.5 font-medium text-heading">{{ $borrowing->borrow_date?->translatedFormat('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Batas Pengembalian</dt>
                        <dd class="mt-0.5 font-medium {{ $borrowing->is_late ? 'text-danger' : 'text-heading' }}">
                            {{ $borrowing->due_date?->translatedFormat('d M Y') }}
                            @if ($borrowing->is_late) (terlambat {{ $borrowing->late_days }} hari) @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Keperluan</dt>
                        <dd class="mt-0.5 text-ink">{{ $borrowing->purpose }}</dd>
                    </div>
                    @if ($borrowing->notes)
                        <div class="sm:col-span-2">
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Catatan</dt>
                            <dd class="mt-0.5 text-ink-secondary">{{ $borrowing->notes }}</dd>
                        </div>
                    @endif
                    @if ($borrowing->approver)
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Diproses oleh</dt>
                            <dd class="mt-0.5 font-medium text-heading">{{ $borrowing->approver->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Waktu Proses</dt>
                            <dd class="mt-0.5 font-medium text-heading">{{ $borrowing->approved_at?->translatedFormat('d M Y H:i') }}</dd>
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
                    <h2 class="font-display text-base font-semibold text-heading">Alat yang Dipinjam</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Alat</th>
                                <th>Kode</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($borrowing->details as $detail)
                                <tr>
                                    <td class="font-medium text-heading">{{ $detail->tool?->name ?? '-' }}</td>
                                    <td class="font-mono text-xs text-ink-secondary">{{ $detail->tool?->code }}</td>
                                    <td>{{ $detail->quantity }}</td>
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
                    <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-3">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Kode</dt>
                            <dd class="mt-0.5 font-medium text-heading">{{ $borrowing->returnRecord->code }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Tanggal Kembali</dt>
                            <dd class="mt-0.5 font-medium text-heading">{{ $borrowing->returnRecord->return_date?->translatedFormat('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Denda</dt>
                            <dd class="mt-0.5 font-medium text-heading">Rp {{ number_format((float) $borrowing->returnRecord->fine_amount, 0, ',', '.') }}</dd>
                        </div>
                    </dl>
                    <a href="{{ route('admin.returns.show', $borrowing->returnRecord) }}" class="btn-secondary btn-sm mt-4">Lihat detail pengembalian</a>
                </div>
            @endif
        </div>

        <div class="space-y-5">
            <div class="card card-pad">
                <h2 class="font-display text-base font-semibold text-heading">Tindakan</h2>

                @if ($borrowing->status === 'menunggu')
                    <form action="{{ route('admin.borrowings.approve', $borrowing) }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="btn-success w-full">
                            <i data-lucide="check" class="h-4 w-4"></i> Setujui Peminjaman
                        </button>
                    </form>

                    <div x-data="{ open: false }" class="mt-2">
                        <button type="button" class="btn-secondary w-full text-danger" x-on:click="open = true">
                            <i data-lucide="x" class="h-4 w-4"></i> Tolak Peminjaman
                        </button>

                        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                            <div x-show="open" x-transition.opacity class="absolute inset-0 bg-ink/45" x-on:click="open = false"></div>
                            <div x-show="open" x-transition class="relative w-full max-w-md rounded-xl bg-surface p-5 shadow-pop">
                                <h3 class="font-display text-base font-semibold text-heading">Tolak peminjaman</h3>
                                <p class="mt-1 text-sm text-ink-secondary">Berikan alasan penolakan agar peminjam memahami keputusan.</p>
                                <form action="{{ route('admin.borrowings.reject', $borrowing) }}" method="POST" class="mt-4">
                                    @csrf
                                    <textarea name="rejection_reason" rows="3" required class="input" placeholder="Alasan penolakan...">{{ old('rejection_reason') }}</textarea>
                                    <div class="mt-4 flex justify-end gap-2">
                                        <button type="button" class="btn-secondary" x-on:click="open = false">Batal</button>
                                        <button type="submit" class="btn-danger">Tolak</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @elseif ($borrowing->status === 'disetujui')
                    <form action="{{ route('admin.borrowings.pickup', $borrowing) }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="btn-primary w-full">
                            <i data-lucide="package-open" class="h-4 w-4"></i> Tandai Alat Diserahkan
                        </button>
                    </form>
                @else
                    <p class="mt-3 text-sm text-ink-secondary">Tidak ada tindakan yang diperlukan untuk status saat ini.</p>
                @endif

                @if (in_array($borrowing->status, ['menunggu', 'ditolak']))
                    <div class="mt-4 border-t border-line pt-4">
                        <x-confirm-form :action="route('admin.borrowings.destroy', $borrowing)" title="Hapus peminjaman"
                            :message="'Peminjaman ' . $borrowing->code . ' akan dihapus permanen.'" label="Hapus Peminjaman" tone="btn-secondary btn-sm w-full text-danger" />
                    </div>
                @endif
            </div>

            <div class="card card-pad">
                <h2 class="font-display text-base font-semibold text-heading">Alur Status</h2>
                <ol class="mt-4 space-y-4">
                    @php
                        $steps = [
                            ['key' => 'menunggu', 'label' => 'Pengajuan dibuat'],
                            ['key' => 'disetujui', 'label' => 'Disetujui petugas'],
                            ['key' => 'dipinjam', 'label' => 'Alat diserahkan'],
                            ['key' => 'selesai', 'label' => 'Pengembalian selesai'],
                        ];
                        $order = ['menunggu' => 0, 'disetujui' => 1, 'dipinjam' => 2, 'terlambat' => 2, 'selesai' => 3, 'ditolak' => -1];
                        $current = $order[$borrowing->status] ?? 0;
                    @endphp
                    @foreach ($steps as $index => $step)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full {{ $current >= $index && $borrowing->status !== 'ditolak' ? 'bg-success text-white' : 'bg-ink/5 text-ink-secondary' }}">
                                <i data-lucide="{{ $current >= $index && $borrowing->status !== 'ditolak' ? 'check' : 'circle' }}" class="h-3.5 w-3.5"></i>
                            </span>
                            <span class="text-sm {{ $current >= $index && $borrowing->status !== 'ditolak' ? 'font-medium text-heading' : 'text-ink-secondary' }}">{{ $step['label'] }}</span>
                        </li>
                    @endforeach
                    @if ($borrowing->status === 'ditolak')
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-full bg-danger text-white">
                                <i data-lucide="x" class="h-3.5 w-3.5"></i>
                            </span>
                            <span class="text-sm font-medium text-danger">Pengajuan ditolak</span>
                        </li>
                    @endif
                </ol>
            </div>
        </div>
    </div>
@endsection