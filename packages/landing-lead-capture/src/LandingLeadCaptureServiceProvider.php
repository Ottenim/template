<?php

namespace Template\LandingLeadCapture;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;
use Template\LandingLeadCapture\Config\LeadCaptureConfig;
use Template\LandingLeadCapture\Support\LeadCaptureFields;

class LandingLeadCaptureServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/landing-lead-capture.php', 'landing-lead-capture');

        // Bind transitório: lê o snapshot atual da config a cada resolução.
        $this->app->bind(LeadCaptureConfig::class, fn () => LeadCaptureConfig::fromConfig());

        $this->app->bind(LeadCaptureFields::class, fn () => new LeadCaptureFields);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'landing-lead-capture');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Blade::componentNamespace('Template\\LandingLeadCapture\\View\\Components', 'lead-capture');

        $this->registerCoreIntegrations();

        $this->publishes([
            __DIR__.'/../config/landing-lead-capture.php' => config_path('landing-lead-capture.php'),
        ], 'landing-lead-capture-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/landing-lead-capture'),
        ], 'landing-lead-capture-views');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'landing-lead-capture-migrations');

        $this->publishes([
            __DIR__.'/../resources/css/lead-capture.css' => public_path('vendor/landing-lead-capture/lead-capture.css'),
        ], 'landing-lead-capture-assets');
    }

    protected function registerCoreIntegrations(): void
    {
        $this->callAfterResolving(ModuleRegistry::class, function (ModuleRegistry $modules) {
            $config = $this->app->make(LeadCaptureConfig::class);

            $modules->register([
                'name' => 'landing-lead-capture',
                'label' => 'Lead Capture',
                'enabled' => $config->enabled(),
                'description' => 'Short conversion form for landing page leads.',
                'section' => [
                    'component' => 'lead-capture::section',
                    'enabled' => $config->sectionEnabled(),
                ],
            ]);
        });

        $this->callAfterResolving(SectionRenderer::class, function (SectionRenderer $sections) {
            $sections->register('lead-capture', [
                'component' => 'lead-capture::section',
            ]);

            $sections->register('lead-capture-form', [
                'component' => 'lead-capture::form',
            ]);
        });

        $this->callAfterResolving(AssetRegistry::class, function (AssetRegistry $assets) {
            $assets->registerStyle('landing-lead-capture', '/vendor/landing-lead-capture/lead-capture.css');
        });
    }
}
