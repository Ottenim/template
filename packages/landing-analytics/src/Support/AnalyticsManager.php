<?php

namespace Template\LandingAnalytics\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Template\LandingAnalytics\Config\AnalyticsConfig;
use Template\LandingCore\Analytics\LandingEvent;
use Template\LandingCore\Support\Coerce;

class AnalyticsManager
{
    public function enabled(mixed $override = null): bool
    {
        return AnalyticsConfig::fromConfig()->enabled()
            && Coerce::bool($override, true);
    }

    public function debug(mixed $override = null): bool
    {
        if (! $this->enabled()) {
            return false;
        }

        $config = AnalyticsConfig::fromConfig();

        if (! Coerce::bool($override, $config->debug())) {
            return false;
        }

        $environments = $config->debugEnvironments();

        return $environments === [] || app()->environment($environments);
    }

    public function providers(): array
    {
        $providers = [];

        foreach (AnalyticsConfig::fromConfig()->providers() as $name => $provider) {
            if (! is_array($provider) || ! Coerce::bool($provider['enabled'] ?? false, false)) {
                continue;
            }

            $id = Coerce::nullableString($provider['id'] ?? null);

            if ($id === null) {
                continue;
            }

            $providers[$name] = [
                'name' => (string) $name,
                'label' => $this->providerLabel((string) $name, $provider),
                'id' => $id,
                'category' => Coerce::nullableString($provider['category'] ?? null) ?? 'analytics',
                'send_page_view' => Coerce::bool($provider['send_page_view'] ?? false, false),
                'conversion_ids' => $this->conversionIds((array) ($provider['conversion_ids'] ?? [])),
            ];
        }

        return $providers;
    }

    public function provider(string $name): ?array
    {
        return $this->providers()[$name] ?? null;
    }

    public function events(): array
    {
        $events = [];
        $configured = AnalyticsConfig::fromConfig()->events();
        $knownEvents = array_map(
            fn (LandingEvent $event): string => $event->value,
            LandingEvent::cases(),
        );

        foreach ($knownEvents as $eventName) {
            if (array_key_exists($eventName, $configured)) {
                $events[$eventName] = $this->eventEnabled($configured[$eventName]);
            }
        }

        foreach ($configured as $name => $event) {
            $eventName = Coerce::nullableString($name);

            if ($eventName === null || in_array($eventName, $knownEvents, true)) {
                continue;
            }

            $events[$eventName] = $this->eventEnabled($event);
        }

        return $events;
    }

    public function enabledEvents(): array
    {
        return array_keys(array_filter($this->events()));
    }

    public function clientConfig(mixed $enabled = null, mixed $debug = null): array
    {
        $config = AnalyticsConfig::fromConfig();

        return [
            'enabled' => $this->enabled($enabled),
            'debug' => $this->debug($debug),
            'dataLayer' => $this->dataLayerName(),
            'providers' => $this->providers(),
            'events' => $this->events(),
            'autoTrack' => [
                'clicks' => $config->autoTrackClicks(),
                'forms' => $config->autoTrackForms(),
                'scrollDepth' => $this->scrollDepthConfig(),
            ],
            'selectors' => $this->selectors(),
            'consent' => $this->consentConfig(),
        ];
    }

    public function json(array $payload): string
    {
        return json_encode(
            $payload,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        ) ?: '{}';
    }

    public function shouldRenderNoScript(): bool
    {
        if (! $this->enabled()) {
            return false;
        }

        $consent = $this->consentConfig();

        return ! $consent['enabled'] || $consent['defaultGranted'];
    }

    protected function providerLabel(string $name, array $provider): string
    {
        return Coerce::nullableString($provider['label'] ?? null)
            ?? Str::headline(str_replace('_', ' ', $name));
    }

    protected function eventEnabled(mixed $event): bool
    {
        return is_array($event)
            ? Coerce::bool($event['enabled'] ?? true, true)
            : Coerce::bool($event, true);
    }

    protected function conversionIds(array $conversionIds): array
    {
        return collect($conversionIds)
            ->mapWithKeys(function (mixed $value, mixed $event): array {
                $eventName = Coerce::nullableString($event);
                $conversionId = Coerce::nullableString($value);

                if ($eventName === null || $conversionId === null) {
                    return [];
                }

                return [$eventName => $conversionId];
            })
            ->all();
    }

    protected function scrollDepthConfig(): array
    {
        $config = AnalyticsConfig::fromConfig()->autoTrackScrollDepth();
        $eventName = Coerce::nullableString($config['event_name'] ?? null) ?? LandingEvent::ScrollDepth->value;

        return [
            'enabled' => Coerce::bool($config['enabled'] ?? false, false)
                && ($this->events()[$eventName] ?? true),
            'event' => $eventName,
            'percentages' => collect($config['percentages'] ?? [25, 50, 75, 100])
                ->map(fn (mixed $value): int => (int) $value)
                ->filter(fn (int $value): bool => $value > 0 && $value <= 100)
                ->unique()
                ->values()
                ->all(),
        ];
    }

    protected function selectors(): array
    {
        return [
            'clicks' => $this->selectorGroup('clicks'),
            'forms' => $this->selectorGroup('forms'),
        ];
    }

    protected function selectorGroup(string $group): array
    {
        return collect(AnalyticsConfig::fromConfig()->selectors($group))
            ->map(function (mixed $selector): ?array {
                if (! is_array($selector)) {
                    return null;
                }

                $cssSelector = Coerce::nullableString($selector['selector'] ?? null);
                $attribute = Coerce::nullableString($selector['attribute'] ?? null);

                if ($cssSelector === null || $attribute === null) {
                    return null;
                }

                return [
                    'selector' => $cssSelector,
                    'attribute' => $attribute,
                    'module' => Coerce::nullableString($selector['module'] ?? null),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function consentConfig(): array
    {
        $config = AnalyticsConfig::fromConfig()->consent();
        $categories = (array) ($config['categories'] ?? []);

        return [
            'enabled' => Coerce::bool($config['enabled'] ?? false, false),
            'storageKey' => Coerce::nullableString($config['storage_key'] ?? null) ?? 'landing_cookie_consent',
            'defaultGranted' => Coerce::bool($config['default_granted'] ?? false, false),
            'categories' => [
                'analytics' => Coerce::nullableString(Arr::get($categories, 'analytics')) ?? 'analytics',
                'marketing' => Coerce::nullableString(Arr::get($categories, 'marketing')) ?? 'marketing',
            ],
        ];
    }

    protected function dataLayerName(): string
    {
        $name = AnalyticsConfig::fromConfig()->dataLayer();

        return preg_match('/^[A-Za-z_$][A-Za-z0-9_$]*$/', $name) ? $name : 'dataLayer';
    }
}
