@extends('layouts.dashboard')

@section('title', 'Ajukan Pengembalian')

@section('content')
    <x-page-header title="Ajukan Pengembalian" :description="'Pengembalian untuk peminjaman ' . $borrowing->code . '.'">
        <x-slot:actions>
            <a href="{{ route('peminjam.borrowings.show', $borrowing) }}" class="btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <form action="{{ route('peminjam.returns.store', $borrowing) }}" method="POST" class="mt-6">
        @csrf
        <div class="grid gap-5 lg:grid-cols-[1.5fr_1fr]">
            <div class="card card-pad">
                <h2 class="font-display text-base font-semibold text-heading">Alat yang Dikembalikan</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($borrowing->details as $detail)
                        <div class="flex flex-col gap-2 rounded-lg border border-line bg-canvas p-3 sm:flex-row sm:items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-heading">{{ $detail->tool?->name }}</p>
                                <p class="text-xs text-ink-secondary">{{ $detail->tool?->code }} &middot; {{ $detail->quantity }} unit</p>
                            </div>
                            <select name="conditions[{{ $detail->tool_id }}]" class="input sm:w-44">
                                <option value="baik">Baik</option>
                                <option value="rusak_ringan">Rusak Ringan</option>
                                <option value="rusak_berat">Rusak Berat</option>
                            </select>
                        </div>
                    @endforeach
                </div>
                @error('conditions')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <div class="card card-pad">
                <h2 class="font-display text-base font-semibold text-heading">Detail Pengembalian</h2>
                <div class="mt-4 space-y-4">
                    <div class="rounded-lg bg-canvas px-3 py-2.5 text-xs text-ink-secondary">
                        Batas pengembalian: <span class="font-medium text-heading">{{ $borrowing->due_date?->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        <label for="return_date" class="label">Tanggal Pengembalian</label>
                        <input id="return_date" type="date" name="return_date" value="{{ old('return_date', now()->toDateString()) }}" required class="input">
                        @error('return_date')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="notes" class="label">Catatan <span class="font-normal text-ink-secondary">(opsional)</span></label>
                        <textarea id="notes" name="notes" rows="3" class="input" placeholder="Keterangan kondisi alat">{{ old('notes') }}</textarea>
                        @error('notes')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                    <div class="rounded-lg bg-accent px-3 py-2.5 text-xs text-heading">
                        Pengembalian akan diverifikasi petugas sebelum dinyatakan selesai.
                    </div>
                </div>
                <button type="submit" class="btn-primary mt-5 w-full">Kirim Pengajuan Pengembalian</button>
            </div>
        </div>
    </form>
@endsection