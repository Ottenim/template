<?php

namespace Template\LandingFaq\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Template\LandingCore\Support\Coerce;
use Template\LandingFaq\Config\FaqConfig;
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

        return json_encode(
            $schema,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        ) ?: null;
    }

    protected function configuredItems(): Collection
    {
        $configured = collect(FaqConfig::fromConfig()->items());

        if ($configured->isNotEmpty()) {
            return $configured;
        }

        return $this->databaseItems();
    }

    protected function databaseItems(): Collection
    {
        $config = FaqConfig::fromConfig();

        if (! $config->databaseEnabled()) {
            return collect();
        }

        $table = $config->databaseTable();

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
        $question = Coerce::nullableString(data_get($item, 'question'));
        $answer = Coerce::nullableString(data_get($item, 'answer'));

        if ($question === null || $answer === null) {
            return null;
        }

        return [
            'id' => data_get($item, 'id'),
            'question' => $question,
            'answer' => $answer,
            'category' => Coerce::nullableString(data_get($item, 'category')),
            'sort_order' => (int) data_get($item, 'sort_order', $index),
            'is_active' => Coerce::bool(data_get($item, 'is_active', data_get($item, 'active', true)), true),
            'position' => $index,
        ];
    }
}
