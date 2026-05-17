@once('landing-testimonials-styles')
    <x-testimonials::styles />
@endonce

<section
    {{ $attributes->class(['lp-section', 'lp-testimonials', 'lp-testimonials-layout-'.$layout]) }}
    style="--lp-testimonials-columns: {{ $columns }};"
>
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

        <div class="lp-section-content lp-testimonials-content">
            <div class="lp-testimonials-list">
                @foreach ($testimonials as $testimonial)
                    @include('landing-testimonials::components.partials.card', [
                        'testimonial' => $testimonial,
                        'showAvatar' => $showAvatar,
                        'showRating' => $showRating,
                        'showCompany' => $showCompany,
                        'showLogo' => $showLogo,
                        'featured' => $layout === 'featured' && $loop->first,
                    ])
                @endforeach
            </div>
        </div>
    </div>
</section>
