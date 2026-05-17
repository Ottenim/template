<?php

namespace Template\LandingTestimonials\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
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
        $configured = collect(config('landing-testimonials.items', []));

        if ($configured->isNotEmpty()) {
            return $configured;
        }

        return $this->databaseItems();
    }

    protected function databaseItems(): Collection
    {
        if (! (bool) config('landing-testimonials.database.enabled', true)) {
            return collect();
        }

        $table = config('landing-testimonials.database.table', 'lp_testimonials');

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
        $name = $this->nullableString(data_get($item, 'name'));
        $text = $this->nullableString(data_get($item, 'text'));

        if ($name === null || $text === null) {
            return null;
        }

        return [
            'id' => data_get($item, 'id'),
            'name' => $name,
            'text' => $text,
            'role' => $this->nullableString(data_get($item, 'role')),
            'company' => $this->nullableString(data_get($item, 'company')),
            'avatar' => $this->nullableString(data_get($item, 'avatar')),
            'logo' => $this->nullableString(data_get($item, 'logo')),
            'rating' => $this->ratingValue(data_get($item, 'rating')),
            'sort_order' => (int) data_get($item, 'sort_order', $index),
            'is_featured' => $this->boolValue(data_get($item, 'is_featured', data_get($item, 'featured', false)), false),
            'is_active' => $this->boolValue(data_get($item, 'is_active', data_get($item, 'active', true)), true),
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

    protected function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
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
}
