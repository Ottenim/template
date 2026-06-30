<?php

namespace Tests\Unit\LandingLeadCapture;

use PHPUnit\Framework\TestCase;
use Template\LandingCore\Analytics\LandingEvent;
use Template\LandingLeadCapture\Config\LeadCaptureConfig;

class LeadCaptureConfigTest extends TestCase
{
    public function test_it_exposes_safe_defaults_when_config_is_empty(): void
    {
        $config = LeadCaptureConfig::fromArray([]);

        $this->assertTrue($config->enabled());
        $this->assertSame('inline', $config->variant());
        $this->assertTrue($config->routeEnabled());
        $this->assertSame('lead-capture', $config->routeUri());
        $this->assertSame('landing-lead-capture.submit', $config->routeName());
        $this->assertSame(['web'], $config->routeMiddleware());
        $this->assertSame('lp_leads', $config->databaseTable());
        $this->assertSame('Quero receber', $config->ctaButtonLabel());
        $this->assertFalse($config->emailEnabled());
        $this->assertSame('Novo lead capturado', $config->emailSubject());
        $this->assertTrue($config->honeypotEnabled());
        $this->assertSame('website', $config->honeypotField());
        $this->assertSame(5, $config->rateLimitMaxAttempts());
        $this->assertSame(1, $config->rateLimitDecayMinutes());
        $this->assertFalse($config->trackingEnabled());
    }

    public function test_tracking_event_default_comes_from_the_canonical_enum(): void
    {
        $config = LeadCaptureConfig::fromArray([]);

        $this->assertSame(LandingEvent::LeadCaptureSubmit->value, $config->trackingEventName());
        $this->assertSame('lead_capture_submit', $config->trackingEventName());
    }

    public function test_it_reads_and_coerces_configured_values(): void
    {
        $config = LeadCaptureConfig::fromArray([
            'enabled' => 'false',
            'route' => [
                'uri' => 'assine',
                'middleware' => ['web', ' ', 'auth'],
            ],
            'lead' => [
                'source' => '  paid-ads  ',
                'campaign' => 'black-friday',
            ],
            'send_email' => [
                'enabled' => '1',
                'to' => '  growth@example.test  ',
            ],
            'anti_spam' => [
                'rate_limit_max_attempts' => '9',
            ],
            'tracking' => [
                'enabled' => 1,
                'event_name' => 'custom_lead_event',
            ],
        ]);

        $this->assertFalse($config->enabled());
        $this->assertSame('assine', $config->routeUri());
        $this->assertSame(['web', 'auth'], $config->routeMiddleware());
        $this->assertSame('paid-ads', $config->leadSource());
        $this->assertSame('black-friday', $config->leadCampaign());
        $this->assertNull($config->leadTag());
        $this->assertTrue($config->emailEnabled());
        $this->assertSame('growth@example.test', $config->emailRecipient());
        $this->assertSame(9, $config->rateLimitMaxAttempts());
        $this->assertTrue($config->trackingEnabled());
        $this->assertSame('custom_lead_event', $config->trackingEventName());
    }
}
