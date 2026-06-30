<?php

namespace Template\LandingTestimonials\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Template\LandingCore\Support\Coerce;
use Template\LandingTestimonials\Config\TestimonialsConfig;
use Template\LandingTestimonials\Http\Requests\StoreTestimonialRequest;
use Template\LandingTestimonials\Http\Requests\UpdateTestimonialRequest;
use Template\LandingTestimonials\Models\Testimonial;

class TestimonialAdminController extends Controller
{
    public function index(TestimonialsConfig $config): View
    {
        $testimonials = Testimonial::query()
            ->ordered()
            ->paginate($config->adminPerPage());

        return view('landing-testimonials::admin.index', [
            'testimonials' => $testimonials,
        ]);
    }

    public function create(): View
    {
        return view('landing-testimonials::admin.create', [
            'testimonial' => new Testimonial([
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(StoreTestimonialRequest $request): RedirectResponse
    {
        Testimonial::query()->create($this->payload($request));

        return redirect()
            ->route('testimonials.admin.index')
            ->with('landing_testimonials_success', 'Depoimento criado com sucesso.');
    }

    public function edit(Testimonial $testimonial): View
    {
        return view('landing-testimonials::admin.edit', [
            'testimonial' => $testimonial,
        ]);
    }

    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update($this->payload($request));

        return redirect()
            ->route('testimonials.admin.index')
            ->with('landing_testimonials_success', 'Depoimento atualizado com sucesso.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return redirect()
            ->route('testimonials.admin.index')
            ->with('landing_testimonials_success', 'Depoimento removido com sucesso.');
    }

    protected function payload(FormRequest $request): array
    {
        return [
            ...$request->validated(),
            'role' => Coerce::nullableString($request->input('role')),
            'company' => Coerce::nullableString($request->input('company')),
            'avatar' => Coerce::nullableString($request->input('avatar')),
            'logo' => Coerce::nullableString($request->input('logo')),
            'rating' => $this->nullableInteger($request->input('rating')),
            'sort_order' => (int) ($request->input('sort_order') ?? 0),
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    protected function nullableInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
