<?php

namespace Template\LandingFaq;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\MenuRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;
use Template\LandingFaq\Support\FaqItems;

class LandingFaqServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/landing-faq.php', 'landing-faq');

        $this->app->singleton(FaqItems::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'landing-faq');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Blade::componentNamespace('Template\\LandingFaq\\View\\Components', 'faq');

        $this->registerCoreIntegrations();

        $this->publishes([
            __DIR__.'/../config/landing-faq.php' => config_path('landing-faq.php'),
        ], 'landing-faq-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/landing-faq'),
        ], 'landing-faq-views');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'landing-faq-migrations');

        $this->publishes([
            __DIR__.'/../resources/css/faq.css' => public_path('vendor/landing-faq/faq.css'),
        ], 'landing-faq-assets');
    }

    protected function registerCoreIntegrations(): void
    {
        $this->callAfterResolving(ModuleRegistry::class, function (ModuleRegistry $modules) {
            $modules->register([
                'name' => 'landing-faq',
                'label' => 'FAQ',
                'enabled' => (bool) config('landing-faq.enabled', true),
                'description' => 'Configurable FAQ section for landing pages.',
                'admin_route' => (bool) config('landing-faq.admin.enabled', false) ? 'faq.admin.index' : null,
                'section' => [
                    'component' => 'faq::section',
                    'enabled' => (bool) config('landing-faq.section.enabled', true),
                ],
            ]);
        });

        $this->callAfterResolving(MenuRegistry::class, function (MenuRegistry $menus) {
            $menus->register([
                'key' => 'landing-faq',
                'label' => 'FAQ',
                'route' => 'faq.admin.index',
                'group' => 'Landing Page',
                'enabled' => (bool) config('landing-faq.enabled', true)
                    && (bool) config('landing-faq.admin.enabled', false),
            ]);
        });

        $this->callAfterResolving(SectionRenderer::class, function (SectionRenderer $sections) {
            $sections->register('faq', [
                'component' => 'faq::section',
            ]);

            $sections->register('faq-section', [
                'component' => 'faq::section',
            ]);
        });

        $this->callAfterResolving(AssetRegistry::class, function (AssetRegistry $assets) {
            $assets->registerStyle('landing-faq', '/vendor/landing-faq/faq.css');
        });
    }
}
