<x-landing-core::base-layout title="Nova pergunta" body-class="lp-faq-admin-page">
    <x-slot:head>
        <x-faq::styles />
    </x-slot:head>

    <section class="lp-section lp-faq-admin">
        <div class="lp-container">
            <header class="lp-section-header">
                <span class="lp-eyebrow">FAQ</span>
                <h1 class="lp-heading">Nova pergunta</h1>
            </header>

            @include('landing-faq::admin.form', [
                'faqItem' => $faqItem,
                'action' => route('faq.admin.store'),
                'method' => 'POST',
                'submitLabel' => 'Criar pergunta',
            ])
        </div>
    </section>
</x-landing-core::base-layout>
