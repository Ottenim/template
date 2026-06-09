<?php

namespace Tests\Feature\LandingAnalytics;

use Template\LandingAnalytics\Support\AnalyticsManager;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Tests\TestCase;

class LandingAnalyticsServiceProviderTest extends TestCase
{
    public function test_it_registers_services_and_core_integrations(): void
    {
        config()->set('landing-analytics.providers.gtm.id', 'GTM-TEST');
        config()->set('landing-analytics.providers.ga4.enabled', true);
        config()->set('landing-analytics.providers.ga4.id', 'G-TEST');

        $this->assertInstanceOf(AnalyticsManager::class, app(AnalyticsManager::class));

        $module = app(ModuleRegistry::class)->get('landing-analytics');

        $this->assertSame('Analytics / Tracking', $module['label']);
        $this->assertTrue($module['enabled']);
        $this->assertSame('analytics::head', $module['technical']['head_component']);
        $this->assertSame('analytics::body', $module['technical']['body_component']);
        $this->assertSame(['gtm', 'ga4'], $module['technical']['providers']);
        $this->assertContains('whatsapp_click', $module['technical']['events']);
        $this->assertContains('pricing_cta_click', $module['technical']['events']);

        $styles = app(AssetRegistry::class)->styles();

        $this->assertSame('/vendor/landing-analytics/analytics.css', $styles['landing-analytics']['url']);
    }

    public function test_module_registration_respects_disabled_configuration(): void
    {
        config()->set('landing-analytics.enabled', false);
        config()->set('landing-analytics.providers.gtm.id', 'GTM-TEST');

        $module = app(ModuleRegistry::class)->get('landing-analytics');

        $this->assertFalse($module['enabled']);
    }
}
