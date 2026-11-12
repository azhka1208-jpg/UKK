@extends('layouts.dashboard')

@section('title', 'Kategori Alat')

@section('content')
    <x-page-header title="Kategori Alat" description="Kelompokkan alat berdasarkan kategori inventaris.">
        <x-slot:actions>
            <a href="{{ route('admin.categories.create') }}" class="btn-primary btn-sm">
                <i data-lucide="plus" class="h-4 w-4"></i> Tambah Kategori
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" action="{{ route('admin.categories.index') }}" class="mt-6 flex flex-col gap-2 sm:flex-row">
        <div class="relative flex-1">
            <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-secondary"></i>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari kategori..." class="input pl-9">
        </div>
        <button type="submit" class="btn-primary">Cari</button>
        <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Reset</a>
    </form>

    <div class="mt-5 card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Alat</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="font-medium text-heading">{{ $category->name }}</td>
                            <td class="max-w-md text-ink-secondary">{{ \Illuminate\Support\Str::limit($category->description, 90) ?: '-' }}</td>
                            <td><span class="badge-neutral">{{ $category->tools_count }} alat</span></td>
                            <td>
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn-icon" title="Edit">
                                        <i data-lucide="pencil" class="h-4 w-4"></i>
                                    </a>
                                    <x-confirm-form :action="route('admin.categories.destroy', $category)" title="Hapus kategori"
                                        :message="'Kategori ' . $category->name . ' akan dihapus permanen.'" label="Hapus" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-empty-state title="Belum ada kategori" description="Tambahkan kategori untuk mengelompokkan alat." icon="tags" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-line px-4 py-3">
            {{ $categories->links() }}
        </div>
    </div>
@endsection