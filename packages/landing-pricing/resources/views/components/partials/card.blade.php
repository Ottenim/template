<article @class([
    'lp-card',
    'lp-pricing-card',
    'lp-pricing-card-featured' => $featured,
])>
    <div class="lp-pricing-card-header">
        @if ($featured && $plan['badge'])
            <span class="lp-badge">{{ $plan['badge'] }}</span>
        @endif

        <h3 class="lp-subheading lp-pricing-title">{{ $plan['name'] }}</h3>

        @if ($plan['description'])
            <p class="lp-muted">{{ $plan['description'] }}</p>
        @endif
    </div>

    @include('landing-pricing::components.partials.price', ['plan' => $plan])

    @if ($plan['features'])
        <ul class="lp-pricing-features">
            @foreach ($plan['features'] as $feature)
                <li>{{ $feature }}</li>
            @endforeach
        </ul>
    @endif

    <div class="lp-pricing-card-footer">
        @include('landing-pricing::components.partials.cta', [
            'plan' => $plan,
            'featured' => $featured,
            'trackingEnabled' => $trackingEnabled,
            'trackingEventName' => $trackingEventName,
        ])

        @if ($plan['note'])
            <p class="lp-muted lp-pricing-note">{{ $plan['note'] }}</p>
        @endif
    </div>
</article>
