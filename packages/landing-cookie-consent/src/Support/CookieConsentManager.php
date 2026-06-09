<?php

namespace Template\LandingCookieConsent\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class CookieConsentManager
{
    public function enabled(mixed $override = null): bool
    {
        return $this->boolValue(config('landing-cookie-consent.enabled', true), true)
            && $this->boolValue(config('landing-cookie-consent.banner.enabled', true), true)
            && $this->boolValue($override, true);
    }

    public function storageKey(): string
    {
        return $this->nullableString(config('landing-cookie-consent.storage_key')) ?? 'landing_cookie_consent';
    }

    public function policyUrl(): ?string
    {
        return $this->nullableString(config('landing-cookie-consent.policy_url'));
    }

    public function lifetimeDays(): int
    {
        $days = (int) config('landing-cookie-consent.consent_lifetime_days', 180);

        return $days > 0 ? $days : 180;
    }

    public function categories(): array
    {
        $categories = [];

        foreach ((array) config('landing-cookie-consent.categories', []) as $name => $category) {
            if (! is_array($category) || ! $this->boolValue($category['enabled'] ?? true, true)) {
                continue;
            }

            $categoryName = $this->categoryName((string) $name);

            if ($categoryName === '') {
                continue;
            }

            $required = $this->boolValue($category['required'] ?? $categoryName === 'necessary', $categoryName === 'necessary');

            $categories[$categoryName] = [
                'name' => $categoryName,
                'label' => $this->nullableString($category['label'] ?? null) ?? Str::headline($categoryName),
                'description' => $this->nullableString($category['description'] ?? null),
                'required' => $required,
                'default' => $required || $this->boolValue($category['default'] ?? false, false),
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
        return [
            'storageKey' => $this->storageKey(),
            'version' => $this->nullableString(config('landing-cookie-consent.version')) ?? '1',
            'lifetimeDays' => $this->lifetimeDays(),
            'policyUrl' => $this->policyUrl(),
            'categories' => array_values($this->categories()),
            'scriptSelector' => $this->nullableString(config('landing-cookie-consent.scripts.selector'))
                ?? 'script[type="text/plain"][data-landing-cookie-category], script[type="text/plain"][data-cookie-category]',
            'showReopenButton' => $this->boolValue(config('landing-cookie-consent.banner.show_reopen_button', true), true),
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
        return $this->boolValue(config('landing-cookie-consent.logging.enabled', true), true)
            && $this->boolValue(config('landing-cookie-consent.logging.database.enabled', true), true)
            && $this->boolValue(config('landing-cookie-consent.logging.route.enabled', true), true);
    }

    protected function loggingEndpoint(): ?string
    {
        if (! $this->loggingEnabled()) {
            return null;
        }

        $name = $this->nullableString(config('landing-cookie-consent.logging.route.name'));

        if ($name && Route::has($name)) {
            return route($name);
        }

        $uri = $this->nullableString(config('landing-cookie-consent.logging.route.uri', 'cookie-consent'));

        return $uri ? url($uri) : null;
    }

    protected function categoryName(string $name): string
    {
        return trim((string) preg_replace('/[^a-z0-9_]+/', '_', strtolower($name)), '_');
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
