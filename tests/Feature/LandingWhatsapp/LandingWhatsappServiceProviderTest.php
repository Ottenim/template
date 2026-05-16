<?php

namespace Tests\Feature\LandingWhatsapp;

use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;
use Template\LandingWhatsapp\Support\WhatsappUrl;
use Tests\TestCase;

class LandingWhatsappServiceProviderTest extends TestCase
{
    public function test_it_registers_services_and_core_integrations(): void
    {
        $this->assertInstanceOf(WhatsappUrl::class, app(WhatsappUrl::class));

        $module = app(ModuleRegistry::class)->get('landing-whatsapp');

        $this->assertSame('WhatsApp CTA', $module['label']);
        $this->assertTrue($module['enabled']);
        $this->assertSame('whatsapp::section', $module['section']['component']);

        $sections = app(SectionRenderer::class)->all();

        $this->assertSame('whatsapp::section', $sections['whatsapp']['component']);
        $this->assertSame('whatsapp::button', $sections['whatsapp-button']['component']);
        $this->assertSame('whatsapp::floating-button', $sections['whatsapp-floating']['component']);

        $styles = app(AssetRegistry::class)->styles();

        $this->assertSame('/vendor/landing-whatsapp/whatsapp.css', $styles['landing-whatsapp']['url']);
    }

    public function test_module_registration_respects_disabled_configuration(): void
    {
        config()->set('landing-whatsapp.enabled', false);
        config()->set('landing-whatsapp.section.enabled', false);

        $module = app(ModuleRegistry::class)->get('landing-whatsapp');

        $this->assertFalse($module['enabled']);
        $this->assertFalse($module['section']['enabled']);
    }
}
