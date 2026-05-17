<?php

namespace Tests\Feature\LandingTestimonials;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Template\LandingTestimonials\Models\Testimonial;
use Tests\TestCase;

class TestimonialAdminCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_routes_are_registered_with_configured_middleware(): void
    {
        $this->registerTestimonialsAdminRoutes(['web', 'auth']);

        $route = Route::getRoutes()->getByName('testimonials.admin.index');

        $this->assertNotNull($route);
        $this->assertContains('web', $route->middleware());
        $this->assertContains('auth', $route->middleware());
    }

    public function test_admin_index_lists_items_in_configured_order(): void
    {
        $this->registerTestimonialsAdminRoutes();

        Testimonial::query()->create([
            'name' => 'Second client',
            'text' => 'Second testimonial',
            'sort_order' => 20,
        ]);
        Testimonial::query()->create([
            'name' => 'Featured client',
            'text' => 'Featured testimonial',
            'company' => 'Acme',
            'rating' => 5,
            'sort_order' => 30,
            'is_featured' => true,
            'is_active' => false,
        ]);
        Testimonial::query()->create([
            'name' => 'First client',
            'text' => 'First testimonial',
            'sort_order' => 10,
        ]);

        $response = $this->get(route('testimonials.admin.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['Featured client', 'First client', 'Second client']);
        $response->assertSee('Acme');
        $response->assertSee('Nota 5/5');
        $response->assertSee('Inativo');
    }

    public function test_admin_crud_can_create_update_and_delete_items(): void
    {
        $this->registerTestimonialsAdminRoutes();

        $response = $this->post(route('testimonials.admin.store'), [
            'name' => 'Maria Souza',
            'text' => 'Excelente atendimento.',
            'role' => ' CEO ',
            'company' => ' Acme ',
            'avatar' => ' /avatar.jpg ',
            'logo' => ' /logo.svg ',
            'rating' => '5',
            'sort_order' => '2',
            'is_featured' => '1',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('testimonials.admin.index'));
        $response->assertSessionHas('landing_testimonials_success', 'Depoimento criado com sucesso.');

        $testimonial = Testimonial::query()->firstOrFail();

        $this->assertSame('Maria Souza', $testimonial->name);
        $this->assertSame('Excelente atendimento.', $testimonial->text);
        $this->assertSame('CEO', $testimonial->role);
        $this->assertSame('Acme', $testimonial->company);
        $this->assertSame('/avatar.jpg', $testimonial->avatar);
        $this->assertSame('/logo.svg', $testimonial->logo);
        $this->assertSame(5, $testimonial->rating);
        $this->assertSame(2, $testimonial->sort_order);
        $this->assertTrue($testimonial->is_featured);
        $this->assertTrue($testimonial->is_active);

        $response = $this->put(route('testimonials.admin.update', $testimonial), [
            'name' => 'Updated client',
            'text' => 'Updated testimonial.',
            'role' => ' ',
            'company' => ' ',
            'avatar' => ' ',
            'logo' => ' ',
            'rating' => '',
            'sort_order' => '0',
        ]);

        $response->assertRedirect(route('testimonials.admin.index'));
        $response->assertSessionHas('landing_testimonials_success', 'Depoimento atualizado com sucesso.');

        $testimonial->refresh();

        $this->assertSame('Updated client', $testimonial->name);
        $this->assertNull($testimonial->role);
        $this->assertNull($testimonial->company);
        $this->assertNull($testimonial->avatar);
        $this->assertNull($testimonial->logo);
        $this->assertNull($testimonial->rating);
        $this->assertSame(0, $testimonial->sort_order);
        $this->assertFalse($testimonial->is_featured);
        $this->assertFalse($testimonial->is_active);

        $response = $this->delete(route('testimonials.admin.destroy', $testimonial));

        $response->assertRedirect(route('testimonials.admin.index'));
        $response->assertSessionHas('landing_testimonials_success', 'Depoimento removido com sucesso.');
        $this->assertDatabaseCount('lp_testimonials', 0);
    }

    public function test_admin_validation_rejects_invalid_payload(): void
    {
        $this->registerTestimonialsAdminRoutes();

        $response = $this->from('/admin/testimonials/create')->post(route('testimonials.admin.store'), [
            'name' => '',
            'text' => '',
            'role' => str_repeat('a', 121),
            'company' => str_repeat('a', 121),
            'avatar' => str_repeat('a', 2049),
            'logo' => str_repeat('a', 2049),
            'rating' => '6',
            'sort_order' => '-1',
            'is_featured' => 'not-boolean',
            'is_active' => 'not-boolean',
        ]);

        $response->assertRedirect('/admin/testimonials/create');
        $response->assertSessionHasErrors([
            'name',
            'text',
            'role',
            'company',
            'avatar',
            'logo',
            'rating',
            'sort_order',
            'is_featured',
            'is_active',
        ]);

        $this->assertDatabaseCount('lp_testimonials', 0);
    }

    protected function registerTestimonialsAdminRoutes(array $middleware = ['web']): void
    {
        config()->set('landing-testimonials.admin.enabled', true);
        config()->set('landing-testimonials.admin.middleware', $middleware);

        require base_path('packages/landing-testimonials/routes/web.php');

        Route::getRoutes()->refreshNameLookups();
    }
}
