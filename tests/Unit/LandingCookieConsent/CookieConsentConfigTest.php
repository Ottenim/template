<?php

namespace Tests\Unit\LandingCookieConsent;

use PHPUnit\Framework\TestCase;
use Template\LandingCookieConsent\Config\CookieConsentConfig;

class CookieConsentConfigTest extends TestCase
{
    public function test_it_exposes_safe_defaults_when_config_is_empty(): void
    {
        $config = CookieConsentConfig::fromArray([]);

        $this->assertTrue($config->enabled());
        $this->assertSame('landing_cookie_consent', $config->storageKey());
        $this->assertSame('1', $config->version());
        $this->assertNull($config->policyUrl());
        $this->assertSame(180, $config->consentLifetimeDays());
        $this->assertSame([], $config->categories());
        $this->assertTrue($config->bannerEnabled());
        $this->assertSame('bottom', $config->bannerPosition());
        $this->assertSame('bar', $config->bannerLayout());
        $this->assertNull($config->bannerTitle());
        $this->assertNull($config->bannerMessage());
        $this->assertSame('Saiba mais', $config->bannerPolicyLabel());
        $this->assertSame('Aceitar todos', $config->bannerAcceptAllLabel());
        $this->assertSame('Recusar opcionais', $config->bannerRejectOptionalLabel());
        $this->assertSame('Configurar', $config->bannerConfigureLabel());
        $this->assertSame('Privacidade', $config->bannerReopenLabel());
        $this->assertTrue($config->bannerShowReopenButton());
        $this->assertSame('Aviso de cookies', $config->bannerAriaLabel());
        $this->assertSame('Gerenciar preferencias de cookies', $config->modalTitle());
        $this->assertSame('Escolha quais categorias opcionais podem ser usadas.', $config->modalDescription());
        $this->assertSame('Salvar preferencias', $config->modalSavePreferencesLabel());
        $this->assertNull($config->modalAcceptAllLabel());
        $this->assertNull($config->modalRejectOptionalLabel());
        $this->assertSame('Fechar', $config->modalCloseLabel());
        $this->assertStringContainsString('data-landing-cookie-category', $config->scriptsSelector());
        $this->assertTrue($config->loggingEnabled());
        $this->assertFalse($config->loggingStoreIp());
        $this->assertTrue($config->loggingStoreUserAgent());
        $this->assertTrue($config->loggingDatabaseEnabled());
        $this->assertSame('lp_cookie_consents', $config->loggingDatabaseTable());
        $this->assertTrue($config->loggingRouteEnabled());
        $this->assertSame('cookie-consent', $config->loggingRouteUri());
        $this->assertSame('landing-cookie-consent.store', $config->loggingRouteName());
        $this->assertSame(['web'], $config->loggingRouteMiddleware());
        $this->assertTrue($config->loggingRouteRateLimit());
        $this->assertSame(30, $config->loggingRouteRateLimitMaxAttempts());
        $this->assertSame(1, $config->loggingRouteRateLimitDecayMinutes());
        $this->assertTrue($config->integrationsAnalyticsEnabled());
        $this->assertTrue($config->integrationsAnalyticsSyncConfig());
        $this->assertFalse($config->integrationsAnalyticsDefaultGranted());
        $this->assertSame([], $config->integrationsAnalyticsCategories());
    }

    public function test_it_reads_and_coerces_configured_values(): void
    {
        $config = CookieConsentConfig::fromArray([
            'enabled' => 'false',
            'storage_key' => ' consent_box ',
            'version' => ' 2 ',
            'policy_url' => ' ',
            'consent_lifetime_days' => '365',
            'banner' => [
                'enabled' => '0',
                'position' => 'top',
                'layout' => 'card',
                'title' => ' Privacidade ',
                'message' => ' Cookies ',
                'show_reopen_button' => 'false',
            ],
            'modal' => [
                'accept_all_label' => ' Aceitar ',
                'reject_optional_label' => ' Recusar ',
            ],
            'logging' => [
                'enabled' => 'true',
                'store_ip' => '1',
                'store_user_agent' => '0',
                'database' => [
                    'enabled' => 'false',
                    'table' => 'custom_cookie_consents',
                ],
                'route' => [
                    'middleware' => ['web', '', ' throttle ', '  '],
                    'rate_limit' => 'false',
                    'rate_limit_max_attempts' => '10',
                    'rate_limit_decay_minutes' => '2',
                ],
            ],
            'integrations' => [
                'analytics' => [
                    'enabled' => 'false',
                    'sync_config' => '0',
                    'default_granted' => '1',
                    'categories' => [
                        'analytics' => 'stats',
                    ],
                ],
            ],
        ]);

        $this->assertFalse($config->enabled());
        $this->assertSame('consent_box', $config->storageKey());
        $this->assertSame('2', $config->version());
        $this->assertNull($config->policyUrl());
        $this->assertSame(365, $config->consentLifetimeDays());
        $this->assertFalse($config->bannerEnabled());
        $this->assertSame('top', $config->bannerPosition());
        $this->assertSame('card', $config->bannerLayout());
        $this->assertSame('Privacidade', $config->bannerTitle());
        $this->assertSame('Cookies', $config->bannerMessage());
        $this->assertFalse($config->bannerShowReopenButton());
        $this->assertSame('Aceitar', $config->modalAcceptAllLabel());
        $this->assertSame('Recusar', $config->modalRejectOptionalLabel());
        $this->assertTrue($config->loggingEnabled());
        $this->assertTrue($config->loggingStoreIp());
        $this->assertFalse($config->loggingStoreUserAgent());
        $this->assertFalse($config->loggingDatabaseEnabled());
        $this->assertSame('custom_cookie_consents', $config->loggingDatabaseTable());
        $this->assertSame(['web', 'throttle'], $config->loggingRouteMiddleware());
        $this->assertFalse($config->loggingRouteRateLimit());
        $this->assertSame(10, $config->loggingRouteRateLimitMaxAttempts());
        $this->assertSame(2, $config->loggingRouteRateLimitDecayMinutes());
        $this->assertFalse($config->integrationsAnalyticsEnabled());
        $this->assertFalse($config->integrationsAnalyticsSyncConfig());
        $this->assertTrue($config->integrationsAnalyticsDefaultGranted());
        $this->assertSame(['analytics' => 'stats'], $config->integrationsAnalyticsCategories());
    }
}
