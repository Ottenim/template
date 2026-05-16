<?php

namespace Tests\Feature\LandingContact;

use Illuminate\Support\Facades\Route;
use Template\LandingContact\Support\ContactFields;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;
use Tests\TestCase;

class LandingContactServiceProviderTest extends TestCase
{
    public function test_it_registers_services_route_and_core_integrations(): void
    {
        $this->assertInstanceOf(ContactFields::class, app(ContactFields::class));

        $module = app(ModuleRegistry::class)->get('landing-contact');

        $this->assertSame('Contact Form', $module['label']);
        $this->assertTrue($module['enabled']);
        $this->assertSame('contact::section', $module['section']['component']);

        $sections = app(SectionRenderer::class)->all();

        $this->assertSame('contact::section', $sections['contact']['component']);
        $this->assertSame('contact::form', $sections['contact-form']['component']);

        $styles = app(AssetRegistry::class)->styles();

        $this->assertSame('/vendor/landing-contact/contact.css', $styles['landing-contact']['url']);

        $route = Route::getRoutes()->getByName('landing-contact.submit');

        $this->assertNotNull($route);
        $this->assertContains('POST', $route->methods());
        $this->assertContains('web', $route->middleware());
        $this->assertContains('throttle:5,1', $route->middleware());
    }

    public function test_module_registration_respects_disabled_configuration(): void
    {
        config()->set('landing-contact.enabled', false);
        config()->set('landing-contact.section.enabled', false);

        $module = app(ModuleRegistry::class)->get('landing-contact');

        $this->assertFalse($module['enabled']);
        $this->assertFalse($module['section']['enabled']);
    }
}
