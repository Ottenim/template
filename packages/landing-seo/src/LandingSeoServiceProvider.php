<?php

namespace Template\LandingSeo;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\MenuRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingSeo\Support\SeoManager;

class LandingSeoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/landing-seo.php', 'landing-seo');

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
            $modules->register([
                'name' => 'landing-seo',
                'label' => 'SEO Manager',
                'enabled' => (bool) config('landing-seo.enabled', true),
                'description' => 'Centralized metadata, schema, sitemap and robots management.',
                'admin_route' => (bool) config('landing-seo.admin.enabled', false) ? 'seo.admin.index' : null,
                'technical' => [
                    'meta_component' => 'seo::meta',
                    'sitemap_route' => (bool) config('landing-seo.sitemap.enabled', true) ? 'seo.sitemap' : null,
                    'robots_route' => (bool) config('landing-seo.robots_txt.enabled', true) ? 'seo.robots' : null,
                ],
            ]);
        });

        $this->callAfterResolving(MenuRegistry::class, function (MenuRegistry $menus) {
            $menus->register([
                'key' => 'landing-seo',
                'label' => 'SEO Manager',
                'route' => 'seo.admin.index',
                'group' => 'Landing Page',
                'enabled' => (bool) config('landing-seo.enabled', true)
                    && (bool) config('landing-seo.admin.enabled', false),
            ]);
        });

        $this->callAfterResolving(AssetRegistry::class, function (AssetRegistry $assets) {
            $assets->registerStyle('landing-seo', '/vendor/landing-seo/seo.css');
        });
    }
}
