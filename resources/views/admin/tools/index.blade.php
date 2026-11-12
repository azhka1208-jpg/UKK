@extends('layouts.dashboard')

@section('title', 'Manajemen Alat')

@section('content')
    <x-page-header title="Manajemen Alat" description="Kelola data inventaris, stok, kondisi, dan ketersediaan alat.">
        <x-slot:actions>
            <a href="{{ route('admin.tools.create') }}" class="btn-primary btn-sm">
                <i data-lucide="plus" class="h-4 w-4"></i> Tambah Alat
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" action="{{ route('admin.tools.index') }}" class="mt-6 card card-pad">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_1fr_auto]">
            <div>
                <label class="label" for="q">Pencarian</label>
                <div class="relative">
                    <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-secondary"></i>
                    <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="Nama, kode, atau lokasi" class="input pl-9">
                </div>
            </div>
            <div>
                <label class="label" for="category">Kategori</label>
                <select id="category" name="category" class="input">
                    <option value="">Semua</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="condition">Kondisi</label>
                <select id="condition" name="condition" class="input">
                    <option value="">Semua</option>
                    <option value="baik" @selected(request('condition') === 'baik')>Baik</option>
                    <option value="rusak_ringan" @selected(request('condition') === 'rusak_ringan')>Rusak Ringan</option>
                    <option value="rusak_berat" @selected(request('condition') === 'rusak_berat')>Rusak Berat</option>
                </select>
            </div>
            <div>
                <label class="label" for="status">Ketersediaan</label>
                <select id="status" name="status" class="input">
                    <option value="">Semua</option>
                    <option value="tersedia" @selected(request('status') === 'tersedia')>Tersedia</option>
                    <option value="tidak_tersedia" @selected(request('status') === 'tidak_tersedia')>Tidak Tersedia</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary flex-1">Terapkan</button>
                <a href="{{ route('admin.tools.index') }}" class="btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="mt-5 card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Alat</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Kondisi</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tools as $tool)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <img src="{{ $tool->photo_url }}" alt="Foto {{ $tool->name }}" class="h-10 w-12 rounded-md border border-line object-cover">
                                    <div>
                                        <a href="{{ route('admin.tools.show', $tool) }}" class="font-medium text-heading hover:underline">{{ $tool->name }}</a>
                                        <p class="font-mono text-xs text-ink-secondary">{{ $tool->code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-ink-secondary">{{ $tool->category?->name ?? '-' }}</td>
                            <td>
                                <span class="font-medium text-heading">{{ $tool->stock_available }}</span>
                                <span class="text-xs text-ink-secondary">/ {{ $tool->stock_total }}</span>
                            </td>
                            <td>
                                <x-status-badge :label="$tool->condition_label"
                                    :tone="$tool->condition === 'baik' ? 'badge-success' : ($tool->condition === 'rusak_ringan' ? 'badge-warning' : 'badge-danger')" />
                            </td>
                            <td>
                                <x-status-badge :label="$tool->is_available ? 'Tersedia' : ($tool->status === 'perbaikan' ? 'Perbaikan' : 'Tidak Tersedia')"
                                    :tone="$tool->is_available ? 'badge-success' : ($tool->status === 'perbaikan' ? 'badge-warning' : 'badge-danger')" dot />
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.tools.show', $tool) }}" class="btn-icon" title="Lihat">
                                        <i data-lucide="eye" class="h-4 w-4"></i>
                                    </a>
                                    <a href="{{ route('admin.tools.edit', $tool) }}" class="btn-icon" title="Edit">
                                        <i data-lucide="pencil" class="h-4 w-4"></i>
                                    </a>
                                    <x-confirm-form :action="route('admin.tools.destroy', $tool)" title="Hapus alat"
                                        :message="'Alat ' . $tool->name . ' akan dihapus permanen.'" label="Hapus" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state title="Belum ada data alat" description="Tambahkan alat pertama untuk mulai mengelola inventaris." icon="wrench">
                                    <x-slot:action>
                                        <a href="{{ route('admin.tools.create') }}" class="btn-primary btn-sm">Tambah Alat</a>
                                    </x-slot:action>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-line px-4 py-3">
            {{ $tools->links() }}
        </div>
    </div>
@endsection