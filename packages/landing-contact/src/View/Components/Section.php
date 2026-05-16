<?php

namespace Template\LandingContact\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

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
        $this->enabled = $this->boolValue(config('landing-contact.enabled', true), true)
            && $this->boolValue(config('landing-contact.section.enabled', true), true)
            && $this->boolValue($enabled, true);
        $this->eyebrow = $this->nullableString($eyebrow ?? config('landing-contact.section.eyebrow'));
        $this->title = $this->nullableString($title ?? config('landing-contact.section.title'));
        $this->subtitle = $this->nullableString($subtitle ?? config('landing-contact.section.subtitle'));
        $this->buttonLabel = $this->nullableString($buttonLabel);
    }

    public function shouldRender(): bool
    {
        return $this->enabled;
    }

    public function render(): View
    {
        return view('landing-contact::components.section');
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
