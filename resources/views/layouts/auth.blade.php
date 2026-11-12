@php
    $appName = \App\Models\Setting::get('app_name', 'SISTEMA');
    $appTagline = \App\Models\Setting::get('app_tagline', 'Sistem Peminjaman Alat');
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masuk') · {{ $appName }}</title>

    <script>
        (function () {
            try {
                var t = localStorage.getItem('theme');
                if (!t) {
                    t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }
                if (t === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body class="bg-canvas text-ink antialiased">
<x-preloader />
<div class="fixed right-4 top-4 z-50">
    <button type="button" data-theme-toggle class="btn-icon" title="Ganti tema" aria-label="Ganti tema">
        <span class="theme-sun"><i data-lucide="sun" class="h-5 w-5"></i></span>
        <span class="theme-moon"><i data-lucide="moon" class="h-5 w-5"></i></span>
    </button>
</div>
<div class="grid min-h-screen lg:grid-cols-2">
    <aside class="relative hidden flex-col justify-between overflow-hidden bg-navy p-10 text-white lg:flex">
        <div class="relative z-10 flex items-center gap-2.5">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/10">
                <i data-lucide="boxes" class="h-5 w-5"></i>
            </span>
            <div class="leading-tight">
                <p class="font-display text-base font-bold">{{ $appName }}</p>
                <p class="text-[11px] text-white/60">{{ $appTagline }}</p>
            </div>
        </div>

        <div class="relative z-10 max-w-md">
            <h1 class="font-display text-3xl font-bold leading-tight text-white">Kelola peminjaman alat secara terstruktur.</h1>
            <p class="mt-4 text-sm leading-relaxed text-white/70">
                Satu sistem untuk inventaris, pengajuan peminjaman, persetujuan petugas, dan pengembalian alat.
            </p>

            <ul class="mt-8 space-y-4 text-sm">
                @foreach ([
                    ['package-check', 'Pantau stok alat secara aktual.'],
                    ['shield-check', 'Hak akses terpisah untuk setiap peran.'],
                    ['history', 'Riwayat peminjaman tersimpan rapi.'],
                ] as $item)
                    <li class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10">
                            <i data-lucide="{{ $item[0] }}" class="h-4 w-4"></i>
                        </span>
                        <span class="text-white/80">{{ $item[1] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <p class="relative z-10 text-xs text-white/50">&copy; {{ now()->year }} {{ $appName }}. UKK Rekayasa Perangkat Lunak.</p>

        <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/5"></div>
        <div class="pointer-events-none absolute -bottom-32 -right-10 h-80 w-80 rounded-full bg-white/5"></div>
    </aside>

    <main class="flex items-center justify-center px-4 py-10 sm:px-6">
        <div class="w-full max-w-md">
            <div class="mb-8 flex items-center gap-2.5 lg:hidden">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-navy text-white">
                    <i data-lucide="boxes" class="h-5 w-5"></i>
                </span>
                <div class="leading-tight">
                    <p class="font-display text-base font-bold text-heading">{{ $appName }}</p>
                    <p class="text-[11px] text-ink-secondary">{{ $appTagline }}</p>
                </div>
            </div>

            <x-alert />

            @yield('content')

            <p class="mt-8 text-center text-xs text-ink-secondary">
                &copy; {{ now()->year }} {{ $appName }} &middot; <a href="{{ route('home') }}" class="link">Kembali ke beranda</a>
            </p>
        </div>
    </main>
</div>
</body>
</html>