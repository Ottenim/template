<?php

namespace Template\LandingAnalytics\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingAnalytics\Support\AnalyticsManager;

class Debug extends Component
{
    public bool $enabled;

    public array $providers;

    public array $events;

    public function __construct(mixed $enabled = null)
    {
        $manager = app(AnalyticsManager::class);

        $this->enabled = $manager->debug($enabled);
        $this->providers = $manager->providers();
        $this->events = $manager->enabledEvents();
    }

    public function shouldRender(): bool
    {
        return $this->enabled;
    }

    public function render(): View
    {
        return view('landing-analytics::components.debug');
    }
}
