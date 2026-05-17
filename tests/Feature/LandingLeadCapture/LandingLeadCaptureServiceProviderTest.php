<?php

namespace Tests\Feature\LandingLeadCapture;

use Illuminate\Support\Facades\Route;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;
use Template\LandingLeadCapture\Support\LeadCaptureFields;
use Tests\TestCase;

class LandingLeadCaptureServiceProviderTest extends TestCase
{
    public function test_it_registers_services_route_and_core_integrations(): void
    {
        $this->assertInstanceOf(LeadCaptureFields::class, app(LeadCaptureFields::class));

        $module = app(ModuleRegistry::class)->get('landing-lead-capture');

        $this->assertSame('Lead Capture', $module['label']);
        $this->assertTrue($module['enabled']);
        $this->assertSame('lead-capture::section', $module['section']['component']);

        $sections = app(SectionRenderer::class)->all();

        $this->assertSame('lead-capture::section', $sections['lead-capture']['component']);
        $this->assertSame('lead-capture::form', $sections['lead-capture-form']['component']);

        $styles = app(AssetRegistry::class)->styles();

        $this->assertSame('/vendor/landing-lead-capture/lead-capture.css', $styles['landing-lead-capture']['url']);

        $route = Route::getRoutes()->getByName('landing-lead-capture.submit');

        $this->assertNotNull($route);
        $this->assertContains('POST', $route->methods());
        $this->assertContains('web', $route->middleware());
        $this->assertContains('throttle:5,1', $route->middleware());
    }

    public function test_module_registration_respects_disabled_configuration(): void
    {
        config()->set('landing-lead-capture.enabled', false);
        config()->set('landing-lead-capture.section.enabled', false);

        $module = app(ModuleRegistry::class)->get('landing-lead-capture');

        $this->assertFalse($module['enabled']);
        $this->assertFalse($module['section']['enabled']);
    }
}
