<?php

return [
    'enabled' => true,

    'layout' => 'grid',

    'columns' => 3,

    'show_avatar' => true,

    'show_rating' => false,

    'show_company' => true,

    'show_logo' => true,

    'limit' => 6,

    'section' => [
        'enabled' => true,
        'eyebrow' => 'Depoimentos',
        'title' => 'O que dizem nossos clientes',
        'subtitle' => 'Relatos de quem ja utilizou nosso servico.',
    ],

    'database' => [
        'enabled' => true,
        'table' => 'lp_testimonials',
    ],

    'items' => [
        // [
        //     'name' => 'Nome do cliente',
        //     'text' => 'Depoimento curto e objetivo.',
        //     'role' => 'Cargo',
        //     'company' => 'Empresa',
        //     'avatar' => null,
        //     'logo' => null,
        //     'rating' => null,
        //     'sort_order' => 0,
        //     'is_featured' => false,
        //     'is_active' => true,
        // ],
    ],

    'admin' => [
        'enabled' => false,
        'prefix' => 'admin/testimonials',
        'middleware' => ['web', 'auth'],
        'per_page' => 15,
    ],
];
