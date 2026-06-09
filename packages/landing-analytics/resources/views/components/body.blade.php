@once('landing-analytics-body')
    @if ($renderNoScript && isset($providers['gtm']))
        <noscript>
            <iframe
                src="https://www.googletagmanager.com/ns.html?id={{ rawurlencode($providers['gtm']['id']) }}"
                height="0"
                width="0"
                style="display:none;visibility:hidden"
                title="Google Tag Manager"
            ></iframe>
        </noscript>
    @endif

    @if ($renderNoScript && isset($providers['meta_pixel']))
        <noscript>
            <img
                height="1"
                width="1"
                style="display:none"
                alt=""
                src="https://www.facebook.com/tr?id={{ rawurlencode($providers['meta_pixel']['id']) }}&amp;ev=PageView&amp;noscript=1"
            >
        </noscript>
    @endif

    @if ($renderNoScript && isset($providers['linkedin_insight']))
        <noscript>
            <img
                height="1"
                width="1"
                style="display:none"
                alt=""
                src="https://px.ads.linkedin.com/collect/?pid={{ rawurlencode($providers['linkedin_insight']['id']) }}&amp;fmt=gif"
            >
        </noscript>
    @endif
@endonce

@if ($debug)
    @once('landing-analytics-styles')
        <x-analytics::styles />
    @endonce

    <x-analytics::debug />
@endif
