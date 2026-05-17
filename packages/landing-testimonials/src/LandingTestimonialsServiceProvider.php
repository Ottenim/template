<?php

namespace Template\LandingTestimonials;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\MenuRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;
use Template\LandingTestimonials\Support\Testimonials;

class LandingTestimonialsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/landing-testimonials.php', 'landing-testimonials');

        $this->app->singleton(Testimonials::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'landing-testimonials');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Blade::componentNamespace('Template\\LandingTestimonials\\View\\Components', 'testimonials');

        $this->registerCoreIntegrations();

        $this->publishes([
            __DIR__.'/../config/landing-testimonials.php' => config_path('landing-testimonials.php'),
        ], 'landing-testimonials-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/landing-testimonials'),
        ], 'landing-testimonials-views');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'landing-testimonials-migrations');

        $this->publishes([
            __DIR__.'/../resources/css/testimonials.css' => public_path('vendor/landing-testimonials/testimonials.css'),
        ], 'landing-testimonials-assets');
    }

    protected function registerCoreIntegrations(): void
    {
        $this->callAfterResolving(ModuleRegistry::class, function (ModuleRegistry $modules) {
            $modules->register([
                'name' => 'landing-testimonials',
                'label' => 'Testimonials',
                'enabled' => (bool) config('landing-testimonials.enabled', true),
                'description' => 'Configurable testimonials section for landing pages.',
                'admin_route' => (bool) config('landing-testimonials.admin.enabled', false) ? 'testimonials.admin.index' : null,
                'section' => [
                    'component' => 'testimonials::section',
                    'enabled' => (bool) config('landing-testimonials.section.enabled', true),
                ],
            ]);
        });

        $this->callAfterResolving(MenuRegistry::class, function (MenuRegistry $menus) {
            $menus->register([
                'key' => 'landing-testimonials',
                'label' => 'Testimonials',
                'route' => 'testimonials.admin.index',
                'group' => 'Landing Page',
                'enabled' => (bool) config('landing-testimonials.enabled', true)
                    && (bool) config('landing-testimonials.admin.enabled', false),
            ]);
        });

        $this->callAfterResolving(SectionRenderer::class, function (SectionRenderer $sections) {
            $sections->register('testimonials', [
                'component' => 'testimonials::section',
            ]);

            $sections->register('testimonials-section', [
                'component' => 'testimonials::section',
            ]);
        });

        $this->callAfterResolving(AssetRegistry::class, function (AssetRegistry $assets) {
            $assets->registerStyle('landing-testimonials', '/vendor/landing-testimonials/testimonials.css');
        });
    }
}
