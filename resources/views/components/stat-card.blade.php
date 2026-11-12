@props(['label', 'value', 'icon' => 'package', 'tone' => 'navy', 'hint' => null, 'href' => null])

@php
    $tones = [
        'navy' => 'bg-navy/10 text-heading',
        'success' => 'bg-success/10 text-success',
        'warning' => 'bg-warning/10 text-[#a8781f]',
        'danger' => 'bg-danger/10 text-danger',
        'slate' => 'bg-ink/5 text-ink-secondary',
    ];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'card card-pad block transition-colors' . ($href ? ' hover:border-navy/30' : '')]) }}
>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium uppercase tracking-wide text-ink-secondary">{{ $label }}</p>
            <p class="mt-2 font-display text-2xl font-semibold text-heading">{{ $value }}</p>
            @if ($hint)
                <p class="mt-1 text-xs text-ink-secondary">{{ $hint }}</p>
            @endif
        </div>
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $tones[$tone] ?? $tones['navy'] }}">
            <i data-lucide="{{ $icon }}" class="h-5 w-5"></i>
        </span>
    </div>
</{{ $tag }}>