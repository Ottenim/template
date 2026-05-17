<x-landing-core::base-layout title="Novo plano" body-class="lp-pricing-admin-page">
    <x-slot:head>
        <x-pricing::styles />
    </x-slot:head>

    <section class="lp-section lp-pricing-admin">
        <div class="lp-container">
            <header class="lp-section-header">
                <span class="lp-eyebrow">Pricing</span>
                <h1 class="lp-heading">Novo plano</h1>
            </header>

            @include('landing-pricing::admin.form', [
                'pricingPlan' => $pricingPlan,
                'action' => route('pricing.admin.store'),
                'method' => 'POST',
                'submitLabel' => 'Criar plano',
            ])
        </div>
    </section>
</x-landing-core::base-layout>
