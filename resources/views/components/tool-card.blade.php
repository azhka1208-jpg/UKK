@props(['tool', 'href'])

@php
    $conditionTone = match ($tool->condition) {
        'rusak_ringan' => 'badge-warning',
        'rusak_berat' => 'badge-danger',
        default => 'badge-success',
    };

    $statusTone = $tool->is_available ? 'badge-success' : ($tool->status === 'perbaikan' ? 'badge-warning' : 'badge-danger');
    $statusLabel = $tool->is_available ? 'Tersedia' : ($tool->status === 'perbaikan' ? 'Perbaikan' : 'Tidak Tersedia');
@endphp

<article class="card group flex flex-col overflow-hidden">
    <a href="{{ $href }}" class="block overflow-hidden bg-ink/5">
        <img src="{{ $tool->photo_url }}" alt="Foto {{ $tool->name }}"
             class="h-40 w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]">
    </a>

    <div class="flex flex-1 flex-col p-4">
        <div class="flex items-center justify-between gap-2">
            <span class="text-xs font-medium text-ink-secondary">{{ $tool->category?->name ?? '-' }}</span>
            <x-status-badge :label="$statusLabel" :tone="$statusTone" dot />
        </div>

        <a href="{{ $href }}" class="mt-2 block font-display text-base font-semibold leading-snug text-heading hover:underline underline-offset-2">
            {{ $tool->name }}
        </a>

        <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-ink-secondary">
            <span class="font-mono">{{ $tool->code }}</span>
            <span class="text-line">|</span>
            <span>Stok {{ $tool->stock_available }}/{{ $tool->stock_total }}</span>
        </div>

        <div class="mt-3 flex items-center gap-2">
            <x-status-badge :label="$tool->condition_label" :tone="$conditionTone" />
            <span class="text-xs text-ink-secondary">{{ $tool->location }}</span>
        </div>

        <div class="mt-4 flex-1"></div>

        <a href="{{ $href }}" class="mt-2 inline-flex items-center gap-1.5 text-sm font-medium text-heading hover:underline underline-offset-2">
            Lihat detail
            <i data-lucide="arrow-right" class="h-4 w-4"></i>
        </a>
    </div>
</article>