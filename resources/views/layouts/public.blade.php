@php
    $appName = \App\Models\Setting::get('app_name', 'SISTEMA');
    $appTagline = \App\Models\Setting::get('app_tagline', 'Sistem Peminjaman Alat');
    $contactEmail = \App\Models\Setting::get('contact_email');
    $contactPhone = \App\Models\Setting::get('contact_phone');
    $contactAddress = \App\Models\Setting::get('contact_address');

    $authUser = auth()->user();
    $dashboardRoute = null;
    if ($authUser) {
        $dashboardRoute = $authUser->isAdmin() ? 'admin.dashboard' : ($authUser->isPetugas() ? 'petugas.dashboard' : 'peminjam.dashboard');
    }

    $navLinks = [
        ['label' => 'Beranda', 'route' => 'home', 'active' => 'home'],
        ['label' => 'Daftar Alat', 'route' => 'landing.tools', 'active' => 'landing.tools*'],
        ['label' => 'Panduan', 'route' => 'landing.guide', 'active' => 'landing.guide'],
        ['label' => 'Tentang', 'route' => 'landing.about', 'active' => 'landing.about'],
    ];
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $appName) · {{ $appName }}</title>
    <meta name="description" content="@yield('meta_description', \App\Models\Setting::get('app_description', $appTagline))">

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
<div x-data="{ open: false }" class="min-h-screen flex flex-col">
        <header class="sticky top-0 z-40 border-b border-line bg-surface/95 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-navy text-white">
                        <i data-lucide="boxes" class="h-5 w-5"></i>
                    </span>
                    <span class="leading-tight">
                        <span class="block font-display text-base font-bold tracking-tight text-heading">{{ $appName }}</span>
                        <span class="block text-[11px] font-medium text-ink-secondary">{{ $appTagline }}</span>
                    </span>
                </a>

                <nav class="hidden items-center gap-1 lg:flex">
                    @foreach ($navLinks as $link)
                        <a href="{{ route($link['route']) }}"
                           class="rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs($link['active']) ? 'text-heading' : 'text-ink-secondary hover:text-heading' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="hidden items-center gap-2 lg:flex">
                    <button type="button" data-theme-toggle class="btn-icon" title="Ganti tema" aria-label="Ganti tema">
                        <span class="theme-sun"><i data-lucide="sun" class="h-5 w-5"></i></span>
                        <span class="theme-moon"><i data-lucide="moon" class="h-5 w-5"></i></span>
                    </button>
                    @auth
                        <a href="{{ route($dashboardRoute) }}" class="btn-primary btn-sm">
                            <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                            Dashboard
                        </a>
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" class="btn-primary btn-sm">
                            <i data-lucide="log-in" class="h-4 w-4"></i>
                            Login
                        </a>
                    @endguest
                </div>

                <button type="button" data-theme-toggle class="btn-icon lg:hidden" title="Ganti tema" aria-label="Ganti tema">
                    <span class="theme-sun"><i data-lucide="sun" class="h-5 w-5"></i></span>
                    <span class="theme-moon"><i data-lucide="moon" class="h-5 w-5"></i></span>
                </button>

                <button type="button" @click="open = !open" class="btn-icon lg:hidden" aria-label="Buka menu">
                    <i data-lucide="menu" class="h-5 w-5" x-show="!open"></i>
                    <i data-lucide="x" class="h-5 w-5" x-show="open" x-cloak></i>
                </button>
            </div>

            <div x-show="open" x-cloak x-transition.opacity class="border-t border-line bg-surface lg:hidden">
                <nav class="mx-auto flex max-w-6xl flex-col gap-1 px-4 py-3 sm:px-6">
                    @foreach ($navLinks as $link)
                        <a href="{{ route($link['route']) }}"
                           class="rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs($link['active']) ? 'bg-accent text-heading' : 'text-ink-secondary hover:bg-accent/60' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                    <div class="mt-2 border-t border-line pt-3">
                        @auth
                            <a href="{{ route($dashboardRoute) }}" class="btn-primary w-full">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary w-full">Login</a>
                        @endauth
                    </div>
                </nav>
            </div>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>

        <footer class="mt-auto bg-navy text-white">
            <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
                <div class="grid gap-10 md:grid-cols-[1.4fr_1fr_1fr]">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 text-white">
                                <i data-lucide="boxes" class="h-5 w-5"></i>
                            </span>
                            <span class="font-display text-lg font-bold">{{ $appName }}</span>
                        </div>
                        <p class="mt-4 max-w-sm text-sm leading-relaxed text-white/70">
                            {{ \App\Models\Setting::get('app_description', $appTagline) }}
                        </p>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-white">Navigasi</h4>
                        <ul class="mt-4 space-y-2.5 text-sm text-white/70">
                            @foreach ($navLinks as $link)
                                <li><a href="{{ route($link['route']) }}" class="transition-colors hover:text-white">{{ $link['label'] }}</a></li>
                            @endforeach
                            <li><a href="{{ route('login') }}" class="transition-colors hover:text-white">Login</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-white">Kontak</h4>
                        <ul class="mt-4 space-y-2.5 text-sm text-white/70">
                            @if ($contactAddress)
                                <li class="flex gap-2.5"><i data-lucide="map-pin" class="mt-0.5 h-4 w-4 shrink-0"></i><span>{{ $contactAddress }}</span></li>
                            @endif
                            @if ($contactPhone)
                                <li class="flex gap-2.5"><i data-lucide="phone" class="mt-0.5 h-4 w-4 shrink-0"></i><span>{{ $contactPhone }}</span></li>
                            @endif
                            @if ($contactEmail)
                                <li class="flex gap-2.5"><i data-lucide="mail" class="mt-0.5 h-4 w-4 shrink-0"></i><span>{{ $contactEmail }}</span></li>
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="mt-10 flex flex-col gap-2 border-t border-white/10 pt-6 text-xs text-white/50 sm:flex-row sm:items-center sm:justify-between">
                    <p>&copy; {{ now()->year }} {{ $appName }}. Seluruh hak cipta dilindungi.</p>
                    <p>Sistem Peminjaman Alat &middot; UKK Rekayasa Perangkat Lunak</p>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>