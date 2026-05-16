@if ($layout === 'grid')
    <article class="lp-card lp-faq-card lp-faq-card-static">
        <h3 class="lp-subheading lp-faq-question-static">{{ $item['question'] }}</h3>

        <div class="lp-muted lp-faq-answer">
            <p>{!! nl2br(e($item['answer'])) !!}</p>
        </div>
    </article>
@else
    <details class="lp-card lp-faq-card" @if ($open) open @endif>
        <summary class="lp-faq-question">
            <span>{{ $item['question'] }}</span>
            <span class="lp-icon-wrapper lp-faq-icon" aria-hidden="true">+</span>
        </summary>

        <div class="lp-muted lp-faq-answer">
            <p>{!! nl2br(e($item['answer'])) !!}</p>
        </div>
    </details>
@endif
