@php
    $appName = \App\Models\Setting::get('app_name', 'SISTEMA');
    $appTagline = \App\Models\Setting::get('app_tagline', 'Sistem Peminjaman Alat');

    $user = auth()->user();
    $unreadNotifications = $user->notifications()->where('is_read', false)->count();

    $menus = [
        'admin' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'layout-dashboard', 'active' => 'admin.dashboard'],
            ['label' => 'Manajemen Pengguna', 'route' => 'admin.users.index', 'icon' => 'users', 'active' => 'admin.users.*'],
            ['label' => 'Manajemen Alat', 'route' => 'admin.tools.index', 'icon' => 'wrench', 'active' => 'admin.tools.*'],
            ['label' => 'Kategori Alat', 'route' => 'admin.categories.index', 'icon' => 'tags', 'active' => 'admin.categories.*'],
            ['label' => 'Data Peminjaman', 'route' => 'admin.borrowings.index', 'icon' => 'clipboard-list', 'active' => 'admin.borrowings.*'],
            ['label' => 'Data Pengembalian', 'route' => 'admin.returns.index', 'icon' => 'undo-2', 'active' => 'admin.returns.*'],
            ['label' => 'Log Aktivitas', 'route' => 'admin.logs.index', 'icon' => 'history', 'active' => 'admin.logs.*'],
            ['label' => 'Pengaturan', 'route' => 'admin.settings.edit', 'icon' => 'settings', 'active' => 'admin.settings.*'],
        ],
        'petugas' => [
            ['label' => 'Dashboard', 'route' => 'petugas.dashboard', 'icon' => 'layout-dashboard', 'active' => 'petugas.dashboard'],
            ['label' => 'Persetujuan Peminjaman', 'route' => 'petugas.approvals.index', 'icon' => 'check-circle-2', 'active' => 'petugas.approvals.*'],
            ['label' => 'Monitoring Pengembalian', 'route' => 'petugas.monitoring.index', 'icon' => 'clock', 'active' => 'petugas.monitoring.*'],
            ['label' => 'Data Pengembalian', 'route' => 'petugas.returns.index', 'icon' => 'undo-2', 'active' => 'petugas.returns.*'],
            ['label' => 'Data Peminjaman', 'route' => 'petugas.borrowings.index', 'icon' => 'clipboard-list', 'active' => 'petugas.borrowings.*'],
            ['label' => 'Laporan', 'route' => 'petugas.reports.index', 'icon' => 'file-text', 'active' => 'petugas.reports.*'],
        ],
        'peminjam' => [
            ['label' => 'Dashboard', 'route' => 'peminjam.dashboard', 'icon' => 'layout-dashboard', 'active' => 'peminjam.dashboard'],
            ['label' => 'Daftar Alat', 'route' => 'peminjam.tools.index', 'icon' => 'package-search', 'active' => 'peminjam.tools.*'],
            ['label' => 'Ajukan Peminjaman', 'route' => 'peminjam.borrowings.create', 'icon' => 'plus-circle', 'active' => 'peminjam.borrowings.create'],
            ['label' => 'Peminjaman Saya', 'route' => 'peminjam.borrowings.index', 'icon' => 'clipboard-list', 'active' => 'peminjam.borrowings.index'],
            ['label' => 'Pengembalian', 'route' => 'peminjam.returns.index', 'icon' => 'undo-2', 'active' => 'peminjam.returns.*'],
            ['label' => 'Riwayat', 'route' => 'peminjam.borrowings.history', 'icon' => 'history', 'active' => 'peminjam.borrowings.history'],
        ],
    ][$user->role] ?? [];
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ $appName }}</title>

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
<div x-data="{ sidebar: false }" class="min-h-screen">
    <div x-show="sidebar" x-cloak x-transition.opacity class="fixed inset-0 z-30 bg-ink/45 lg:hidden" x-on:click="sidebar = false"></div>

    <aside
        class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-navy text-white transition-transform duration-200 lg:translate-x-0"
        x-bind:class="sidebar ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex h-16 items-center gap-2.5 border-b border-white/10 px-5">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/10">
                <i data-lucide="boxes" class="h-5 w-5"></i>
            </span>
            <span class="leading-tight">
                <span class="block font-display text-base font-bold">{{ $appName }}</span>
                <span class="block text-[11px] text-white/60">{{ $user->role_label }}</span>
            </span>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4">
            <p class="sidebar-group-label">Menu Utama</p>
            <div class="space-y-1">
                @foreach ($menus as $menu)
                    <a href="{{ route($menu['route']) }}"
                       class="sidebar-link {{ request()->routeIs($menu['active']) ? 'sidebar-link-active' : '' }}">
                        <i data-lucide="{{ $menu['icon'] }}" class="h-[18px] w-[18px] shrink-0"></i>
                        <span class="truncate">{{ $menu['label'] }}</span>
                    </a>
                @endforeach
            </div>

            <p class="sidebar-group-label">Akun</p>
            <div class="space-y-1">
                <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'sidebar-link-active' : '' }}">
                    <i data-lucide="user-round" class="h-[18px] w-[18px] shrink-0"></i>
                    <span>Profil Saya</span>
                </a>
                <a href="{{ route('notifications.index') }}" class="sidebar-link {{ request()->routeIs('notifications.*') ? 'sidebar-link-active' : '' }}">
                    <i data-lucide="bell" class="h-[18px] w-[18px] shrink-0"></i>
                    <span>Notifikasi</span>
                    @if ($unreadNotifications)
                        <span class="ml-auto rounded-full bg-danger px-1.5 py-0.5 text-[10px] font-semibold text-white">{{ $unreadNotifications }}</span>
                    @endif
                </a>
            </div>
        </nav>

        <div class="border-t border-white/10 p-3">
            <div class="flex items-center gap-3 rounded-lg px-2 py-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-xs font-semibold">{{ $user->initials() }}</span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium">{{ $user->name }}</p>
                    <p class="truncate text-[11px] text-white/55">{{ $user->email }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="rounded-md p-1.5 text-white/60 transition-colors hover:bg-white/10 hover:text-white" title="Keluar">
                        <i data-lucide="log-out" class="h-4 w-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="flex min-h-screen flex-col lg:pl-64">
        <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-line bg-surface/95 px-4 backdrop-blur sm:px-6">
            <button type="button" class="btn-icon lg:hidden" x-on:click="sidebar = true" aria-label="Buka menu">
                <i data-lucide="menu" class="h-5 w-5"></i>
            </button>

            <div class="hidden sm:block">
                <p class="text-xs text-ink-secondary">{{ now()->translatedFormat('l, d F Y') }}</p>
                <p class="text-sm font-medium text-heading">Selamat datang, {{ \Illuminate\Support\Str::words($user->name, 2, '') }}</p>
            </div>

            <div class="ml-auto flex items-center gap-2">
                <button type="button" data-theme-toggle class="btn-icon" title="Ganti tema" aria-label="Ganti tema">
                    <span class="theme-sun"><i data-lucide="sun" class="h-5 w-5"></i></span>
                    <span class="theme-moon"><i data-lucide="moon" class="h-5 w-5"></i></span>
                </button>
                <a href="{{ route('notifications.index') }}" class="relative btn-icon" title="Notifikasi">
                    <i data-lucide="bell" class="h-5 w-5"></i>
                    @if ($unreadNotifications)
                        <span class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-danger px-1 text-[10px] font-semibold text-white">{{ $unreadNotifications }}</span>
                    @endif
                </a>

                <div x-data="{ open: false }" class="relative" x-on:click.outside="open = false">
                    <button type="button" x-on:click="open = !open" class="flex items-center gap-2 rounded-lg border border-line bg-surface px-2 py-1.5 transition-colors hover:border-navy/40">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-navy text-[11px] font-semibold text-white">{{ $user->initials() }}</span>
                        <span class="hidden text-left sm:block">
                            <span class="block max-w-[140px] truncate text-xs font-semibold text-heading">{{ $user->name }}</span>
                            <span class="block text-[10px] text-ink-secondary">{{ $user->role_label }}</span>
                        </span>
                        <i data-lucide="chevron-down" class="hidden h-4 w-4 text-ink-secondary sm:block"></i>
                    </button>

                    <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-52 overflow-hidden rounded-lg border border-line bg-surface py-1 shadow-pop">
                        <div class="border-b border-line px-4 py-2.5">
                            <p class="text-sm font-medium text-heading">{{ $user->name }}</p>
                            <p class="text-xs text-ink-secondary">{{ $user->username }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-ink hover:bg-accent">
                            <i data-lucide="user-round" class="h-4 w-4"></i> Profil Saya
                        </a>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-ink hover:bg-accent">
                            <i data-lucide="layout-dashboard" class="h-4 w-4"></i> Dashboard
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="border-t border-line">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-danger hover:bg-danger/5">
                                <i data-lucide="log-out" class="h-4 w-4"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6 lg:py-8">
            <x-alert />
            @yield('content')
        </main>

        <footer class="border-t border-line px-4 py-4 text-center text-xs text-ink-secondary sm:px-6">
            {{ $appName }} &middot; {{ $appTagline }} &middot; &copy; {{ now()->year }}
        </footer>
    </div>
</div>

@stack('scripts')
</body>
</html>