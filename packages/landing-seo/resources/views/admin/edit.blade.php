<x-landing-core::base-layout title="Editar pagina SEO" body-class="lp-seo-admin-page">
    <x-slot:head>
        <x-seo::styles />
    </x-slot:head>

    <section class="lp-section lp-seo-admin">
        <div class="lp-container">
            <header class="lp-section-header">
                <span class="lp-eyebrow">SEO Manager</span>
                <h1 class="lp-heading">Editar pagina SEO</h1>
            </header>

            @include('landing-seo::admin.form', [
                'seoPage' => $seoPage,
                'action' => route('seo.admin.update', $seoPage),
                'method' => 'PUT',
                'submitLabel' => 'Salvar pagina',
                'previewDefaults' => $previewDefaults,
            ])
        </div>
    </section>
</x-landing-core::base-layout>
