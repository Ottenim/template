@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
@endphp

<form class="lp-card lp-testimonials-admin-form" method="POST" action="{{ $action }}">
    @csrf

    @if ($method !== 'POST')
        @method($method)
    @endif

    @if ($errors->any())
        <p class="lp-error lp-testimonials-admin-feedback" role="alert">
            Revise os campos destacados e tente novamente.
        </p>
    @endif

    <label class="lp-label">
        <span>Nome</span>
        <input
            class="lp-input @error('name') lp-testimonials-input-invalid @enderror"
            type="text"
            name="name"
            value="{{ old('name', $testimonial->name) }}"
            required
        >

        @error('name')
            <span class="lp-error">{{ $message }}</span>
        @enderror
    </label>

    <label class="lp-label">
        <span>Depoimento</span>
        <textarea
            class="lp-textarea @error('text') lp-testimonials-input-invalid @enderror"
            name="text"
            rows="6"
            required
        >{{ old('text', $testimonial->text) }}</textarea>

        @error('text')
            <span class="lp-error">{{ $message }}</span>
        @enderror
    </label>

    <div class="lp-testimonials-admin-row">
        <label class="lp-label">
            <span>Cargo</span>
            <input
                class="lp-input @error('role') lp-testimonials-input-invalid @enderror"
                type="text"
                name="role"
                value="{{ old('role', $testimonial->role) }}"
            >

            @error('role')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>

        <label class="lp-label">
            <span>Empresa</span>
            <input
                class="lp-input @error('company') lp-testimonials-input-invalid @enderror"
                type="text"
                name="company"
                value="{{ old('company', $testimonial->company) }}"
            >

            @error('company')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <div class="lp-testimonials-admin-row">
        <label class="lp-label">
            <span>Avatar</span>
            <input
                class="lp-input @error('avatar') lp-testimonials-input-invalid @enderror"
                type="text"
                name="avatar"
                value="{{ old('avatar', $testimonial->avatar) }}"
                placeholder="/storage/avatar.jpg"
            >

            @error('avatar')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>

        <label class="lp-label">
            <span>Logo</span>
            <input
                class="lp-input @error('logo') lp-testimonials-input-invalid @enderror"
                type="text"
                name="logo"
                value="{{ old('logo', $testimonial->logo) }}"
                placeholder="/storage/logo.svg"
            >

            @error('logo')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <div class="lp-testimonials-admin-row">
        <label class="lp-label">
            <span>Nota</span>
            <input
                class="lp-input @error('rating') lp-testimonials-input-invalid @enderror"
                type="number"
                name="rating"
                value="{{ old('rating', $testimonial->rating) }}"
                min="1"
                max="5"
            >

            @error('rating')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>

        <label class="lp-label">
            <span>Ordem</span>
            <input
                class="lp-input @error('sort_order') lp-testimonials-input-invalid @enderror"
                type="number"
                name="sort_order"
                value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}"
                min="0"
            >

            @error('sort_order')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>
    </div>

    <div class="lp-testimonials-admin-checks">
        <label class="lp-testimonials-admin-check">
            <input
                type="checkbox"
                name="is_featured"
                value="1"
                @checked(old('is_featured', $testimonial->is_featured ?? false))
            >

            <span class="lp-muted">Destaque</span>
        </label>

        <label class="lp-testimonials-admin-check">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                @checked(old('is_active', $testimonial->is_active ?? true))
            >

            <span class="lp-muted">Ativo</span>
        </label>
    </div>

    <div class="lp-testimonials-admin-actions">
        <button class="lp-button lp-button-primary" type="submit">
            {{ $submitLabel }}
        </button>

        <a class="lp-button lp-button-secondary" href="{{ route('testimonials.admin.index') }}">
            Cancelar
        </a>
    </div>
</form>
