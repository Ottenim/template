<?php

namespace Tests\Feature\LandingPricing;

use Illuminate\Support\Facades\Route;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\MenuRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;
use Template\LandingPricing\Support\PricingPlans;
use Tests\TestCase;

class LandingPricingServiceProviderTest extends TestCase
{
    public function test_it_registers_services_and_core_integrations(): void
    {
        $this->assertInstanceOf(PricingPlans::class, app(PricingPlans::class));

        $module = app(ModuleRegistry::class)->get('landing-pricing');

        $this->assertSame('Pricing', $module['label']);
        $this->assertTrue($module['enabled']);
        $this->assertNull($module['admin_route']);
        $this->assertSame('pricing::section', $module['section']['component']);
        $this->assertTrue($module['section']['enabled']);

        $menus = app(MenuRegistry::class)->all();

        $this->assertFalse($menus['landing-pricing']['enabled']);
        $this->assertSame('pricing.admin.index', $menus['landing-pricing']['route']);

        $sections = app(SectionRenderer::class)->all();

        $this->assertSame('pricing::section', $sections['pricing']['component']);
        $this->assertSame('pricing::section', $sections['pricing-section']['component']);

        $styles = app(AssetRegistry::class)->styles();

        $this->assertSame('/vendor/landing-pricing/pricing.css', $styles['landing-pricing']['url']);
        $this->assertNull(Route::getRoutes()->getByName('pricing.admin.index'));
    }

    public function test_module_registration_respects_disabled_configuration(): void
    {
        config()->set('landing-pricing.enabled', false);
        config()->set('landing-pricing.section.enabled', false);

        $module = app(ModuleRegistry::class)->get('landing-pricing');

        $this->assertFalse($module['enabled']);
        $this->assertFalse($module['section']['enabled']);
    }
}
