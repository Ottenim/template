<?php

namespace Tests\Feature\LandingCore;

use Illuminate\Support\Facades\Blade;
use Template\LandingCore\Theme\Theme;
use Template\LandingCore\Theme\ThemeManager;
use Tests\TestCase;

class ThemeManagerTest extends TestCase
{
    public function test_it_resolves_active_theme_and_merges_theme_overrides_with_default_tokens(): void
    {
        config()->set('landing-theme.active', 'dark');

        $manager = app(ThemeManager::class);
        $theme = $manager->theme();

        $this->assertSame('dark', $manager->active());
        $this->assertSame('dark', $theme->name());
        $this->assertSame('#60a5fa', $theme->get('colors.primary'));
        $this->assertSame('#dc2626', $theme->get('colors.danger'));
        $this->assertSame('Inter, ui-sans-serif, system-ui, sans-serif', $theme->get('font.sans'));
    }

    public function test_theme_generates_the_core_css_variable_contract(): void
    {
        $theme = new Theme('custom', [
            'font' => [
                'sans' => 'Inter, sans-serif',
            ],
            'colors' => [
                'primary' => '#2563eb',
                'on_primary' => '#ffffff',
            ],
            'radius' => [
                'md' => '0.75rem',
            ],
            'spacing' => [
                'section_y' => '5rem',
                'container' => '1120px',
                'content_gap' => '2rem',
            ],
            'shadow' => [
                'card' => '0 10px 30px rgba(15, 23, 42, 0.08)',
            ],
        ]);

        $this->assertSame([
            '--lp-font-sans' => 'Inter, sans-serif',
            '--lp-color-primary' => '#2563eb',
            '--lp-color-on-primary' => '#ffffff',
            '--lp-radius-md' => '0.75rem',
            '--lp-section-y' => '5rem',
            '--lp-container' => '1120px',
            '--lp-spacing-content-gap' => '2rem',
            '--lp-shadow-card' => '0 10px 30px rgba(15, 23, 42, 0.08)',
        ], $theme->cssVariables());
    }

    public function test_theme_helpers_return_the_active_theme_and_tokens(): void
    {
        config()->set('landing-theme.active', 'dark');

        $this->assertInstanceOf(Theme::class, landing_theme());
        $this->assertSame('#60a5fa', landing_theme('colors.primary'));
        $this->assertSame('fallback', landing_theme('colors.unknown', 'fallback'));
        $this->assertSame('dark', landing_theme_object()->name());
    }

    public function test_theme_variables_component_renders_active_css_variables(): void
    {
        $html = Blade::render('<x-landing-core::theme-variables theme="dark" />');

        $this->assertStringContainsString('id="landing-core-theme-variables"', $html);
        $this->assertStringContainsString('data-theme="dark"', $html);
        $this->assertStringContainsString('--lp-color-primary: #60a5fa;', $html);
        $this->assertStringContainsString('--lp-color-danger: #dc2626;', $html);
        $this->assertStringContainsString('--lp-section-y: 5rem;', $html);
    }
}
