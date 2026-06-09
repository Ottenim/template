<?php

namespace Template\LandingAnalytics\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingAnalytics\Support\AnalyticsManager;

class Body extends Component
{
    public array $providers;

    public bool $renderNoScript;

    public bool $debug;

    public function __construct(mixed $debug = null)
    {
        $manager = app(AnalyticsManager::class);

        $this->providers = $manager->providers();
        $this->renderNoScript = $manager->shouldRenderNoScript();
        $this->debug = $manager->debug($debug);
    }

    public function shouldRender(): bool
    {
        return app(AnalyticsManager::class)->enabled();
    }

    public function render(): View
    {
        return view('landing-analytics::components.body');
    }
}
