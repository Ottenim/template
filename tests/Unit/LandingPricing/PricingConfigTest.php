<?php

namespace Tests\Unit\LandingPricing;

use PHPUnit\Framework\TestCase;
use Template\LandingCore\Analytics\LandingEvent;
use Template\LandingPricing\Config\PricingConfig;

class PricingConfigTest extends TestCase
{
    public function test_it_exposes_safe_defaults_when_config_is_empty(): void
    {
        $config = PricingConfig::fromArray([]);

        $this->assertTrue($config->enabled());
        $this->assertSame('cards', $config->layout());
        $this->assertSame(3, $config->columns());
        $this->assertTrue($config->showFeaturedPlan());
        $this->assertNull($config->featuredLabel());
        $this->assertSame('R$', $config->currency());
        $this->assertSame('/mes', $config->billingPeriodLabel());
        $this->assertNull($config->limit());
        $this->assertTrue($config->sectionEnabled());
        $this->assertTrue($config->databaseEnabled());
        $this->assertSame('lp_pricing_plans', $config->databaseTable());
        $this->assertSame([], $config->plans());
        $this->assertSame('Escolher plano', $config->ctaDefaultLabel());
        $this->assertSame('#contact', $config->ctaDefaultUrl());
        $this->assertTrue($config->trackingEnabled());
        $this->assertFalse($config->adminEnabled());
        $this->assertSame('admin/pricing', $config->adminPrefix());
        $this->assertSame(['web', 'auth'], $config->adminMiddleware());
        $this->assertSame(15, $config->adminPerPage());
    }

    public function test_tracking_event_default_comes_from_the_canonical_enum(): void
    {
        $config = PricingConfig::fromArray([]);

        $this->assertSame(LandingEvent::PricingCtaClick->value, $config->trackingEventName());
        $this->assertSame('pricing_cta_click', $config->trackingEventName());
    }

    public function test_it_reads_and_coerces_configured_values(): void
    {
        $config = PricingConfig::fromArray([
            'enabled' => 'false',
            'layout' => 'table',
            'columns' => '4',
            'show_featured_plan' => '0',
            'featured_label' => '  Top  ',
            'currency' => 'US$',
            'billing_period_label' => '/mo',
            'limit' => '3',
            'database' => [
                'enabled' => 'false',
                'table' => 'custom_plans',
            ],
            'cta' => [
                'default_label' => 'Assinar',
                'default_url' => '#planos',
            ],
            'tracking' => [
                'enabled' => 'false',
                'event_name' => 'plan_click',
            ],
            'admin' => [
                'enabled' => 'true',
                'prefix' => 'painel/planos',
                'middleware' => ['web', '', 'auth'],
                'per_page' => '20',
            ],
        ]);

        $this->assertFalse($config->enabled());
        $this->assertSame('table', $config->layout());
        $this->assertSame(4, $config->columns());
        $this->assertFalse($config->showFeaturedPlan());
        $this->assertSame('Top', $config->featuredLabel());
        $this->assertSame('US$', $config->currency());
        $this->assertSame('/mo', $config->billingPeriodLabel());
        $this->assertSame(3, $config->limit());
        $this->assertFalse($config->databaseEnabled());
        $this->assertSame('custom_plans', $config->databaseTable());
        $this->assertSame('Assinar', $config->ctaDefaultLabel());
        $this->assertSame('#planos', $config->ctaDefaultUrl());
        $this->assertFalse($config->trackingEnabled());
        $this->assertSame('plan_click', $config->trackingEventName());
        $this->assertTrue($config->adminEnabled());
        $this->assertSame('painel/planos', $config->adminPrefix());
        $this->assertSame(['web', 'auth'], $config->adminMiddleware());
        $this->assertSame(20, $config->adminPerPage());
    }
}
