<?php

namespace Template\LandingCookieConsent\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingCookieConsent\Support\CookieConsentManager;

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
        $manager = app(CookieConsentManager::class);

        $this->enabled = $manager->enabled($enabled);
        $this->categories = $manager->categories();
        $this->hasOptionalCategories = $manager->hasOptionalCategories();
        $this->policyUrl = $this->nullableString($policyUrl ?? config('landing-cookie-consent.policy_url'));
        $this->position = $this->positionValue($position ?? config('landing-cookie-consent.banner.position', 'bottom'));
        $this->layout = $this->layoutValue($layout ?? config('landing-cookie-consent.banner.layout', 'bar'));
        $this->title = $this->nullableString($title ?? config('landing-cookie-consent.banner.title'));
        $this->message = $this->nullableString($message ?? config('landing-cookie-consent.banner.message'))
            ?? 'Usamos cookies para melhorar sua experiencia.';
        $this->ariaLabel = $this->nullableString(config('landing-cookie-consent.banner.aria_label')) ?? 'Aviso de cookies';
        $this->bannerLabels = [
            'policy' => $this->stringValue(config('landing-cookie-consent.banner.policy_label'), 'Saiba mais'),
            'accept_all' => $this->stringValue(config('landing-cookie-consent.banner.accept_all_label'), 'Aceitar todos'),
            'reject_optional' => $this->stringValue(config('landing-cookie-consent.banner.reject_optional_label'), 'Recusar opcionais'),
            'configure' => $this->stringValue(config('landing-cookie-consent.banner.configure_label'), 'Configurar'),
            'reopen' => $this->stringValue(config('landing-cookie-consent.banner.reopen_label'), 'Privacidade'),
        ];
        $this->modalLabels = [
            'title' => $this->stringValue(config('landing-cookie-consent.modal.title'), 'Gerenciar preferencias de cookies'),
            'description' => $this->stringValue(config('landing-cookie-consent.modal.description'), 'Escolha quais categorias opcionais podem ser usadas.'),
            'save_preferences' => $this->stringValue(config('landing-cookie-consent.modal.save_preferences_label'), 'Salvar preferencias'),
            'accept_all' => $this->stringValue(config('landing-cookie-consent.modal.accept_all_label'), $this->bannerLabels['accept_all']),
            'reject_optional' => $this->stringValue(config('landing-cookie-consent.modal.reject_optional_label'), $this->bannerLabels['reject_optional']),
            'close' => $this->stringValue(config('landing-cookie-consent.modal.close_label'), 'Fechar'),
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
        $position = $this->nullableString($value) ?? 'bottom';

        return in_array($position, ['bottom', 'top', 'bottom-left', 'bottom-right'], true)
            ? $position
            : 'bottom';
    }

    protected function layoutValue(mixed $value): string
    {
        $layout = $this->nullableString($value) ?? 'bar';

        return in_array($layout, ['bar', 'card', 'compact'], true) ? $layout : 'bar';
    }

    protected function stringValue(mixed $value, mixed $default): string
    {
        return $this->nullableString($value) ?? $this->nullableString($default) ?? '';
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
