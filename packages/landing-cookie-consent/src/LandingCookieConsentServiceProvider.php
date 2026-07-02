<?php

namespace Template\LandingCookieConsent;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Template\LandingCookieConsent\Config\CookieConsentConfig;
use Template\LandingCookieConsent\Support\CookieConsentManager;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\Coerce;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;

class LandingCookieConsentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/landing-cookie-consent.php', 'landing-cookie-consent');

        // Bind transitório: lê o snapshot atual da config a cada resolução.
        $this->app->bind(CookieConsentConfig::class, fn () => CookieConsentConfig::fromConfig());

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
        $config = $this->app->make(CookieConsentConfig::class);

        if (! $config->enabled()) {
            return;
        }

        if (! $config->integrationsAnalyticsEnabled() || ! $config->integrationsAnalyticsSyncConfig()) {
            return;
        }

        $categories = $config->integrationsAnalyticsCategories();
        $existing = (array) config()->get('landing-analytics.consent', []);

        config()->set('landing-analytics.consent', array_replace($existing, [
            'enabled' => true,
            'storage_key' => $config->storageKey(),
            'default_granted' => $config->integrationsAnalyticsDefaultGranted(),
            'categories' => [
                'analytics' => Coerce::nullableString($categories['analytics'] ?? null) ?? 'analytics',
                'marketing' => Coerce::nullableString($categories['marketing'] ?? null) ?? 'marketing',
            ],
        ]));
    }
}
