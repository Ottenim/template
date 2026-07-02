<?php

namespace Template\LandingCookieConsent\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingCookieConsent\Config\CookieConsentConfig;
use Template\LandingCookieConsent\Support\CookieConsentManager;
use Template\LandingCore\Support\Coerce;

class Banner extends Component
{
    public bool $enabled;

    public array $categories;

    public bool $hasOptionalCategories;

    public ?string $policyUrl;

    public string $position;

    public string $layout;

    public ?string $title;

    public string $message;

    public array $bannerLabels;

    public array $modalLabels;

    public string $ariaLabel;

    public string $configJson;

    public function __construct(
        ?string $position = null,
        ?string $layout = null,
        ?string $title = null,
        ?string $message = null,
        ?string $policyUrl = null,
        mixed $enabled = null,
    ) {
        $config = app(CookieConsentConfig::class);
        $manager = app(CookieConsentManager::class);

        $this->enabled = $manager->enabled($enabled);
        $this->categories = $manager->categories();
        $this->hasOptionalCategories = $manager->hasOptionalCategories();
        $this->policyUrl = Coerce::nullableString($policyUrl ?? $config->policyUrl());
        $this->position = $this->positionValue($position ?? $config->bannerPosition());
        $this->layout = $this->layoutValue($layout ?? $config->bannerLayout());
        $this->title = Coerce::nullableString($title ?? $config->bannerTitle());
        $this->message = Coerce::nullableString($message ?? $config->bannerMessage())
            ?? 'Usamos cookies para melhorar sua experiencia.';
        $this->ariaLabel = $config->bannerAriaLabel();
        $this->bannerLabels = [
            'policy' => $config->bannerPolicyLabel(),
            'accept_all' => $config->bannerAcceptAllLabel(),
            'reject_optional' => $config->bannerRejectOptionalLabel(),
            'configure' => $config->bannerConfigureLabel(),
            'reopen' => $config->bannerReopenLabel(),
        ];
        $this->modalLabels = [
            'title' => $config->modalTitle(),
            'description' => $config->modalDescription(),
            'save_preferences' => $config->modalSavePreferencesLabel(),
            'accept_all' => $config->modalAcceptAllLabel() ?? $this->bannerLabels['accept_all'],
            'reject_optional' => $config->modalRejectOptionalLabel() ?? $this->bannerLabels['reject_optional'],
            'close' => $config->modalCloseLabel(),
        ];
        $this->configJson = $manager->json($manager->clientConfig());
    }

    public function shouldRender(): bool
    {
        return $this->enabled && $this->categories !== [];
    }

    public function render(): View
    {
        return view('landing-cookie-consent::components.banner');
    }

    protected function positionValue(mixed $value): string
    {
        $position = Coerce::string($value, 'bottom');

        return in_array($position, ['bottom', 'top', 'bottom-left', 'bottom-right'], true)
            ? $position
            : 'bottom';
    }

    protected function layoutValue(mixed $value): string
    {
        $layout = Coerce::string($value, 'bar');

        return in_array($layout, ['bar', 'card', 'compact'], true) ? $layout : 'bar';
    }
}
