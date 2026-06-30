<?php

namespace Template\LandingWhatsapp\Config;

use Template\LandingCore\Analytics\LandingEvent;
use Template\LandingCore\Config\ModuleConfig;

/**
 * Config tipada do módulo de WhatsApp. Concentra as chaves
 * config('landing-whatsapp.*') antes espalhadas pelos componentes Button,
 * FloatingButton e Section.
 *
 * Allowlists de apresentação (visibility, position) e validação de cor
 * permanecem nos componentes, que conhecem o domínio visual.
 */
class WhatsappConfig extends ModuleConfig
{
    public static function key(): string
    {
        return 'landing-whatsapp';
    }

    public function enabled(): bool
    {
        return $this->bool('enabled', true);
    }

    public function phone(): ?string
    {
        return $this->nullableString('phone');
    }

    public function message(): ?string
    {
        return $this->nullableString('message');
    }

    public function visibility(): string
    {
        return $this->string('visibility', 'all');
    }

    public function buttonLabel(): string
    {
        return $this->string('button.label', 'Falar no WhatsApp');
    }

    public function buttonAriaLabel(): ?string
    {
        return $this->nullableString('button.aria_label');
    }

    public function buttonShowText(): bool
    {
        return $this->bool('button.show_text', true);
    }

    public function buttonShowIcon(): bool
    {
        return $this->bool('button.show_icon', true);
    }

    public function buttonTooltip(): ?string
    {
        return $this->nullableString('button.tooltip');
    }

    public function floatingEnabled(): bool
    {
        return $this->bool('floating.enabled', true);
    }

    public function floatingPosition(): string
    {
        return $this->string('floating.position', 'bottom-right');
    }

    public function floatingShowText(): bool
    {
        return $this->bool('floating.show_text', false);
    }

    public function floatingShowIcon(): bool
    {
        return $this->bool('floating.show_icon', true);
    }

    public function floatingTooltip(): ?string
    {
        return $this->nullableString('floating.tooltip');
    }

    public function floatingVisibility(): string
    {
        return $this->string('floating.visibility', 'all');
    }

    public function floatingMobileBar(): bool
    {
        return $this->bool('floating.mobile_bar', false);
    }

    public function sectionEnabled(): bool
    {
        return $this->bool('section.enabled', true);
    }

    public function sectionEyebrow(): ?string
    {
        return $this->nullableString('section.eyebrow');
    }

    public function sectionTitle(): ?string
    {
        return $this->nullableString('section.title');
    }

    public function sectionSubtitle(): ?string
    {
        return $this->nullableString('section.subtitle');
    }

    public function sectionText(): ?string
    {
        return $this->nullableString('section.text');
    }

    public function sectionCard(): bool
    {
        return $this->bool('section.card', true);
    }

    public function trackingEnabled(): bool
    {
        return $this->bool('tracking.enabled', false);
    }

    public function trackingEventName(): string
    {
        return $this->string('tracking.event_name', LandingEvent::WhatsappClick->value);
    }

    public function styleUseBrandColor(): bool
    {
        return $this->bool('style.use_brand_color', false);
    }

    public function styleBrandColor(): string
    {
        return $this->string('style.brand_color', '#25D366');
    }

    public function styleBrandTextColor(): string
    {
        return $this->string('style.brand_text_color', '#ffffff');
    }
}
