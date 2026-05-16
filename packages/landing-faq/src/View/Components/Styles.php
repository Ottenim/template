<?php

namespace Template\LandingFaq\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Styles extends Component
{
    public string $css;

    public function __construct()
    {
        $this->css = file_get_contents(__DIR__.'/../../../resources/css/faq.css') ?: '';
    }

    public function render(): View
    {
        return view('landing-faq::components.styles');
    }
}
