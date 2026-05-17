@if ($plan['price'] !== null)
    <div class="lp-pricing-price">
        @if ($plan['currency'])
            <span>{{ $plan['currency'] }}</span>
        @endif

        <strong>{{ $plan['price'] }}</strong>

        @if ($plan['billing_period_label'])
            <span class="lp-muted">{{ $plan['billing_period_label'] }}</span>
        @endif
    </div>
@endif
