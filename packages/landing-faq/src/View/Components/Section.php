<?php

namespace Template\LandingFaq\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingCore\Support\Coerce;
use Template\LandingFaq\Config\FaqConfig;
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
        $config = app(FaqConfig::class);

        $this->enabled = $config->enabled()
            && $config->sectionEnabled()
            && Coerce::bool($enabled, true);

        $this->eyebrow = Coerce::nullableString($eyebrow ?? $config->sectionEyebrow());
        $this->title = Coerce::nullableString($title ?? $config->sectionTitle());
        $this->subtitle = Coerce::nullableString($subtitle ?? $config->sectionSubtitle());
        $this->layout = $this->layoutValue($layout ?? $config->layout());
        $this->showCategories = Coerce::bool($showCategories, $config->showCategories());
        $this->defaultOpenFirstItem = Coerce::bool($defaultOpenFirstItem, $config->defaultOpenFirstItem());

        $this->items = app(FaqItems::class)->publicItems($items, $this->limitValue($limit, $config));
        $this->categorizedItems = $this->items->groupBy(
            fn (array $item) => $item['category'] ?: 'Geral',
        );
        $this->schemaJson = $config->schemaEnabled()
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
        $layout = Coerce::string($value, 'accordion');

        return in_array($layout, ['accordion', 'grid', 'compact'], true) ? $layout : 'accordion';
    }

    protected function limitValue(mixed $value, FaqConfig $config): ?int
    {
        $value ??= $config->limit();

        if ($value === null || $value === '') {
            return null;
        }

        $limit = (int) $value;

        return $limit > 0 ? $limit : null;
    }
}
