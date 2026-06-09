<?php

namespace Tests\Unit\LandingAnalytics;

use Template\LandingAnalytics\Support\AnalyticsManager;
use Tests\TestCase;

class AnalyticsManagerTest extends TestCase
{
    public function test_it_normalizes_providers_events_consent_and_scroll_depth_config(): void
    {
        config()->set('landing-analytics.providers.gtm.id', ' GTM-TEST ');
        config()->set('landing-analytics.providers.ga4.enabled', true);
        config()->set('landing-analytics.providers.ga4.id', ' G-TEST ');
        config()->set('landing-analytics.providers.ga4.send_page_view', 'true');
        config()->set('landing-analytics.providers.meta_pixel.enabled', true);
        config()->set('landing-analytics.providers.meta_pixel.id', ' ');
        config()->set('landing-analytics.providers.linkedin_insight.enabled', true);
        config()->set('landing-analytics.providers.linkedin_insight.id', '123456');
        config()->set('landing-analytics.providers.linkedin_insight.conversion_ids', [
            'contact_submit' => ' 789 ',
            'empty' => ' ',
        ]);
        config()->set('landing-analytics.events.scroll_depth', true);
        config()->set('landing-analytics.events.disabled_event', false);
        config()->set('landing-analytics.auto_track.scroll_depth.enabled', 'true');
        config()->set('landing-analytics.auto_track.scroll_depth.percentages', [0, 25, '50', 50, 120]);
        config()->set('landing-analytics.data_layer', 'invalid-name');
        config()->set('landing-analytics.consent.enabled', true);
        config()->set('landing-analytics.consent.storage_key', ' cookie-consent ');
        config()->set('landing-analytics.consent.categories.analytics', 'stats');

        $config = (new AnalyticsManager)->clientConfig();

        $this->assertSame('dataLayer', $config['dataLayer']);
        $this->assertSame(['gtm', 'ga4', 'linkedin_insight'], array_keys($config['providers']));
        $this->assertSame('GTM-TEST', $config['providers']['gtm']['id']);
        $this->assertSame('Google Tag Manager', $config['providers']['gtm']['label']);
        $this->assertSame('G-TEST', $config['providers']['ga4']['id']);
        $this->assertTrue($config['providers']['ga4']['send_page_view']);
        $this->assertSame(['contact_submit' => '789'], $config['providers']['linkedin_insight']['conversion_ids']);
        $this->assertArrayNotHasKey('meta_pixel', $config['providers']);

        $this->assertTrue($config['events']['scroll_depth']);
        $this->assertFalse($config['events']['disabled_event']);
        $this->assertSame([
            'enabled' => true,
            'event' => 'scroll_depth',
            'percentages' => [25, 50],
        ], $config['autoTrack']['scrollDepth']);
        $this->assertSame('cookie-consent', $config['consent']['storageKey']);
        $this->assertSame('stats', $config['consent']['categories']['analytics']);
    }

    public function test_it_respects_disabled_module_and_escapes_json_for_script_context(): void
    {
        config()->set('landing-analytics.enabled', false);
        config()->set('landing-analytics.providers.gtm.id', 'GTM-TEST');

        $manager = new AnalyticsManager;

        $this->assertFalse($manager->enabled());
        $this->assertFalse($manager->debug(true));
        $this->assertFalse($manager->shouldRenderNoScript());

        $json = $manager->json([
            'event' => '</script><script>alert(1)</script>',
        ]);

        $this->assertStringNotContainsString('</script>', $json);
        $this->assertStringContainsString('\u003C/script\u003E', $json);
        $this->assertSame('</script><script>alert(1)</script>', json_decode($json, true)['event']);
    }

    public function test_noscript_respects_consent_defaults(): void
    {
        config()->set('landing-analytics.providers.gtm.id', 'GTM-TEST');
        config()->set('landing-analytics.consent.enabled', true);
        config()->set('landing-analytics.consent.default_granted', false);

        $manager = new AnalyticsManager;

        $this->assertFalse($manager->shouldRenderNoScript());

        config()->set('landing-analytics.consent.default_granted', true);

        $this->assertTrue($manager->shouldRenderNoScript());
    }
}
