<?php

namespace Template\LandingPricing;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\MenuRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;
use Template\LandingPricing\Config\PricingConfig;
use Template\LandingPricing\Support\PricingPlans;

class LandingPricingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/landing-pricing.php', 'landing-pricing');

        // Bind transitório: lê o snapshot atual da config a cada resolução.
        $this->app->bind(PricingConfig::class, fn () => PricingConfig::fromConfig());

        $this->app->singleton(PricingPlans::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'landing-pricing');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Blade::componentNamespace('Template\\LandingPricing\\View\\Components', 'pricing');

        $this->registerCoreIntegrations();

        $this->publishes([
            __DIR__.'/../config/landing-pricing.php' => config_path('landing-pricing.php'),
        ], 'landing-pricing-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/landing-pricing'),
        ], 'landing-pricing-views');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'landing-pricing-migrations');

        $this->publishes([
            __DIR__.'/../resources/css/pricing.css' => public_path('vendor/landing-pricing/pricing.css'),
        ], 'landing-pricing-assets');
    }

    protected function registerCoreIntegrations(): void
    {
        $this->callAfterResolving(ModuleRegistry::class, function (ModuleRegistry $modules) {
            $config = $this->app->make(PricingConfig::class);

            $modules->register([
                'name' => 'landing-pricing',
                'label' => 'Pricing',
                'enabled' => $config->enabled(),
                'description' => 'Configurable pricing plans section for landing pages.',
                'admin_route' => $config->adminEnabled() ? 'pricing.admin.index' : null,
                'section' => [
                    'component' => 'pricing::section',
                    'enabled' => $config->sectionEnabled(),
                ],
            ]);
        });

        $this->callAfterResolving(MenuRegistry::class, function (MenuRegistry $menus) {
            $config = $this->app->make(PricingConfig::class);

            $menus->register([
                'key' => 'landing-pricing',
                'label' => 'Pricing',
                'route' => 'pricing.admin.index',
                'group' => 'Landing Page',
                'enabled' => $config->enabled() && $config->adminEnabled(),
            ]);
        });

        $this->callAfterResolving(SectionRenderer::class, function (SectionRenderer $sections) {
            $sections->register('pricing', [
                'component' => 'pricing::section',
            ]);

            $sections->register('pricing-section', [
                'component' => 'pricing::section',
            ]);
        });

        $this->callAfterResolving(AssetRegistry::class, function (AssetRegistry $assets) {
            $assets->registerStyle('landing-pricing', '/vendor/landing-pricing/pricing.css');
        });
    }
}
