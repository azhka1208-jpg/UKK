@extends('layouts.dashboard')

@section('title', 'Daftar Alat')

@section('content')
    <x-page-header title="Daftar Alat" description="Telusuri alat yang tersedia beserta kategori dan status ketersediaannya." />

    <form method="GET" action="{{ route('peminjam.tools.index') }}" class="mt-5 card card-pad">
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
                    <option value="">Semua</option>
                    <option value="tersedia" @selected(request('status') === 'tersedia')>Tersedia</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary flex-1">Terapkan</button>
                <a href="{{ route('peminjam.tools.index') }}" class="btn-secondary">Reset</a>
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
                                        <a href="{{ route('peminjam.tools.show', $tool) }}" class="font-medium text-heading hover:underline">{{ $tool->name }}</a>
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
                            <td><x-status-badge :label="$tool->is_available ? 'Tersedia' : 'Tidak Tersedia'" :tone="$tool->is_available ? 'badge-success' : 'badge-danger'" dot /></td>
                            <td>
                                <a href="{{ route('peminjam.tools.show', $tool) }}" class="btn-primary btn-sm">Lihat Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state title="Alat tidak ditemukan" description="Tidak ada alat yang tersedia pada filter ini." icon="package" />
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