<?php

namespace Template\LandingCore\Theme;

use Illuminate\Support\Arr;

class ThemeManager
{
    public function active(): string
    {
        return config('landing-theme.active', 'default');
    }

    public function themes(): array
    {
        return config('landing-theme.themes', []);
    }

    public function theme(?string $name = null): Theme
    {
        $name ??= $this->active();

        $themes = $this->themes();
        $default = $themes['default'] ?? [];
        $tokens = $themes[$name] ?? $default;

        if ($name !== 'default') {
            $tokens = array_replace_recursive($default, $tokens);
        }

        return new Theme($name, $tokens);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->theme()->get($key, $default);
    }

    public function cssVariables(?string $name = null): array
    {
        return $this->theme($name)->cssVariables();
    }

    public function componentClass(string $key, mixed $default = null): mixed
    {
        return Arr::get(config('landing-theme.components', []), $key, $default);
    }
}
