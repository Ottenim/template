<?php

namespace Tests\Feature\LandingFaq;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Template\LandingFaq\Models\FaqItem;
use Tests\TestCase;

class FaqComponentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_section_renders_base_structure_escapes_content_and_includes_schema(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-faq::section
                :items="$items"
                :title="$title"
                subtitle="Perguntas antes do contato"
                layout="accordion"
            />
        BLADE, [
            'title' => '<strong>Unsafe</strong>',
            'items' => [
                [
                    'question' => 'Is <strong>unsafe</strong> rendered?',
                    'answer' => "Line 1\n<script>alert(1)</script>",
                    'category' => 'Basics',
                    'sort_order' => 0,
                ],
                [
                    'question' => 'Hidden question',
                    'answer' => 'Hidden answer',
                    'is_active' => false,
                    'sort_order' => 1,
                ],
            ],
        ]);

        $this->assertSame(1, substr_count($html, 'id="landing-faq-styles"'));
        $this->assertStringContainsString('class="lp-section lp-faq lp-faq-layout-accordion"', $html);
        $this->assertStringContainsString('class="lp-container"', $html);
        $this->assertStringContainsString('class="lp-card lp-faq-card"', $html);
        $this->assertStringContainsString('&lt;strong&gt;Unsafe&lt;/strong&gt;', $html);
        $this->assertStringContainsString('Is &lt;strong&gt;unsafe&lt;/strong&gt; rendered?', $html);
        $this->assertStringContainsString('Line 1<br />', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
        $this->assertStringNotContainsString('<strong>Unsafe</strong>', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringNotContainsString('Hidden question', $html);
        $this->assertMatchesRegularExpression('/<details[^>]+open[^>]*>/', $html);
        $this->assertStringContainsString('type="application/ld+json"', $html);
        $this->assertStringContainsString('FAQPage', $html);
    }

    public function test_section_supports_categories_grid_layout_limit_and_schema_toggle(): void
    {
        config()->set('landing-faq.schema.enabled', false);

        $html = Blade::render(<<<'BLADE'
            <x-faq::section
                :items="$items"
                layout="grid"
                show-categories="true"
                default-open-first-item="false"
                limit="1"
            />
        BLADE, [
            'items' => [
                [
                    'question' => 'General question',
                    'answer' => 'General answer',
                    'category' => 'Geral',
                    'sort_order' => 0,
                ],
                [
                    'question' => 'Billing question',
                    'answer' => 'Billing answer',
                    'category' => 'Billing',
                    'sort_order' => 1,
                ],
            ],
        ]);

        $this->assertStringContainsString('lp-faq-layout-grid', $html);
        $this->assertStringContainsString('lp-faq-category-title', $html);
        $this->assertStringContainsString('Geral', $html);
        $this->assertStringContainsString('lp-faq-card-static', $html);
        $this->assertStringContainsString('General question', $html);
        $this->assertStringNotContainsString('Billing question', $html);
        $this->assertStringNotContainsString('application/ld+json', $html);
        $this->assertStringNotContainsString('<details', $html);
    }

    public function test_section_uses_configured_items_and_falls_back_from_invalid_layout(): void
    {
        config()->set('landing-faq.layout', 'invalid');
        config()->set('landing-faq.items', [
            [
                'question' => 'Configured question',
                'answer' => 'Configured answer',
            ],
        ]);

        $html = Blade::render('<x-faq::section />');

        $this->assertStringContainsString('lp-faq-layout-accordion', $html);
        $this->assertStringContainsString('Configured question', $html);
        $this->assertStringContainsString('Configured answer', $html);
    }

    public function test_section_can_load_active_items_from_database(): void
    {
        FaqItem::query()->create([
            'question' => 'Second database question',
            'answer' => 'Second database answer',
            'sort_order' => 20,
        ]);
        FaqItem::query()->create([
            'question' => 'Hidden database question',
            'answer' => 'Hidden database answer',
            'sort_order' => 0,
            'is_active' => false,
        ]);
        FaqItem::query()->create([
            'question' => 'First database question',
            'answer' => 'First database answer',
            'sort_order' => 10,
        ]);

        config()->set('landing-faq.limit', 1);

        $html = Blade::render('<x-faq::section />');

        $this->assertStringContainsString('First database question', $html);
        $this->assertStringNotContainsString('Second database question', $html);
        $this->assertStringNotContainsString('Hidden database question', $html);
    }

    public function test_section_does_not_render_when_disabled_or_empty(): void
    {
        config()->set('landing-faq.enabled', false);

        $disabledHtml = Blade::render('<x-faq::section :items="$items" />', [
            'items' => [
                [
                    'question' => 'Disabled question',
                    'answer' => 'Disabled answer',
                ],
            ],
        ]);

        config()->set('landing-faq.enabled', true);

        $emptyHtml = Blade::render('<x-faq::section :items="[]" />');

        $this->assertSame('', trim($disabledHtml));
        $this->assertSame('', trim($emptyHtml));
    }
}
