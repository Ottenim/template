<?php

namespace Template\LandingWhatsapp\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingCore\Support\Coerce;
use Template\LandingWhatsapp\Config\WhatsappConfig;
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
        $config = app(WhatsappConfig::class);

        $this->enabled = $config->enabled() && Coerce::bool($enabled, true);

        $this->label = Coerce::string($label, $config->buttonLabel());
        $this->showText = Coerce::bool($showText, $config->buttonShowText());
        $this->showIcon = Coerce::bool($showIcon, $config->buttonShowIcon());
        $this->tooltip = Coerce::nullableString($tooltip ?? $config->buttonTooltip());
        $this->ariaLabel = Coerce::string($ariaLabel, $config->buttonAriaLabel() ?? $this->label);
        $this->trackingEnabled = Coerce::bool($tracking, $config->trackingEnabled());
        $this->trackingEvent = Coerce::string($trackingEvent, $config->trackingEventName());
        $this->useBrandColor = Coerce::bool($useBrandColor, $config->styleUseBrandColor());
        $this->brandColor = $this->cssColor($config->styleBrandColor(), '#25D366');
        $this->brandTextColor = $this->cssColor($config->styleBrandTextColor(), '#ffffff');
        $this->style = $this->useBrandColor
            ? "--lp-whatsapp-brand-color: {$this->brandColor}; --lp-whatsapp-brand-text-color: {$this->brandTextColor};"
            : '';

        $phone = $phone ?? $config->phone();
        $message = $message ?? $config->message();
        $this->url = Coerce::nullableString($url) ?? app(WhatsappUrl::class)->make($phone, $message);

        $visibility = $this->normalizeVisibility($visibility ?? $config->visibility());
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

    protected function cssColor(mixed $value, string $default): string
    {
        $value = Coerce::nullableString($value) ?? $default;

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
