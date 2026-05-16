<?php

namespace Template\LandingWhatsapp\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingWhatsapp\Support\WhatsappUrl;

class Section extends Component
{
    public bool $enabled;

    public ?string $eyebrow;

    public ?string $title;

    public ?string $subtitle;

    public ?string $text;

    public bool $card;

    public ?string $phone;

    public ?string $message;

    public string $url;

    public string $buttonLabel;

    public bool $showIcon;

    public ?string $tooltip;

    public function __construct(
        ?string $eyebrow = null,
        ?string $title = null,
        ?string $subtitle = null,
        ?string $text = null,
        mixed $card = null,
        ?string $phone = null,
        ?string $message = null,
        ?string $url = null,
        ?string $buttonLabel = null,
        mixed $showIcon = null,
        ?string $tooltip = null,
        mixed $enabled = null,
    ) {
        $this->enabled = $this->boolValue(config('landing-whatsapp.enabled', true), true)
            && $this->boolValue(config('landing-whatsapp.section.enabled', true), true)
            && $this->boolValue($enabled, true);

        $this->eyebrow = $this->nullableString($eyebrow ?? config('landing-whatsapp.section.eyebrow'));
        $this->title = $this->nullableString($title ?? config('landing-whatsapp.section.title'));
        $this->subtitle = $this->nullableString($subtitle ?? config('landing-whatsapp.section.subtitle'));
        $this->text = $this->nullableString($text ?? config('landing-whatsapp.section.text'));
        $this->card = $this->boolValue($card, (bool) config('landing-whatsapp.section.card', true));
        $this->phone = $phone ?? config('landing-whatsapp.phone');
        $this->message = $message ?? config('landing-whatsapp.message');
        $this->url = $this->nullableString($url) ?? app(WhatsappUrl::class)->make($this->phone, $this->message);
        $this->buttonLabel = $this->stringValue($buttonLabel, config('landing-whatsapp.button.label', 'Falar no WhatsApp'));
        $this->showIcon = $this->boolValue($showIcon, (bool) config('landing-whatsapp.button.show_icon', true));
        $this->tooltip = $this->nullableString($tooltip ?? config('landing-whatsapp.button.tooltip'));
    }

    public function shouldRender(): bool
    {
        return $this->enabled && $this->url !== '';
    }

    public function render(): View
    {
        return view('landing-whatsapp::components.section');
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
}
