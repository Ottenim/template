<?php

namespace Template\LandingCore\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingCore\Theme\ThemeManager;

class ThemeVariables extends Component
{
    public array $variables;

    public string $themeName;

    public function __construct(?string $theme = null)
    {
        $manager = app(ThemeManager::class);
        $activeTheme = $manager->theme($theme);

        $this->themeName = $activeTheme->name();
        $this->variables = $activeTheme->cssVariables();
    }

    public function render(): View
    {
        return view('landing-core::components.theme-variables');
    }
}
