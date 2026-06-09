<?php

return [
    'enabled' => true,

    'debug' => env('APP_DEBUG', false),

    'debug_environments' => ['local', 'testing'],

    'data_layer' => 'dataLayer',

    'providers' => [
        'gtm' => [
            'enabled' => true,
            'label' => 'Google Tag Manager',
            'id' => env('GTM_ID'),
            'category' => 'analytics',
        ],

        'ga4' => [
            'enabled' => false,
            'label' => 'Google Analytics 4',
            'id' => env('GA4_ID'),
            'category' => 'analytics',
            'send_page_view' => false,
        ],

        'meta_pixel' => [
            'enabled' => false,
            'label' => 'Meta Pixel',
            'id' => env('META_PIXEL_ID'),
            'category' => 'marketing',
        ],

        'tiktok_pixel' => [
            'enabled' => false,
            'label' => 'TikTok Pixel',
            'id' => env('TIKTOK_PIXEL_ID'),
            'category' => 'marketing',
        ],

        'linkedin_insight' => [
            'enabled' => false,
            'label' => 'LinkedIn Insight Tag',
            'id' => env('LINKEDIN_PARTNER_ID'),
            'category' => 'marketing',
            'conversion_ids' => [
                'contact_submit' => env('LINKEDIN_CONTACT_CONVERSION_ID'),
                'lead_capture_submit' => env('LINKEDIN_LEAD_CONVERSION_ID'),
                'pricing_cta_click' => env('LINKEDIN_PRICING_CONVERSION_ID'),
            ],
        ],
    ],

    'events' => [
        'page_view' => true,
        'contact_submit' => true,
        'contact_form_submit' => true,
        'lead_capture_submit' => true,
        'whatsapp_click' => true,
        'pricing_cta_click' => true,
        'cta_click' => true,
        'scroll_depth' => false,
    ],

    'auto_track' => [
        'clicks' => true,
        'forms' => true,

        'scroll_depth' => [
            'enabled' => false,
            'event_name' => 'scroll_depth',
            'percentages' => [25, 50, 75, 100],
        ],
    ],

    'selectors' => [
        'clicks' => [
            [
                'selector' => '[data-landing-event]',
                'attribute' => 'data-landing-event',
            ],
            [
                'selector' => '[data-event]',
                'attribute' => 'data-event',
            ],
        ],

        'forms' => [
            [
                'selector' => 'form[data-landing-contact-event]',
                'attribute' => 'data-landing-contact-event',
                'module' => 'contact',
            ],
            [
                'selector' => 'form[data-landing-lead-capture-event]',
                'attribute' => 'data-landing-lead-capture-event',
                'module' => 'lead_capture',
            ],
        ],
    ],

    'consent' => [
        'enabled' => false,
        'storage_key' => 'landing_cookie_consent',
        'default_granted' => false,
        'categories' => [
            'analytics' => 'analytics',
            'marketing' => 'marketing',
        ],
    ],
];
