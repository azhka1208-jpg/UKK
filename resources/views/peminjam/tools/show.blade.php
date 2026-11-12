@extends('layouts.dashboard')

@section('title', $tool->name)

@section('content')
    <x-page-header title="{{ $tool->name }}" description="Detail alat dan opsi peminjaman.">
        <x-slot:actions>
            <a href="{{ route('peminjam.tools.index') }}" class="btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-5 lg:grid-cols-[1.1fr_1fr]">
        <div class="card overflow-hidden">
            <img src="{{ $tool->photo_url }}" alt="Foto {{ $tool->name }}" class="aspect-[4/3] w-full object-cover">
        </div>

        <div class="card card-pad">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-accent px-3 py-1 text-xs font-medium text-heading">{{ $tool->category?->name ?? '-' }}</span>
                <x-status-badge :label="$tool->is_available ? 'Tersedia' : ($tool->status === 'perbaikan' ? 'Perbaikan' : 'Tidak Tersedia')"
                    :tone="$tool->is_available ? 'badge-success' : ($tool->status === 'perbaikan' ? 'badge-warning' : 'badge-danger')" dot />
            </div>

            <h1 class="mt-3 font-display text-xl font-semibold text-heading">{{ $tool->name }}</h1>
            <p class="font-mono text-sm text-ink-secondary">{{ $tool->code }}</p>

            <p class="mt-4 text-sm leading-relaxed text-ink-secondary">
                {{ $tool->description ?: 'Belum ada deskripsi untuk alat ini.' }}
            </p>

            <dl class="mt-6 grid grid-cols-2 gap-4 border-t border-line pt-5 text-sm">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-ink-secondary">Stok Tersedia</dt>
                    <dd class="mt-0.5 font-display text-lg font-semibold text-heading">{{ $tool->stock_available }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-ink-secondary">Kondisi</dt>
                    <dd class="mt-0.5 font-medium text-heading">{{ $tool->condition_label }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-ink-secondary">Lokasi</dt>
                    <dd class="mt-0.5 font-medium text-heading">{{ $tool->location ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-ink-secondary">Status</dt>
                    <dd class="mt-0.5 font-medium text-heading">{{ $tool->status_label }}</dd>
                </div>
            </dl>

            <div class="mt-6 border-t border-line pt-5">
                @if ($tool->is_available && auth()->user()->isPeminjam())
                    <a href="{{ route('peminjam.borrowings.create', ['tool' => $tool->id]) }}" class="btn-primary w-full">
                        <i data-lucide="plus-circle" class="h-4 w-4"></i>
                        Ajukan Peminjaman
                    </a>
                @else
                    <button type="button" class="btn-secondary w-full" disabled>
                        Alat tidak tersedia
                    </button>
                @endif
            </div>
        </div>
    </div>
@endsection