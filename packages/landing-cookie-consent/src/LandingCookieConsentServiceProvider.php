<?php

namespace Template\LandingCookieConsent;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Template\LandingCookieConsent\Support\CookieConsentManager;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;

class LandingCookieConsentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/landing-cookie-consent.php', 'landing-cookie-consent');

        $this->app->singleton(CookieConsentManager::class);

        $this->syncAnalyticsConsentConfig();
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'landing-cookie-consent');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Blade::componentNamespace('Template\\LandingCookieConsent\\View\\Components', 'cookie-consent');

        $this->registerCoreIntegrations();

        $this->publishes([
            __DIR__.'/../config/landing-cookie-consent.php' => config_path('landing-cookie-consent.php'),
        ], 'landing-cookie-consent-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/landing-cookie-consent'),
        ], 'landing-cookie-consent-views');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'landing-cookie-consent-migrations');

        $this->publishes([
            __DIR__.'/../resources/css/cookie-consent.css' => public_path('vendor/landing-cookie-consent/cookie-consent.css'),
        ], 'landing-cookie-consent-assets');
    }

    protected function registerCoreIntegrations(): void
    {
        $this->callAfterResolving(ModuleRegistry::class, function (ModuleRegistry $modules) {
            $manager = app(CookieConsentManager::class);

            $modules->register([
                'name' => 'landing-cookie-consent',
                'label' => 'Cookie Consent / LGPD',
                'enabled' => $manager->enabled(),
                'description' => 'Cookie banner, consent preferences and privacy controls for landing pages.',
                'technical' => [
                    'component' => 'cookie-consent::banner',
                    'storage_key' => $manager->storageKey(),
                    'categories' => array_keys($manager->categories()),
                    'policy_url' => $manager->policyUrl(),
                ],
            ]);
        });

        $this->callAfterResolving(SectionRenderer::class, function (SectionRenderer $sections) {
            $sections->register('cookie-consent', [
                'component' => 'cookie-consent::banner',
            ]);

            $sections->register('lgpd', [
                'component' => 'cookie-consent::banner',
            ]);
        });

        $this->callAfterResolving(AssetRegistry::class, function (AssetRegistry $assets) {
            $assets->registerStyle('landing-cookie-consent', '/vendor/landing-cookie-consent/cookie-consent.css');
        });
    }

    protected function syncAnalyticsConsentConfig(): void
    {
        if (! $this->boolValue(config('landing-cookie-consent.enabled', true), true)) {
            return;
        }

        $integration = (array) config('landing-cookie-consent.integrations.analytics', []);

        if (! $this->boolValue($integration['enabled'] ?? true, true)
            || ! $this->boolValue($integration['sync_config'] ?? true, true)) {
            return;
        }

        $categories = (array) ($integration['categories'] ?? []);
        $existing = (array) config('landing-analytics.consent', []);

        config()->set('landing-analytics.consent', array_replace($existing, [
            'enabled' => true,
            'storage_key' => $this->nullableString(config('landing-cookie-consent.storage_key')) ?? 'landing_cookie_consent',
            'default_granted' => $this->boolValue($integration['default_granted'] ?? false, false),
            'categories' => [
                'analytics' => $this->nullableString($categories['analytics'] ?? null) ?? 'analytics',
                'marketing' => $this->nullableString($categories['marketing'] ?? null) ?? 'marketing',
            ],
        ]));
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
