@extends('layouts.dashboard')

@section('title', 'Notifikasi')

@section('content')
    <x-page-header title="Notifikasi" description="Informasi status pengajuan dan aktivitas akun Anda.">
        <x-slot:actions>
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn-secondary btn-sm">
                    <i data-lucide="check-check" class="h-4 w-4"></i> Tandai semua dibaca
                </button>
            </form>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 card overflow-hidden">
        <ul class="divide-y divide-line">
            @forelse ($notifications as $notification)
                <li class="flex items-start gap-3 px-5 py-4 {{ $notification->is_read ? '' : 'bg-accent/40' }}">
                    <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-accent text-heading">
                        <i data-lucide="{{ $notification->type === 'success' ? 'check-circle-2' : ($notification->type === 'danger' ? 'alert-circle' : 'info') }}" class="h-4 w-4"></i>
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-sm font-medium text-heading">{{ $notification->title }}</p>
                            <x-status-badge :label="ucfirst($notification->type)" :tone="$notification->type_badge" />
                        </div>
                        <p class="mt-0.5 text-sm text-ink-secondary">{{ $notification->message }}</p>
                        <p class="mt-1 text-xs text-ink-secondary">{{ $notification->created_at?->translatedFormat('d M Y H:i') }}</p>
                    </div>
                    @if ($notification->url)
                        <form action="{{ route('notifications.read', $notification) }}" method="POST" class="shrink-0">
                            @csrf
                            <button type="submit" class="btn-secondary btn-sm">Buka</button>
                        </form>
                    @endif
                </li>
            @empty
                <li>
                    <x-empty-state title="Belum ada notifikasi" description="Notifikasi terkait peminjaman dan pengembalian akan muncul di sini." icon="bell" />
                </li>
            @endforelse
        </ul>
        <div class="border-t border-line px-4 py-3">
            {{ $notifications->links() }}
        </div>
    </div>
@endsection