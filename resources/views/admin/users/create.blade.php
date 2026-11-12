@extends('layouts.dashboard')

@section('title', 'Tambah Pengguna')

@section('content')
    <x-page-header title="Tambah Pengguna" description="Buat akun baru untuk admin, petugas, atau peminjam.">
        <x-slot:actions>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <form action="{{ route('admin.users.store') }}" method="POST" class="mt-6">
        @csrf
        <div class="card card-pad">
            @include('admin.users._form', ['user' => null, 'roles' => $roles])
        </div>
        <div class="mt-5 flex justify-end gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">
                <i data-lucide="save" class="h-4 w-4"></i> Simpan Pengguna
            </button>
        </div>
    </form>
@endsection