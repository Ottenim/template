<?php

namespace Template\LandingPricing\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Template\LandingCore\Support\Coerce;
use Template\LandingPricing\Config\PricingConfig;
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
        $configured = collect(PricingConfig::fromConfig()->plans());

        if ($configured->isNotEmpty()) {
            return $configured;
        }

        return $this->databasePlans();
    }

    protected function databasePlans(): Collection
    {
        $config = PricingConfig::fromConfig();

        if (! $config->databaseEnabled()) {
            return collect();
        }

        $table = $config->databaseTable();

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
        $name = Coerce::nullableString(data_get($plan, 'name'));

        if ($name === null) {
            return null;
        }

        $config = PricingConfig::fromConfig();
        $featured = Coerce::bool(data_get($plan, 'is_featured', data_get($plan, 'featured', false)), false);
        $badge = Coerce::nullableString(data_get($plan, 'badge'));

        return [
            'id' => data_get($plan, 'id'),
            'name' => $name,
            'description' => Coerce::nullableString(data_get($plan, 'description')),
            'price' => Coerce::nullableString(data_get($plan, 'price')),
            'currency' => Coerce::nullableString(data_get($plan, 'currency', $config->currency())),
            'billing_period_label' => Coerce::nullableString(
                data_get($plan, 'billing_period_label', data_get($plan, 'period', $config->billingPeriodLabel())),
            ),
            'features' => $this->featuresValue(data_get($plan, 'features', [])),
            'cta_label' => Coerce::nullableString(data_get($plan, 'cta_label', $config->ctaDefaultLabel())),
            'cta_url' => PricingUrl::normalize(data_get($plan, 'cta_url', $config->ctaDefaultUrl())),
            'note' => Coerce::nullableString(data_get($plan, 'note')),
            'badge' => $badge ?: ($featured ? $config->featuredLabel() : null),
            'sort_order' => (int) data_get($plan, 'sort_order', $index),
            'is_featured' => $featured,
            'is_active' => Coerce::bool(data_get($plan, 'is_active', data_get($plan, 'active', true)), true),
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
                    return Coerce::nullableString(data_get($feature, 'text', data_get($feature, 'label')));
                }

                return Coerce::nullableString($feature);
            })
            ->filter()
            ->values()
            ->all();
    }
}
