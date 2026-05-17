<x-landing-core::base-layout title="SEO Manager" body-class="lp-seo-admin-page">
    <x-slot:head>
        <x-seo::styles />
    </x-slot:head>

    <section class="lp-section lp-seo-admin">
        <div class="lp-container">
            <header class="lp-section-header">
                <span class="lp-eyebrow">SEO Manager</span>
                <h1 class="lp-heading">Paginas SEO</h1>
                <p class="lp-muted">Gerencie metadados, schema e sitemap das paginas da landing page.</p>
            </header>

            <div class="lp-section-content">
                @if (session('landing_seo_success'))
                    <p class="lp-success lp-seo-admin-feedback" role="status">
                        {{ session('landing_seo_success') }}
                    </p>
                @endif

                <div class="lp-seo-admin-actions">
                    <a class="lp-button lp-button-primary" href="{{ route('seo.admin.create') }}">
                        Nova pagina
                    </a>
                </div>

                @if ($seoPages->isEmpty())
                    <article class="lp-card lp-seo-admin-empty">
                        <p class="lp-muted">Nenhuma pagina SEO cadastrada.</p>
                    </article>
                @else
                    <div class="lp-seo-admin-list">
                        @foreach ($seoPages as $seoPage)
                            <article class="lp-card lp-seo-admin-item">
                                <div class="lp-seo-admin-summary">
                                    <div>
                                        <p class="lp-seo-admin-title">{{ $seoPage->title ?: $seoPage->page_key }}</p>

                                        @if ($seoPage->description)
                                            <p class="lp-muted">{{ $seoPage->description }}</p>
                                        @endif

                                        <div class="lp-seo-admin-meta">
                                            <span class="lp-badge">{{ $seoPage->path ?: $seoPage->route_name ?: $seoPage->page_key }}</span>
                                            <span class="lp-muted">{{ $seoPage->robots ?: config('landing-seo.defaults.robots') }}</span>
                                            <span class="lp-muted">{{ $seoPage->sitemap_enabled ? 'Sitemap' : 'Fora do sitemap' }}</span>
                                            <span class="lp-muted">{{ $seoPage->is_active ? 'Ativa' : 'Inativa' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="lp-seo-admin-item-actions">
                                    <a class="lp-button lp-button-secondary" href="{{ route('seo.admin.edit', $seoPage) }}">
                                        Editar
                                    </a>

                                    <form method="POST" action="{{ route('seo.admin.destroy', $seoPage) }}">
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

                    {{ $seoPages->links() }}
                @endif
            </div>
        </div>
    </section>
</x-landing-core::base-layout>
