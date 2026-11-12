@props(['label', 'tone' => 'badge-neutral', 'dot' => false])

<span {{ $attributes->merge(['class' => 'badge ' . $tone]) }}>
    @if ($dot)
        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
    @endif
    {{ $label }}
</span>