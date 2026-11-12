@extends('layouts.dashboard')

@section('title', 'Laporan')

@section('content')
    <x-page-header title="Laporan" description="Buat laporan berdasarkan data aktual dari database.">
        <x-slot:actions>
            <a href="{{ route('petugas.reports.print', array_merge(request()->query(), ['type' => $type])) }}" target="_blank" class="btn-secondary btn-sm">
                <i data-lucide="printer" class="h-4 w-4"></i> Cetak
            </a>
            <a href="{{ route('petugas.reports.pdf', array_merge(request()->query(), ['type' => $type])) }}" class="btn-secondary btn-sm">
                <i data-lucide="file-down" class="h-4 w-4"></i> Export PDF
            </a>
            <a href="{{ route('petugas.reports.excel', array_merge(request()->query(), ['type' => $type])) }}" class="btn-secondary btn-sm">
                <i data-lucide="file-spreadsheet" class="h-4 w-4"></i> Export Excel
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" action="{{ route('petugas.reports.index') }}" class="mt-6 card card-pad">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <div>
                <label class="label" for="type">Jenis Laporan</label>
                <select id="type" name="type" class="input" onchange="this.form.submit()">
                    @foreach ($types as $key => $label)
                        <option value="{{ $key }}" @selected($type === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="from">Dari Tanggal</label>
                <input id="from" type="date" name="from" value="{{ $filters['from'] }}" class="input" @if($type === 'tools') disabled @endif>
            </div>
            <div>
                <label class="label" for="to">Sampai Tanggal</label>
                <input id="to" type="date" name="to" value="{{ $filters['to'] }}" class="input" @if($type === 'tools') disabled @endif>
            </div>
            <div>
                <label class="label" for="category">Kategori</label>
                <select id="category" name="category" class="input" @if($type === 'activity') disabled @endif>
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $filters['category'] === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="status">Status</label>
                @if ($type === 'activity')
                    <input id="status" type="text" name="action" value="{{ $filters['action'] }}" placeholder="Aksi (login, create...)" class="input">
                @elseif ($type === 'tools')
                    <select id="status" name="status" class="input">
                        <option value="">Semua</option>
                        <option value="tersedia" @selected($filters['status'] === 'tersedia')>Tersedia</option>
                        <option value="perbaikan" @selected($filters['status'] === 'perbaikan')>Perbaikan</option>
                        <option value="tidak_tersedia" @selected($filters['status'] === 'tidak_tersedia')>Tidak Tersedia</option>
                    </select>
                @else
                    <select id="status" name="status" class="input">
                        <option value="">Semua status</option>
                        @foreach (['menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'dipinjam' => 'Dipinjam', 'terlambat' => 'Terlambat', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $key => $label)
                            <option value="{{ $key }}" @selected($filters['status'] === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
        <div class="mt-4 flex justify-end gap-2">
            <a href="{{ route('petugas.reports.index') }}" class="btn-secondary">Reset</a>
            <button type="submit" class="btn-primary">
                <i data-lucide="filter" class="h-4 w-4"></i> Tampilkan
            </button>
        </div>
    </form>

    <div class="mt-5 card overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line px-5 py-4">
            <div>
                <h2 class="font-display text-base font-semibold text-heading">{{ $report['meta']['title'] }}</h2>
                <p class="text-xs text-ink-secondary">Periode: {{ $report['meta']['period'] }} &middot; {{ $report['total'] }} data</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        @foreach ($report['columns'] as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($report['rows'] as $row)
                        <tr>
                            @foreach ($row as $cell)
                                <td>{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($report['columns']) }}">
                                <x-empty-state title="Tidak ada data" description="Tidak ada data pada periode dan filter yang dipilih." icon="file-x" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection