@php
    $meta = collect([
        $testimonial['role'],
        $showCompany ? $testimonial['company'] : null,
    ])->filter()->implode(' - ');
@endphp

<article @class([
    'lp-card',
    'lp-testimonial-card',
    'lp-testimonial-card-featured' => $featured,
])>
    @if ($showRating && $testimonial['rating'])
        <div class="lp-testimonial-rating" aria-label="Nota {{ $testimonial['rating'] }} de 5">
            @for ($star = 1; $star <= 5; $star++)
                <span @class([
                    'lp-testimonial-star',
                    'lp-testimonial-star-muted' => $star > $testimonial['rating'],
                ]) aria-hidden="true">&#9733;</span>
            @endfor
        </div>
    @endif

    <blockquote class="lp-testimonial-quote">
        <p>{!! nl2br(e($testimonial['text'])) !!}</p>
    </blockquote>

    <footer class="lp-testimonial-footer">
        @if ($showAvatar && $testimonial['avatar'])
            <img
                class="lp-testimonial-avatar"
                src="{{ $testimonial['avatar'] }}"
                alt="Foto de {{ $testimonial['name'] }}"
                loading="lazy"
            >
        @endif

        <div class="lp-testimonial-author">
            <strong>{{ $testimonial['name'] }}</strong>

            @if ($meta)
                <span class="lp-muted">{{ $meta }}</span>
            @endif
        </div>

        @if ($showLogo && $testimonial['logo'])
            <img
                class="lp-testimonial-logo"
                src="{{ $testimonial['logo'] }}"
                alt="Logo {{ $testimonial['company'] ?: $testimonial['name'] }}"
                loading="lazy"
            >
        @endif
    </footer>
</article>
