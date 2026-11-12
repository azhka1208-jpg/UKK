@extends('layouts.dashboard')

@section('title', 'Manajemen Pengguna')

@section('content')
    <x-page-header title="Manajemen Pengguna" description="Kelola akun admin, petugas, dan peminjam.">
        <x-slot:actions>
            <a href="{{ route('admin.users.create') }}" class="btn-primary btn-sm">
                <i data-lucide="user-plus" class="h-4 w-4"></i> Tambah Pengguna
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-3 sm:grid-cols-4">
        @foreach ([
            ['Semua', $counts['all'], ''],
            ['Admin', $counts['admin'], 'admin'],
            ['Petugas', $counts['petugas'], 'petugas'],
            ['Peminjam', $counts['peminjam'], 'peminjam'],
        ] as $tab)
            <a href="{{ route('admin.users.index', $tab[2] ? ['role' => $tab[2]] : []) }}"
               class="card flex items-center justify-between px-4 py-3 transition-colors hover:border-navy/30 {{ request('role', '') === $tab[2] ? 'border-navy/40' : '' }}">
                <span class="text-sm font-medium text-heading">{{ $tab[0] }}</span>
                <span class="badge-neutral">{{ $tab[1] }}</span>
            </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="mt-5 card card-pad">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_auto]">
            <div>
                <label class="label" for="q">Pencarian</label>
                <div class="relative">
                    <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-secondary"></i>
                    <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="Nama, username, atau email" class="input pl-9">
                </div>
            </div>
            <div>
                <label class="label" for="role">Role</label>
                <select id="role" name="role" class="input">
                    <option value="">Semua role</option>
                    <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                    <option value="petugas" @selected(request('role') === 'petugas')>Petugas</option>
                    <option value="peminjam" @selected(request('role') === 'peminjam')>Peminjam</option>
                </select>
            </div>
            <div>
                <label class="label" for="status">Status</label>
                <select id="status" name="status" class="input">
                    <option value="">Semua status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary flex-1">Terapkan</button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="mt-5 card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kontak</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-accent text-xs font-semibold text-heading">{{ $user->initials() }}</span>
                                    <div>
                                        <p class="font-medium text-heading">{{ $user->name }}</p>
                                        <p class="text-xs text-ink-secondary">{{ $user->username }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="text-sm text-ink">{{ $user->email }}</p>
                                <p class="text-xs text-ink-secondary">{{ $user->phone ?: 'Tidak ada telepon' }}</p>
                            </td>
                            <td>
                                <x-status-badge :label="$user->role_label"
                                    :tone="$user->role === 'admin' ? 'badge-info' : ($user->role === 'petugas' ? 'badge-warning' : 'badge-neutral')" />
                            </td>
                            <td>
                                <x-status-badge :label="$user->is_active ? 'Aktif' : 'Nonaktif'"
                                    :tone="$user->is_active ? 'badge-success' : 'badge-danger'" dot />
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn-icon" title="Lihat">
                                        <i data-lucide="eye" class="h-4 w-4"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn-icon" title="Edit">
                                        <i data-lucide="pencil" class="h-4 w-4"></i>
                                    </a>
                                    @if ($user->id !== auth()->id())
                                        <x-confirm-form :action="route('admin.users.destroy', $user)" title="Hapus pengguna"
                                            :message="'Pengguna ' . $user->name . ' akan dihapus permanen.'" label="Hapus" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-empty-state title="Pengguna tidak ditemukan" description="Coba ubah kata kunci atau filter." icon="users" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-line px-4 py-3">
            {{ $users->links() }}
        </div>
    </div>
@endsection