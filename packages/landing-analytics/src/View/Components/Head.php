<?php

namespace Template\LandingAnalytics\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingAnalytics\Support\AnalyticsManager;

class Head extends Component
{
    public bool $enabled;

    public string $configJson;

    public function __construct(mixed $enabled = null, mixed $debug = null)
    {
        $manager = app(AnalyticsManager::class);

        $this->enabled = $manager->enabled($enabled);
        $this->configJson = $manager->json($manager->clientConfig($enabled, $debug));
    }

    public function shouldRender(): bool
    {
        return $this->enabled;
    }

    public function render(): View
    {
        return view('landing-analytics::components.head');
    }
}
