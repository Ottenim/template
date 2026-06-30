<?php

namespace Template\LandingPricing\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingCore\Support\Coerce;
use Template\LandingPricing\Config\PricingConfig;
use Template\LandingPricing\Support\PricingPlans;

class Section extends Component
{
    public bool $enabled;

    public ?string $eyebrow;

    public ?string $title;

    public ?string $subtitle;

    public string $layout;

    public int $columns;

    public bool $showFeaturedPlan;

    public bool $trackingEnabled;

    public ?string $trackingEventName;

    public Collection $plans;

    public function __construct(
        mixed $plans = null,
        mixed $items = null,
        ?string $eyebrow = null,
        ?string $title = null,
        ?string $subtitle = null,
        ?string $layout = null,
        mixed $columns = null,
        mixed $showFeaturedPlan = null,
        mixed $trackingEnabled = null,
        mixed $trackingEventName = null,
        mixed $limit = null,
        mixed $enabled = null,
    ) {
        $config = app(PricingConfig::class);

        $this->enabled = $config->enabled()
            && $config->sectionEnabled()
            && Coerce::bool($enabled, true);

        $this->eyebrow = Coerce::nullableString($eyebrow ?? $config->sectionEyebrow());
        $this->title = Coerce::nullableString($title ?? $config->sectionTitle());
        $this->subtitle = Coerce::nullableString($subtitle ?? $config->sectionSubtitle());
        $this->layout = $this->layoutValue($layout ?? $config->layout());
        $this->columns = $this->columnsValue($columns ?? $config->columns());
        $this->showFeaturedPlan = Coerce::bool($showFeaturedPlan, $config->showFeaturedPlan());
        $this->trackingEnabled = Coerce::bool($trackingEnabled, $config->trackingEnabled());
        $this->trackingEventName = Coerce::nullableString($trackingEventName ?? $config->trackingEventName());

        $this->plans = app(PricingPlans::class)->publicPlans($plans ?? $items, $this->limitValue($limit, $config));
    }

    public function shouldRender(): bool
    {
        return $this->enabled && $this->plans->isNotEmpty();
    }

    public function render(): View
    {
        return view('landing-pricing::components.section');
    }

    protected function layoutValue(mixed $value): string
    {
        $layout = Coerce::string($value, 'cards');

        return in_array($layout, ['cards', 'compact', 'table'], true) ? $layout : 'cards';
    }

    protected function columnsValue(mixed $value): int
    {
        $columns = (int) ($value ?: 3);

        return max(1, min(4, $columns));
    }

    protected function limitValue(mixed $value, PricingConfig $config): ?int
    {
        $value ??= $config->limit();

        if ($value === null || $value === '') {
            return null;
        }

        $limit = (int) $value;

        return $limit > 0 ? $limit : null;
    }
}
