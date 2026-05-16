<?php

namespace Template\LandingCore\View\Components;

use Illuminate\Support\HtmlString;
use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingCore\Support\SectionRenderer;

class LandingSection extends Component
{
    public HtmlString $html;

    public function __construct(
        public string $name,
        public array $data = [],
    ) {
        $this->html = app(SectionRenderer::class)->render($name, $data);
    }

    public function render(): View
    {
        return view('landing-core::components.landing-section');
    }
}
