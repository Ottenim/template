<aside class="lp-card lp-analytics-debug" data-landing-analytics-debug>
    <header class="lp-analytics-debug-header">
        <strong>Analytics Debug</strong>
        <span class="lp-muted">{{ count($providers) }} providers</span>
    </header>

    <div class="lp-analytics-debug-grid">
        <section>
            <span class="lp-analytics-debug-label">Providers ativos</span>

            <ul>
                @forelse ($providers as $provider)
                    <li>{{ $provider['label'] }}</li>
                @empty
                    <li class="lp-muted">Nenhum provider com ID configurado.</li>
                @endforelse
            </ul>
        </section>

        <section>
            <span class="lp-analytics-debug-label">Eventos ativos</span>

            <ul>
                @forelse ($events as $event)
                    <li>{{ $event }}</li>
                @empty
                    <li class="lp-muted">Nenhum evento ativo.</li>
                @endforelse
            </ul>
        </section>
    </div>

    <section class="lp-analytics-debug-stream">
        <span class="lp-analytics-debug-label">Eventos recebidos</span>
        <ul data-landing-analytics-events></ul>
    </section>
</aside>
