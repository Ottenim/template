@props([
    'eyebrow' => null,
    'title' => null,
    'subtitle' => null,
    'center' => false,
])

<header {{ $attributes->class(['lp-section-header', 'lp-section-header-center' => $center]) }}>
    @if ($eyebrow)
        <span class="lp-eyebrow">{{ $eyebrow }}</span>
    @endif

    @if ($title)
        <h2 class="lp-heading">{{ $title }}</h2>
    @endif

    @if ($subtitle)
        <p class="lp-muted">{{ $subtitle }}</p>
    @endif

    {{ $slot }}
</header>
