@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
    $previewDefaults = $previewDefaults ?? [];
    $schema = old('schema', $seoPage->schema ? json_encode($seoPage->schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '');
    $previewTitle = old('title', $seoPage->title) ?: ($previewDefaults['title'] ?? null);
    $previewDescription = old('description', $seoPage->description) ?: ($previewDefaults['description'] ?? null);
    $previewUrl = old('canonical_url', $seoPage->canonical_url) ?: rtrim(config('app.url'), '/').(old('path', $seoPage->path) ?: '/');
    $previewImage = old('image_url', $seoPage->image_url) ?: ($previewDefaults['image'] ?? null);
@endphp

<div class="lp-seo-admin-grid">
    <form class="lp-card lp-seo-admin-form" method="POST" action="{{ $action }}">
        @csrf

        @if ($method !== 'POST')
            @method($method)
        @endif

        @if ($errors->any())
            <p class="lp-error lp-seo-admin-feedback" role="alert">
                Revise os campos destacados e tente novamente.
            </p>
        @endif

        <div class="lp-seo-admin-row">
            <label class="lp-label">
                <span>Chave da pagina</span>
                <input
                    class="lp-input @error('page_key') lp-seo-input-invalid @enderror"
                    type="text"
                    name="page_key"
                    value="{{ old('page_key', $seoPage->page_key) }}"
                    required
                    placeholder="home"
                >

                @error('page_key')
                    <span class="lp-error">{{ $message }}</span>
                @enderror
            </label>

            <label class="lp-label">
                <span>Caminho publico</span>
                <input
                    class="lp-input @error('path') lp-seo-input-invalid @enderror"
                    type="text"
                    name="path"
                    value="{{ old('path', $seoPage->path) }}"
                    placeholder="/"
                >

                @error('path')
                    <span class="lp-error">{{ $message }}</span>
                @enderror
            </label>
        </div>

        <label class="lp-label">
            <span>Nome da rota</span>
            <input
                class="lp-input @error('route_name') lp-seo-input-invalid @enderror"
                type="text"
                name="route_name"
                value="{{ old('route_name', $seoPage->route_name) }}"
                placeholder="landing.home"
            >

            @error('route_name')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>

        <label class="lp-label">
            <span>Titulo SEO</span>
            <input
                class="lp-input @error('title') lp-seo-input-invalid @enderror"
                type="text"
                name="title"
                value="{{ old('title', $seoPage->title) }}"
                maxlength="255"
            >

            @error('title')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>

        <label class="lp-label">
            <span>Descricao SEO</span>
            <textarea
                class="lp-textarea @error('description') lp-seo-input-invalid @enderror"
                name="description"
                rows="3"
                maxlength="500"
            >{{ old('description', $seoPage->description) }}</textarea>

            @error('description')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>

        <div class="lp-seo-admin-row">
            <label class="lp-label">
                <span>Canonical URL</span>
                <input
                    class="lp-input @error('canonical_url') lp-seo-input-invalid @enderror"
                    type="text"
                    name="canonical_url"
                    value="{{ old('canonical_url', $seoPage->canonical_url) }}"
                    placeholder="https://example.com/"
                >

                @error('canonical_url')
                    <span class="lp-error">{{ $message }}</span>
                @enderror
            </label>

            <label class="lp-label">
                <span>Robots</span>
                <input
                    class="lp-input @error('robots') lp-seo-input-invalid @enderror"
                    type="text"
                    name="robots"
                    value="{{ old('robots', $seoPage->robots) }}"
                    placeholder="index,follow"
                >

                @error('robots')
                    <span class="lp-error">{{ $message }}</span>
                @enderror
            </label>
        </div>

        <div class="lp-seo-admin-row">
            <label class="lp-label">
                <span>Imagem social padrao</span>
                <input
                    class="lp-input @error('image_url') lp-seo-input-invalid @enderror"
                    type="text"
                    name="image_url"
                    value="{{ old('image_url', $seoPage->image_url) }}"
                    placeholder="/images/share.jpg"
                >

                @error('image_url')
                    <span class="lp-error">{{ $message }}</span>
                @enderror
            </label>

            <label class="lp-label">
                <span>Favicon</span>
                <input
                    class="lp-input @error('favicon_url') lp-seo-input-invalid @enderror"
                    type="text"
                    name="favicon_url"
                    value="{{ old('favicon_url', $seoPage->favicon_url) }}"
                    placeholder="/favicon.ico"
                >

                @error('favicon_url')
                    <span class="lp-error">{{ $message }}</span>
                @enderror
            </label>
        </div>

        <div class="lp-seo-admin-row">
            <label class="lp-label">
                <span>Open Graph title</span>
                <input
                    class="lp-input @error('og_title') lp-seo-input-invalid @enderror"
                    type="text"
                    name="og_title"
                    value="{{ old('og_title', $seoPage->og_title) }}"
                >

                @error('og_title')
                    <span class="lp-error">{{ $message }}</span>
                @enderror
            </label>

            <label class="lp-label">
                <span>Open Graph type</span>
                <input
                    class="lp-input @error('og_type') lp-seo-input-invalid @enderror"
                    type="text"
                    name="og_type"
                    value="{{ old('og_type', $seoPage->og_type) }}"
                    placeholder="website"
                >

                @error('og_type')
                    <span class="lp-error">{{ $message }}</span>
                @enderror
            </label>
        </div>

        <label class="lp-label">
            <span>Open Graph description</span>
            <textarea
                class="lp-textarea @error('og_description') lp-seo-input-invalid @enderror"
                name="og_description"
                rows="2"
                maxlength="500"
            >{{ old('og_description', $seoPage->og_description) }}</textarea>

            @error('og_description')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>

        <label class="lp-label">
            <span>Open Graph image</span>
            <input
                class="lp-input @error('og_image') lp-seo-input-invalid @enderror"
                type="text"
                name="og_image"
                value="{{ old('og_image', $seoPage->og_image) }}"
                placeholder="/images/og.jpg"
            >

            @error('og_image')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>

        <div class="lp-seo-admin-row">
            <label class="lp-label">
                <span>Twitter/X title</span>
                <input
                    class="lp-input @error('twitter_title') lp-seo-input-invalid @enderror"
                    type="text"
                    name="twitter_title"
                    value="{{ old('twitter_title', $seoPage->twitter_title) }}"
                >

                @error('twitter_title')
                    <span class="lp-error">{{ $message }}</span>
                @enderror
            </label>

            <label class="lp-label">
                <span>Twitter/X card</span>
                <input
                    class="lp-input @error('twitter_card') lp-seo-input-invalid @enderror"
                    type="text"
                    name="twitter_card"
                    value="{{ old('twitter_card', $seoPage->twitter_card) }}"
                    placeholder="summary_large_image"
                >

                @error('twitter_card')
                    <span class="lp-error">{{ $message }}</span>
                @enderror
            </label>
        </div>

        <label class="lp-label">
            <span>Twitter/X description</span>
            <textarea
                class="lp-textarea @error('twitter_description') lp-seo-input-invalid @enderror"
                name="twitter_description"
                rows="2"
                maxlength="500"
            >{{ old('twitter_description', $seoPage->twitter_description) }}</textarea>

            @error('twitter_description')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>

        <label class="lp-label">
            <span>Twitter/X image</span>
            <input
                class="lp-input @error('twitter_image') lp-seo-input-invalid @enderror"
                type="text"
                name="twitter_image"
                value="{{ old('twitter_image', $seoPage->twitter_image) }}"
                placeholder="/images/twitter.jpg"
            >

            @error('twitter_image')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>

        <label class="lp-label">
            <span>Schema JSON opcional</span>
            <textarea
                class="lp-textarea @error('schema') lp-seo-input-invalid @enderror"
                name="schema"
                rows="8"
                placeholder='{"@type":"BreadcrumbList","itemListElement":[]}'
            >{{ $schema }}</textarea>

            @error('schema')
                <span class="lp-error">{{ $message }}</span>
            @enderror
        </label>

        <div class="lp-seo-admin-row">
            <label class="lp-label">
                <span>Frequencia no sitemap</span>
                <select class="lp-select @error('sitemap_changefreq') lp-seo-input-invalid @enderror" name="sitemap_changefreq">
                    @foreach (['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'] as $changefreq)
                        <option value="{{ $changefreq }}" @selected(old('sitemap_changefreq', $seoPage->sitemap_changefreq) === $changefreq)>
                            {{ $changefreq }}
                        </option>
                    @endforeach
                </select>

                @error('sitemap_changefreq')
                    <span class="lp-error">{{ $message }}</span>
                @enderror
            </label>

            <label class="lp-label">
                <span>Prioridade no sitemap</span>
                <input
                    class="lp-input @error('sitemap_priority') lp-seo-input-invalid @enderror"
                    type="number"
                    name="sitemap_priority"
                    value="{{ old('sitemap_priority', $seoPage->sitemap_priority) }}"
                    min="0"
                    max="1"
                    step="0.1"
                >

                @error('sitemap_priority')
                    <span class="lp-error">{{ $message }}</span>
                @enderror
            </label>
        </div>

        <div class="lp-seo-admin-checks">
            <label class="lp-seo-admin-check">
                <input
                    type="checkbox"
                    name="sitemap_enabled"
                    value="1"
                    @checked(old('sitemap_enabled', $seoPage->sitemap_enabled ?? true))
                >

                <span class="lp-muted">Incluir no sitemap</span>
            </label>

            <label class="lp-seo-admin-check">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    @checked(old('is_active', $seoPage->is_active ?? true))
                >

                <span class="lp-muted">Ativa</span>
            </label>
        </div>

        <div class="lp-seo-admin-actions">
            <button class="lp-button lp-button-primary" type="submit">
                {{ $submitLabel }}
            </button>

            <a class="lp-button lp-button-secondary" href="{{ route('seo.admin.index') }}">
                Cancelar
            </a>
        </div>
    </form>

    <aside class="lp-card lp-seo-preview" aria-label="Preview SEO">
        <div class="lp-seo-preview-block">
            <span class="lp-muted">Preview Google</span>
            <p class="lp-seo-preview-url">{{ $previewUrl }}</p>
            <h2 class="lp-subheading lp-seo-preview-title">{{ $previewTitle }}</h2>

            @if ($previewDescription)
                <p class="lp-muted">{{ $previewDescription }}</p>
            @endif
        </div>

        <div class="lp-seo-preview-block">
            <span class="lp-muted">Preview social</span>

            @if ($previewImage)
                <div class="lp-seo-preview-image">{{ $previewImage }}</div>
            @endif

            <h3 class="lp-subheading lp-seo-preview-title">{{ old('og_title', $seoPage->og_title) ?: $previewTitle }}</h3>

            @if (old('og_description', $seoPage->og_description) ?: $previewDescription)
                <p class="lp-muted">{{ old('og_description', $seoPage->og_description) ?: $previewDescription }}</p>
            @endif
        </div>
    </aside>
</div>
