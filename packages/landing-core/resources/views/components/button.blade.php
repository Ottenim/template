@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
])

@php
    $variantClass = $variant === 'secondary' ? 'lp-button-secondary' : 'lp-button-primary';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class(['lp-button', $variantClass]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class(['lp-button', $variantClass]) }}>
        {{ $slot }}
    </button>
@endif
