<?php

return [
    'enabled' => true,

    'layout' => 'cards',

    'columns' => 3,

    'show_featured_plan' => true,

    'featured_label' => 'Mais escolhido',

    'currency' => 'R$',

    'billing_period_label' => '/mes',

    'limit' => null,

    'section' => [
        'enabled' => true,
        'eyebrow' => 'Planos',
        'title' => 'Escolha o melhor plano',
        'subtitle' => 'Opcoes flexiveis para diferentes necessidades.',
    ],

    'database' => [
        'enabled' => true,
        'table' => 'lp_pricing_plans',
    ],

    'plans' => [
        // [
        //     'name' => 'Plano inicial',
        //     'description' => 'Ideal para validar uma nova landing page.',
        //     'price' => '99',
        //     'currency' => 'R$',
        //     'billing_period_label' => '/mes',
        //     'features' => [
        //         'Landing page responsiva',
        //         'Formulario de contato',
        //         'CTA por WhatsApp',
        //     ],
        //     'cta_label' => 'Escolher plano',
        //     'cta_url' => '#contact',
        //     'note' => null,
        //     'badge' => null,
        //     'sort_order' => 0,
        //     'is_featured' => false,
        //     'is_active' => true,
        // ],
    ],

    'cta' => [
        'default_label' => 'Escolher plano',
        'default_url' => '#contact',
    ],

    'tracking' => [
        'enabled' => true,
        'event_name' => 'pricing_cta_click',
    ],

    'admin' => [
        'enabled' => false,
        'prefix' => 'admin/pricing',
        'middleware' => ['web', 'auth'],
        'per_page' => 15,
    ],
];
