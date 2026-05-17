<?php

namespace Tests\Feature\LandingTestimonials;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Template\LandingTestimonials\Models\Testimonial;
use Tests\TestCase;

class TestimonialsComponentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_section_renders_base_structure_escapes_content_and_optional_media(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-testimonials::section
                :items="$items"
                :title="$title"
                subtitle="Clientes que confiam"
                layout="featured"
                show-rating="true"
            />
        BLADE, [
            'title' => '<strong>Unsafe</strong>',
            'items' => [
                [
                    'name' => 'Ana <script>alert(1)</script>',
                    'text' => "Line 1\n<script>alert(1)</script>",
                    'role' => 'Founder',
                    'company' => 'Acme',
                    'avatar' => '/avatar.jpg',
                    'logo' => '/logo.svg',
                    'rating' => 4,
                    'sort_order' => 0,
                ],
                [
                    'name' => 'Hidden client',
                    'text' => 'Hidden testimonial',
                    'is_active' => false,
                    'sort_order' => 1,
                ],
            ],
        ]);

        $this->assertSame(1, substr_count($html, 'id="landing-testimonials-styles"'));
        $this->assertStringContainsString('class="lp-section lp-testimonials lp-testimonials-layout-featured"', $html);
        $this->assertStringContainsString('class="lp-container"', $html);
        $this->assertStringContainsString('class="lp-card lp-testimonial-card lp-testimonial-card-featured"', $html);
        $this->assertStringContainsString('&lt;strong&gt;Unsafe&lt;/strong&gt;', $html);
        $this->assertStringContainsString('Line 1<br />', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
        $this->assertStringContainsString('aria-label="Nota 4 de 5"', $html);
        $this->assertStringContainsString('src="/avatar.jpg"', $html);
        $this->assertStringContainsString('alt="Foto de Ana &lt;script&gt;alert(1)&lt;/script&gt;"', $html);
        $this->assertStringContainsString('src="/logo.svg"', $html);
        $this->assertStringContainsString('Founder - Acme', $html);
        $this->assertStringNotContainsString('<strong>Unsafe</strong>', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringNotContainsString('Hidden client', $html);
    }

    public function test_section_supports_layout_options_limit_and_visibility_toggles(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-testimonials::section
                :items="$items"
                layout="carousel"
                columns="8"
                limit="1"
                show-avatar="false"
                show-rating="false"
                show-company="false"
                show-logo="false"
            />
        BLADE, [
            'items' => [
                [
                    'name' => 'First client',
                    'text' => 'First testimonial',
                    'company' => 'First Company',
                    'avatar' => '/first.jpg',
                    'logo' => '/first.svg',
                    'rating' => 5,
                    'sort_order' => 0,
                ],
                [
                    'name' => 'Second client',
                    'text' => 'Second testimonial',
                    'sort_order' => 1,
                ],
            ],
        ]);

        $this->assertStringContainsString('lp-testimonials-layout-carousel', $html);
        $this->assertStringContainsString('--lp-testimonials-columns: 4;', $html);
        $this->assertStringContainsString('First client', $html);
        $this->assertStringNotContainsString('Second client', $html);
        $this->assertStringNotContainsString('src="/first.jpg"', $html);
        $this->assertStringNotContainsString('src="/first.svg"', $html);
        $this->assertStringNotContainsString('Nota 5 de 5', $html);
        $this->assertStringNotContainsString('First Company', $html);
    }

    public function test_section_uses_configured_items_and_falls_back_from_invalid_layout(): void
    {
        config()->set('landing-testimonials.layout', 'invalid');
        config()->set('landing-testimonials.items', [
            [
                'name' => 'Configured client',
                'text' => 'Configured testimonial',
            ],
        ]);

        $html = Blade::render('<x-testimonials::section />');

        $this->assertStringContainsString('lp-testimonials-layout-grid', $html);
        $this->assertStringContainsString('Configured client', $html);
        $this->assertStringContainsString('Configured testimonial', $html);
    }

    public function test_section_can_load_active_items_from_database(): void
    {
        Testimonial::query()->create([
            'name' => 'Second database client',
            'text' => 'Second database testimonial',
            'sort_order' => 20,
        ]);
        Testimonial::query()->create([
            'name' => 'Hidden database client',
            'text' => 'Hidden database testimonial',
            'sort_order' => 0,
            'is_active' => false,
        ]);
        Testimonial::query()->create([
            'name' => 'First database client',
            'text' => 'First database testimonial',
            'sort_order' => 10,
        ]);

        config()->set('landing-testimonials.limit', 1);

        $html = Blade::render('<x-testimonials::section />');

        $this->assertStringContainsString('First database client', $html);
        $this->assertStringNotContainsString('Second database client', $html);
        $this->assertStringNotContainsString('Hidden database client', $html);
    }

    public function test_section_does_not_render_when_disabled_or_empty(): void
    {
        config()->set('landing-testimonials.enabled', false);

        $disabledHtml = Blade::render('<x-testimonials::section :items="$items" />', [
            'items' => [
                [
                    'name' => 'Disabled client',
                    'text' => 'Disabled testimonial',
                ],
            ],
        ]);

        config()->set('landing-testimonials.enabled', true);

        $emptyHtml = Blade::render('<x-testimonials::section :items="[]" />');

        $this->assertSame('', trim($disabledHtml));
        $this->assertSame('', trim($emptyHtml));
    }
}
