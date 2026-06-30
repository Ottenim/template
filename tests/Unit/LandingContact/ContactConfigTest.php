<?php

namespace Tests\Unit\LandingContact;

use PHPUnit\Framework\TestCase;
use Template\LandingContact\Config\ContactConfig;
use Template\LandingCore\Analytics\LandingEvent;

class ContactConfigTest extends TestCase
{
    public function test_it_exposes_safe_defaults_when_config_is_empty(): void
    {
        $config = ContactConfig::fromArray([]);

        $this->assertTrue($config->enabled());
        $this->assertTrue($config->routeEnabled());
        $this->assertSame('contact', $config->routeUri());
        $this->assertSame('landing-contact.submit', $config->routeName());
        $this->assertSame(['web'], $config->routeMiddleware());
        $this->assertSame('lp_contact_messages', $config->databaseTable());
        $this->assertSame('Nova mensagem de contato', $config->emailSubject());
        $this->assertNull($config->emailRecipient());
        $this->assertSame('Solicitar contato', $config->buttonLabel());
        $this->assertTrue($config->honeypotEnabled());
        $this->assertSame('website', $config->honeypotField());
        $this->assertSame(5, $config->rateLimitMaxAttempts());
        $this->assertSame(1, $config->rateLimitDecayMinutes());
        $this->assertFalse($config->trackingEnabled());
    }

    public function test_tracking_event_default_comes_from_the_canonical_enum(): void
    {
        $config = ContactConfig::fromArray([]);

        $this->assertSame(LandingEvent::ContactFormSubmit->value, $config->trackingEventName());
        $this->assertSame('contact_form_submit', $config->trackingEventName());
    }

    public function test_it_reads_and_coerces_configured_values(): void
    {
        $config = ContactConfig::fromArray([
            'enabled' => 'false',
            'route' => [
                'uri' => 'fale-conosco',
                'middleware' => ['web', ' ', 'auth'],
            ],
            'send_email' => [
                'to' => '  sales@example.test  ',
            ],
            'anti_spam' => [
                'honeypot_field' => '  trap  ',
                'rate_limit_max_attempts' => '12',
            ],
            'tracking' => [
                'enabled' => 1,
                'event_name' => 'custom_event',
            ],
        ]);

        $this->assertFalse($config->enabled());
        $this->assertSame('fale-conosco', $config->routeUri());
        $this->assertSame(['web', 'auth'], $config->routeMiddleware());
        $this->assertSame('sales@example.test', $config->emailRecipient());
        $this->assertSame('trap', $config->honeypotField());
        $this->assertSame(12, $config->rateLimitMaxAttempts());
        $this->assertTrue($config->trackingEnabled());
        $this->assertSame('custom_event', $config->trackingEventName());
    }
}
