<?php

namespace Tests\Feature\LandingCore;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Template\LandingCore\Support\ModuleRegistry;
use Template\LandingCore\Support\SectionRenderer;
use Tests\TestCase;

class SectionRendererTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        View::addNamespace('landing-core-test', base_path('tests/Fixtures/views'));
    }

    public function test_it_renders_registered_view_sections_with_default_and_override_data(): void
    {
        $renderer = new SectionRenderer(new ModuleRegistry, [
            'hero' => [
                'view' => 'landing-core-test::landing-section',
                'data' => [
                    'title' => 'Default title',
                    'body' => 'Default body',
                ],
            ],
        ]);

        $html = $renderer->render('hero', ['title' => 'Custom title'])->toHtml();

        $this->assertStringContainsString('<h2>Custom title</h2>', $html);
        $this->assertStringContainsString('<p>Default body</p>', $html);
    }

    public function test_it_renders_component_sections_with_payload_data(): void
    {
        $renderer = new SectionRenderer(new ModuleRegistry, [
            'headline' => [
                'component' => 'landing-core::section-header',
                'data' => [
                    'title' => 'Reusable structure',
                    'subtitle' => 'Theme controls appearance',
                    'center' => true,
                ],
            ],
        ]);

        $html = $renderer->render('headline')->toHtml();

        $this->assertStringContainsString('lp-section-header-center', $html);
        $this->assertStringContainsString('<h2 class="lp-heading">Reusable structure</h2>', $html);
        $this->assertStringContainsString('<p class="lp-muted">Theme controls appearance</p>', $html);
    }

    public function test_it_resolves_sections_declared_by_enabled_modules(): void
    {
        $modules = new ModuleRegistry([
            'faq' => [
                'section' => [
                    'component' => 'landing-core::section-header',
                    'data' => ['title' => 'FAQ'],
                ],
            ],
            'disabled-module' => [
                'enabled' => false,
                'section' => 'landing-core-test::landing-section',
            ],
        ]);

        $renderer = new SectionRenderer($modules);

        $this->assertStringContainsString('FAQ', $renderer->render('faq')->toHtml());
        $this->assertSame('', $renderer->render('disabled-module')->toHtml());
        $this->assertSame('', $renderer->render('missing-section')->toHtml());
    }

    public function test_helpers_and_blade_directive_render_registered_sections(): void
    {
        app(SectionRenderer::class)->register('call-to-action', [
            'component' => 'landing-core::section-header',
            'data' => ['title' => 'Rendered by helper'],
        ]);

        $helperHtml = landing_section('call-to-action')->toHtml();
        $directiveHtml = Blade::render("@landingSection('call-to-action')");

        $this->assertStringContainsString('Rendered by helper', $helperHtml);
        $this->assertStringContainsString('Rendered by helper', $directiveHtml);
    }
}
