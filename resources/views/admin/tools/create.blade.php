@extends('layouts.dashboard')

@section('title', 'Tambah Alat')

@section('content')
    <x-page-header title="Tambah Alat" description="Daftarkan alat baru ke dalam inventaris.">
        <x-slot:actions>
            <a href="{{ route('admin.tools.index') }}" class="btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <form action="{{ route('admin.tools.store') }}" method="POST" enctype="multipart/form-data" class="mt-6">
        @csrf
        <div class="card card-pad">
            @include('admin.tools._form', ['tool' => null, 'categories' => $categories])
        </div>
        <div class="mt-5 flex justify-end gap-2">
            <a href="{{ route('admin.tools.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">
                <i data-lucide="save" class="h-4 w-4"></i> Simpan Alat
            </button>
        </div>
    </form>
@endsection