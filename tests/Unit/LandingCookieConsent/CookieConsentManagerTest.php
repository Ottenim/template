<?php

namespace Tests\Unit\LandingCookieConsent;

use Template\LandingCookieConsent\Support\CookieConsentManager;
use Tests\TestCase;

class CookieConsentManagerTest extends TestCase
{
    public function test_it_normalizes_categories_client_config_and_script_safe_json(): void
    {
        config()->set('landing-cookie-consent.storage_key', ' cookie_box ');
        config()->set('landing-cookie-consent.policy_url', ' ');
        config()->set('landing-cookie-consent.version', '</script><script>alert(1)</script>');
        config()->set('landing-cookie-consent.consent_lifetime_days', 0);
        config()->set('landing-cookie-consent.scripts.selector', ' ');
        config()->set('landing-cookie-consent.banner.show_reopen_button', 'false');
        config()->set('landing-cookie-consent.categories', [
            'necessary' => [
                'enabled' => false,
            ],
            'analytics data' => [
                'enabled' => true,
                'required' => 'false',
                'default' => 'true',
                'label' => ' Estatisticas ',
                'description' => ' Medicao opcional ',
            ],
            'marketing' => [
                'enabled' => 'false',
            ],
            'personalization' => [
                'enabled' => true,
                'required' => true,
                'label' => ' ',
            ],
        ]);

        $manager = new CookieConsentManager;
        $categories = $manager->categories();
        $clientConfig = $manager->clientConfig();

        $this->assertSame('cookie_box', $manager->storageKey());
        $this->assertNull($manager->policyUrl());
        $this->assertSame(180, $manager->lifetimeDays());
        $this->assertSame(['necessary', 'analytics_data', 'personalization'], array_keys($categories));
        $this->assertTrue($categories['necessary']['required']);
        $this->assertTrue($categories['necessary']['default']);
        $this->assertSame('Estatisticas', $categories['analytics_data']['label']);
        $this->assertSame('Medicao opcional', $categories['analytics_data']['description']);
        $this->assertFalse($categories['analytics_data']['required']);
        $this->assertTrue($categories['analytics_data']['default']);
        $this->assertSame('Personalization', $categories['personalization']['label']);
        $this->assertTrue($manager->hasOptionalCategories());

        $this->assertSame('cookie_box', $clientConfig['storageKey']);
        $this->assertSame(180, $clientConfig['lifetimeDays']);
        $this->assertSame('script[type="text/plain"][data-landing-cookie-category], script[type="text/plain"][data-cookie-category]', $clientConfig['scriptSelector']);
        $this->assertFalse($clientConfig['showReopenButton']);

        $json = $manager->json($clientConfig);

        $this->assertStringNotContainsString('</script>', $json);
        $this->assertStringContainsString('\u003C/script\u003E', $json);
        $this->assertSame('</script><script>alert(1)</script>', json_decode($json, true)['version']);
    }

    public function test_it_respects_disabled_flags_and_override(): void
    {
        $manager = new CookieConsentManager;

        $this->assertTrue($manager->enabled());
        $this->assertFalse($manager->enabled(false));

        config()->set('landing-cookie-consent.banner.enabled', false);

        $this->assertFalse($manager->enabled());

        config()->set('landing-cookie-consent.banner.enabled', true);
        config()->set('landing-cookie-consent.enabled', false);

        $this->assertFalse($manager->enabled());
    }

    public function test_logging_config_disables_endpoint_when_database_or_route_logging_is_off(): void
    {
        $manager = new CookieConsentManager;

        $this->assertTrue($manager->clientConfig()['logging']['enabled']);
        $this->assertSame('http://localhost:8000/cookie-consent', $manager->clientConfig()['logging']['endpoint']);

        config()->set('landing-cookie-consent.logging.database.enabled', false);

        $clientConfig = $manager->clientConfig();

        $this->assertFalse($clientConfig['logging']['enabled']);
        $this->assertNull($clientConfig['logging']['endpoint']);

        config()->set('landing-cookie-consent.logging.database.enabled', true);
        config()->set('landing-cookie-consent.logging.route.enabled', false);

        $clientConfig = $manager->clientConfig();

        $this->assertFalse($clientConfig['logging']['enabled']);
        $this->assertNull($clientConfig['logging']['endpoint']);
    }
}
