<?php

namespace Tests\Feature\LandingAnalytics;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class AnalyticsComponentsTest extends TestCase
{
    public function test_head_component_renders_script_safe_config_and_event_dispatcher(): void
    {
        config()->set('landing-analytics.providers.gtm.id', 'GTM-<unsafe>');
        config()->set('landing-analytics.providers.ga4.enabled', true);
        config()->set('landing-analytics.providers.ga4.id', 'G-&TEST');
        config()->set('landing-analytics.debug', true);

        $html = Blade::render('<x-analytics::head />');

        $this->assertStringContainsString('id="landing-analytics-config"', $html);
        $this->assertStringContainsString('id="landing-analytics-loader"', $html);
        $this->assertStringContainsString('window.addEventListener(\'landing:track\'', $html);
        $this->assertStringContainsString('dataLayer', $html);
        $this->assertStringContainsString('Google Tag Manager', $html);
        $this->assertStringContainsString('Google Analytics 4', $html);
        $this->assertStringContainsString('\u003Cunsafe\u003E', $html);
        $this->assertStringContainsString('G-\u0026TEST', $html);
        $this->assertStringNotContainsString('GTM-<unsafe>', $html);
        $this->assertStringNotContainsString('G-&TEST', $html);
    }

    public function test_head_component_does_not_render_when_disabled(): void
    {
        config()->set('landing-analytics.enabled', false);
        config()->set('landing-analytics.providers.gtm.id', 'GTM-TEST');

        $html = Blade::render('<x-analytics::head />');

        $this->assertSame('', trim($html));
    }

    public function test_body_component_renders_safe_noscript_tags_and_debug_panel(): void
    {
        config()->set('landing-analytics.providers.gtm.id', 'GTM-<unsafe>');
        config()->set('landing-analytics.providers.meta_pixel.enabled', true);
        config()->set('landing-analytics.providers.meta_pixel.id', '123&456');
        config()->set('landing-analytics.providers.linkedin_insight.enabled', true);
        config()->set('landing-analytics.providers.linkedin_insight.id', '789');
        config()->set('landing-analytics.debug', true);

        $html = Blade::render('<x-analytics::body />');

        $this->assertStringContainsString('googletagmanager.com/ns.html?id=GTM-%3Cunsafe%3E', $html);
        $this->assertStringContainsString('facebook.com/tr?id=123%26456', $html);
        $this->assertStringContainsString('px.ads.linkedin.com/collect/?pid=789', $html);
        $this->assertStringContainsString('id="landing-analytics-styles"', $html);
        $this->assertStringContainsString('class="lp-card lp-analytics-debug"', $html);
        $this->assertStringContainsString('Google Tag Manager', $html);
        $this->assertStringContainsString('Meta Pixel', $html);
        $this->assertStringContainsString('LinkedIn Insight Tag', $html);
        $this->assertStringNotContainsString('GTM-<unsafe>', $html);
        $this->assertStringNotContainsString('123&456', $html);
    }

    public function test_body_component_omits_noscript_when_consent_is_required_first(): void
    {
        config()->set('landing-analytics.providers.gtm.id', 'GTM-TEST');
        config()->set('landing-analytics.consent.enabled', true);
        config()->set('landing-analytics.consent.default_granted', false);
        config()->set('landing-analytics.debug', false);

        $html = Blade::render('<x-analytics::body />');

        $this->assertStringNotContainsString('<noscript>', $html);
        $this->assertStringNotContainsString('landing-analytics-debug', $html);
    }

    public function test_debug_component_escapes_provider_labels_and_lists_events(): void
    {
        config()->set('landing-analytics.providers.gtm.id', 'GTM-TEST');
        config()->set('landing-analytics.providers.gtm.label', '<script>alert(1)</script>');
        config()->set('landing-analytics.events.custom_event', true);

        $html = Blade::render('<x-analytics::debug enabled="true" />');

        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
        $this->assertStringContainsString('custom_event', $html);
        $this->assertStringContainsString('data-landing-analytics-events', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    public function test_styles_component_renders_debug_css_once_per_render(): void
    {
        $html = Blade::render('<x-analytics::styles />');

        $this->assertStringContainsString('id="landing-analytics-styles"', $html);
        $this->assertStringContainsString('.lp-analytics-debug', $html);
        $this->assertStringContainsString('var(--lp-color-text)', $html);
    }
}
