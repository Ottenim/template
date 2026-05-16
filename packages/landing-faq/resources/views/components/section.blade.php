@once('landing-faq-styles')
    <x-faq::styles />
@endonce

<section {{ $attributes->class(['lp-section', 'lp-faq', 'lp-faq-layout-'.$layout]) }}>
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

        <div class="lp-section-content lp-faq-content">
            @php($itemIndex = 0)

            @if ($showCategories)
                @foreach ($categorizedItems as $category => $categoryItems)
                    <div class="lp-faq-category">
                        <h3 class="lp-subheading lp-faq-category-title">{{ $category }}</h3>

                        <div class="lp-faq-list">
                            @foreach ($categoryItems as $item)
                                @include('landing-faq::components.partials.item', [
                                    'item' => $item,
                                    'layout' => $layout,
                                    'open' => $defaultOpenFirstItem && $itemIndex === 0,
                                ])

                                @php($itemIndex++)
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="lp-faq-list">
                    @foreach ($items as $item)
                        @include('landing-faq::components.partials.item', [
                            'item' => $item,
                            'layout' => $layout,
                            'open' => $defaultOpenFirstItem && $itemIndex === 0,
                        ])

                        @php($itemIndex++)
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if ($schemaJson)
        <script type="application/ld+json">{!! $schemaJson !!}</script>
    @endif
</section>
