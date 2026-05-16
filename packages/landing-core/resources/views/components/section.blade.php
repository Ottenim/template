@props([
    'id' => null,
    'container' => true,
])

<section @if ($id) id="{{ $id }}" @endif {{ $attributes->class(['lp-section']) }}>
    @if ($container)
        <div class="lp-container">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</section>
