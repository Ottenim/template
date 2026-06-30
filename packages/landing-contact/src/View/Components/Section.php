<?php

namespace Template\LandingContact\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingContact\Config\ContactConfig;
use Template\LandingCore\Support\Coerce;

class Section extends Component
{
    public bool $enabled;

    public ?string $eyebrow;

    public ?string $title;

    public ?string $subtitle;

    public ?string $buttonLabel;

    public function __construct(
        ?string $eyebrow = null,
        ?string $title = null,
        ?string $subtitle = null,
        ?string $buttonLabel = null,
        mixed $enabled = null,
    ) {
        $config = app(ContactConfig::class);

        $this->enabled = $config->enabled()
            && $config->sectionEnabled()
            && Coerce::bool($enabled, true);
        $this->eyebrow = Coerce::nullableString($eyebrow) ?? $config->sectionEyebrow();
        $this->title = Coerce::nullableString($title) ?? $config->sectionTitle();
        $this->subtitle = Coerce::nullableString($subtitle) ?? $config->sectionSubtitle();
        $this->buttonLabel = Coerce::nullableString($buttonLabel);
    }

    public function shouldRender(): bool
    {
        return $this->enabled;
    }

    public function render(): View
    {
        return view('landing-contact::components.section');
    }
}
