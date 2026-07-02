<?php

namespace Template\LandingAnalytics\Config;

use Template\LandingCore\Analytics\LandingEvent;
use Template\LandingCore\Config\ModuleConfig;

/**
 * Config tipada do módulo de analytics. Centraliza as chaves
 * config('landing-analytics.*') antes espalhadas no AnalyticsManager e no
 * provider.
 *
 * A normalização de providers/selectors/consentimento continua no manager,
 * que monta o payload público para JavaScript.
 */
class AnalyticsConfig extends ModuleConfig
{
    public static function key(): string
    {
        return 'landing-analytics';
    }

    public function enabled(): bool
    {
        return $this->bool('enabled', true);
    }

    public function debug(): bool
    {
        return $this->bool('debug', false);
    }

    /**
     * @return array<int, string>
     */
    public function debugEnvironments(): array
    {
        return array_values(array_filter(
            array_map(
                fn (mixed $environment): string => trim((string) $environment),
                $this->list('debug_environments', ['local', 'testing']),
            ),
            fn (string $environment): bool => $environment !== '',
        ));
    }

    public function dataLayer(): string
    {
        return $this->string('data_layer', 'dataLayer');
    }

    /**
     * @return array<int|string, mixed>
     */
    public function providers(): array
    {
        return $this->list('providers', []);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function events(): array
    {
        return $this->list('events', $this->defaultEvents());
    }

    public function autoTrackClicks(): bool
    {
        return $this->bool('auto_track.clicks', true);
    }

    public function autoTrackForms(): bool
    {
        return $this->bool('auto_track.forms', true);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function autoTrackScrollDepth(): array
    {
        return $this->list('auto_track.scroll_depth', [
            'enabled' => false,
            'event_name' => LandingEvent::ScrollDepth->value,
            'percentages' => [25, 50, 75, 100],
        ]);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function selectors(string $group): array
    {
        return $this->list("selectors.{$group}", []);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function consent(): array
    {
        return $this->list('consent', [
            'enabled' => false,
            'storage_key' => 'landing_cookie_consent',
            'default_granted' => false,
            'categories' => [
                'analytics' => 'analytics',
                'marketing' => 'marketing',
            ],
        ]);
    }

    /**
     * @return array<string, bool>
     */
    protected function defaultEvents(): array
    {
        $events = [];

        foreach (LandingEvent::cases() as $event) {
            $events[$event->value] = $event !== LandingEvent::ScrollDepth;
        }

        return $events;
    }
}
