<?php

namespace Tests\Feature\LandingFaq;

use Illuminate\Support\Facades\Route;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\MenuRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;
use Template\LandingFaq\Support\FaqItems;
use Tests\TestCase;

class LandingFaqServiceProviderTest extends TestCase
{
    public function test_it_registers_services_and_core_integrations(): void
    {
        $this->assertInstanceOf(FaqItems::class, app(FaqItems::class));

        $module = app(ModuleRegistry::class)->get('landing-faq');

        $this->assertSame('FAQ', $module['label']);
        $this->assertTrue($module['enabled']);
        $this->assertNull($module['admin_route']);
        $this->assertSame('faq::section', $module['section']['component']);
        $this->assertTrue($module['section']['enabled']);

        $menus = app(MenuRegistry::class)->all();

        $this->assertFalse($menus['landing-faq']['enabled']);
        $this->assertSame('faq.admin.index', $menus['landing-faq']['route']);

        $sections = app(SectionRenderer::class)->all();

        $this->assertSame('faq::section', $sections['faq']['component']);
        $this->assertSame('faq::section', $sections['faq-section']['component']);

        $styles = app(AssetRegistry::class)->styles();

        $this->assertSame('/vendor/landing-faq/faq.css', $styles['landing-faq']['url']);
        $this->assertNull(Route::getRoutes()->getByName('faq.admin.index'));
    }

    public function test_module_registration_respects_disabled_configuration(): void
    {
        config()->set('landing-faq.enabled', false);
        config()->set('landing-faq.section.enabled', false);

        $module = app(ModuleRegistry::class)->get('landing-faq');

        $this->assertFalse($module['enabled']);
        $this->assertFalse($module['section']['enabled']);
    }
}
