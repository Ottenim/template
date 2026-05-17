<?php

namespace Template\LandingLeadCapture\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Styles extends Component
{
    public string $css;

    public function __construct()
    {
        $this->css = file_get_contents(__DIR__.'/../../../resources/css/lead-capture.css') ?: '';
    }

    public function render(): View
    {
        return view('landing-lead-capture::components.styles');
    }
}
