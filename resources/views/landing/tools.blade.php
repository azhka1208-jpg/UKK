@extends('layouts.public')

@section('title', 'Daftar Alat')

@section('content')
    <section class="border-b border-line bg-surface">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
            <p class="section-eyebrow">Katalog Inventaris</p>
            <h1 class="mt-2 font-display text-2xl font-semibold text-heading sm:text-3xl">Daftar Alat</h1>
            <p class="mt-1 max-w-2xl text-sm text-ink-secondary">
                Telusuri alat yang tersedia beserta kategori, kondisi, dan status ketersediaannya.
            </p>
        </div>
    </section>

    <section class="bg-canvas">
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
            <form method="GET" action="{{ route('landing.tools') }}" class="card card-pad">
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_auto]">
                    <div>
                        <label class="label" for="q">Pencarian</label>
                        <div class="relative">
                            <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-secondary"></i>
                            <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="Nama atau kode alat" class="input pl-9">
                        </div>
                    </div>
                    <div>
                        <label class="label" for="category">Kategori</label>
                        <select id="category" name="category" class="input">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="status">Status</label>
                        <select id="status" name="status" class="input">
                            <option value="">Semua status</option>
                            <option value="tersedia" @selected(request('status') === 'tersedia')>Tersedia</option>
                            <option value="tidak_tersedia" @selected(request('status') === 'tidak_tersedia')>Tidak tersedia</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="btn-primary flex-1">Terapkan</button>
                        <a href="{{ route('landing.tools') }}" class="btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <div class="mt-6 flex items-center justify-between text-sm text-ink-secondary">
                <p>Menampilkan {{ $tools->firstItem() ?? 0 }}&ndash;{{ $tools->lastItem() ?? 0 }} dari {{ $tools->total() }} alat</p>
            </div>

            <div class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($tools as $tool)
                    <x-tool-card :tool="$tool" :href="route('landing.tools.show', $tool)" />
                @empty
                    <div class="sm:col-span-2 lg:col-span-3 card">
                        <x-empty-state title="Alat tidak ditemukan" description="Coba ubah kata kunci atau filter yang digunakan." icon="search-x" />
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $tools->links() }}
            </div>
        </div>
    </section>
@endsection