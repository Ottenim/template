@if ($plan['cta_label'] && $plan['cta_url'])
    <a
        @class([
            'lp-button',
            'lp-button-primary' => $featured,
            'lp-button-secondary' => ! $featured,
            'lp-pricing-cta',
        ])
        href="{{ $plan['cta_url'] }}"
        @if ($trackingEnabled && $trackingEventName)
            data-event="{{ $trackingEventName }}"
            data-pricing-plan="{{ $plan['name'] }}"
            data-pricing-plan-id="{{ $plan['id'] }}"
        @endif
    >
        {{ $plan['cta_label'] }}
    </a>
@endif
