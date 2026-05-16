<?php

namespace Template\LandingWhatsapp\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Styles extends Component
{
    public string $css;

    public function __construct()
    {
        $this->css = file_get_contents(__DIR__.'/../../../resources/css/whatsapp.css') ?: '';
    }

    public function render(): View
    {
        return view('landing-whatsapp::components.styles');
    }
}
