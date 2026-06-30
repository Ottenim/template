<?php

namespace Template\LandingWhatsapp;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;
use Template\LandingWhatsapp\Config\WhatsappConfig;
use Template\LandingWhatsapp\Support\WhatsappUrl;

class LandingWhatsappServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/landing-whatsapp.php', 'landing-whatsapp');

        // Bind transitório: lê o snapshot atual da config a cada resolução.
        $this->app->bind(WhatsappConfig::class, fn () => WhatsappConfig::fromConfig());

        $this->app->singleton(WhatsappUrl::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'landing-whatsapp');

        Blade::componentNamespace('Template\\LandingWhatsapp\\View\\Components', 'whatsapp');

        $this->registerCoreIntegrations();

        $this->publishes([
            __DIR__.'/../config/landing-whatsapp.php' => config_path('landing-whatsapp.php'),
        ], 'landing-whatsapp-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/landing-whatsapp'),
        ], 'landing-whatsapp-views');

        $this->publishes([
            __DIR__.'/../resources/css/whatsapp.css' => public_path('vendor/landing-whatsapp/whatsapp.css'),
        ], 'landing-whatsapp-assets');
    }

    protected function registerCoreIntegrations(): void
    {
        $this->callAfterResolving(ModuleRegistry::class, function (ModuleRegistry $modules) {
            $config = $this->app->make(WhatsappConfig::class);

            $modules->register([
                'name' => 'landing-whatsapp',
                'label' => 'WhatsApp CTA',
                'enabled' => $config->enabled(),
                'description' => 'Configurable WhatsApp call to action for landing pages.',
                'section' => [
                    'component' => 'whatsapp::section',
                    'enabled' => $config->sectionEnabled(),
                ],
            ]);
        });

        $this->callAfterResolving(SectionRenderer::class, function (SectionRenderer $sections) {
            $sections->register('whatsapp', [
                'component' => 'whatsapp::section',
            ]);

            $sections->register('whatsapp-button', [
                'component' => 'whatsapp::button',
            ]);

            $sections->register('whatsapp-floating', [
                'component' => 'whatsapp::floating-button',
            ]);
        });

        $this->callAfterResolving(AssetRegistry::class, function (AssetRegistry $assets) {
            $assets->registerStyle('landing-whatsapp', '/vendor/landing-whatsapp/whatsapp.css');
        });
    }
}
