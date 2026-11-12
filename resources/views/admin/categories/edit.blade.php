@extends('layouts.dashboard')

@section('title', 'Edit Kategori')

@section('content')
    <x-page-header title="Edit Kategori" :description="'Perbarui kategori ' . $category->name . '.'">
        <x-slot:actions>
            <a href="{{ route('admin.categories.index') }}" class="btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="mt-6 max-w-2xl">
        @csrf
        @method('PUT')
        <div class="card card-pad">
            @include('admin.categories._form', ['category' => $category])
        </div>
        <div class="mt-5 flex justify-end gap-2">
            <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">
                <i data-lucide="save" class="h-4 w-4"></i> Simpan Perubahan
            </button>
        </div>
    </form>
@endsection