<?php

namespace Template\LandingCookieConsent\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Template\LandingCookieConsent\Config\CookieConsentConfig;
use Template\LandingCore\Support\Coerce;

class CookieConsentManager
{
    public function enabled(mixed $override = null): bool
    {
        $config = CookieConsentConfig::fromConfig();

        return $config->enabled()
            && $config->bannerEnabled()
            && Coerce::bool($override, true);
    }

    public function storageKey(): string
    {
        return CookieConsentConfig::fromConfig()->storageKey();
    }

    public function policyUrl(): ?string
    {
        return CookieConsentConfig::fromConfig()->policyUrl();
    }

    public function lifetimeDays(): int
    {
        $days = CookieConsentConfig::fromConfig()->consentLifetimeDays();

        return $days > 0 ? $days : 180;
    }

    public function categories(): array
    {
        $categories = [];

        foreach (CookieConsentConfig::fromConfig()->categories() as $name => $category) {
            if (! is_array($category) || ! Coerce::bool($category['enabled'] ?? true, true)) {
                continue;
            }

            $categoryName = $this->categoryName((string) $name);

            if ($categoryName === '') {
                continue;
            }

            $required = Coerce::bool($category['required'] ?? $categoryName === 'necessary', $categoryName === 'necessary');

            $categories[$categoryName] = [
                'name' => $categoryName,
                'label' => Coerce::nullableString($category['label'] ?? null) ?? Str::headline($categoryName),
                'description' => Coerce::nullableString($category['description'] ?? null),
                'required' => $required,
                'default' => $required || Coerce::bool($category['default'] ?? false, false),
            ];
        }

        if (! isset($categories['necessary'])) {
            $categories = [
                'necessary' => [
                    'name' => 'necessary',
                    'label' => 'Necessarios',
                    'description' => 'Essenciais para o funcionamento do site.',
                    'required' => true,
                    'default' => true,
                ],
                ...$categories,
            ];
        }

        return $categories;
    }

    public function hasOptionalCategories(): bool
    {
        foreach ($this->categories() as $category) {
            if (! $category['required']) {
                return true;
            }
        }

        return false;
    }

    public function clientConfig(): array
    {
        $config = CookieConsentConfig::fromConfig();

        return [
            'storageKey' => $this->storageKey(),
            'version' => $config->version(),
            'lifetimeDays' => $this->lifetimeDays(),
            'policyUrl' => $this->policyUrl(),
            'categories' => array_values($this->categories()),
            'scriptSelector' => $config->scriptsSelector(),
            'showReopenButton' => $config->bannerShowReopenButton(),
            'logging' => [
                'enabled' => $this->loggingEnabled(),
                'endpoint' => $this->loggingEndpoint(),
                'csrfToken' => csrf_token(),
            ],
        ];
    }

    public function json(array $payload): string
    {
        return json_encode(
            $payload,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        ) ?: '{}';
    }

    protected function loggingEnabled(): bool
    {
        $config = CookieConsentConfig::fromConfig();

        return $config->loggingEnabled()
            && $config->loggingDatabaseEnabled()
            && $config->loggingRouteEnabled();
    }

    protected function loggingEndpoint(): ?string
    {
        if (! $this->loggingEnabled()) {
            return null;
        }

        $config = CookieConsentConfig::fromConfig();
        $name = $config->loggingRouteName();

        if ($name && Route::has($name)) {
            return route($name);
        }

        $uri = $config->loggingRouteUri();

        return $uri ? url($uri) : null;
    }

    protected function categoryName(string $name): string
    {
        return trim((string) preg_replace('/[^a-z0-9_]+/', '_', strtolower($name)), '_');
    }
}
