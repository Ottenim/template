<?php

namespace Template\LandingLeadCapture\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

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
        $this->enabled = $this->boolValue(config('landing-lead-capture.enabled', true), true)
            && $this->boolValue(config('landing-lead-capture.section.enabled', true), true)
            && $this->boolValue($enabled, true);
        $this->eyebrow = $this->nullableString($eyebrow ?? config('landing-lead-capture.section.eyebrow'));
        $this->title = $this->nullableString($title ?? config('landing-lead-capture.cta.title'));
        $this->subtitle = $this->nullableString($subtitle ?? config('landing-lead-capture.cta.subtitle'));
        $this->benefit = $this->nullableString($benefit ?? config('landing-lead-capture.section.benefit'));
        $this->buttonLabel = $this->nullableString($buttonLabel ?? config('landing-lead-capture.cta.button_label'));
        $this->variant = $this->variantValue($variant ?? config('landing-lead-capture.variant', 'inline'));
        $this->source = $this->nullableString($source ?? config('landing-lead-capture.lead.source'));
        $this->campaign = $this->nullableString($campaign ?? config('landing-lead-capture.lead.campaign'));
        $this->tag = $this->nullableString($tag ?? config('landing-lead-capture.lead.tag'));
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
        $variant = $this->nullableString($value) ?? 'inline';

        return in_array($variant, ['inline', 'card', 'bar'], true) ? $variant : 'inline';
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
