@extends('layouts.dashboard')

@section('title', 'Detail Pengguna')

@section('content')
    <x-page-header title="Detail Pengguna" description="Informasi akun dan riwayat peminjaman.">
        <x-slot:actions>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn-secondary btn-sm">
                <i data-lucide="pencil" class="h-4 w-4"></i> Edit
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary btn-sm">Kembali</a>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-5 lg:grid-cols-[1fr_2fr]">
        <div class="card card-pad">
            <div class="flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-navy text-sm font-semibold text-white">{{ $user->initials() }}</span>
                <div>
                    <p class="font-display text-base font-semibold text-heading">{{ $user->name }}</p>
                    <p class="text-xs text-ink-secondary">{{ $user->username }}</p>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <x-status-badge :label="$user->role_label"
                    :tone="$user->role === 'admin' ? 'badge-info' : ($user->role === 'petugas' ? 'badge-warning' : 'badge-neutral')" />
                <x-status-badge :label="$user->is_active ? 'Aktif' : 'Nonaktif'" :tone="$user->is_active ? 'badge-success' : 'badge-danger'" dot />
            </div>

            <dl class="mt-5 space-y-3 border-t border-line pt-4 text-sm">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-ink-secondary">Email</dt>
                    <dd class="mt-0.5 text-ink">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-ink-secondary">Telepon</dt>
                    <dd class="mt-0.5 text-ink">{{ $user->phone ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-ink-secondary">Alamat</dt>
                    <dd class="mt-0.5 text-ink">{{ $user->address ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-ink-secondary">Terdaftar</dt>
                    <dd class="mt-0.5 text-ink">{{ $user->created_at?->translatedFormat('d M Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-ink-secondary">Total Peminjaman</dt>
                    <dd class="mt-0.5 text-ink">{{ $user->borrowings_count }}</dd>
                </div>
            </dl>
        </div>

        <div class="card">
            <div class="border-b border-line px-5 py-4">
                <h2 class="font-display text-base font-semibold text-heading">Peminjaman Terakhir</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Item</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($borrowings as $borrowing)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.borrowings.show', $borrowing) }}" class="font-medium text-heading hover:underline">{{ $borrowing->code }}</a>
                                </td>
                                <td class="whitespace-nowrap text-ink-secondary">{{ $borrowing->borrow_date?->format('d/m/Y') }}</td>
                                <td>{{ $borrowing->total_items }}</td>
                                <td><x-status-badge :label="$borrowing->status_label" :tone="$borrowing->status_badge" dot /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-empty-state title="Belum ada peminjaman" description="Pengguna ini belum memiliki riwayat peminjaman." icon="clipboard-list" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection