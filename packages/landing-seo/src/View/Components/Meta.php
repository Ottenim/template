<?php

namespace Template\LandingSeo\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingCore\Support\Coerce;
use Template\LandingSeo\Config\SeoConfig;
use Template\LandingSeo\Support\SeoManager;

class Meta extends Component
{
    public bool $enabled;

    public bool $includeTitle;

    public array $data;

    public ?string $schemaJson;

    public function __construct(
        mixed $page = null,
        ?string $title = null,
        ?string $description = null,
        ?string $canonical = null,
        ?string $robots = null,
        ?string $image = null,
        ?string $type = null,
        mixed $schema = null,
        mixed $includeTitle = null,
    ) {
        $this->enabled = app(SeoConfig::class)->enabled();
        $this->includeTitle = Coerce::bool($includeTitle, true);

        $overrides = array_filter([
            'title' => $title,
            'description' => $description,
            'canonical_url' => $canonical,
            'robots' => $robots,
            'image_url' => $image,
            'og_type' => $type,
            'schema' => $schema,
        ], fn (mixed $value) => $value !== null);

        $manager = app(SeoManager::class);
        $this->data = $manager->resolve($page, $overrides);
        $this->schemaJson = $manager->schemaJson($this->data);
    }

    public function shouldRender(): bool
    {
        return $this->enabled;
    }

    public function render(): View
    {
        return view('landing-seo::components.meta');
    }
}
