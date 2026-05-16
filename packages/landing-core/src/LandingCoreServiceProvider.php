<?php

namespace Template\LandingCore;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\MenuRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;
use Template\LandingCore\Theme\ThemeManager;

class LandingCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/landing-core.php', 'landing-core');
        $this->mergeConfigFrom(__DIR__.'/../config/landing-theme.php', 'landing-theme');

        $this->app->singleton(ThemeManager::class);

        $this->app->singleton(ModuleRegistry::class, function () {
            return new ModuleRegistry(config('landing-core.modules', []));
        });

        $this->app->singleton(MenuRegistry::class, function () {
            return new MenuRegistry(config('landing-core.menus', []));
        });

        $this->app->singleton(AssetRegistry::class, function () {
            return new AssetRegistry(config('landing-core.assets', []));
        });

        $this->app->singleton(SectionRenderer::class, function ($app) {
            return new SectionRenderer(
                $app->make(ModuleRegistry::class),
                config('landing-core.sections', []),
            );
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'landing-core');

        Blade::componentNamespace('Template\\LandingCore\\View\\Components', 'landing-core');

        Blade::directive('landingSection', function (string $expression) {
            return "<?php echo app(\\Template\\LandingCore\\Support\\SectionRenderer::class)->render(...[$expression]); ?>";
        });

        $this->publishes([
            __DIR__.'/../config/landing-core.php' => config_path('landing-core.php'),
            __DIR__.'/../config/landing-theme.php' => config_path('landing-theme.php'),
        ], 'landing-core-config');

        $this->publishes([
            __DIR__.'/../config/landing-theme.php' => config_path('landing-theme.php'),
        ], 'landing-theme-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/landing-core'),
        ], 'landing-core-views');

        $this->publishes([
            __DIR__.'/../resources/css/core.css' => public_path('vendor/landing-core/core.css'),
        ], 'landing-core-assets');
    }
}
