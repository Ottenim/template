<?php

namespace Template\LandingTestimonials\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingTestimonials\Support\Testimonials;

class Section extends Component
{
    public bool $enabled;

    public ?string $eyebrow;

    public ?string $title;

    public ?string $subtitle;

    public string $layout;

    public int $columns;

    public bool $showAvatar;

    public bool $showRating;

    public bool $showCompany;

    public bool $showLogo;

    public Collection $testimonials;

    public function __construct(
        mixed $items = null,
        ?string $eyebrow = null,
        ?string $title = null,
        ?string $subtitle = null,
        ?string $layout = null,
        mixed $columns = null,
        mixed $showAvatar = null,
        mixed $showRating = null,
        mixed $showCompany = null,
        mixed $showLogo = null,
        mixed $limit = null,
        mixed $enabled = null,
    ) {
        $this->enabled = $this->boolValue(config('landing-testimonials.enabled', true), true)
            && $this->boolValue(config('landing-testimonials.section.enabled', true), true)
            && $this->boolValue($enabled, true);

        $this->eyebrow = $this->nullableString($eyebrow ?? config('landing-testimonials.section.eyebrow'));
        $this->title = $this->nullableString($title ?? config('landing-testimonials.section.title'));
        $this->subtitle = $this->nullableString($subtitle ?? config('landing-testimonials.section.subtitle'));
        $this->layout = $this->layoutValue($layout ?? config('landing-testimonials.layout', 'grid'));
        $this->columns = $this->columnsValue($columns ?? config('landing-testimonials.columns', 3));
        $this->showAvatar = $this->boolValue($showAvatar, (bool) config('landing-testimonials.show_avatar', true));
        $this->showRating = $this->boolValue($showRating, (bool) config('landing-testimonials.show_rating', false));
        $this->showCompany = $this->boolValue($showCompany, (bool) config('landing-testimonials.show_company', true));
        $this->showLogo = $this->boolValue($showLogo, (bool) config('landing-testimonials.show_logo', true));

        $this->testimonials = app(Testimonials::class)->publicItems($items, $this->limitValue($limit));
    }

    public function shouldRender(): bool
    {
        return $this->enabled && $this->testimonials->isNotEmpty();
    }

    public function render(): View
    {
        return view('landing-testimonials::components.section');
    }

    protected function layoutValue(mixed $value): string
    {
        $layout = $this->nullableString($value) ?? 'grid';

        return in_array($layout, ['grid', 'featured', 'carousel', 'list'], true) ? $layout : 'grid';
    }

    protected function columnsValue(mixed $value): int
    {
        $columns = (int) ($value ?: 3);

        return max(1, min(4, $columns));
    }

    protected function limitValue(mixed $value): ?int
    {
        $value ??= config('landing-testimonials.limit');

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
