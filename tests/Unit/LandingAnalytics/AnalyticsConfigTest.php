<?php

namespace Tests\Unit\LandingAnalytics;

use PHPUnit\Framework\TestCase;
use Template\LandingAnalytics\Config\AnalyticsConfig;
use Template\LandingCore\Analytics\LandingEvent;

class AnalyticsConfigTest extends TestCase
{
    public function test_it_exposes_safe_defaults_when_config_is_empty(): void
    {
        $config = AnalyticsConfig::fromArray([]);

        $this->assertTrue($config->enabled());
        $this->assertFalse($config->debug());
        $this->assertSame(['local', 'testing'], $config->debugEnvironments());
        $this->assertSame('dataLayer', $config->dataLayer());
        $this->assertSame([], $config->providers());
        $this->assertSame($this->defaultEvents(), $config->events());
        $this->assertTrue($config->autoTrackClicks());
        $this->assertTrue($config->autoTrackForms());
        $this->assertSame([
            'enabled' => false,
            'event_name' => LandingEvent::ScrollDepth->value,
            'percentages' => [25, 50, 75, 100],
        ], $config->autoTrackScrollDepth());
        $this->assertSame([], $config->selectors('clicks'));
        $this->assertSame([
            'enabled' => false,
            'storage_key' => 'landing_cookie_consent',
            'default_granted' => false,
            'categories' => [
                'analytics' => 'analytics',
                'marketing' => 'marketing',
            ],
        ], $config->consent());
    }

    public function test_it_reads_and_coerces_configured_values(): void
    {
        $config = AnalyticsConfig::fromArray([
            'enabled' => 'false',
            'debug' => '1',
            'debug_environments' => [' local ', '', ' production '],
            'data_layer' => ' lpDataLayer ',
            'providers' => [
                'gtm' => ['enabled' => true],
            ],
            'events' => [
                LandingEvent::PageView->value => 'false',
                'custom_event' => ['enabled' => '1'],
            ],
            'auto_track' => [
                'clicks' => '0',
                'forms' => 'false',
                'scroll_depth' => [
                    'enabled' => 'true',
                    'event_name' => ' custom_scroll ',
                    'percentages' => ['10', 50],
                ],
            ],
            'selectors' => [
                'clicks' => [
                    ['selector' => '[data-event]', 'attribute' => 'data-event'],
                ],
            ],
            'consent' => [
                'enabled' => true,
                'storage_key' => 'cookie_box',
            ],
        ]);

        $this->assertFalse($config->enabled());
        $this->assertTrue($config->debug());
        $this->assertSame(['local', 'production'], $config->debugEnvironments());
        $this->assertSame('lpDataLayer', $config->dataLayer());
        $this->assertSame(['gtm' => ['enabled' => true]], $config->providers());
        $this->assertSame([
            LandingEvent::PageView->value => 'false',
            'custom_event' => ['enabled' => '1'],
        ], $config->events());
        $this->assertFalse($config->autoTrackClicks());
        $this->assertFalse($config->autoTrackForms());
        $this->assertSame([
            'enabled' => 'true',
            'event_name' => ' custom_scroll ',
            'percentages' => ['10', 50],
        ], $config->autoTrackScrollDepth());
        $this->assertSame([
            ['selector' => '[data-event]', 'attribute' => 'data-event'],
        ], $config->selectors('clicks'));
        $this->assertSame([
            'enabled' => true,
            'storage_key' => 'cookie_box',
        ], $config->consent());
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
