<?php

namespace Template\LandingFaq;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\MenuRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;
use Template\LandingFaq\Config\FaqConfig;
use Template\LandingFaq\Support\FaqItems;

class LandingFaqServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/landing-faq.php', 'landing-faq');

        // Bind transitório: lê o snapshot atual da config a cada resolução.
        $this->app->bind(FaqConfig::class, fn () => FaqConfig::fromConfig());

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
            $config = $this->app->make(FaqConfig::class);

            $modules->register([
                'name' => 'landing-faq',
                'label' => 'FAQ',
                'enabled' => $config->enabled(),
                'description' => 'Configurable FAQ section for landing pages.',
                'admin_route' => $config->adminEnabled() ? 'faq.admin.index' : null,
                'section' => [
                    'component' => 'faq::section',
                    'enabled' => $config->sectionEnabled(),
                ],
            ]);
        });

        $this->callAfterResolving(MenuRegistry::class, function (MenuRegistry $menus) {
            $config = $this->app->make(FaqConfig::class);

            $menus->register([
                'key' => 'landing-faq',
                'label' => 'FAQ',
                'route' => 'faq.admin.index',
                'group' => 'Landing Page',
                'enabled' => $config->enabled() && $config->adminEnabled(),
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
