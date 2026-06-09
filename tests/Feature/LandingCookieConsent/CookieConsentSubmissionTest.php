<?php

namespace Tests\Feature\LandingCookieConsent;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Template\LandingCookieConsent\Models\CookieConsent;
use Tests\TestCase;

class CookieConsentSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_records_valid_consent_payload_and_normalizes_categories(): void
    {
        $acceptedAt = now()->subMinute()->toISOString();
        $expiresAt = now()->addDays(180)->toISOString();

        $response = $this->withHeader('User-Agent', 'Landing Test Browser')
            ->postJson(route('landing-cookie-consent.store'), [
                'consent_id' => 'consent-123',
                'version' => '1',
                'action' => 'save_preferences',
                'categories' => [
                    'necessary' => false,
                    'analytics' => true,
                    'marketing' => false,
                    'unknown' => true,
                ],
                'policy_url' => '/politica-de-privacidade',
                'url' => 'https://example.test/landing',
                'accepted_at' => $acceptedAt,
                'expires_at' => $expiresAt,
            ]);

        $response->assertOk();
        $response->assertJson([
            'recorded' => true,
        ]);

        $this->assertDatabaseHas('lp_cookie_consents', [
            'consent_id' => 'consent-123',
            'version' => '1',
            'action' => 'save_preferences',
            'policy_url' => '/politica-de-privacidade',
            'page_url' => 'https://example.test/landing',
            'ip_address' => null,
            'user_agent' => 'Landing Test Browser',
        ]);

        $consent = CookieConsent::query()->firstOrFail();

        $this->assertSame([
            'necessary' => true,
            'analytics' => true,
            'marketing' => false,
        ], $consent->categories);
        $this->assertNotNull($consent->accepted_at);
        $this->assertNotNull($consent->expires_at);
    }

    public function test_it_rejects_invalid_consent_payload(): void
    {
        $response = $this->postJson(route('landing-cookie-consent.store'), [
            'consent_id' => str_repeat('a', 101),
            'version' => str_repeat('b', 81),
            'action' => 'invalid',
            'categories' => 'not-array',
            'policy_url' => str_repeat('c', 2049),
            'url' => 'not-a-url',
            'accepted_at' => '2026-01-02T00:00:00Z',
            'expires_at' => '2026-01-01T00:00:00Z',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'consent_id',
            'version',
            'action',
            'categories',
            'policy_url',
            'url',
            'expires_at',
        ]);

        $this->assertDatabaseCount('lp_cookie_consents', 0);
    }

    public function test_it_can_skip_database_logging(): void
    {
        config()->set('landing-cookie-consent.logging.enabled', false);

        $response = $this->postJson(route('landing-cookie-consent.store'), [
            'consent_id' => 'consent-456',
            'version' => '1',
            'action' => 'accept_all',
            'categories' => [
                'necessary' => true,
                'analytics' => true,
                'marketing' => true,
            ],
            'url' => 'https://example.test/landing',
        ]);

        $response->assertOk();
        $response->assertJson([
            'recorded' => false,
        ]);

        $this->assertDatabaseCount('lp_cookie_consents', 0);
    }
}
