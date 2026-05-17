<x-landing-core::base-layout title="Testimonials" body-class="lp-testimonials-admin-page">
    <x-slot:head>
        <x-testimonials::styles />
    </x-slot:head>

    <section class="lp-section lp-testimonials-admin">
        <div class="lp-container">
            <header class="lp-section-header">
                <span class="lp-eyebrow">Testimonials</span>
                <h1 class="lp-heading">Depoimentos</h1>
                <p class="lp-muted">Gerencie os depoimentos exibidos no componente publico.</p>
            </header>

            <div class="lp-section-content">
                @if (session('landing_testimonials_success'))
                    <p class="lp-success lp-testimonials-admin-feedback" role="status">
                        {{ session('landing_testimonials_success') }}
                    </p>
                @endif

                <div class="lp-testimonials-admin-actions">
                    <a class="lp-button lp-button-primary" href="{{ route('testimonials.admin.create') }}">
                        Novo depoimento
                    </a>
                </div>

                @if ($testimonials->isEmpty())
                    <article class="lp-card lp-testimonials-admin-empty">
                        <p class="lp-muted">Nenhum depoimento cadastrado.</p>
                    </article>
                @else
                    <div class="lp-testimonials-admin-list">
                        @foreach ($testimonials as $testimonial)
                            <article class="lp-card lp-testimonials-admin-item">
                                <div class="lp-testimonials-admin-summary">
                                    @if ($testimonial->avatar)
                                        <img
                                            class="lp-testimonials-admin-avatar"
                                            src="{{ $testimonial->avatar }}"
                                            alt="Foto de {{ $testimonial->name }}"
                                            loading="lazy"
                                        >
                                    @endif

                                    <div>
                                        <p class="lp-testimonials-admin-name">{{ $testimonial->name }}</p>
                                        <p class="lp-muted">{{ $testimonial->text }}</p>

                                        <div class="lp-testimonials-admin-meta">
                                            @if ($testimonial->company)
                                                <span class="lp-badge">{{ $testimonial->company }}</span>
                                            @endif

                                            @if ($testimonial->rating)
                                                <span class="lp-muted">Nota {{ $testimonial->rating }}/5</span>
                                            @endif

                                            <span class="lp-muted">Ordem {{ $testimonial->sort_order }}</span>
                                            <span class="lp-muted">{{ $testimonial->is_featured ? 'Destaque' : 'Padrao' }}</span>
                                            <span class="lp-muted">{{ $testimonial->is_active ? 'Ativo' : 'Inativo' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="lp-testimonials-admin-item-actions">
                                    <a class="lp-button lp-button-secondary" href="{{ route('testimonials.admin.edit', $testimonial) }}">
                                        Editar
                                    </a>

                                    <form method="POST" action="{{ route('testimonials.admin.destroy', $testimonial) }}">
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

                    {{ $testimonials->links() }}
                @endif
            </div>
        </div>
    </section>
</x-landing-core::base-layout>
