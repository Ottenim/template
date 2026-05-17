@once('landing-pricing-styles')
    <x-pricing::styles />
@endonce

<section
    {{ $attributes->class(['lp-section', 'lp-pricing', 'lp-pricing-layout-'.$layout]) }}
    style="--lp-pricing-columns: {{ $columns }};"
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

        @if ($layout === 'table')
            <div class="lp-section-content lp-pricing-table-wrap">
                <table class="lp-pricing-table">
                    <thead>
                        <tr>
                            <th scope="col">Plano</th>
                            <th scope="col">Preco</th>
                            <th scope="col">Recursos</th>
                            <th scope="col">Acao</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($plans as $plan)
                            <tr @class([
                                'lp-pricing-table-featured' => $showFeaturedPlan && $plan['is_featured'],
                            ])>
                                <th scope="row">
                                    <span>{{ $plan['name'] }}</span>

                                    @if ($showFeaturedPlan && $plan['is_featured'] && $plan['badge'])
                                        <span class="lp-badge">{{ $plan['badge'] }}</span>
                                    @endif

                                    @if ($plan['description'])
                                        <span class="lp-muted">{{ $plan['description'] }}</span>
                                    @endif
                                </th>
                                <td>
                                    @include('landing-pricing::components.partials.price', ['plan' => $plan])
                                </td>
                                <td>
                                    @if ($plan['features'])
                                        <ul class="lp-pricing-features">
                                            @foreach ($plan['features'] as $feature)
                                                <li>{{ $feature }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                                <td>
                                    @include('landing-pricing::components.partials.cta', [
                                        'plan' => $plan,
                                        'featured' => $showFeaturedPlan && $plan['is_featured'],
                                        'trackingEnabled' => $trackingEnabled,
                                        'trackingEventName' => $trackingEventName,
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="lp-section-content lp-pricing-grid">
                @foreach ($plans as $plan)
                    @include('landing-pricing::components.partials.card', [
                        'plan' => $plan,
                        'featured' => $showFeaturedPlan && $plan['is_featured'],
                        'trackingEnabled' => $trackingEnabled,
                        'trackingEventName' => $trackingEventName,
                    ])
                @endforeach
            </div>
        @endif
    </div>
</section>
