<x-landing-core::base-layout title="Editar pergunta" body-class="lp-faq-admin-page">
    <x-slot:head>
        <x-faq::styles />
    </x-slot:head>

    <section class="lp-section lp-faq-admin">
        <div class="lp-container">
            <header class="lp-section-header">
                <span class="lp-eyebrow">FAQ</span>
                <h1 class="lp-heading">Editar pergunta</h1>
            </header>

            @include('landing-faq::admin.form', [
                'faqItem' => $faqItem,
                'action' => route('faq.admin.update', $faqItem),
                'method' => 'PUT',
                'submitLabel' => 'Salvar pergunta',
            ])
        </div>
    </section>
</x-landing-core::base-layout>
