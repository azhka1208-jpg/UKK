@extends('layouts.dashboard')

@section('title', 'Detail Pengembalian')

@section('content')
    <x-page-header :title="'Pengembalian ' . $return->code" description="Status pengajuan pengembalian alat Anda.">
        <x-slot:actions>
            <a href="{{ route('peminjam.returns.index') }}" class="btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="h-4 w-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-5 lg:grid-cols-[1.6fr_1fr]">
        <div class="space-y-5">
            <div class="card card-pad">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <x-status-badge :label="$return->status_label" :tone="$return->status_badge" dot />
                    <div class="text-right">
                        <p class="text-xs uppercase tracking-wide text-ink-secondary">Denda</p>
                        <p class="font-display text-lg font-semibold text-heading">Rp {{ number_format((float) $return->fine_amount, 0, ',', '.') }}</p>
                    </div>
                </div>

                <dl class="mt-5 grid gap-4 border-t border-line pt-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Peminjaman</dt>
                        <dd class="mt-0.5 font-medium text-heading">{{ $return->borrowing?->code ?? '-' }}</dd>
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
                        <dt class="text-xs uppercase tracking-wide text-ink-secondary">Status Denda</dt>
                        <dd class="mt-0.5">
                            <x-status-badge :label="$return->fine_paid ? 'Sudah dibayar' : 'Belum dibayar'" :tone="$return->fine_paid ? 'badge-success' : 'badge-warning'" />
                        </dd>
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
                            <tr><th>Alat</th><th>Kode</th><th>Jumlah</th><th>Kondisi</th></tr>
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

        <div class="card card-pad">
            <h2 class="font-display text-base font-semibold text-heading">Status Pengajuan</h2>
            <ol class="mt-4 space-y-4 text-sm">
                <li class="flex gap-3">
                    <i data-lucide="check" class="mt-0.5 h-4 w-4 shrink-0 text-success"></i>
                    <span>Pengajuan dikirim &middot; {{ $return->created_at?->translatedFormat('d M Y H:i') }}</span>
                </li>
                <li class="flex gap-3">
                    <i data-lucide="{{ $return->status === 'diverifikasi' ? 'check' : 'circle' }}" class="mt-0.5 h-4 w-4 shrink-0 {{ $return->status === 'diverifikasi' ? 'text-success' : 'text-slate-300' }}"></i>
                    <span>Diverifikasi petugas &middot; {{ $return->verified_at?->translatedFormat('d M Y H:i') ?? 'Menunggu verifikasi' }}</span>
                </li>
            </ol>
            @if ($return->receiver)
                <p class="mt-4 border-t border-line pt-3 text-xs text-ink-secondary">Diverifikasi oleh {{ $return->receiver->name }}</p>
            @endif
        </div>
    </div>
@endsection