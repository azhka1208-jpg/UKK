@extends('layouts.dashboard')

@section('title', 'Tambah Kategori')

@section('content')
    <x-page-header title="Tambah Kategori" description="Buat kategori baru untuk pengelompokan alat.">
        <x-slot:actions>
            <a href="{{ route('admin.categories.index') }}" class="btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <form action="{{ route('admin.categories.store') }}" method="POST" class="mt-6 max-w-2xl">
        @csrf
        <div class="card card-pad">
            @include('admin.categories._form', ['category' => null])
        </div>
        <div class="mt-5 flex justify-end gap-2">
            <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">
                <i data-lucide="save" class="h-4 w-4"></i> Simpan Kategori
            </button>
        </div>
    </form>
@endsection