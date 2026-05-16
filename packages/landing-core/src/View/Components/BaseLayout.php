<?php

namespace Template\LandingCore\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class BaseLayout extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $lang = null,
        public string $bodyClass = '',
    ) {
        $this->title ??= config('app.name', 'Laravel');
        $this->lang ??= str_replace('_', '-', app()->getLocale());
    }

    public function render(): View
    {
        return view('landing-core::components.base-layout');
    }
}
