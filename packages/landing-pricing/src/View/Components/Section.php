<?php

namespace Template\LandingPricing\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;
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
        $this->enabled = $this->boolValue(config('landing-pricing.enabled', true), true)
            && $this->boolValue(config('landing-pricing.section.enabled', true), true)
            && $this->boolValue($enabled, true);

        $this->eyebrow = $this->nullableString($eyebrow ?? config('landing-pricing.section.eyebrow'));
        $this->title = $this->nullableString($title ?? config('landing-pricing.section.title'));
        $this->subtitle = $this->nullableString($subtitle ?? config('landing-pricing.section.subtitle'));
        $this->layout = $this->layoutValue($layout ?? config('landing-pricing.layout', 'cards'));
        $this->columns = $this->columnsValue($columns ?? config('landing-pricing.columns', 3));
        $this->showFeaturedPlan = $this->boolValue($showFeaturedPlan, (bool) config('landing-pricing.show_featured_plan', true));
        $this->trackingEnabled = $this->boolValue($trackingEnabled, (bool) config('landing-pricing.tracking.enabled', true));
        $this->trackingEventName = $this->nullableString($trackingEventName ?? config('landing-pricing.tracking.event_name'));

        $this->plans = app(PricingPlans::class)->publicPlans($plans ?? $items, $this->limitValue($limit));
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
        $layout = $this->nullableString($value) ?? 'cards';

        return in_array($layout, ['cards', 'compact', 'table'], true) ? $layout : 'cards';
    }

    protected function columnsValue(mixed $value): int
    {
        $columns = (int) ($value ?: 3);

        return max(1, min(4, $columns));
    }

    protected function limitValue(mixed $value): ?int
    {
        $value ??= config('landing-pricing.limit');

        if ($value === null || $value === '') {
            return null;
        }

        $limit = (int) $value;

        return $limit > 0 ? $limit : null;
    }

    protected function boolValue(mixed $value, bool $default): bool
    {
        if ($value === null) {
            return $default;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
        }

        return (bool) $value;
    }

    protected function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
