<?php

namespace Tests\Feature\LandingLeadCapture;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class LeadCaptureComponentsTest extends TestCase
{
    public function test_form_renders_configured_fields_privacy_honeypot_sources_and_tracking_contracts(): void
    {
        config()->set('landing-lead-capture.fields.company', true);
        config()->set('landing-lead-capture.fields.interest', [
            'enabled' => true,
            'required' => true,
            'options' => [
                'catalog' => 'Receber catalogo',
            ],
        ]);
        config()->set('landing-lead-capture.tracking.enabled', true);

        $html = Blade::render(<<<'BLADE'
            <x-lead-capture::form
                action="/capturar"
                button-label="Baixar & converter"
                source-page="LP <unsafe>"
                source-url="https://example.test/origem"
                source="ebook"
                campaign="lancamento"
                tag="catalogo"
                tracking-event="lead_submit"
            />
        BLADE);

        $this->assertSame(1, substr_count($html, 'id="landing-lead-capture-styles"'));
        $this->assertStringContainsString('lp-lead-capture-form', $html);
        $this->assertStringContainsString('lp-card', $html);
        $this->assertStringContainsString('action="/capturar"', $html);
        $this->assertStringContainsString('data-landing-lead-capture-event="lead_submit"', $html);
        $this->assertStringContainsString('name="company"', $html);
        $this->assertStringContainsString('name="interest"', $html);
        $this->assertStringContainsString('value="catalog"', $html);
        $this->assertStringContainsString('name="privacy_consent"', $html);
        $this->assertStringContainsString('name="website"', $html);
        $this->assertStringContainsString('name="source"', $html);
        $this->assertStringContainsString('value="ebook"', $html);
        $this->assertStringContainsString('name="campaign"', $html);
        $this->assertStringContainsString('value="lancamento"', $html);
        $this->assertStringContainsString('name="tag"', $html);
        $this->assertStringContainsString('value="catalogo"', $html);
        $this->assertStringContainsString('value="LP &lt;unsafe&gt;"', $html);
        $this->assertStringContainsString('Baixar &amp; converter', $html);
        $this->assertStringNotContainsString('LP <unsafe>', $html);
    }

    public function test_section_renders_base_structure_escapes_content_and_includes_styles_once(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-lead-capture::section
                :title="$title"
                subtitle="Receba o catalogo"
                benefit="Acesso imediato"
                button-label="Baixar agora"
            />
        BLADE, [
            'title' => '<strong>Unsafe</strong>',
        ]);

        $this->assertSame(1, substr_count($html, 'id="landing-lead-capture-styles"'));
        $this->assertStringContainsString('class="lp-section lp-lead-capture"', $html);
        $this->assertStringContainsString('class="lp-container"', $html);
        $this->assertStringContainsString('class="lp-section-header lp-lead-capture-header"', $html);
        $this->assertStringContainsString('&lt;strong&gt;Unsafe&lt;/strong&gt;', $html);
        $this->assertStringNotContainsString('<strong>Unsafe</strong>', $html);
        $this->assertStringContainsString('Receba o catalogo', $html);
        $this->assertStringContainsString('Acesso imediato', $html);
        $this->assertStringContainsString('Baixar agora', $html);
    }

    public function test_components_do_not_render_when_lead_capture_module_is_disabled(): void
    {
        config()->set('landing-lead-capture.enabled', false);

        $formHtml = Blade::render('<x-lead-capture::form />');
        $sectionHtml = Blade::render('<x-lead-capture::section />');

        $this->assertSame('', trim($formHtml));
        $this->assertSame('', trim($sectionHtml));
    }
}
