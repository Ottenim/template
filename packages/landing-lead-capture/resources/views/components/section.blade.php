@once('landing-lead-capture-styles')
    <x-lead-capture::styles />
@endonce

<section {{ $attributes->class(['lp-section', 'lp-lead-capture']) }}>
    <div class="lp-container">
        <div class="lp-card lp-lead-capture-card lp-lead-capture-card-{{ $variant }}">
            @if ($eyebrow || $title || $subtitle || $benefit)
                <header class="lp-section-header lp-lead-capture-header">
                    @if ($eyebrow)
                        <span class="lp-eyebrow">{{ $eyebrow }}</span>
                    @endif

                    @if ($title)
                        <h2 class="lp-heading">{{ $title }}</h2>
                    @endif

                    @if ($subtitle)
                        <p class="lp-muted">{{ $subtitle }}</p>
                    @endif

                    @if ($benefit)
                        <p class="lp-text lp-lead-capture-benefit">{{ $benefit }}</p>
                    @endif
                </header>
            @endif

            <x-lead-capture::form
                :button-label="$buttonLabel"
                :variant="$variant"
                :source="$source"
                :campaign="$campaign"
                :tag="$tag"
                :framed="false"
            />
        </div>
    </div>
</section>
