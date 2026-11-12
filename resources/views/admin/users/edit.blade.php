@extends('layouts.dashboard')

@section('title', 'Edit Pengguna')

@section('content')
    <x-page-header title="Edit Pengguna" :description="'Perbarui data akun ' . $user->name . '.'">
        <x-slot:actions>
            <a href="{{ route('admin.users.show', $user) }}" class="btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="mt-6">
        @csrf
        @method('PUT')
        <div class="card card-pad">
            @include('admin.users._form', ['user' => $user, 'roles' => $roles])
        </div>
        <div class="mt-5 flex justify-end gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">
                <i data-lucide="save" class="h-4 w-4"></i> Simpan Perubahan
            </button>
        </div>
    </form>
@endsection