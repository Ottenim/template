<?php

namespace Template\LandingTestimonials\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Template\LandingCore\Support\Coerce;
use Template\LandingTestimonials\Config\TestimonialsConfig;
use Template\LandingTestimonials\Models\Testimonial;
use Throwable;

class Testimonials
{
    public function publicItems(mixed $items = null, ?int $limit = null, bool $activeOnly = true): Collection
    {
        $items = $items !== null ? collect($items) : $this->configuredItems();

        $normalized = $items
            ->values()
            ->map(fn (mixed $item, int $index) => $this->normalizeItem($item, $index))
            ->filter()
            ->when($activeOnly, fn (Collection $items) => $items->filter(fn (array $item) => $item['is_active']))
            ->sortBy([
                ['is_featured', 'desc'],
                ['sort_order', 'asc'],
                ['position', 'asc'],
            ])
            ->values()
            ->map(function (array $item) {
                unset($item['position']);

                return $item;
            });

        if ($limit !== null && $limit > 0) {
            return $normalized->take($limit)->values();
        }

        return $normalized;
    }

    protected function configuredItems(): Collection
    {
        $configured = collect(TestimonialsConfig::fromConfig()->items());

        if ($configured->isNotEmpty()) {
            return $configured;
        }

        return $this->databaseItems();
    }

    protected function databaseItems(): Collection
    {
        $config = TestimonialsConfig::fromConfig();

        if (! $config->databaseEnabled()) {
            return collect();
        }

        $table = $config->databaseTable();

        try {
            if (! Schema::hasTable($table)) {
                return collect();
            }

            return Testimonial::query()->ordered()->get();
        } catch (Throwable) {
            return collect();
        }
    }

    protected function normalizeItem(mixed $item, int $index): ?array
    {
        $name = Coerce::nullableString(data_get($item, 'name'));
        $text = Coerce::nullableString(data_get($item, 'text'));

        if ($name === null || $text === null) {
            return null;
        }

        return [
            'id' => data_get($item, 'id'),
            'name' => $name,
            'text' => $text,
            'role' => Coerce::nullableString(data_get($item, 'role')),
            'company' => Coerce::nullableString(data_get($item, 'company')),
            'avatar' => Coerce::nullableString(data_get($item, 'avatar')),
            'logo' => Coerce::nullableString(data_get($item, 'logo')),
            'rating' => $this->ratingValue(data_get($item, 'rating')),
            'sort_order' => (int) data_get($item, 'sort_order', $index),
            'is_featured' => Coerce::bool(data_get($item, 'is_featured', data_get($item, 'featured', false)), false),
            'is_active' => Coerce::bool(data_get($item, 'is_active', data_get($item, 'active', true)), true),
            'position' => $index,
        ];
    }

    protected function ratingValue(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $rating = (int) $value;

        return $rating >= 1 && $rating <= 5 ? $rating : null;
    }
}
