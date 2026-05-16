<?php

namespace Template\LandingCore\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class CoreStyles extends Component
{
    public string $css;

    public function __construct()
    {
        $this->css = file_get_contents(__DIR__.'/../../../resources/css/core.css') ?: '';
    }

    public function render(): View
    {
        return view('landing-core::components.core-styles');
    }
}
