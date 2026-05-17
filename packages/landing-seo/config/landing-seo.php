<?php

return [
    'enabled' => true,

    'defaults' => [
        'title' => env('APP_NAME', 'Landing Page'),
        'title_template' => '%s',
        'description' => null,
        'canonical_url' => null,
        'image' => null,
        'favicon' => null,
        'robots' => 'index,follow',
        'site_name' => env('APP_NAME', 'Landing Page'),
        'locale' => str_replace('_', '-', env('APP_LOCALE', 'pt_BR')),
    ],

    'open_graph' => [
        'enabled' => true,
        'type' => 'website',
    ],

    'twitter' => [
        'enabled' => true,
        'card' => 'summary_large_image',
        'site' => null,
    ],

    'schema' => [
        'enabled' => true,
        'type' => 'WebSite',
        'organization' => [
            'name' => env('APP_NAME', 'Landing Page'),
            'url' => env('APP_URL'),
            'logo' => null,
        ],
    ],

    'sitemap' => [
        'enabled' => true,
        'include_home' => true,
        'default_changefreq' => 'weekly',
        'default_priority' => 0.5,
    ],

    'robots_txt' => [
        'enabled' => true,
        'rules' => [
            [
                'user_agent' => '*',
                'disallow' => [],
                'allow' => [],
            ],
        ],
        'include_sitemap' => true,
    ],

    'database' => [
        'enabled' => true,
        'table' => 'lp_seo_pages',
    ],

    'pages' => [
        // '/' => [
        //     'title' => 'Landing Page',
        //     'description' => 'Descricao curta da pagina.',
        //     'sitemap_enabled' => true,
        // ],
    ],

    'admin' => [
        'enabled' => false,
        'prefix' => 'admin/seo',
        'middleware' => ['web', 'auth'],
        'per_page' => 15,
    ],
];
