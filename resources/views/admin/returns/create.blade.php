@extends('layouts.dashboard')

@section('title', 'Catat Pengembalian')

@section('content')
    <x-page-header title="Catat Pengembalian" description="Catat pengembalian alat yang sedang dipinjam.">
        <x-slot:actions>
            <a href="{{ route('admin.returns.index') }}" class="btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    @if (! $selected)
        <div class="mt-6 card overflow-hidden">
            <div class="border-b border-line px-5 py-4">
                <h2 class="font-display text-base font-semibold text-heading">Peminjaman Aktif</h2>
                <p class="text-xs text-ink-secondary">Pilih peminjaman yang alatnya akan dikembalikan.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Peminjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Alat</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activeBorrowings as $borrowing)
                            <tr>
                                <td class="font-medium text-heading">{{ $borrowing->code }}</td>
                                <td>{{ $borrowing->user?->name }}</td>
                                <td class="whitespace-nowrap">
                                    {{ $borrowing->due_date?->format('d/m/Y') }}
                                    @if ($borrowing->is_late)
                                        <span class="ml-1 text-xs text-danger">+{{ $borrowing->late_days }} hari</span>
                                    @endif
                                </td>
                                <td class="text-xs text-ink-secondary">{{ $borrowing->details->map(fn ($d) => ($d->tool?->name ?? '-') . ' (' . $d->quantity . ')')->join(', ') }}</td>
                                <td>
                                    <div class="flex justify-end">
                                        <a href="{{ route('admin.returns.create', ['borrowing_id' => $borrowing->id]) }}" class="btn-primary btn-sm">Catat</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <x-empty-state title="Tidak ada peminjaman aktif" description="Belum ada alat yang sedang dipinjam saat ini." icon="check-circle-2" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <form action="{{ route('admin.returns.store') }}" method="POST" class="mt-6">
            @csrf
            <input type="hidden" name="borrowing_id" value="{{ $selected->id }}">

            <div class="grid gap-5 lg:grid-cols-[1.5fr_1fr]">
                <div class="card card-pad">
                    <h2 class="font-display text-base font-semibold text-heading">Data Peminjaman</h2>
                    <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Kode</dt>
                            <dd class="mt-0.5 font-medium text-heading">{{ $selected->code }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Peminjam</dt>
                            <dd class="mt-0.5 font-medium text-heading">{{ $selected->user?->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Tanggal Pinjam</dt>
                            <dd class="mt-0.5 text-heading">{{ $selected->borrow_date?->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Jatuh Tempo</dt>
                            <dd class="mt-0.5 text-heading">{{ $selected->due_date?->format('d/m/Y') }}</dd>
                        </div>
                    </dl>

                    <div class="mt-5 border-t border-line pt-4">
                        <p class="label">Kondisi alat saat dikembalikan</p>
                        <div class="space-y-3">
                            @foreach ($selected->details as $detail)
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
                    </div>
                </div>

                <div class="card card-pad">
                    <h2 class="font-display text-base font-semibold text-heading">Detail Pengembalian</h2>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="return_date" class="label">Tanggal Pengembalian</label>
                            <input id="return_date" type="date" name="return_date" value="{{ old('return_date', now()->toDateString()) }}" required class="input">
                            @error('return_date')<p class="error-text">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="notes" class="label">Catatan <span class="font-normal text-ink-secondary">(opsional)</span></label>
                            <textarea id="notes" name="notes" rows="3" class="input" placeholder="Catatan kondisi atau keterangan tambahan">{{ old('notes') }}</textarea>
                            @error('notes')<p class="error-text">{{ $message }}</p>@enderror
                        </div>
                        <div class="rounded-lg bg-accent px-3 py-2.5 text-xs text-heading">
                            Denda keterlambatan dihitung otomatis berdasarkan tanggal pengembalian dan pengaturan denda.
                        </div>
                    </div>
                    <div class="mt-5 flex justify-end gap-2">
                        <a href="{{ route('admin.returns.create') }}" class="btn-secondary">Batal</a>
                        <button type="submit" class="btn-primary">Catat Pengembalian</button>
                    </div>
                </div>
            </div>
        </form>
    @endif
@endsection