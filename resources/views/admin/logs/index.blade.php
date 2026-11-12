@extends('layouts.dashboard')

@section('title', 'Log Aktivitas')

@section('content')
    <x-page-header title="Log Aktivitas" description="Catatan aktivitas penting di dalam sistem. Log tidak dapat diubah atau dihapus." />

    <form method="GET" action="{{ route('admin.logs.index') }}" class="mt-6 card card-pad">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_1fr_auto]">
            <div>
                <label class="label" for="q">Pencarian</label>
                <div class="relative">
                    <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-secondary"></i>
                    <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="Deskripsi atau pengguna" class="input pl-9">
                </div>
            </div>
            <div>
                <label class="label" for="action">Aksi</label>
                <select id="action" name="action" class="input">
                    <option value="">Semua aksi</option>
                    @foreach ($actions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="from">Dari</label>
                <input id="from" type="date" name="from" value="{{ request('from') }}" class="input">
            </div>
            <div>
                <label class="label" for="to">Sampai</label>
                <input id="to" type="date" name="to" value="{{ request('to') }}" class="input">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary flex-1">Terapkan</button>
                <a href="{{ route('admin.logs.index') }}" class="btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="mt-5 card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pengguna</th>
                        <th>Aksi</th>
                        <th>Deskripsi</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="whitespace-nowrap text-ink-secondary">{{ $log->created_at?->translatedFormat('d M Y H:i') }}</td>
                            <td class="text-ink">{{ $log->user?->name ?? 'Sistem' }}</td>
                            <td><span class="badge-neutral">{{ $log->action }}</span></td>
                            <td class="max-w-md text-ink-secondary">{{ $log->description ?: '-' }}</td>
                            <td class="font-mono text-xs text-ink-secondary">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-empty-state title="Belum ada log aktivitas" description="Aktivitas pengguna akan tercatat di sini." icon="history" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-line px-4 py-3">
            {{ $logs->links() }}
        </div>
    </div>
@endsection