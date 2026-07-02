<?php

namespace Template\LandingSeo;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\MenuRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingSeo\Config\SeoConfig;
use Template\LandingSeo\Support\SeoManager;

class LandingSeoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/landing-seo.php', 'landing-seo');

        // Bind transitório: lê o snapshot atual da config a cada resolução.
        $this->app->bind(SeoConfig::class, fn () => SeoConfig::fromConfig());

        $this->app->singleton(SeoManager::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'landing-seo');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Blade::componentNamespace('Template\\LandingSeo\\View\\Components', 'seo');

        $this->registerCoreIntegrations();

        $this->publishes([
            __DIR__.'/../config/landing-seo.php' => config_path('landing-seo.php'),
        ], 'landing-seo-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/landing-seo'),
        ], 'landing-seo-views');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'landing-seo-migrations');

        $this->publishes([
            __DIR__.'/../resources/css/seo.css' => public_path('vendor/landing-seo/seo.css'),
        ], 'landing-seo-assets');
    }

    protected function registerCoreIntegrations(): void
    {
        $this->callAfterResolving(ModuleRegistry::class, function (ModuleRegistry $modules) {
            $config = $this->app->make(SeoConfig::class);

            $modules->register([
                'name' => 'landing-seo',
                'label' => 'SEO Manager',
                'enabled' => $config->enabled(),
                'description' => 'Centralized metadata, schema, sitemap and robots management.',
                'admin_route' => $config->adminEnabled() ? 'seo.admin.index' : null,
                'technical' => [
                    'meta_component' => 'seo::meta',
                    'sitemap_route' => $config->sitemapEnabled() ? 'seo.sitemap' : null,
                    'robots_route' => $config->robotsTxtEnabled() ? 'seo.robots' : null,
                ],
            ]);
        });

        $this->callAfterResolving(MenuRegistry::class, function (MenuRegistry $menus) {
            $config = $this->app->make(SeoConfig::class);

            $menus->register([
                'key' => 'landing-seo',
                'label' => 'SEO Manager',
                'route' => 'seo.admin.index',
                'group' => 'Landing Page',
                'enabled' => $config->enabled() && $config->adminEnabled(),
            ]);
        });

        $this->callAfterResolving(AssetRegistry::class, function (AssetRegistry $assets) {
            $assets->registerStyle('landing-seo', '/vendor/landing-seo/seo.css');
        });
    }
}
