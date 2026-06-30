<?php

namespace Template\LandingPricing\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Template\LandingCore\Support\Coerce;
use Template\LandingPricing\Config\PricingConfig;
use Template\LandingPricing\Http\Requests\StorePricingPlanRequest;
use Template\LandingPricing\Http\Requests\UpdatePricingPlanRequest;
use Template\LandingPricing\Models\PricingPlan;
use Template\LandingPricing\Support\PricingUrl;

class PricingPlanAdminController extends Controller
{
    public function index(PricingConfig $config): View
    {
        $pricingPlans = PricingPlan::query()
            ->ordered()
            ->paginate($config->adminPerPage());

        return view('landing-pricing::admin.index', [
            'pricingPlans' => $pricingPlans,
        ]);
    }

    public function create(PricingConfig $config): View
    {
        return view('landing-pricing::admin.create', [
            'pricingPlan' => new PricingPlan([
                'currency' => $config->currency(),
                'billing_period_label' => $config->billingPeriodLabel(),
                'cta_label' => $config->ctaDefaultLabel(),
                'cta_url' => $config->ctaDefaultUrl(),
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
            'description' => Coerce::nullableString($request->input('description')),
            'price' => Coerce::nullableString($request->input('price')),
            'currency' => Coerce::nullableString($request->input('currency')),
            'billing_period_label' => Coerce::nullableString($request->input('billing_period_label')),
            'features' => $this->featuresArray($request->input('features')),
            'cta_label' => Coerce::nullableString($request->input('cta_label')),
            'cta_url' => PricingUrl::normalize($request->input('cta_url')),
            'note' => Coerce::nullableString($request->input('note')),
            'badge' => Coerce::nullableString($request->input('badge')),
            'sort_order' => (int) ($request->input('sort_order') ?? 0),
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    protected function featuresArray(mixed $value): array
    {
        if (is_array($value)) {
            return collect($value)
                ->map(fn (mixed $feature) => Coerce::nullableString($feature))
                ->filter()
                ->values()
                ->all();
        }

        if (! is_string($value)) {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n/', $value) ?: [])
            ->map(fn (mixed $feature) => Coerce::nullableString($feature))
            ->filter()
            ->values()
            ->all();
    }
}
