@once('landing-whatsapp-styles')
    <x-whatsapp::styles />
@endonce

<section {{ $attributes->class(['lp-section', 'lp-whatsapp-section']) }}>
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
            <article @class(['lp-whatsapp-card', 'lp-card' => $card])>
                @if ($text)
                    <p class="lp-muted lp-whatsapp-card-text">{{ $text }}</p>
                @endif

                <x-whatsapp::button
                    :phone="$phone"
                    :message="$message"
                    :url="$url"
                    :label="$buttonLabel"
                    :show-icon="$showIcon"
                    :show-text="true"
                    :tooltip="$tooltip"
                />
            </article>
        </div>
    </div>
</section>
