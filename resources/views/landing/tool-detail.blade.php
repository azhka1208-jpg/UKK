@extends('layouts.public')

@section('title', $tool->name)

@section('content')
    <section class="border-b border-line bg-surface">
        <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6">
            <nav class="flex items-center gap-2 text-sm text-ink-secondary">
                <a href="{{ route('home') }}" class="hover:text-heading">Beranda</a>
                <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                <a href="{{ route('landing.tools') }}" class="hover:text-heading">Daftar Alat</a>
                <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                <span class="text-heading">{{ $tool->name }}</span>
            </nav>
        </div>
    </section>

    <section class="bg-canvas">
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
            <div class="grid gap-6 lg:grid-cols-[1.1fr_1fr]">
                <div class="card overflow-hidden">
                    <img src="{{ $tool->photo_url }}" alt="Foto {{ $tool->name }}" class="aspect-[4/3] w-full object-cover">
                </div>

                <div class="card card-pad">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-accent px-3 py-1 text-xs font-medium text-heading">{{ $tool->category?->name ?? '-' }}</span>
                        <x-status-badge :label="$tool->is_available ? 'Tersedia' : ($tool->status === 'perbaikan' ? 'Perbaikan' : 'Tidak Tersedia')"
                            :tone="$tool->is_available ? 'badge-success' : ($tool->status === 'perbaikan' ? 'badge-warning' : 'badge-danger')" dot />
                    </div>

                    <h1 class="mt-3 font-display text-2xl font-semibold text-heading">{{ $tool->name }}</h1>
                    <p class="mt-1 font-mono text-sm text-ink-secondary">{{ $tool->code }}</p>

                    <p class="mt-4 text-sm leading-relaxed text-ink-secondary">
                        {{ $tool->description ?: 'Belum ada deskripsi untuk alat ini.' }}
                    </p>

                    <dl class="mt-6 grid grid-cols-2 gap-4 border-t border-line pt-5 text-sm">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Stok tersedia</dt>
                            <dd class="mt-1 font-display text-lg font-semibold text-heading">{{ $tool->stock_available }} <span class="text-sm font-normal text-ink-secondary">/ {{ $tool->stock_total }} unit</span></dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Kondisi</dt>
                            <dd class="mt-1 font-medium text-heading">{{ $tool->condition_label }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Lokasi penyimpanan</dt>
                            <dd class="mt-1 font-medium text-heading">{{ $tool->location ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Status</dt>
                            <dd class="mt-1 font-medium text-heading">{{ $tool->status_label }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 border-t border-line pt-5">
                        @auth
                            @if (auth()->user()->isPeminjam())
                                @if ($tool->is_available)
                                    <a href="{{ route('peminjam.borrowings.create', ['tool' => $tool->id]) }}" class="btn-primary w-full sm:w-auto">
                                        <i data-lucide="plus-circle" class="h-4 w-4"></i>
                                        Ajukan Peminjaman
                                    </a>
                                @else
                                    <button type="button" class="btn-secondary w-full sm:w-auto" disabled>Alat tidak tersedia</button>
                                @endif
                            @else
                                <a href="{{ route('dashboard') }}" class="btn-secondary w-full sm:w-auto">Buka Dashboard</a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn-primary w-full sm:w-auto">
                                <i data-lucide="log-in" class="h-4 w-4"></i>
                                Masuk untuk meminjam
                            </a>
                            <p class="mt-2 text-xs text-ink-secondary">Anda perlu masuk sebagai peminjam untuk mengajukan peminjaman.</p>
                        @endauth
                    </div>
                </div>
            </div>

            @if ($related->isNotEmpty())
                <div class="mt-10">
                    <h2 class="font-display text-lg font-semibold text-heading">Alat lain di kategori ini</h2>
                    <div class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($related as $item)
                            <x-tool-card :tool="$item" :href="route('landing.tools.show', $item)" />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection