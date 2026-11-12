@php
    $flash = [
        'success' => ['icon' => 'check-circle-2', 'class' => 'bg-success/10 text-success border-success/20'],
        'error' => ['icon' => 'alert-circle', 'class' => 'bg-danger/10 text-danger border-danger/20'],
        'warning' => ['icon' => 'alert-triangle', 'class' => 'bg-warning/10 text-[#a8781f] border-warning/20'],
        'info' => ['icon' => 'info', 'class' => 'bg-accent text-heading border-navy/15'],
    ];
@endphp

<div class="space-y-3">
    @foreach ($flash as $key => $config)
        @if (session($key))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-transition.opacity
                class="flex items-start gap-3 rounded-lg border px-4 py-3 text-sm {{ $config['class'] }}"
            >
                <i data-lucide="{{ $config['icon'] }}" class="mt-0.5 h-4 w-4 shrink-0"></i>
                <p class="flex-1">{{ session($key) }}</p>
                <button type="button" @click="show = false" class="shrink-0 opacity-60 transition-opacity hover:opacity-100" aria-label="Tutup">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>
        @endif
    @endforeach
</div>