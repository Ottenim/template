<?php

namespace Template\LandingWhatsapp\View\Components;

use Illuminate\View\View;

class FloatingButton extends Button
{
    public bool $floatingEnabled;

    public string $position;

    public bool $mobileBar;

    public array $wrapperClasses;

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
        ?string $position = null,
        mixed $mobileBar = null,
    ) {
        parent::__construct(
            phone: $phone,
            message: $message,
            url: $url,
            label: $label,
            showText: $showText ?? config('landing-whatsapp.floating.show_text', false),
            showIcon: $showIcon ?? config('landing-whatsapp.floating.show_icon', true),
            tooltip: $tooltip ?? config('landing-whatsapp.floating.tooltip'),
            ariaLabel: $ariaLabel,
            enabled: $enabled,
            visibility: $visibility ?? config('landing-whatsapp.floating.visibility', 'all'),
            tracking: $tracking,
            trackingEvent: $trackingEvent,
            useBrandColor: $useBrandColor,
        );

        $this->floatingEnabled = $this->boolValue(config('landing-whatsapp.floating.enabled', true), true);
        $this->position = $this->normalizePosition($position ?? config('landing-whatsapp.floating.position', 'bottom-right'));
        $this->mobileBar = $this->boolValue($mobileBar, (bool) config('landing-whatsapp.floating.mobile_bar', false));
        $visibility = $this->normalizeVisibility($visibility ?? config('landing-whatsapp.floating.visibility', 'all'));

        $this->buttonClasses[] = 'lp-whatsapp-floating-button';
        $this->wrapperClasses = array_filter([
            'lp-whatsapp-floating',
            'lp-whatsapp-position-'.$this->position,
            'lp-whatsapp-mobile-bar' => $this->mobileBar,
            $this->visibilityClass($visibility),
        ]);
    }

    public function shouldRender(): bool
    {
        return $this->floatingEnabled && parent::shouldRender();
    }

    public function render(): View
    {
        return view('landing-whatsapp::components.floating-button');
    }

    protected function normalizePosition(?string $position): string
    {
        return in_array($position, ['bottom-right', 'bottom-left', 'top-right', 'top-left'], true)
            ? $position
            : 'bottom-right';
    }
}
