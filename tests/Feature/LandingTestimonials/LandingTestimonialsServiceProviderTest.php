<?php

namespace Tests\Feature\LandingTestimonials;

use Illuminate\Support\Facades\Route;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\MenuRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;
use Template\LandingTestimonials\Support\Testimonials;
use Tests\TestCase;

class LandingTestimonialsServiceProviderTest extends TestCase
{
    public function test_it_registers_services_and_core_integrations(): void
    {
        $this->assertInstanceOf(Testimonials::class, app(Testimonials::class));

        $module = app(ModuleRegistry::class)->get('landing-testimonials');

        $this->assertSame('Testimonials', $module['label']);
        $this->assertTrue($module['enabled']);
        $this->assertNull($module['admin_route']);
        $this->assertSame('testimonials::section', $module['section']['component']);
        $this->assertTrue($module['section']['enabled']);

        $menus = app(MenuRegistry::class)->all();

        $this->assertFalse($menus['landing-testimonials']['enabled']);
        $this->assertSame('testimonials.admin.index', $menus['landing-testimonials']['route']);

        $sections = app(SectionRenderer::class)->all();

        $this->assertSame('testimonials::section', $sections['testimonials']['component']);
        $this->assertSame('testimonials::section', $sections['testimonials-section']['component']);

        $styles = app(AssetRegistry::class)->styles();

        $this->assertSame('/vendor/landing-testimonials/testimonials.css', $styles['landing-testimonials']['url']);
        $this->assertNull(Route::getRoutes()->getByName('testimonials.admin.index'));
    }

    public function test_module_registration_respects_disabled_configuration(): void
    {
        config()->set('landing-testimonials.enabled', false);
        config()->set('landing-testimonials.section.enabled', false);

        $module = app(ModuleRegistry::class)->get('landing-testimonials');

        $this->assertFalse($module['enabled']);
        $this->assertFalse($module['section']['enabled']);
    }
}
