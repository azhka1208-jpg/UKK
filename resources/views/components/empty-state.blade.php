@props(['title', 'description' => null, 'icon' => 'inbox'])

<div class="empty-state">
    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-accent text-heading">
        <i data-lucide="{{ $icon }}" class="h-6 w-6"></i>
    </span>
    <p class="mt-4 font-display text-base font-semibold text-heading">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 max-w-sm text-sm text-ink-secondary">{{ $description }}</p>
    @endif
    @isset($action)
        <div class="mt-5">{{ $action }}</div>
    @endisset
</div>