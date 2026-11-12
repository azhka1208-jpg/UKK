@extends('layouts.public')

@section('title', 'Beranda')

@section('content')
    <section class="border-b border-line bg-surface">
        <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 py-14 sm:px-6 lg:grid-cols-2 lg:py-20">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full border border-line bg-canvas px-3 py-1 text-xs font-medium text-heading">
                    <i data-lucide="shield-check" class="h-3.5 w-3.5"></i>
                    Sistem Inventaris Terpadu
                </span>

                <h1 class="mt-5 font-display text-3xl font-bold leading-tight text-heading sm:text-4xl lg:text-[2.6rem]">
                    Kelola Peminjaman Alat dengan Lebih Mudah dan Terorganisir.
                </h1>

                <p class="mt-4 max-w-xl text-base leading-relaxed text-ink-secondary">
                    Platform digital untuk membantu pengelolaan inventaris, peminjaman, dan pengembalian alat secara terstruktur, transparan, dan efisien.
                </p>

                <div class="mt-7 flex flex-wrap items-center gap-3">
                    <a href="{{ route('landing.tools') }}" class="btn-primary">
                        <i data-lucide="package-search" class="h-4 w-4"></i>
                        Lihat Daftar Alat
                    </a>
                    <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="btn-secondary">
                        <i data-lucide="log-in" class="h-4 w-4"></i>
                        {{ auth()->check() ? 'Buka Dashboard' : 'Masuk ke Sistem' }}
                    </a>
                </div>

                <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-ink-secondary">
                    <span class="flex items-center gap-2"><i data-lucide="check-circle-2" class="h-4 w-4 text-success"></i> Data stok aktual</span>
                    <span class="flex items-center gap-2"><i data-lucide="check-circle-2" class="h-4 w-4 text-success"></i> Persetujuan terverifikasi</span>
                    <span class="flex items-center gap-2"><i data-lucide="check-circle-2" class="h-4 w-4 text-success"></i> Riwayat transparan</span>
                </div>
            </div>

            <div class="relative">
                <div class="overflow-hidden rounded-2xl border border-line bg-accent shadow-card">
                    <img src="{{ asset('images/hero-inventory.svg') }}" alt="Ilustrasi inventaris alat" class="w-full">
                </div>
                <div class="absolute -bottom-5 left-5 hidden items-center gap-3 rounded-xl border border-line bg-surface px-4 py-3 shadow-pop sm:flex">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-success/10 text-success">
                        <i data-lucide="package-check" class="h-5 w-5"></i>
                    </span>
                    <div>
                        <p class="text-xs text-ink-secondary">Alat tersedia</p>
                        <p class="font-display text-sm font-semibold text-heading">{{ $stats['available_tools'] }} jenis alat</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-canvas">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-stat-card label="Total Alat" :value="$stats['total_tools']" icon="wrench" tone="navy" hint="Jenis alat terdata" />
                <x-stat-card label="Kategori Alat" :value="$stats['total_categories']" icon="tags" tone="slate" hint="Kelompok inventaris" />
                <x-stat-card label="Alat Tersedia" :value="$stats['available_tools']" icon="package-check" tone="success" hint="Siap dipinjam" />
                <x-stat-card label="Total Peminjaman" :value="$stats['total_borrowings']" icon="clipboard-list" tone="warning" hint="Seluruh transaksi" />
            </div>
        </div>
    </section>

    <section class="bg-surface">
        <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="section-eyebrow">Katalog Inventaris</p>
                    <h2 class="mt-2 font-display text-2xl font-semibold text-heading">Alat yang tersedia</h2>
                    <p class="mt-1 text-sm text-ink-secondary">Lihat alat, kategori, kondisi, dan status ketersediaannya.</p>
                </div>
                <form action="{{ route('landing.tools') }}" method="GET" class="flex w-full max-w-sm gap-2">
                    <div class="relative flex-1">
                        <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-secondary"></i>
                        <input type="search" name="q" placeholder="Cari alat..." class="input pl-9">
                    </div>
                    <button type="submit" class="btn-secondary">Cari</button>
                </form>
            </div>

            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($tools as $tool)
                    <x-tool-card :tool="$tool" :href="route('landing.tools.show', $tool)" />
                @empty
                    <div class="sm:col-span-2 lg:col-span-3">
                        <x-empty-state title="Belum ada data alat" description="Data alat belum tersedia. Silakan hubungi petugas inventaris." icon="package" />
                    </div>
                @endforelse
            </div>

            <div class="mt-8 flex justify-center">
                <a href="{{ route('landing.tools') }}" class="btn-secondary">
                    Lihat semua alat
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="border-y border-line bg-canvas">
        <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
            <div class="max-w-2xl">
                <p class="section-eyebrow">Panduan Peminjaman</p>
                <h2 class="mt-2 font-display text-2xl font-semibold text-heading">Alur peminjaman yang jelas</h2>
                <p class="mt-1 text-sm text-ink-secondary">Ikuti langkah berikut agar pengajuan peminjaman diproses dengan cepat.</p>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['login', 'Masuk ke sistem', 'Gunakan akun yang sudah terdaftar.'],
                    ['package-search', 'Pilih alat tersedia', 'Periksa stok dan kondisi alat.'],
                    ['file-pen', 'Isi formulir peminjaman', 'Lengkapi tanggal dan keperluan.'],
                    ['hourglass', 'Tunggu persetujuan petugas', 'Pengajuan diperiksa oleh petugas.'],
                    ['package-open', 'Ambil alat', 'Ambil alat sesuai jadwal yang disetujui.'],
                    ['undo-2', 'Kembalikan sesuai jadwal', 'Kembalikan alat sebelum batas waktu.'],
                ] as $index => $step)
                    <div class="card card-pad">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-accent text-heading">
                                <i data-lucide="{{ $step[0] }}" class="h-[18px] w-[18px]"></i>
                            </span>
                            <span class="ml-auto text-xs font-semibold text-ink-secondary">Langkah {{ $index + 1 }}</span>
                        </div>
                        <p class="mt-3 font-display text-sm font-semibold text-heading">{{ $step[1] }}</p>
                        <p class="mt-1 text-sm text-ink-secondary">{{ $step[2] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if ($announcement['active'] && $announcement['title'])
        <section class="bg-surface">
            <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
                <div class="rounded-xl border border-navy/15 bg-accent p-6 sm:p-8">
                    <div class="flex flex-col gap-4 sm:flex-row">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-navy text-white">
                            <i data-lucide="megaphone" class="h-5 w-5"></i>
                        </span>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-heading/70">Pengumuman</p>
                            <h3 class="mt-1 font-display text-lg font-semibold text-heading">{{ $announcement['title'] }}</h3>
                            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-ink-secondary">{{ $announcement['content'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection