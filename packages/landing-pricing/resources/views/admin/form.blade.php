@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
    $features = old('features', implode("\n", $pricingPlan->features ?? []));
@endphp

<form class="lp-card lp-pricing-admin-form" method="POST" action="{{ $action }}">
    @csrf

    @if ($method !== 'POST')
        @method($method)
    @endif

    @if ($errors->any())
        <p class="lp-error lp-pricing-admin-feedback" role="alert">
            Revise os campos destacados e tente novamente.
        </p>
    @endif

    <label class="lp-label">
        <span>Nome</span>
        <input
            class="lp-input @error('name') lp-pricing-input-invalid @enderror"
            type="text"
            name="name"
            value="{{ old('name', $pricingPlan->name) }}"
            required
        >

        @error('name')
            <span class="lp-error">{{ $message }}</span>
        @enderror
    </label>

    <label class="lp-label">
        <span>Descricao</span>
        <textarea
            class="lp-textarea @error('description') lp-pricing-input-invalid @enderror"
            name="description"
            rows="3"
        >{{ old('description', $pricingPlan->description) }}</textarea>

        @error('description')
            <span class="lp-error">{{ $message }}</span>
        @enderror
    </label>

    <div class="lp-pricing-admin-row">
        <label class="lp-label">
            <span>Preco</span>
            <input
                class="lp-input @error('price') lp-pricing-input-invalid @enderror"
                type="text"
                name="price"
                value="{{ old('price', $pricingPlan->price) }}"
                placeholder="99"
            >

            @error('price')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>

        <label class="lp-label">
            <span>Moeda</span>
            <input
                class="lp-input @error('currency') lp-pricing-input-invalid @enderror"
                type="text"
                name="currency"
                value="{{ old('currency', $pricingPlan->currency) }}"
                placeholder="R$"
            >

            @error('currency')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <div class="lp-pricing-admin-row">
        <label class="lp-label">
            <span>Periodicidade</span>
            <input
                class="lp-input @error('billing_period_label') lp-pricing-input-invalid @enderror"
                type="text"
                name="billing_period_label"
                value="{{ old('billing_period_label', $pricingPlan->billing_period_label) }}"
                placeholder="/mes"
            >

            @error('billing_period_label')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>

        <label class="lp-label">
            <span>Ordem</span>
            <input
                class="lp-input @error('sort_order') lp-pricing-input-invalid @enderror"
                type="number"
                name="sort_order"
                value="{{ old('sort_order', $pricingPlan->sort_order ?? 0) }}"
                min="0"
            >

            @error('sort_order')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <label class="lp-label">
        <span>Recursos</span>
        <textarea
            class="lp-textarea @error('features') lp-pricing-input-invalid @enderror"
            name="features"
            rows="6"
            placeholder="Um recurso por linha"
        >{{ $features }}</textarea>

        @error('features')
            <span class="lp-error">{{ $message }}</span>
        @enderror
    </label>

    <div class="lp-pricing-admin-row">
        <label class="lp-label">
            <span>Texto do CTA</span>
            <input
                class="lp-input @error('cta_label') lp-pricing-input-invalid @enderror"
                type="text"
                name="cta_label"
                value="{{ old('cta_label', $pricingPlan->cta_label) }}"
            >

            @error('cta_label')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>

        <label class="lp-label">
            <span>URL do CTA</span>
            <input
                class="lp-input @error('cta_url') lp-pricing-input-invalid @enderror"
                type="text"
                name="cta_url"
                value="{{ old('cta_url', $pricingPlan->cta_url) }}"
                placeholder="#contact"
            >

            @error('cta_url')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <div class="lp-pricing-admin-row">
        <label class="lp-label">
            <span>Selo</span>
            <input
                class="lp-input @error('badge') lp-pricing-input-invalid @enderror"
                type="text"
                name="badge"
                value="{{ old('badge', $pricingPlan->badge) }}"
                placeholder="Mais escolhido"
            >

            @error('badge')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>

        <label class="lp-label">
            <span>Observacao</span>
            <input
                class="lp-input @error('note') lp-pricing-input-invalid @enderror"
                type="text"
                name="note"
                value="{{ old('note', $pricingPlan->note) }}"
            >

            @error('note')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <div class="lp-pricing-admin-checks">
        <label class="lp-pricing-admin-check">
            <input
                type="checkbox"
                name="is_featured"
                value="1"
                @checked(old('is_featured', $pricingPlan->is_featured ?? false))
            >

            <span class="lp-muted">Destaque</span>
        </label>

        <label class="lp-pricing-admin-check">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                @checked(old('is_active', $pricingPlan->is_active ?? true))
            >

            <span class="lp-muted">Ativo</span>
        </label>
    </div>

    <div class="lp-pricing-admin-actions">
        <button class="lp-button lp-button-primary" type="submit">
            {{ $submitLabel }}
        </button>

        <a class="lp-button lp-button-secondary" href="{{ route('pricing.admin.index') }}">
            Cancelar
        </a>
    </div>
</form>
