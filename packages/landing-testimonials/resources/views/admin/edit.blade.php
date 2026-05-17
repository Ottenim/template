<x-landing-core::base-layout title="Editar depoimento" body-class="lp-testimonials-admin-page">
    <x-slot:head>
        <x-testimonials::styles />
    </x-slot:head>

    <section class="lp-section lp-testimonials-admin">
        <div class="lp-container">
            <header class="lp-section-header">
                <span class="lp-eyebrow">Testimonials</span>
                <h1 class="lp-heading">Editar depoimento</h1>
            </header>

            @include('landing-testimonials::admin.form', [
                'testimonial' => $testimonial,
                'action' => route('testimonials.admin.update', $testimonial),
                'method' => 'PUT',
                'submitLabel' => 'Salvar depoimento',
            ])
        </div>
    </section>
</x-landing-core::base-layout>
