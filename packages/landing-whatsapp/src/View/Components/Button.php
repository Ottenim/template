<?php

namespace Template\LandingWhatsapp\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingWhatsapp\Support\WhatsappUrl;

class Button extends Component
{
    public bool $enabled;

    public string $url;

    public string $label;

    public bool $showText;

    public bool $showIcon;

    public ?string $tooltip;

    public string $ariaLabel;

    public bool $trackingEnabled;

    public string $trackingEvent;

    public bool $useBrandColor;

    public string $brandColor;

    public string $brandTextColor;

    public string $style;

    public array $buttonClasses;

    public function __construct(
        ?string $phone = null,
        ?string $message = null,
        ?string $url = null,
        ?string $label = null,
        mixed $showText = null,
        mixed $showIcon = null,
        ?string $tooltip = null,
        ?string $ariaLabel = null,
        mixed $enabled = null,
        ?string $visibility = null,
        mixed $tracking = null,
        ?string $trackingEvent = null,
        mixed $useBrandColor = null,
    ) {
        $this->enabled = $this->boolValue(config('landing-whatsapp.enabled', true), true)
            && $this->boolValue($enabled, true);

        $this->label = $this->stringValue($label, config('landing-whatsapp.button.label', 'Falar no WhatsApp'));
        $this->showText = $this->boolValue($showText, (bool) config('landing-whatsapp.button.show_text', true));
        $this->showIcon = $this->boolValue($showIcon, (bool) config('landing-whatsapp.button.show_icon', true));
        $this->tooltip = $this->nullableString($tooltip ?? config('landing-whatsapp.button.tooltip'));
        $this->ariaLabel = $this->stringValue(
            $ariaLabel,
            config('landing-whatsapp.button.aria_label', $this->label),
        );
        $this->trackingEnabled = $this->boolValue($tracking, (bool) config('landing-whatsapp.tracking.enabled', false));
        $this->trackingEvent = $this->stringValue(
            $trackingEvent,
            config('landing-whatsapp.tracking.event_name', 'whatsapp_click'),
        );
        $this->useBrandColor = $this->boolValue(
            $useBrandColor,
            (bool) config('landing-whatsapp.style.use_brand_color', false),
        );
        $this->brandColor = $this->cssColor(
            config('landing-whatsapp.style.brand_color', '#25D366'),
            '#25D366',
        );
        $this->brandTextColor = $this->cssColor(
            config('landing-whatsapp.style.brand_text_color', '#ffffff'),
            '#ffffff',
        );
        $this->style = $this->useBrandColor
            ? "--lp-whatsapp-brand-color: {$this->brandColor}; --lp-whatsapp-brand-text-color: {$this->brandTextColor};"
            : '';

        $phone = $phone ?? config('landing-whatsapp.phone');
        $message = $message ?? config('landing-whatsapp.message');
        $this->url = $this->nullableString($url) ?? app(WhatsappUrl::class)->make($phone, $message);

        $visibility = $this->normalizeVisibility($visibility ?? config('landing-whatsapp.visibility', 'all'));
        $this->buttonClasses = array_filter([
            'lp-whatsapp-button',
            'lp-button',
            'lp-button-primary',
            'lp-whatsapp-button-icon-only' => ! $this->showText,
            'lp-whatsapp-button-with-tooltip' => $this->tooltip !== null,
            'lp-whatsapp-button-brand' => $this->useBrandColor,
            $this->visibilityClass($visibility),
        ]);
    }

    public function shouldRender(): bool
    {
        return $this->enabled && $this->url !== '';
    }

    public function render(): View
    {
        return view('landing-whatsapp::components.button');
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

    protected function stringValue(mixed $value, mixed $default): string
    {
        return $this->nullableString($value) ?? $this->nullableString($default) ?? '';
    }

    protected function cssColor(mixed $value, string $default): string
    {
        $value = $this->nullableString($value) ?? $default;

        if (! preg_match('/^[#a-zA-Z0-9\s(),.%+-]+$/', $value)) {
            return $default;
        }

        return $value;
    }

    protected function normalizeVisibility(?string $visibility): string
    {
        return in_array($visibility, ['all', 'mobile', 'desktop'], true) ? $visibility : 'all';
    }

    protected function visibilityClass(string $visibility): ?string
    {
        return $visibility === 'all' ? null : 'lp-whatsapp-visibility-'.$visibility;
    }
}
