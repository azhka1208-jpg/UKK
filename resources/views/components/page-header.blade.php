@props(['title', 'description' => null])

<div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="font-display text-xl font-semibold text-heading sm:text-2xl">{{ $title }}</h1>
        @if ($description)
            <p class="mt-1 text-sm text-ink-secondary">{{ $description }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</div>