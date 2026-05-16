<?php

return [
    'enabled' => true,

    'layout' => 'accordion',

    'show_categories' => false,

    'default_open_first_item' => true,

    'limit' => null,

    'section' => [
        'enabled' => true,
        'eyebrow' => 'Duvidas',
        'title' => 'Perguntas frequentes',
        'subtitle' => 'Veja as principais duvidas antes de entrar em contato.',
    ],

    'database' => [
        'enabled' => true,
        'table' => 'lp_faq_items',
    ],

    'items' => [
        // [
        //     'question' => 'Qual e a principal duvida?',
        //     'answer' => 'Resposta curta e objetiva.',
        //     'category' => null,
        //     'sort_order' => 0,
        //     'is_active' => true,
        // ],
    ],

    'admin' => [
        'enabled' => false,
        'prefix' => 'admin/faq',
        'middleware' => ['web', 'auth'],
        'per_page' => 15,
    ],

    'schema' => [
        'enabled' => true,
    ],
];
