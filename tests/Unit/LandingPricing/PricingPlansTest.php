<?php

namespace Tests\Unit\LandingPricing;

use Template\LandingPricing\Support\PricingPlans;
use Template\LandingPricing\Support\PricingUrl;
use Tests\TestCase;

class PricingPlansTest extends TestCase
{
    public function test_it_normalizes_filters_sorts_and_limits_public_plans(): void
    {
        config()->set('landing-pricing.currency', 'R$');
        config()->set('landing-pricing.billing_period_label', '/mes');
        config()->set('landing-pricing.featured_label', 'Mais escolhido');
        config()->set('landing-pricing.cta.default_label', 'Escolher plano');
        config()->set('landing-pricing.cta.default_url', '#contact');

        $plans = (new PricingPlans)->publicPlans([
            [
                'name' => ' Later plan ',
                'description' => ' Later description ',
                'price' => ' 199 ',
                'features' => [
                    ['text' => ' Feature one '],
                    ['label' => ' Feature two '],
                    '',
                ],
                'cta_label' => ' Buy later ',
                'cta_url' => ' /later ',
                'sort_order' => 20,
            ],
            [
                'name' => 'Hidden plan',
                'price' => '49',
                'sort_order' => 0,
                'is_active' => false,
            ],
            [
                'name' => ' Featured plan ',
                'description' => ' Featured description ',
                'price' => '99',
                'features' => "First feature\n\nSecond feature",
                'cta_url' => "java\nscript:alert(1)",
                'sort_order' => 10,
                'is_featured' => 'true',
            ],
            [
                'name' => '',
                'price' => 'Missing name',
            ],
        ], 2);

        $this->assertCount(2, $plans);
        $this->assertSame(['Featured plan', 'Later plan'], $plans->pluck('name')->all());
        $this->assertSame('Featured description', $plans->first()['description']);
        $this->assertSame('R$', $plans->first()['currency']);
        $this->assertSame('/mes', $plans->first()['billing_period_label']);
        $this->assertSame(['First feature', 'Second feature'], $plans->first()['features']);
        $this->assertSame('Escolher plano', $plans->first()['cta_label']);
        $this->assertNull($plans->first()['cta_url']);
        $this->assertSame('Mais escolhido', $plans->first()['badge']);
        $this->assertTrue($plans->first()['is_featured']);
        $this->assertSame(['Feature one', 'Feature two'], $plans->last()['features']);
        $this->assertSame('/later', $plans->last()['cta_url']);
        $this->assertArrayNotHasKey('position', $plans->first());
    }

    public function test_it_can_include_inactive_plans_when_requested(): void
    {
        $plans = (new PricingPlans)->publicPlans([
            [
                'name' => 'Inactive plan',
                'is_active' => false,
            ],
        ], activeOnly: false);

        $this->assertCount(1, $plans);
        $this->assertFalse($plans->first()['is_active']);
    }

    public function test_pricing_url_normalizes_safe_urls_and_rejects_unsafe_schemes(): void
    {
        $this->assertSame('#contact', PricingUrl::normalize(' #contact '));
        $this->assertSame('/checkout', PricingUrl::normalize('/checkout'));
        $this->assertSame('https://example.com/checkout', PricingUrl::normalize('https://example.com/checkout'));
        $this->assertSame('mailto:sales@example.com', PricingUrl::normalize('mailto:sales@example.com'));

        $this->assertNull(PricingUrl::normalize('javascript:alert(1)'));
        $this->assertNull(PricingUrl::normalize("java\nscript:alert(1)"));
        $this->assertNull(PricingUrl::normalize('data:text/html,<script>alert(1)</script>'));
        $this->assertNull(PricingUrl::normalize('vbscript:msgbox(1)'));
    }
}
