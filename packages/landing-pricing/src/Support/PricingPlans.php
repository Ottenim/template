<?php

namespace Template\LandingPricing\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Template\LandingPricing\Models\PricingPlan;
use Throwable;

class PricingPlans
{
    public function publicPlans(mixed $plans = null, ?int $limit = null, bool $activeOnly = true): Collection
    {
        $plans = $plans !== null ? collect($plans) : $this->configuredPlans();

        $normalized = $plans
            ->values()
            ->map(fn (mixed $plan, int $index) => $this->normalizePlan($plan, $index))
            ->filter()
            ->when($activeOnly, fn (Collection $plans) => $plans->filter(fn (array $plan) => $plan['is_active']))
            ->sortBy([
                ['sort_order', 'asc'],
                ['position', 'asc'],
            ])
            ->values()
            ->map(function (array $plan) {
                unset($plan['position']);

                return $plan;
            });

        if ($limit !== null && $limit > 0) {
            return $normalized->take($limit)->values();
        }

        return $normalized;
    }

    protected function configuredPlans(): Collection
    {
        $configured = collect(config('landing-pricing.plans', []));

        if ($configured->isNotEmpty()) {
            return $configured;
        }

        return $this->databasePlans();
    }

    protected function databasePlans(): Collection
    {
        if (! (bool) config('landing-pricing.database.enabled', true)) {
            return collect();
        }

        $table = config('landing-pricing.database.table', 'lp_pricing_plans');

        try {
            if (! Schema::hasTable($table)) {
                return collect();
            }

            return PricingPlan::query()->ordered()->get();
        } catch (Throwable) {
            return collect();
        }
    }

    protected function normalizePlan(mixed $plan, int $index): ?array
    {
        $name = $this->nullableString(data_get($plan, 'name'));

        if ($name === null) {
            return null;
        }

        $featured = $this->boolValue(data_get($plan, 'is_featured', data_get($plan, 'featured', false)), false);
        $badge = $this->nullableString(data_get($plan, 'badge'));

        return [
            'id' => data_get($plan, 'id'),
            'name' => $name,
            'description' => $this->nullableString(data_get($plan, 'description')),
            'price' => $this->nullableString(data_get($plan, 'price')),
            'currency' => $this->nullableString(data_get($plan, 'currency', config('landing-pricing.currency'))),
            'billing_period_label' => $this->nullableString(
                data_get($plan, 'billing_period_label', data_get($plan, 'period', config('landing-pricing.billing_period_label'))),
            ),
            'features' => $this->featuresValue(data_get($plan, 'features', [])),
            'cta_label' => $this->nullableString(data_get($plan, 'cta_label', config('landing-pricing.cta.default_label'))),
            'cta_url' => PricingUrl::normalize(data_get($plan, 'cta_url', config('landing-pricing.cta.default_url'))),
            'note' => $this->nullableString(data_get($plan, 'note')),
            'badge' => $badge ?: ($featured ? $this->nullableString(config('landing-pricing.featured_label')) : null),
            'sort_order' => (int) data_get($plan, 'sort_order', $index),
            'is_featured' => $featured,
            'is_active' => $this->boolValue(data_get($plan, 'is_active', data_get($plan, 'active', true)), true),
            'position' => $index,
        ];
    }

    protected function featuresValue(mixed $value): array
    {
        if (is_string($value)) {
            $value = preg_split('/\r\n|\r|\n/', $value) ?: [];
        }

        if (! is_iterable($value)) {
            return [];
        }

        return collect($value)
            ->map(function (mixed $feature) {
                if (is_array($feature) || is_object($feature)) {
                    return $this->nullableString(data_get($feature, 'text', data_get($feature, 'label')));
                }

                return $this->nullableString($feature);
            })
            ->filter()
            ->values()
            ->all();
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
