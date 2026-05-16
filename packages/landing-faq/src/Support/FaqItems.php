<?php

namespace Template\LandingFaq\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Template\LandingFaq\Models\FaqItem;
use Throwable;

class FaqItems
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

    public function schemaJson(Collection $items): ?string
    {
        if ($items->isEmpty()) {
            return null;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $items->map(fn (array $item) => [
                '@type' => 'Question',
                'name' => $item['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $item['answer'],
                ],
            ])->values()->all(),
        ];

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: null;
    }

    protected function configuredItems(): Collection
    {
        $configured = collect(config('landing-faq.items', []));

        if ($configured->isNotEmpty()) {
            return $configured;
        }

        return $this->databaseItems();
    }

    protected function databaseItems(): Collection
    {
        if (! (bool) config('landing-faq.database.enabled', true)) {
            return collect();
        }

        $table = config('landing-faq.database.table', 'lp_faq_items');

        try {
            if (! Schema::hasTable($table)) {
                return collect();
            }

            return FaqItem::query()->ordered()->get();
        } catch (Throwable) {
            return collect();
        }
    }

    protected function normalizeItem(mixed $item, int $index): ?array
    {
        $question = $this->nullableString(data_get($item, 'question'));
        $answer = $this->nullableString(data_get($item, 'answer'));

        if ($question === null || $answer === null) {
            return null;
        }

        return [
            'id' => data_get($item, 'id'),
            'question' => $question,
            'answer' => $answer,
            'category' => $this->nullableString(data_get($item, 'category')),
            'sort_order' => (int) data_get($item, 'sort_order', $index),
            'is_active' => $this->boolValue(data_get($item, 'is_active', data_get($item, 'active', true)), true),
            'position' => $index,
        ];
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
