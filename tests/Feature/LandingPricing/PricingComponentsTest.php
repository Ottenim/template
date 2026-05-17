<?php

namespace Tests\Feature\LandingPricing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Template\LandingPricing\Models\PricingPlan;
use Tests\TestCase;

class PricingComponentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_section_renders_base_structure_escapes_content_and_tracking_attributes(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-pricing::section
                :plans="$plans"
                :title="$title"
                subtitle="Planos para comparar"
                layout="cards"
            />
        BLADE, [
            'title' => '<strong>Unsafe</strong>',
            'plans' => [
                [
                    'id' => 10,
                    'name' => 'Starter <script>alert(1)</script>',
                    'description' => 'Start <strong>fast</strong>',
                    'price' => '99',
                    'currency' => 'R$',
                    'billing_period_label' => '/mes',
                    'features' => [
                        'Feature <script>alert(1)</script>',
                    ],
                    'cta_label' => 'Escolher <script>alert(1)</script>',
                    'cta_url' => '#checkout',
                    'note' => 'Cancel anytime <script>alert(1)</script>',
                    'is_featured' => true,
                    'sort_order' => 0,
                ],
                [
                    'name' => 'Unsafe CTA plan',
                    'price' => '199',
                    'cta_label' => 'Unsafe CTA',
                    'cta_url' => 'javascript:alert(1)',
                    'sort_order' => 1,
                ],
                [
                    'name' => 'Hidden plan',
                    'price' => '299',
                    'is_active' => false,
                    'sort_order' => 2,
                ],
            ],
        ]);

        $this->assertSame(1, substr_count($html, 'id="landing-pricing-styles"'));
        $this->assertStringContainsString('class="lp-section lp-pricing lp-pricing-layout-cards"', $html);
        $this->assertStringContainsString('class="lp-container"', $html);
        $this->assertStringContainsString('class="lp-card lp-pricing-card lp-pricing-card-featured"', $html);
        $this->assertStringContainsString('&lt;strong&gt;Unsafe&lt;/strong&gt;', $html);
        $this->assertStringContainsString('Starter &lt;script&gt;alert(1)&lt;/script&gt;', $html);
        $this->assertStringContainsString('Feature &lt;script&gt;alert(1)&lt;/script&gt;', $html);
        $this->assertStringContainsString('href="#checkout"', $html);
        $this->assertStringContainsString('data-event="pricing_cta_click"', $html);
        $this->assertStringContainsString('data-pricing-plan="Starter &lt;script&gt;alert(1)&lt;/script&gt;"', $html);
        $this->assertStringContainsString('data-pricing-plan-id="10"', $html);
        $this->assertStringContainsString('Cancel anytime &lt;script&gt;alert(1)&lt;/script&gt;', $html);
        $this->assertStringContainsString('Unsafe CTA plan', $html);
        $this->assertStringNotContainsString('href="javascript:alert(1)"', $html);
        $this->assertStringNotContainsString('<strong>Unsafe</strong>', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringNotContainsString('Hidden plan', $html);
    }

    public function test_section_supports_table_layout_limit_columns_and_visibility_toggles(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-pricing::section
                :plans="$plans"
                layout="table"
                columns="8"
                limit="1"
                show-featured-plan="false"
                tracking-enabled="false"
            />
        BLADE, [
            'plans' => [
                [
                    'name' => 'First plan',
                    'price' => '99',
                    'features' => ['First feature'],
                    'cta_label' => 'Choose first',
                    'cta_url' => '#first',
                    'badge' => 'Popular',
                    'is_featured' => true,
                    'sort_order' => 0,
                ],
                [
                    'name' => 'Second plan',
                    'price' => '199',
                    'sort_order' => 1,
                ],
            ],
        ]);

        $this->assertStringContainsString('lp-pricing-layout-table', $html);
        $this->assertStringContainsString('--lp-pricing-columns: 4;', $html);
        $this->assertStringContainsString('<table class="lp-pricing-table">', $html);
        $this->assertStringContainsString('First plan', $html);
        $this->assertStringContainsString('First feature', $html);
        $this->assertStringContainsString('href="#first"', $html);
        $this->assertStringNotContainsString('Second plan', $html);
        $this->assertStringNotContainsString('Popular', $html);
        $this->assertStringNotContainsString('class="lp-pricing-table-featured"', $html);
        $this->assertStringNotContainsString('data-event=', $html);
    }

    public function test_section_uses_configured_plans_and_falls_back_from_invalid_layout(): void
    {
        config()->set('landing-pricing.layout', 'invalid');
        config()->set('landing-pricing.plans', [
            [
                'name' => 'Configured plan',
                'price' => '149',
                'features' => ['Configured feature'],
            ],
        ]);

        $html = Blade::render('<x-pricing::section />');

        $this->assertStringContainsString('lp-pricing-layout-cards', $html);
        $this->assertStringContainsString('Configured plan', $html);
        $this->assertStringContainsString('Configured feature', $html);
    }

    public function test_section_can_load_active_plans_from_database(): void
    {
        PricingPlan::query()->create([
            'name' => 'Second database plan',
            'price' => '199',
            'sort_order' => 20,
        ]);
        PricingPlan::query()->create([
            'name' => 'Hidden database plan',
            'price' => '299',
            'sort_order' => 0,
            'is_active' => false,
        ]);
        PricingPlan::query()->create([
            'name' => 'First database plan',
            'price' => '99',
            'sort_order' => 10,
        ]);

        config()->set('landing-pricing.limit', 1);

        $html = Blade::render('<x-pricing::section />');

        $this->assertStringContainsString('First database plan', $html);
        $this->assertStringNotContainsString('Second database plan', $html);
        $this->assertStringNotContainsString('Hidden database plan', $html);
    }

    public function test_section_does_not_render_when_disabled_or_empty(): void
    {
        config()->set('landing-pricing.enabled', false);

        $disabledHtml = Blade::render('<x-pricing::section :plans="$plans" />', [
            'plans' => [
                [
                    'name' => 'Disabled plan',
                    'price' => '99',
                ],
            ],
        ]);

        config()->set('landing-pricing.enabled', true);

        $emptyHtml = Blade::render('<x-pricing::section :plans="[]" />');

        $this->assertSame('', trim($disabledHtml));
        $this->assertSame('', trim($emptyHtml));
    }
}
