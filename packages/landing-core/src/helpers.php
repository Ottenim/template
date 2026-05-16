<?php

use Illuminate\Support\HtmlString;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\MenuRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;
use Template\LandingCore\Theme\Theme;
use Template\LandingCore\Theme\ThemeManager;

if (! function_exists('landing_theme')) {
    function landing_theme(?string $key = null, mixed $default = null): mixed
    {
        $manager = app(ThemeManager::class);

        if ($key === null) {
            return $manager->theme();
        }

        return $manager->get($key, $default);
    }
}

if (! function_exists('landing_theme_object')) {
    function landing_theme_object(?string $name = null): Theme
    {
        return app(ThemeManager::class)->theme($name);
    }
}

if (! function_exists('landing_modules')) {
    function landing_modules(): ModuleRegistry
    {
        return app(ModuleRegistry::class);
    }
}

if (! function_exists('landing_menus')) {
    function landing_menus(): MenuRegistry
    {
        return app(MenuRegistry::class);
    }
}

if (! function_exists('landing_assets')) {
    function landing_assets(): AssetRegistry
    {
        return app(AssetRegistry::class);
    }
}

if (! function_exists('landing_section')) {
    function landing_section(string $name, array $data = []): HtmlString
    {
        return app(SectionRenderer::class)->render($name, $data);
    }
}
