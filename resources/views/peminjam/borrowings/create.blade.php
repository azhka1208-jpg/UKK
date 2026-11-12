@extends('layouts.dashboard')

@section('title', 'Ajukan Peminjaman')

@section('content')
    <x-page-header title="Ajukan Peminjaman" description="Pilih alat yang tersedia dan lengkapi formulir peminjaman.">
        <x-slot:actions>
            <a href="{{ route('peminjam.borrowings.index') }}" class="btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    @php
        $defaultItems = [];
        if ($selectedId) {
            $defaultItems = [['tool_id' => (string) $selectedId, 'quantity' => 1]];
        }
        $initialItems = old('items', $defaultItems);
    @endphp

    <form action="{{ route('peminjam.borrowings.store') }}" method="POST" class="mt-6">
        @csrf
        <div class="card card-pad">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="borrow_date" class="label">Tanggal Peminjaman</label>
                    <input id="borrow_date" type="date" name="borrow_date" value="{{ old('borrow_date', now()->toDateString()) }}" required
                           class="input @error('borrow_date') input-error @enderror">
                    @error('borrow_date')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="due_date" class="label">Rencana Tanggal Pengembalian</label>
                    <input id="due_date" type="date" name="due_date" value="{{ old('due_date', now()->addDays(7)->toDateString()) }}" required
                           class="input @error('due_date') input-error @enderror">
                    @error('due_date')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="purpose" class="label">Keperluan Peminjaman</label>
                    <textarea id="purpose" name="purpose" rows="3" required class="input @error('purpose') input-error @enderror"
                              placeholder="Jelaskan tujuan penggunaan alat">{{ old('purpose') }}</textarea>
                    @error('purpose')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="notes" class="label">Catatan Tambahan <span class="font-normal text-ink-secondary">(opsional)</span></label>
                    <textarea id="notes" name="notes" rows="2" class="input @error('notes') input-error @enderror"
                              placeholder="Catatan tambahan untuk petugas">{{ old('notes') }}</textarea>
                    @error('notes')<p class="error-text">{{ $message }}</p>@enderror
                </div>

                @include('partials.borrowing-items', ['tools' => $tools, 'initialItems' => $initialItems])
            </div>

            <div class="mt-6 flex flex-col-reverse gap-2 border-t border-line pt-5 sm:flex-row sm:justify-end">
                <a href="{{ route('peminjam.borrowings.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <i data-lucide="send" class="h-4 w-4"></i> Kirim Pengajuan
                </button>
            </div>
        </div>
    </form>
@endsection