<?php

namespace Template\LandingPricing\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Template\LandingPricing\Http\Requests\StorePricingPlanRequest;
use Template\LandingPricing\Http\Requests\UpdatePricingPlanRequest;
use Template\LandingPricing\Models\PricingPlan;
use Template\LandingPricing\Support\PricingUrl;

class PricingPlanAdminController extends Controller
{
    public function index(): View
    {
        $pricingPlans = PricingPlan::query()
            ->ordered()
            ->paginate((int) config('landing-pricing.admin.per_page', 15));

        return view('landing-pricing::admin.index', [
            'pricingPlans' => $pricingPlans,
        ]);
    }

    public function create(): View
    {
        return view('landing-pricing::admin.create', [
            'pricingPlan' => new PricingPlan([
                'currency' => config('landing-pricing.currency', 'R$'),
                'billing_period_label' => config('landing-pricing.billing_period_label', '/mes'),
                'cta_label' => config('landing-pricing.cta.default_label', 'Escolher plano'),
                'cta_url' => config('landing-pricing.cta.default_url', '#contact'),
                'features' => [],
                'sort_order' => 0,
                'is_featured' => false,
                'is_active' => true,
            ]),
        ]);
    }

    public function store(StorePricingPlanRequest $request): RedirectResponse
    {
        PricingPlan::query()->create($this->payload($request));

        return redirect()
            ->route('pricing.admin.index')
            ->with('landing_pricing_success', 'Plano criado com sucesso.');
    }

    public function edit(PricingPlan $pricingPlan): View
    {
        return view('landing-pricing::admin.edit', [
            'pricingPlan' => $pricingPlan,
        ]);
    }

    public function update(UpdatePricingPlanRequest $request, PricingPlan $pricingPlan): RedirectResponse
    {
        $pricingPlan->update($this->payload($request));

        return redirect()
            ->route('pricing.admin.index')
            ->with('landing_pricing_success', 'Plano atualizado com sucesso.');
    }

    public function destroy(PricingPlan $pricingPlan): RedirectResponse
    {
        $pricingPlan->delete();

        return redirect()
            ->route('pricing.admin.index')
            ->with('landing_pricing_success', 'Plano removido com sucesso.');
    }

    protected function payload(FormRequest $request): array
    {
        return [
            ...$request->validated(),
            'description' => $this->nullableString($request->input('description')),
            'price' => $this->nullableString($request->input('price')),
            'currency' => $this->nullableString($request->input('currency')),
            'billing_period_label' => $this->nullableString($request->input('billing_period_label')),
            'features' => $this->featuresArray($request->input('features')),
            'cta_label' => $this->nullableString($request->input('cta_label')),
            'cta_url' => PricingUrl::normalize($request->input('cta_url')),
            'note' => $this->nullableString($request->input('note')),
            'badge' => $this->nullableString($request->input('badge')),
            'sort_order' => (int) ($request->input('sort_order') ?? 0),
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    protected function featuresArray(mixed $value): array
    {
        if (is_array($value)) {
            return collect($value)
                ->map(fn (mixed $feature) => $this->nullableString($feature))
                ->filter()
                ->values()
                ->all();
        }

        if (! is_string($value)) {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n/', $value) ?: [])
            ->map(fn (mixed $feature) => $this->nullableString($feature))
            ->filter()
            ->values()
            ->all();
    }

    protected function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
