<?php

namespace Tests\Feature\LandingCore;

use Illuminate\Support\Facades\Blade;
use Template\LandingCore\Support\AssetRegistry;
use Template\LandingCore\Support\MenuRegistry;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;
use Template\LandingCore\Theme\ThemeManager;
use Tests\TestCase;

class LandingCoreServiceProviderTest extends TestCase
{
    public function test_it_registers_core_services_from_configuration(): void
    {
        $this->assertInstanceOf(ThemeManager::class, app(ThemeManager::class));
        $this->assertInstanceOf(ModuleRegistry::class, landing_modules());
        $this->assertInstanceOf(MenuRegistry::class, landing_menus());
        $this->assertInstanceOf(AssetRegistry::class, landing_assets());
        $this->assertInstanceOf(SectionRenderer::class, app(SectionRenderer::class));

        $coreModule = landing_modules()->get('landing-core');

        $this->assertSame('Landing Core', $coreModule['label']);
        $this->assertTrue($coreModule['enabled']);
    }

    public function test_base_layout_renders_theme_variables_core_styles_and_slot_content(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-landing-core::base-layout title="Landing Test" body-class="custom-body">
                <main>Landing content</main>
            </x-landing-core::base-layout>
        BLADE);

        $this->assertStringContainsString('<!DOCTYPE html>', $html);
        $this->assertStringContainsString('<title>Landing Test</title>', $html);
        $this->assertStringContainsString('id="landing-core-theme-variables"', $html);
        $this->assertStringContainsString('id="landing-core-base-styles"', $html);
        $this->assertStringContainsString('class="lp-body custom-body"', $html);
        $this->assertStringContainsString('<main>Landing content</main>', $html);
    }

    public function test_core_blade_components_render_their_base_contract_classes(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-landing-core::section id="hero">
                <x-landing-core::section-header eyebrow="Core" title="Landing Core" subtitle="Base module" center />
                <x-landing-core::card>
                    <x-landing-core::button href="/contact" variant="secondary">Contact</x-landing-core::button>
                </x-landing-core::card>
            </x-landing-core::section>
        BLADE);

        $this->assertStringContainsString('id="hero"', $html);
        $this->assertStringContainsString('class="lp-section"', $html);
        $this->assertStringContainsString('class="lp-container"', $html);
        $this->assertStringContainsString('lp-section-header-center', $html);
        $this->assertStringContainsString('class="lp-card"', $html);
        $this->assertStringContainsString('class="lp-button lp-button-secondary"', $html);
        $this->assertStringContainsString('href="/contact"', $html);
    }
}
