<a
    href="{{ $url }}"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="{{ $ariaLabel }}"
    @if ($trackingEnabled) data-landing-event="{{ $trackingEvent }}" @endif
    @if ($style !== '') style="{{ $style }}" @endif
    {{ $attributes->class($buttonClasses) }}
>
    @if ($showIcon)
        <span class="lp-icon-wrapper lp-whatsapp-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" role="img" focusable="false">
                <path d="M19.1 4.9A9.8 9.8 0 0 0 3.7 16.7L2.5 21.2l4.6-1.2A9.8 9.8 0 0 0 21.9 11.6a9.7 9.7 0 0 0-2.8-6.7Zm-7 14.9a8 8 0 0 1-4.1-1.1l-.3-.2-2.7.7.7-2.6-.2-.3A8 8 0 1 1 12.1 19.8Zm4.4-6c-.2-.1-1.4-.7-1.6-.8s-.4-.1-.6.1-.6.8-.8.9-.3.2-.6.1a6.6 6.6 0 0 1-3.3-2.9c-.2-.3 0-.4.1-.6l.4-.5c.1-.2.2-.4.3-.6a.5.5 0 0 0 0-.5c-.1-.1-.6-1.4-.8-1.9s-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 2.8 2.8 0 0 0-.9 2.1 4.9 4.9 0 0 0 1 2.6 11.2 11.2 0 0 0 4.3 3.8c1.6.7 2.2.7 3 .6a2.5 2.5 0 0 0 1.7-1.2 2 2 0 0 0 .1-1.2c-.1-.1-.3-.2-.5-.3Z" />
            </svg>
        </span>
    @endif

    @if ($showText)
        <span class="lp-whatsapp-label">{{ $label }}</span>
    @else
        <span class="lp-visually-hidden">{{ $label }}</span>
    @endif

    @if ($tooltip)
        <span class="lp-whatsapp-tooltip" role="tooltip">{{ $tooltip }}</span>
    @endif
</a>
