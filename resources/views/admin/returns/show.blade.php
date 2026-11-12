@extends('layouts.dashboard')

@section('title', 'Detail Pengembalian')

@section('content')
    <x-page-header :title="'Pengembalian ' . $return->code" description="Detail pengembalian alat dan verifikasi.">
        <x-slot:actions>
            <a href="{{ route('admin.returns.index') }}" class="btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-5 lg:grid-cols-[1.6fr_1fr]">
        <div class="space-y-5">
            <div class="card card-pad">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-ink-secondary">Status</p>
                        <div class="mt-1"><x-status-badge :label="$return->status_label" :tone="$return->status_badge" dot /></div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs uppercase tracking-wide text-ink-secondary">Denda</p>
                        <p class="font-display text-lg font-semibold text-heading">Rp {{ number_format((float) $return->fine_amount, 0, ',', '.') }}</p>
                    </div>
                </div>

                <dl class="mt-5 grid gap-4 border-t border-line pt-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Kode Peminjaman</dt>
                        <dd class="mt-0.5 font-medium text-heading">
                            @if ($return->borrowing)
                                <a href="{{ route('admin.borrowings.show', $return->borrowing) }}" class="hover:underline">{{ $return->borrowing->code }}</a>
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Peminjam</dt>
                        <dd class="mt-0.5 font-medium text-heading">{{ $return->borrowing?->user?->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Jatuh Tempo</dt>
                        <dd class="mt-0.5 text-heading">{{ $return->borrowing?->due_date?->format('d/m/Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Tanggal Kembali</dt>
                        <dd class="mt-0.5 text-heading">{{ $return->return_date?->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Keterlambatan</dt>
                        <dd class="mt-0.5 {{ $return->late_days > 0 ? 'font-medium text-danger' : 'text-heading' }}">{{ $return->late_days }} hari</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Diverifikasi oleh</dt>
                        <dd class="mt-0.5 text-heading">{{ $return->receiver?->name ?? '-' }}</dd>
                    </div>
                    @if ($return->notes)
                        <div class="sm:col-span-2">
                            <dt class="text-xs uppercase tracking-wide text-ink-secondary">Catatan</dt>
                            <dd class="mt-0.5 text-ink-secondary">{{ $return->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="card">
                <div class="border-b border-line px-5 py-4">
                    <h2 class="font-display text-base font-semibold text-heading">Kondisi Alat</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Alat</th>
                                <th>Kode</th>
                                <th>Jumlah</th>
                                <th>Kondisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($return->details as $detail)
                                <tr>
                                    <td class="font-medium text-heading">{{ $detail->tool?->name ?? '-' }}</td>
                                    <td class="font-mono text-xs text-ink-secondary">{{ $detail->tool?->code }}</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>
                                        <x-status-badge :label="$detail->condition_label"
                                            :tone="$detail->condition === 'baik' ? 'badge-success' : ($detail->condition === 'rusak_ringan' ? 'badge-warning' : 'badge-danger')" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-5">
            @if ($return->status === 'diajukan')
                <div class="card card-pad">
                    <h2 class="font-display text-base font-semibold text-heading">Verifikasi Pengembalian</h2>
                    <p class="mt-1 text-xs text-ink-secondary">Stok alat akan diperbarui setelah diverifikasi.</p>

                    <form action="{{ route('admin.returns.verify', $return) }}" method="POST" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <label for="return_date" class="label">Tanggal Pengembalian</label>
                            <input id="return_date" type="date" name="return_date" value="{{ old('return_date', $return->return_date?->format('Y-m-d')) }}" required class="input">
                            @error('return_date')<p class="error-text">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="notes" class="label">Catatan <span class="font-normal text-ink-secondary">(opsional)</span></label>
                            <textarea id="notes" name="notes" rows="2" class="input">{{ old('notes', $return->notes) }}</textarea>
                        </div>
                        <label class="flex items-center gap-2 text-sm text-ink">
                            <input type="checkbox" name="fine_paid" value="1" class="rounded border-line text-heading focus:ring-navy/30">
                            Denda telah dibayar
                        </label>
                        <button type="submit" class="btn-success w-full">
                            <i data-lucide="check" class="h-4 w-4"></i> Verifikasi Pengembalian
                        </button>
                    </form>

                    <div class="mt-4 border-t border-line pt-4">
                        <x-confirm-form :action="route('admin.returns.destroy', $return)" title="Hapus pengajuan pengembalian"
                            :message="'Pengajuan ' . $return->code . ' akan dihapus dan status peminjaman dikembalikan.'" label="Hapus Pengajuan" tone="btn-secondary btn-sm w-full text-danger" />
                    </div>
                </div>
            @else
                <div class="card card-pad">
                    <h2 class="font-display text-base font-semibold text-heading">Pembaruan Catatan</h2>
                    <form action="{{ route('admin.returns.update', $return) }}" method="POST" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="edit-notes" class="label">Catatan</label>
                            <textarea id="edit-notes" name="notes" rows="2" class="input">{{ old('notes', $return->notes) }}</textarea>
                        </div>
                        <label class="flex items-center gap-2 text-sm text-ink">
                            <input type="checkbox" name="fine_paid" value="1" class="rounded border-line text-heading focus:ring-navy/30" @checked($return->fine_paid)>
                            Denda telah dibayar
                        </label>
                        <button type="submit" class="btn-primary w-full">Simpan Perubahan</button>
                    </form>
                </div>

                <div class="card card-pad">
                    <p class="text-xs uppercase tracking-wide text-ink-secondary">Diverifikasi pada</p>
                    <p class="mt-1 text-sm font-medium text-heading">{{ $return->verified_at?->translatedFormat('d M Y H:i') ?? '-' }}</p>
                    <p class="mt-3 text-xs uppercase tracking-wide text-ink-secondary">Status Denda</p>
                    <p class="mt-1"><x-status-badge :label="$return->fine_paid ? 'Sudah dibayar' : 'Belum dibayar'" :tone="$return->fine_paid ? 'badge-success' : 'badge-warning'" /></p>
                </div>
            @endif
        </div>
    </div>
@endsection