@once('landing-contact-styles')
    <x-contact::styles />
@endonce

<section {{ $attributes->class(['lp-section', 'lp-contact']) }}>
    <div class="lp-container">
        @if ($eyebrow || $title || $subtitle)
            <header class="lp-section-header">
                @if ($eyebrow)
                    <span class="lp-eyebrow">{{ $eyebrow }}</span>
                @endif

                @if ($title)
                    <h2 class="lp-heading">{{ $title }}</h2>
                @endif

                @if ($subtitle)
                    <p class="lp-muted">{{ $subtitle }}</p>
                @endif
            </header>
        @endif

        <div class="lp-section-content">
            <x-contact::form :button-label="$buttonLabel" />
        </div>
    </div>
</section>
