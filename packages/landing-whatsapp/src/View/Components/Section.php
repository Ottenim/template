<?php

namespace Template\LandingWhatsapp\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingCore\Support\Coerce;
use Template\LandingWhatsapp\Config\WhatsappConfig;
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
        $config = app(WhatsappConfig::class);

        $this->enabled = $config->enabled()
            && $config->sectionEnabled()
            && Coerce::bool($enabled, true);

        $this->eyebrow = Coerce::nullableString($eyebrow ?? $config->sectionEyebrow());
        $this->title = Coerce::nullableString($title ?? $config->sectionTitle());
        $this->subtitle = Coerce::nullableString($subtitle ?? $config->sectionSubtitle());
        $this->text = Coerce::nullableString($text ?? $config->sectionText());
        $this->card = Coerce::bool($card, $config->sectionCard());
        $this->phone = $phone ?? $config->phone();
        $this->message = $message ?? $config->message();
        $this->url = Coerce::nullableString($url) ?? app(WhatsappUrl::class)->make($this->phone, $this->message);
        $this->buttonLabel = Coerce::string($buttonLabel, $config->buttonLabel());
        $this->showIcon = Coerce::bool($showIcon, $config->buttonShowIcon());
        $this->tooltip = Coerce::nullableString($tooltip ?? $config->buttonTooltip());
    }

    public function shouldRender(): bool
    {
        return $this->enabled && $this->url !== '';
    }

    public function render(): View
    {
        return view('landing-whatsapp::components.section');
    }
}
