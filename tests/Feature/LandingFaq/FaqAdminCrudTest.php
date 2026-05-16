<?php

namespace Tests\Feature\LandingFaq;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Template\LandingFaq\Models\FaqItem;
use Tests\TestCase;

class FaqAdminCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_routes_are_registered_with_configured_middleware(): void
    {
        $this->registerFaqAdminRoutes(['web', 'auth']);

        $route = Route::getRoutes()->getByName('faq.admin.index');

        $this->assertNotNull($route);
        $this->assertContains('web', $route->middleware());
        $this->assertContains('auth', $route->middleware());
    }

    public function test_admin_index_lists_items_in_configured_order(): void
    {
        $this->registerFaqAdminRoutes();

        FaqItem::query()->create([
            'question' => 'Second question',
            'answer' => 'Second answer',
            'sort_order' => 20,
        ]);
        FaqItem::query()->create([
            'question' => 'First question',
            'answer' => 'First answer',
            'category' => 'Billing',
            'sort_order' => 10,
            'is_active' => false,
        ]);

        $response = $this->get(route('faq.admin.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['First question', 'Second question']);
        $response->assertSee('Billing');
        $response->assertSee('Inativo');
    }

    public function test_admin_crud_can_create_update_and_delete_items(): void
    {
        $this->registerFaqAdminRoutes();

        $response = $this->post(route('faq.admin.store'), [
            'question' => 'How does billing work?',
            'answer' => 'Billing is monthly.',
            'category' => ' Billing ',
            'sort_order' => '2',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('faq.admin.index'));
        $response->assertSessionHas('landing_faq_success', 'Pergunta criada com sucesso.');

        $faqItem = FaqItem::query()->firstOrFail();

        $this->assertSame('How does billing work?', $faqItem->question);
        $this->assertSame('Billing', $faqItem->category);
        $this->assertSame(2, $faqItem->sort_order);
        $this->assertTrue($faqItem->is_active);

        $response = $this->put(route('faq.admin.update', $faqItem), [
            'question' => 'Updated billing question',
            'answer' => 'Updated billing answer.',
            'category' => ' ',
            'sort_order' => '0',
        ]);

        $response->assertRedirect(route('faq.admin.index'));
        $response->assertSessionHas('landing_faq_success', 'Pergunta atualizada com sucesso.');

        $faqItem->refresh();

        $this->assertSame('Updated billing question', $faqItem->question);
        $this->assertNull($faqItem->category);
        $this->assertSame(0, $faqItem->sort_order);
        $this->assertFalse($faqItem->is_active);

        $response = $this->delete(route('faq.admin.destroy', $faqItem));

        $response->assertRedirect(route('faq.admin.index'));
        $response->assertSessionHas('landing_faq_success', 'Pergunta removida com sucesso.');
        $this->assertDatabaseCount('lp_faq_items', 0);
    }

    public function test_admin_validation_rejects_invalid_payload(): void
    {
        $this->registerFaqAdminRoutes();

        $response = $this->from('/admin/faq/create')->post(route('faq.admin.store'), [
            'question' => '',
            'answer' => '',
            'category' => str_repeat('a', 101),
            'sort_order' => '-1',
            'is_active' => 'not-boolean',
        ]);

        $response->assertRedirect('/admin/faq/create');
        $response->assertSessionHasErrors([
            'question',
            'answer',
            'category',
            'sort_order',
            'is_active',
        ]);

        $this->assertDatabaseCount('lp_faq_items', 0);
    }

    protected function registerFaqAdminRoutes(array $middleware = ['web']): void
    {
        config()->set('landing-faq.admin.enabled', true);
        config()->set('landing-faq.admin.middleware', $middleware);

        require base_path('packages/landing-faq/routes/web.php');

        Route::getRoutes()->refreshNameLookups();
    }
}
