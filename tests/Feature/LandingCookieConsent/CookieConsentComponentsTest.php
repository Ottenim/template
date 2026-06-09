<?php

namespace Tests\Feature\LandingCookieConsent;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class CookieConsentComponentsTest extends TestCase
{
    public function test_banner_component_renders_safe_banner_modal_and_client_config(): void
    {
        config()->set('landing-cookie-consent.banner.title', '<script>alert(1)</script>');
        config()->set('landing-cookie-consent.banner.message', '</script><script>alert(2)</script>');
        config()->set('landing-cookie-consent.policy_url', '/privacidade?ref=<unsafe>');
        config()->set('landing-cookie-consent.categories.analytics.label', '<script>alert(3)</script>');
        config()->set('landing-cookie-consent.categories.analytics.description', 'Analise <strong>opcional</strong>');

        $html = Blade::render('<x-cookie-consent::banner position="bottom-right" layout="card" />');

        $this->assertStringContainsString('id="landing-cookie-consent"', $html);
        $this->assertStringContainsString('id="landing-cookie-consent-config"', $html);
        $this->assertStringContainsString('id="landing-cookie-consent-loader"', $html);
        $this->assertStringContainsString('lp-cookie-consent-position-bottom-right', $html);
        $this->assertStringContainsString('lp-cookie-consent-layout-card', $html);
        $this->assertStringContainsString('data-cookie-accept-all', $html);
        $this->assertStringContainsString('data-cookie-reject-optional', $html);
        $this->assertStringContainsString('data-cookie-open-preferences', $html);
        $this->assertStringContainsString('data-cookie-save-preferences', $html);
        $this->assertStringContainsString('data-landing-cookie-category', $html);
        $this->assertStringContainsString('landing:consent-updated', $html);
        $this->assertStringContainsString('fetch(logging.endpoint', $html);
        $this->assertStringContainsString('class="lp-card lp-cookie-dialog"', $html);
        $this->assertStringContainsString('class="lp-muted lp-cookie-message"', $html);
        $this->assertStringContainsString('\u003Cscript\u003Ealert(3)\u003C/script\u003E', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
        $this->assertStringContainsString('&lt;/script&gt;&lt;script&gt;alert(2)&lt;/script&gt;', $html);
        $this->assertStringContainsString('/privacidade?ref=&lt;unsafe&gt;', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringNotContainsString('</script><script>alert(2)</script>', $html);
        $this->assertStringNotContainsString('<script>alert(3)</script>', $html);
    }

    public function test_banner_component_does_not_render_when_disabled(): void
    {
        config()->set('landing-cookie-consent.enabled', false);

        $html = Blade::render('<x-cookie-consent::banner />');

        $this->assertSame('', trim($html));
    }

    public function test_banner_hides_optional_actions_when_only_required_categories_are_enabled(): void
    {
        config()->set('landing-cookie-consent.categories.analytics.enabled', false);
        config()->set('landing-cookie-consent.categories.marketing.enabled', false);

        $html = Blade::render('<x-cookie-consent::banner />');

        $this->assertStringContainsString('Aceitar todos', $html);
        $this->assertStringContainsString('data-required="true"', $html);
        $this->assertStringContainsString('disabled', $html);
        $this->assertStringNotContainsString('Configurar', $html);
        $this->assertStringNotContainsString('Recusar opcionais', $html);
    }

    public function test_styles_component_uses_theme_tokens(): void
    {
        $html = Blade::render('<x-cookie-consent::styles />');

        $this->assertStringContainsString('id="landing-cookie-consent-styles"', $html);
        $this->assertStringContainsString('.lp-cookie-banner', $html);
        $this->assertStringContainsString('var(--lp-color-surface)', $html);
        $this->assertStringContainsString('var(--lp-color-border)', $html);
        $this->assertStringContainsString('var(--lp-radius-lg)', $html);
        $this->assertStringNotContainsString('#ffffff', $html);
        $this->assertStringNotContainsString('#000000', $html);
    }
}
