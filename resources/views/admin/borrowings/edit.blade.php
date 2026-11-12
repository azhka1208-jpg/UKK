@extends('layouts.dashboard')

@section('title', 'Edit Peminjaman')

@section('content')
    <x-page-header title="Edit Peminjaman" :description="'Perbarui pengajuan ' . $borrowing->code . '.'">
        <x-slot:actions>
            <a href="{{ route('admin.borrowings.show', $borrowing) }}" class="btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <form action="{{ route('admin.borrowings.update', $borrowing) }}" method="POST" class="mt-6">
        @csrf
        @method('PUT')
        <div class="card card-pad">
            @include('admin.borrowings._form', ['borrowing' => $borrowing, 'borrowers' => $borrowers, 'tools' => $tools])
        </div>
        <div class="mt-5 flex justify-end gap-2">
            <a href="{{ route('admin.borrowings.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">
                <i data-lucide="save" class="h-4 w-4"></i> Simpan Perubahan
            </button>
        </div>
    </form>
@endsection