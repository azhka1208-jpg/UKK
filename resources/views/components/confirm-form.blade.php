@props([
    'action',
    'method' => 'DELETE',
    'title' => 'Konfirmasi tindakan',
    'message' => 'Apakah Anda yakin ingin melanjutkan? Tindakan ini tidak dapat dibatalkan.',
    'label' => 'Hapus',
    'tone' => 'btn-danger btn-sm',
    'icon' => 'trash-2',
])

<div x-data="{ open: false }">
    <button type="button" @click="open = true" class="{{ $tone }}">
        <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>
        {{ $slot->isNotEmpty() ? $slot : $label }}
    </button>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-ink/45" @click="open = false"></div>
        <div x-show="open" x-transition class="relative w-full max-w-md rounded-xl bg-surface p-5 shadow-pop">
            <div class="flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-danger/10 text-danger">
                    <i data-lucide="alert-triangle" class="h-5 w-5"></i>
                </span>
                <div>
                    <h3 class="font-display text-base font-semibold text-heading">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-ink-secondary">{{ $message }}</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" class="btn-secondary" @click="open = false">Batal</button>
                <form action="{{ $action }}" method="POST">
                    @csrf
                    @method($method)
                    <button type="submit" class="btn-danger">{{ $label }}</button>
                </form>
            </div>
        </div>
    </div>
</div>