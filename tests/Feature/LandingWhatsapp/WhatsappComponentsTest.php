<?php

namespace Tests\Feature\LandingWhatsapp;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class WhatsappComponentsTest extends TestCase
{
    public function test_button_renders_configured_whatsapp_link_and_optional_contracts(): void
    {
        config()->set('landing-whatsapp.phone', '+55 (11) 91234-5678');
        config()->set('landing-whatsapp.message', 'Ola pelo site');
        config()->set('landing-whatsapp.button.tooltip', 'Chamar agora');
        config()->set('landing-whatsapp.tracking.enabled', true);
        config()->set('landing-whatsapp.style.use_brand_color', true);

        $html = Blade::render('<x-whatsapp::button />');

        $this->assertStringContainsString('href="https://wa.me/5511912345678?text=Ola%20pelo%20site"', $html);
        $this->assertStringContainsString('aria-label="Falar no WhatsApp"', $html);
        $this->assertStringContainsString('data-landing-event="whatsapp_click"', $html);
        $this->assertStringContainsString('lp-whatsapp-button-brand', $html);
        $this->assertStringContainsString('--lp-whatsapp-brand-color: #25D366;', $html);
        $this->assertStringContainsString('Chamar agora', $html);
    }

    public function test_button_does_not_render_without_phone_or_explicit_url(): void
    {
        config()->set('landing-whatsapp.phone', null);

        $html = Blade::render('<x-whatsapp::button />');

        $this->assertSame('', trim($html));
    }

    public function test_floating_button_renders_position_visibility_and_mobile_bar_classes(): void
    {
        config()->set('landing-whatsapp.phone', '+55 11 91234-5678');

        $html = Blade::render(<<<'BLADE'
            <x-whatsapp::floating-button position="top-left" visibility="mobile" mobile-bar="true" />
        BLADE);

        $this->assertStringContainsString('lp-whatsapp-floating', $html);
        $this->assertStringContainsString('lp-whatsapp-position-top-left', $html);
        $this->assertStringContainsString('lp-whatsapp-mobile-bar', $html);
        $this->assertStringContainsString('lp-whatsapp-visibility-mobile', $html);
        $this->assertStringContainsString('lp-whatsapp-floating-button', $html);
    }

    public function test_section_renders_base_classes_escapes_content_and_includes_styles_once(): void
    {
        config()->set('landing-whatsapp.phone', '+55 11 91234-5678');

        $html = Blade::render(<<<'BLADE'
            <x-whatsapp::section
                :title="$title"
                subtitle="Chame agora"
                text="Atendimento direto"
                button-label="Conversar"
            />
        BLADE, [
            'title' => '<strong>Unsafe</strong>',
        ]);

        $this->assertSame(1, substr_count($html, 'id="landing-whatsapp-styles"'));
        $this->assertStringContainsString('class="lp-section lp-whatsapp-section"', $html);
        $this->assertStringContainsString('class="lp-container"', $html);
        $this->assertStringContainsString('class="lp-whatsapp-card lp-card"', $html);
        $this->assertStringContainsString('&lt;strong&gt;Unsafe&lt;/strong&gt;', $html);
        $this->assertStringNotContainsString('<strong>Unsafe</strong>', $html);
        $this->assertStringContainsString('Conversar', $html);
    }

    public function test_section_does_not_render_when_section_is_disabled(): void
    {
        config()->set('landing-whatsapp.phone', '+55 11 91234-5678');
        config()->set('landing-whatsapp.section.enabled', false);

        $html = Blade::render('<x-whatsapp::section />');

        $this->assertSame('', trim($html));
    }
}
