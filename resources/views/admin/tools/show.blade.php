@extends('layouts.dashboard')

@section('title', 'Detail Alat')

@section('content')
    <x-page-header title="Detail Alat" description="Informasi lengkap alat dan riwayat peminjaman.">
        <x-slot:actions>
            <a href="{{ route('admin.tools.edit', $tool) }}" class="btn-secondary btn-sm">
                <i data-lucide="pencil" class="h-4 w-4"></i> Edit
            </a>
            <a href="{{ route('admin.tools.index') }}" class="btn-secondary btn-sm">Kembali</a>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-5 lg:grid-cols-[1fr_1.4fr]">
        <div class="card overflow-hidden">
            <img src="{{ $tool->photo_url }}" alt="Foto {{ $tool->name }}" class="aspect-[4/3] w-full object-cover">
            <div class="p-5">
                <p class="font-mono text-xs text-ink-secondary">{{ $tool->code }}</p>
                <h2 class="mt-1 font-display text-lg font-semibold text-heading">{{ $tool->name }}</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="rounded-full bg-accent px-3 py-1 text-xs font-medium text-heading">{{ $tool->category?->name ?? '-' }}</span>
                    <x-status-badge :label="$tool->is_available ? 'Tersedia' : ($tool->status === 'perbaikan' ? 'Perbaikan' : 'Tidak Tersedia')"
                        :tone="$tool->is_available ? 'badge-success' : ($tool->status === 'perbaikan' ? 'badge-warning' : 'badge-danger')" dot />
                </div>
                <p class="mt-4 text-sm leading-relaxed text-ink-secondary">{{ $tool->description ?: 'Belum ada deskripsi.' }}</p>
            </div>
        </div>

        <div class="space-y-5">
            <div class="card card-pad">
                <h3 class="font-display text-base font-semibold text-heading">Informasi Inventaris</h3>
                <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Stok Total</dt>
                        <dd class="mt-1 font-display text-lg font-semibold text-heading">{{ $tool->stock_total }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Stok Tersedia</dt>
                        <dd class="mt-1 font-display text-lg font-semibold text-heading">{{ $tool->stock_available }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Kondisi</dt>
                        <dd class="mt-1 font-medium text-heading">{{ $tool->condition_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Lokasi</dt>
                        <dd class="mt-1 font-medium text-heading">{{ $tool->location ?: '-' }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Terakhir diperbarui</dt>
                        <dd class="mt-1 font-medium text-heading">{{ $tool->updated_at?->translatedFormat('d M Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="card">
                <div class="border-b border-line px-5 py-4">
                    <h3 class="font-display text-base font-semibold text-heading">Riwayat Peminjaman Alat</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Peminjam</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($borrowings as $detail)
                                <tr>
                                    <td>
                                        @if ($detail->borrowing)
                                            <a href="{{ route('admin.borrowings.show', $detail->borrowing) }}" class="font-medium text-heading hover:underline">{{ $detail->borrowing->code }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-ink-secondary">{{ $detail->borrowing?->user?->name ?? '-' }}</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>
                                        @if ($detail->borrowing)
                                            <x-status-badge :label="$detail->borrowing->status_label" :tone="$detail->borrowing->status_badge" dot />
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <x-empty-state title="Belum pernah dipinjam" description="Alat ini belum memiliki riwayat peminjaman." icon="history" />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection