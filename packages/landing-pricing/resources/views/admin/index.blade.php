<x-landing-core::base-layout title="Pricing" body-class="lp-pricing-admin-page">
    <x-slot:head>
        <x-pricing::styles />
    </x-slot:head>

    <section class="lp-section lp-pricing-admin">
        <div class="lp-container">
            <header class="lp-section-header">
                <span class="lp-eyebrow">Pricing</span>
                <h1 class="lp-heading">Planos</h1>
                <p class="lp-muted">Gerencie os planos exibidos no componente publico.</p>
            </header>

            <div class="lp-section-content">
                @if (session('landing_pricing_success'))
                    <p class="lp-success lp-pricing-admin-feedback" role="status">
                        {{ session('landing_pricing_success') }}
                    </p>
                @endif

                <div class="lp-pricing-admin-actions">
                    <a class="lp-button lp-button-primary" href="{{ route('pricing.admin.create') }}">
                        Novo plano
                    </a>
                </div>

                @if ($pricingPlans->isEmpty())
                    <article class="lp-card lp-pricing-admin-empty">
                        <p class="lp-muted">Nenhum plano cadastrado.</p>
                    </article>
                @else
                    <div class="lp-pricing-admin-list">
                        @foreach ($pricingPlans as $pricingPlan)
                            <article class="lp-card lp-pricing-admin-item">
                                <div class="lp-pricing-admin-summary">
                                    <div>
                                        <p class="lp-pricing-admin-name">{{ $pricingPlan->name }}</p>

                                        @if ($pricingPlan->description)
                                            <p class="lp-muted">{{ $pricingPlan->description }}</p>
                                        @endif

                                        <div class="lp-pricing-admin-meta">
                                            @if ($pricingPlan->price)
                                                <span class="lp-badge">
                                                    {{ collect([$pricingPlan->currency, $pricingPlan->price, $pricingPlan->billing_period_label])->filter()->implode(' ') }}
                                                </span>
                                            @endif

                                            <span class="lp-muted">Ordem {{ $pricingPlan->sort_order }}</span>
                                            <span class="lp-muted">{{ $pricingPlan->is_featured ? 'Destaque' : 'Padrao' }}</span>
                                            <span class="lp-muted">{{ $pricingPlan->is_active ? 'Ativo' : 'Inativo' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="lp-pricing-admin-item-actions">
                                    <a class="lp-button lp-button-secondary" href="{{ route('pricing.admin.edit', $pricingPlan) }}">
                                        Editar
                                    </a>

                                    <form method="POST" action="{{ route('pricing.admin.destroy', $pricingPlan) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button class="lp-button lp-button-secondary" type="submit">
                                            Remover
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{ $pricingPlans->links() }}
                @endif
            </div>
        </div>
    </section>
</x-landing-core::base-layout>
