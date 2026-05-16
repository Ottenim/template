<x-landing-core::base-layout title="FAQ" body-class="lp-faq-admin-page">
    <x-slot:head>
        <x-faq::styles />
    </x-slot:head>

    <section class="lp-section lp-faq-admin">
        <div class="lp-container">
            <header class="lp-section-header">
                <span class="lp-eyebrow">FAQ</span>
                <h1 class="lp-heading">Perguntas frequentes</h1>
                <p class="lp-muted">Gerencie as perguntas exibidas no componente publico.</p>
            </header>

            <div class="lp-section-content">
                @if (session('landing_faq_success'))
                    <p class="lp-success lp-faq-admin-feedback" role="status">
                        {{ session('landing_faq_success') }}
                    </p>
                @endif

                <div class="lp-faq-admin-actions">
                    <a class="lp-button lp-button-primary" href="{{ route('faq.admin.create') }}">
                        Nova pergunta
                    </a>
                </div>

                @if ($items->isEmpty())
                    <article class="lp-card lp-faq-admin-empty">
                        <p class="lp-muted">Nenhuma pergunta cadastrada.</p>
                    </article>
                @else
                    <div class="lp-faq-admin-list">
                        @foreach ($items as $faqItem)
                            <article class="lp-card lp-faq-admin-item">
                                <div>
                                    <p class="lp-faq-admin-question">{{ $faqItem->question }}</p>

                                    <div class="lp-faq-admin-meta">
                                        @if ($faqItem->category)
                                            <span class="lp-badge">{{ $faqItem->category }}</span>
                                        @endif

                                        <span class="lp-muted">Ordem {{ $faqItem->sort_order }}</span>
                                        <span class="lp-muted">{{ $faqItem->is_active ? 'Ativo' : 'Inativo' }}</span>
                                    </div>
                                </div>

                                <div class="lp-faq-admin-item-actions">
                                    <a class="lp-button lp-button-secondary" href="{{ route('faq.admin.edit', $faqItem) }}">
                                        Editar
                                    </a>

                                    <form method="POST" action="{{ route('faq.admin.destroy', $faqItem) }}">
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

                    {{ $items->links() }}
                @endif
            </div>
        </div>
    </section>
</x-landing-core::base-layout>
