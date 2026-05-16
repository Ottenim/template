@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
@endphp

<form class="lp-card lp-faq-admin-form" method="POST" action="{{ $action }}">
    @csrf

    @if ($method !== 'POST')
        @method($method)
    @endif

    @if ($errors->any())
        <p class="lp-error lp-faq-admin-feedback" role="alert">
            Revise os campos destacados e tente novamente.
        </p>
    @endif

    <label class="lp-label">
        <span>Pergunta</span>
        <input
            class="lp-input @error('question') lp-faq-input-invalid @enderror"
            type="text"
            name="question"
            value="{{ old('question', $faqItem->question) }}"
            required
        >

        @error('question')
            <span class="lp-error">{{ $message }}</span>
        @enderror
    </label>

    <label class="lp-label">
        <span>Resposta</span>
        <textarea
            class="lp-textarea @error('answer') lp-faq-input-invalid @enderror"
            name="answer"
            rows="6"
            required
        >{{ old('answer', $faqItem->answer) }}</textarea>

        @error('answer')
            <span class="lp-error">{{ $message }}</span>
        @enderror
    </label>

    <div class="lp-faq-admin-row">
        <label class="lp-label">
            <span>Categoria</span>
            <input
                class="lp-input @error('category') lp-faq-input-invalid @enderror"
                type="text"
                name="category"
                value="{{ old('category', $faqItem->category) }}"
            >

            @error('category')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>

        <label class="lp-label">
            <span>Ordem</span>
            <input
                class="lp-input @error('sort_order') lp-faq-input-invalid @enderror"
                type="number"
                name="sort_order"
                value="{{ old('sort_order', $faqItem->sort_order ?? 0) }}"
                min="0"
            >

            @error('sort_order')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <label class="lp-faq-admin-check">
        <input
            type="checkbox"
            name="is_active"
            value="1"
            @checked(old('is_active', $faqItem->is_active ?? true))
        >

        <span class="lp-muted">Ativo</span>
    </label>

    <div class="lp-faq-admin-actions">
        <button class="lp-button lp-button-primary" type="submit">
            {{ $submitLabel }}
        </button>

        <a class="lp-button lp-button-secondary" href="{{ route('faq.admin.index') }}">
            Cancelar
        </a>
    </div>
</form>
