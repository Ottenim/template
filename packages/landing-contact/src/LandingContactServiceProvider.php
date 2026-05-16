<?php

namespace Template\LandingContact;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Template\LandingContact\Support\ContactFields;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;

class LandingContactServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/landing-contact.php', 'landing-contact');

        $this->app->bind(ContactFields::class, fn () => new ContactFields);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'landing-contact');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Blade::componentNamespace('Template\\LandingContact\\View\\Components', 'contact');

        $this->registerCoreIntegrations();

        $this->publishes([
            __DIR__.'/../config/landing-contact.php' => config_path('landing-contact.php'),
        ], 'landing-contact-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/landing-contact'),
        ], 'landing-contact-views');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'landing-contact-migrations');

        $this->publishes([
            __DIR__.'/../resources/css/contact.css' => public_path('vendor/landing-contact/contact.css'),
        ], 'landing-contact-assets');
    }

    protected function registerCoreIntegrations(): void
    {
        $this->callAfterResolving(ModuleRegistry::class, function (ModuleRegistry $modules) {
            $modules->register([
                'name' => 'landing-contact',
                'label' => 'Contact Form',
                'enabled' => (bool) config('landing-contact.enabled', true),
                'description' => 'Configurable contact form for landing page leads.',
                'section' => [
                    'component' => 'contact::section',
                    'enabled' => (bool) config('landing-contact.section.enabled', true),
                ],
            ]);
        });

        $this->callAfterResolving(SectionRenderer::class, function (SectionRenderer $sections) {
            $sections->register('contact', [
                'component' => 'contact::section',
            ]);

            $sections->register('contact-form', [
                'component' => 'contact::form',
            ]);
        });

        $this->callAfterResolving(AssetRegistry::class, function (AssetRegistry $assets) {
            $assets->registerStyle('landing-contact', '/vendor/landing-contact/contact.css');
        });
    }
}
