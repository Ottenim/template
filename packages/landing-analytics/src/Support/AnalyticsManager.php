<?php

namespace Template\LandingAnalytics\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class AnalyticsManager
{
    public function enabled(mixed $override = null): bool
    {
        return $this->boolValue(config('landing-analytics.enabled', true), true)
            && $this->boolValue($override, true);
    }

    public function debug(mixed $override = null): bool
    {
        if (! $this->enabled()) {
            return false;
        }

        if (! $this->boolValue($override ?? config('landing-analytics.debug', false), false)) {
            return false;
        }

        $environments = array_filter((array) config('landing-analytics.debug_environments', ['local', 'testing']));

        return $environments === [] || app()->environment($environments);
    }

    public function providers(): array
    {
        $providers = [];

        foreach ((array) config('landing-analytics.providers', []) as $name => $provider) {
            if (! is_array($provider) || ! $this->boolValue($provider['enabled'] ?? false, false)) {
                continue;
            }

            $id = $this->nullableString($provider['id'] ?? null);

            if ($id === null) {
                continue;
            }

            $providers[$name] = [
                'name' => (string) $name,
                'label' => $this->providerLabel((string) $name, $provider),
                'id' => $id,
                'category' => $this->nullableString($provider['category'] ?? null) ?? 'analytics',
                'send_page_view' => $this->boolValue($provider['send_page_view'] ?? false, false),
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

        foreach ((array) config('landing-analytics.events', []) as $name => $event) {
            $eventName = $this->nullableString($name);

            if ($eventName === null) {
                continue;
            }

            $events[$eventName] = is_array($event)
                ? $this->boolValue($event['enabled'] ?? true, true)
                : $this->boolValue($event, true);
        }

        return $events;
    }

    public function enabledEvents(): array
    {
        return array_keys(array_filter($this->events()));
    }

    public function clientConfig(mixed $enabled = null, mixed $debug = null): array
    {
        return [
            'enabled' => $this->enabled($enabled),
            'debug' => $this->debug($debug),
            'dataLayer' => $this->dataLayerName(),
            'providers' => $this->providers(),
            'events' => $this->events(),
            'autoTrack' => [
                'clicks' => $this->boolValue(config('landing-analytics.auto_track.clicks', true), true),
                'forms' => $this->boolValue(config('landing-analytics.auto_track.forms', true), true),
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
        return $this->nullableString($provider['label'] ?? null)
            ?? Str::headline(str_replace('_', ' ', $name));
    }

    protected function conversionIds(array $conversionIds): array
    {
        return collect($conversionIds)
            ->mapWithKeys(function (mixed $value, mixed $event): array {
                $eventName = $this->nullableString($event);
                $conversionId = $this->nullableString($value);

                if ($eventName === null || $conversionId === null) {
                    return [];
                }

                return [$eventName => $conversionId];
            })
            ->all();
    }

    protected function scrollDepthConfig(): array
    {
        $config = (array) config('landing-analytics.auto_track.scroll_depth', []);
        $eventName = $this->nullableString($config['event_name'] ?? null) ?? 'scroll_depth';

        return [
            'enabled' => $this->boolValue($config['enabled'] ?? false, false)
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
        return collect((array) config("landing-analytics.selectors.{$group}", []))
            ->map(function (mixed $selector): ?array {
                if (! is_array($selector)) {
                    return null;
                }

                $cssSelector = $this->nullableString($selector['selector'] ?? null);
                $attribute = $this->nullableString($selector['attribute'] ?? null);

                if ($cssSelector === null || $attribute === null) {
                    return null;
                }

                return [
                    'selector' => $cssSelector,
                    'attribute' => $attribute,
                    'module' => $this->nullableString($selector['module'] ?? null),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function consentConfig(): array
    {
        $config = (array) config('landing-analytics.consent', []);
        $categories = (array) ($config['categories'] ?? []);

        return [
            'enabled' => $this->boolValue($config['enabled'] ?? false, false),
            'storageKey' => $this->nullableString($config['storage_key'] ?? null) ?? 'landing_cookie_consent',
            'defaultGranted' => $this->boolValue($config['default_granted'] ?? false, false),
            'categories' => [
                'analytics' => $this->nullableString(Arr::get($categories, 'analytics')) ?? 'analytics',
                'marketing' => $this->nullableString(Arr::get($categories, 'marketing')) ?? 'marketing',
            ],
        ];
    }

    protected function dataLayerName(): string
    {
        $name = $this->nullableString(config('landing-analytics.data_layer', 'dataLayer')) ?? 'dataLayer';

        return preg_match('/^[A-Za-z_$][A-Za-z0-9_$]*$/', $name) ? $name : 'dataLayer';
    }

    protected function boolValue(mixed $value, bool $default): bool
    {
        if ($value === null) {
            return $default;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
        }

        return (bool) $value;
    }

    protected function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
