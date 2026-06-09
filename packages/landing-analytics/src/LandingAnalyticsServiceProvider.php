<?php

namespace Template\LandingAnalytics;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Template\LandingAnalytics\Support\AnalyticsManager;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\ModuleRegistry;

class LandingAnalyticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/landing-analytics.php', 'landing-analytics');

        $this->app->singleton(AnalyticsManager::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'landing-analytics');

        Blade::componentNamespace('Template\\LandingAnalytics\\View\\Components', 'analytics');

        $this->registerCoreIntegrations();

        $this->publishes([
            __DIR__.'/../config/landing-analytics.php' => config_path('landing-analytics.php'),
        ], 'landing-analytics-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/landing-analytics'),
        ], 'landing-analytics-views');

        $this->publishes([
            __DIR__.'/../resources/css/analytics.css' => public_path('vendor/landing-analytics/analytics.css'),
        ], 'landing-analytics-assets');
    }

    protected function registerCoreIntegrations(): void
    {
        $this->callAfterResolving(ModuleRegistry::class, function (ModuleRegistry $modules) {
            $manager = app(AnalyticsManager::class);

            $modules->register([
                'name' => 'landing-analytics',
                'label' => 'Analytics / Tracking',
                'enabled' => $manager->enabled(),
                'description' => 'Centralized analytics providers, dataLayer and landing page event tracking.',
                'technical' => [
                    'head_component' => 'analytics::head',
                    'body_component' => 'analytics::body',
                    'providers' => array_keys($manager->providers()),
                    'events' => $manager->enabledEvents(),
                ],
            ]);
        });

        $this->callAfterResolving(AssetRegistry::class, function (AssetRegistry $assets) {
            $assets->registerStyle('landing-analytics', '/vendor/landing-analytics/analytics.css');
        });
    }
}
