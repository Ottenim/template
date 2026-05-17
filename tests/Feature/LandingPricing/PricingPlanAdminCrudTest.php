<?php

namespace Tests\Feature\LandingPricing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Template\LandingPricing\Models\PricingPlan;
use Tests\TestCase;

class PricingPlanAdminCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_routes_are_registered_with_configured_middleware(): void
    {
        $this->registerPricingAdminRoutes(['web', 'auth']);

        $route = Route::getRoutes()->getByName('pricing.admin.index');

        $this->assertNotNull($route);
        $this->assertContains('web', $route->middleware());
        $this->assertContains('auth', $route->middleware());
    }

    public function test_admin_index_lists_plans_in_configured_order(): void
    {
        $this->registerPricingAdminRoutes();

        PricingPlan::query()->create([
            'name' => 'Second plan',
            'price' => '199',
            'sort_order' => 20,
        ]);
        PricingPlan::query()->create([
            'name' => 'Featured plan',
            'description' => 'Best for teams',
            'price' => '299',
            'currency' => 'R$',
            'billing_period_label' => '/mes',
            'sort_order' => 30,
            'is_featured' => true,
            'is_active' => false,
        ]);
        PricingPlan::query()->create([
            'name' => 'First plan',
            'price' => '99',
            'sort_order' => 10,
        ]);

        $response = $this->get(route('pricing.admin.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['First plan', 'Second plan', 'Featured plan']);
        $response->assertSee('Best for teams');
        $response->assertSee('R$ 299 /mes');
        $response->assertSee('Destaque');
        $response->assertSee('Inativo');
    }

    public function test_admin_crud_can_create_update_and_delete_plans(): void
    {
        $this->registerPricingAdminRoutes();

        $response = $this->post(route('pricing.admin.store'), [
            'name' => 'Growth',
            'description' => ' For growing teams ',
            'price' => ' 199 ',
            'currency' => ' R$ ',
            'billing_period_label' => ' /mes ',
            'features' => "Feature one\n\nFeature two",
            'cta_label' => ' Start now ',
            'cta_url' => ' /checkout ',
            'note' => ' Cancel anytime ',
            'badge' => ' Popular ',
            'sort_order' => '2',
            'is_featured' => '1',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('pricing.admin.index'));
        $response->assertSessionHas('landing_pricing_success', 'Plano criado com sucesso.');

        $pricingPlan = PricingPlan::query()->firstOrFail();

        $this->assertSame('Growth', $pricingPlan->name);
        $this->assertSame('For growing teams', $pricingPlan->description);
        $this->assertSame('199', $pricingPlan->price);
        $this->assertSame('R$', $pricingPlan->currency);
        $this->assertSame('/mes', $pricingPlan->billing_period_label);
        $this->assertSame(['Feature one', 'Feature two'], $pricingPlan->features);
        $this->assertSame('Start now', $pricingPlan->cta_label);
        $this->assertSame('/checkout', $pricingPlan->cta_url);
        $this->assertSame('Cancel anytime', $pricingPlan->note);
        $this->assertSame('Popular', $pricingPlan->badge);
        $this->assertSame(2, $pricingPlan->sort_order);
        $this->assertTrue($pricingPlan->is_featured);
        $this->assertTrue($pricingPlan->is_active);

        $response = $this->put(route('pricing.admin.update', $pricingPlan), [
            'name' => 'Updated plan',
            'description' => ' ',
            'price' => ' ',
            'currency' => ' ',
            'billing_period_label' => ' ',
            'features' => "Updated feature\n ",
            'cta_label' => ' ',
            'cta_url' => ' ',
            'note' => ' ',
            'badge' => ' ',
            'sort_order' => '0',
        ]);

        $response->assertRedirect(route('pricing.admin.index'));
        $response->assertSessionHas('landing_pricing_success', 'Plano atualizado com sucesso.');

        $pricingPlan->refresh();

        $this->assertSame('Updated plan', $pricingPlan->name);
        $this->assertNull($pricingPlan->description);
        $this->assertNull($pricingPlan->price);
        $this->assertNull($pricingPlan->currency);
        $this->assertNull($pricingPlan->billing_period_label);
        $this->assertSame(['Updated feature'], $pricingPlan->features);
        $this->assertNull($pricingPlan->cta_label);
        $this->assertNull($pricingPlan->cta_url);
        $this->assertNull($pricingPlan->note);
        $this->assertNull($pricingPlan->badge);
        $this->assertSame(0, $pricingPlan->sort_order);
        $this->assertFalse($pricingPlan->is_featured);
        $this->assertFalse($pricingPlan->is_active);

        $response = $this->delete(route('pricing.admin.destroy', $pricingPlan));

        $response->assertRedirect(route('pricing.admin.index'));
        $response->assertSessionHas('landing_pricing_success', 'Plano removido com sucesso.');
        $this->assertDatabaseCount('lp_pricing_plans', 0);
    }

    public function test_admin_validation_rejects_invalid_payload(): void
    {
        $this->registerPricingAdminRoutes();

        $response = $this->from('/admin/pricing/create')->post(route('pricing.admin.store'), [
            'name' => '',
            'description' => str_repeat('a', 501),
            'price' => str_repeat('a', 81),
            'currency' => str_repeat('a', 21),
            'billing_period_label' => str_repeat('a', 41),
            'features' => str_repeat('a', 2001),
            'cta_label' => str_repeat('a', 81),
            'cta_url' => 'javascript:alert(1)',
            'note' => str_repeat('a', 501),
            'badge' => str_repeat('a', 81),
            'sort_order' => '-1',
            'is_featured' => 'not-boolean',
            'is_active' => 'not-boolean',
        ]);

        $response->assertRedirect('/admin/pricing/create');
        $response->assertSessionHasErrors([
            'name',
            'description',
            'price',
            'currency',
            'billing_period_label',
            'features',
            'cta_label',
            'cta_url',
            'note',
            'badge',
            'sort_order',
            'is_featured',
            'is_active',
        ]);

        $this->assertDatabaseCount('lp_pricing_plans', 0);
    }

    protected function registerPricingAdminRoutes(array $middleware = ['web']): void
    {
        config()->set('landing-pricing.admin.enabled', true);
        config()->set('landing-pricing.admin.middleware', $middleware);

        require base_path('packages/landing-pricing/routes/web.php');

        Route::getRoutes()->refreshNameLookups();
    }
}
