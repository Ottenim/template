<?php

namespace Tests\Feature\LandingCookieConsent;

use Template\LandingAnalytics\Support\AnalyticsManager;
use Template\LandingCookieConsent\Support\CookieConsentManager;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;
use Tests\TestCase;

class LandingCookieConsentServiceProviderTest extends TestCase
{
    public function test_it_registers_services_core_integrations_and_analytics_consent(): void
    {
        $this->assertInstanceOf(CookieConsentManager::class, app(CookieConsentManager::class));

        $module = app(ModuleRegistry::class)->get('landing-cookie-consent');

        $this->assertSame('Cookie Consent / LGPD', $module['label']);
        $this->assertTrue($module['enabled']);
        $this->assertSame('cookie-consent::banner', $module['technical']['component']);
        $this->assertSame('landing_cookie_consent', $module['technical']['storage_key']);
        $this->assertSame(['necessary', 'analytics', 'marketing'], $module['technical']['categories']);
        $this->assertSame('/politica-de-privacidade', $module['technical']['policy_url']);

        $sections = app(SectionRenderer::class)->all();

        $this->assertSame('cookie-consent::banner', $sections['cookie-consent']['component']);
        $this->assertSame('cookie-consent::banner', $sections['lgpd']['component']);

        $styles = app(AssetRegistry::class)->styles();

        $this->assertSame('/vendor/landing-cookie-consent/cookie-consent.css', $styles['landing-cookie-consent']['url']);

        $analyticsConsent = app(AnalyticsManager::class)->clientConfig()['consent'];

        $this->assertTrue($analyticsConsent['enabled']);
        $this->assertSame('landing_cookie_consent', $analyticsConsent['storageKey']);
        $this->assertFalse($analyticsConsent['defaultGranted']);
        $this->assertSame('analytics', $analyticsConsent['categories']['analytics']);
        $this->assertSame('marketing', $analyticsConsent['categories']['marketing']);
    }

    public function test_module_registration_respects_disabled_configuration(): void
    {
        config()->set('landing-cookie-consent.enabled', false);

        $module = app(ModuleRegistry::class)->get('landing-cookie-consent');

        $this->assertFalse($module['enabled']);
    }
}
