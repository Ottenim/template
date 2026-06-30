<?php

namespace Template\LandingLeadCapture\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingCore\Support\Coerce;
use Template\LandingLeadCapture\Config\LeadCaptureConfig;

class Section extends Component
{
    public bool $enabled;

    public ?string $eyebrow;

    public ?string $title;

    public ?string $subtitle;

    public ?string $benefit;

    public ?string $buttonLabel;

    public string $variant;

    public ?string $source;

    public ?string $campaign;

    public ?string $tag;

    public function __construct(
        ?string $eyebrow = null,
        ?string $title = null,
        ?string $subtitle = null,
        ?string $benefit = null,
        ?string $buttonLabel = null,
        ?string $variant = null,
        ?string $source = null,
        ?string $campaign = null,
        ?string $tag = null,
        mixed $enabled = null,
    ) {
        $config = app(LeadCaptureConfig::class);

        $this->enabled = $config->enabled()
            && $config->sectionEnabled()
            && Coerce::bool($enabled, true);
        $this->eyebrow = Coerce::nullableString($eyebrow ?? $config->sectionEyebrow());
        $this->title = Coerce::nullableString($title ?? $config->ctaTitle());
        $this->subtitle = Coerce::nullableString($subtitle ?? $config->ctaSubtitle());
        $this->benefit = Coerce::nullableString($benefit ?? $config->sectionBenefit());
        $this->buttonLabel = Coerce::nullableString($buttonLabel ?? $config->ctaButtonLabel());
        $this->variant = $this->variantValue($variant ?? $config->variant());
        $this->source = Coerce::nullableString($source ?? $config->leadSource());
        $this->campaign = Coerce::nullableString($campaign ?? $config->leadCampaign());
        $this->tag = Coerce::nullableString($tag ?? $config->leadTag());
    }

    public function shouldRender(): bool
    {
        return $this->enabled;
    }

    public function render(): View
    {
        return view('landing-lead-capture::components.section');
    }

    protected function variantValue(mixed $value): string
    {
        $variant = Coerce::string($value, 'inline');

        return in_array($variant, ['inline', 'card', 'bar'], true) ? $variant : 'inline';
    }
}
