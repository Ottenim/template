<?php

namespace Template\LandingFaq\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingFaq\Support\FaqItems;

class Section extends Component
{
    public bool $enabled;

    public ?string $eyebrow;

    public ?string $title;

    public ?string $subtitle;

    public string $layout;

    public bool $showCategories;

    public bool $defaultOpenFirstItem;

    public Collection $items;

    public Collection $categorizedItems;

    public ?string $schemaJson;

    public function __construct(
        mixed $items = null,
        ?string $eyebrow = null,
        ?string $title = null,
        ?string $subtitle = null,
        ?string $layout = null,
        mixed $showCategories = null,
        mixed $defaultOpenFirstItem = null,
        mixed $limit = null,
        mixed $enabled = null,
    ) {
        $this->enabled = $this->boolValue(config('landing-faq.enabled', true), true)
            && $this->boolValue(config('landing-faq.section.enabled', true), true)
            && $this->boolValue($enabled, true);

        $this->eyebrow = $this->nullableString($eyebrow ?? config('landing-faq.section.eyebrow'));
        $this->title = $this->nullableString($title ?? config('landing-faq.section.title'));
        $this->subtitle = $this->nullableString($subtitle ?? config('landing-faq.section.subtitle'));
        $this->layout = $this->layoutValue($layout ?? config('landing-faq.layout', 'accordion'));
        $this->showCategories = $this->boolValue($showCategories, (bool) config('landing-faq.show_categories', false));
        $this->defaultOpenFirstItem = $this->boolValue(
            $defaultOpenFirstItem,
            (bool) config('landing-faq.default_open_first_item', true),
        );

        $this->items = app(FaqItems::class)->publicItems($items, $this->limitValue($limit));
        $this->categorizedItems = $this->items->groupBy(
            fn (array $item) => $item['category'] ?: 'Geral',
        );
        $this->schemaJson = (bool) config('landing-faq.schema.enabled', true)
            ? app(FaqItems::class)->schemaJson($this->items)
            : null;
    }

    public function shouldRender(): bool
    {
        return $this->enabled && $this->items->isNotEmpty();
    }

    public function render(): View
    {
        return view('landing-faq::components.section');
    }

    protected function layoutValue(mixed $value): string
    {
        $layout = $this->nullableString($value) ?? 'accordion';

        return in_array($layout, ['accordion', 'grid', 'compact'], true) ? $layout : 'accordion';
    }

    protected function limitValue(mixed $value): ?int
    {
        $value ??= config('landing-faq.limit');

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
