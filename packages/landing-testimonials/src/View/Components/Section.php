<?php

namespace Template\LandingTestimonials\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingCore\Support\Coerce;
use Template\LandingTestimonials\Config\TestimonialsConfig;
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
        $config = app(TestimonialsConfig::class);

        $this->enabled = $config->enabled()
            && $config->sectionEnabled()
            && Coerce::bool($enabled, true);

        $this->eyebrow = Coerce::nullableString($eyebrow ?? $config->sectionEyebrow());
        $this->title = Coerce::nullableString($title ?? $config->sectionTitle());
        $this->subtitle = Coerce::nullableString($subtitle ?? $config->sectionSubtitle());
        $this->layout = $this->layoutValue($layout ?? $config->layout());
        $this->columns = $this->columnsValue($columns ?? $config->columns());
        $this->showAvatar = Coerce::bool($showAvatar, $config->showAvatar());
        $this->showRating = Coerce::bool($showRating, $config->showRating());
        $this->showCompany = Coerce::bool($showCompany, $config->showCompany());
        $this->showLogo = Coerce::bool($showLogo, $config->showLogo());

        $this->testimonials = app(Testimonials::class)->publicItems($items, $this->limitValue($limit, $config));
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
        $layout = Coerce::string($value, 'grid');

        return in_array($layout, ['grid', 'featured', 'carousel', 'list'], true) ? $layout : 'grid';
    }

    protected function columnsValue(mixed $value): int
    {
        $columns = (int) ($value ?: 3);

        return max(1, min(4, $columns));
    }

    protected function limitValue(mixed $value, TestimonialsConfig $config): ?int
    {
        $value ??= $config->limit();

        if ($value === null || $value === '') {
            return null;
        }

        $limit = (int) $value;

        return $limit > 0 ? $limit : null;
    }
}
