<?php

namespace Tests\Feature\LandingContact;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class ContactComponentsTest extends TestCase
{
    public function test_form_renders_configured_fields_privacy_honeypot_and_tracking_contracts(): void
    {
        config()->set('landing-contact.fields.company', true);
        config()->set('landing-contact.fields.interest', [
            'enabled' => true,
            'required' => true,
            'options' => [
                'proposal' => 'Receber proposta',
            ],
        ]);
        config()->set('landing-contact.tracking.enabled', true);

        $html = Blade::render(<<<'BLADE'
            <x-contact::form
                action="/lead"
                button-label="Falar & converter"
                source-page="LP <unsafe>"
                source-url="https://example.test/origem"
                tracking-event="lead_submit"
            />
        BLADE);

        $this->assertSame(1, substr_count($html, 'id="landing-contact-styles"'));
        $this->assertStringContainsString('class="lp-card lp-contact-form"', $html);
        $this->assertStringContainsString('action="/lead"', $html);
        $this->assertStringContainsString('data-landing-contact-event="lead_submit"', $html);
        $this->assertStringContainsString('name="company"', $html);
        $this->assertStringContainsString('name="interest"', $html);
        $this->assertStringContainsString('value="proposal"', $html);
        $this->assertStringContainsString('name="privacy_consent"', $html);
        $this->assertStringContainsString('name="website"', $html);
        $this->assertStringContainsString('value="LP &lt;unsafe&gt;"', $html);
        $this->assertStringContainsString('Falar &amp; converter', $html);
        $this->assertStringNotContainsString('LP <unsafe>', $html);
    }

    public function test_section_renders_base_structure_escapes_content_and_includes_styles_once(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-contact::section
                :title="$title"
                subtitle="Preencha o formulario"
                button-label="Enviar mensagem"
            />
        BLADE, [
            'title' => '<strong>Unsafe</strong>',
        ]);

        $this->assertSame(1, substr_count($html, 'id="landing-contact-styles"'));
        $this->assertStringContainsString('class="lp-section lp-contact"', $html);
        $this->assertStringContainsString('class="lp-container"', $html);
        $this->assertStringContainsString('class="lp-section-header"', $html);
        $this->assertStringContainsString('&lt;strong&gt;Unsafe&lt;/strong&gt;', $html);
        $this->assertStringNotContainsString('<strong>Unsafe</strong>', $html);
        $this->assertStringContainsString('Enviar mensagem', $html);
    }

    public function test_components_do_not_render_when_contact_module_is_disabled(): void
    {
        config()->set('landing-contact.enabled', false);

        $formHtml = Blade::render('<x-contact::form />');
        $sectionHtml = Blade::render('<x-contact::section />');

        $this->assertSame('', trim($formHtml));
        $this->assertSame('', trim($sectionHtml));
    }
}
