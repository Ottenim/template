<?php

return [
    'enabled' => true,

    'phone' => env('LANDING_WHATSAPP_PHONE'),

    'message' => 'Ola, vim pelo site e quero saber mais.',

    'visibility' => 'all',

    'button' => [
        'label' => 'Falar no WhatsApp',
        'aria_label' => 'Falar no WhatsApp',
        'show_text' => true,
        'show_icon' => true,
        'tooltip' => null,
    ],

    'floating' => [
        'enabled' => true,
        'position' => 'bottom-right',
        'show_text' => false,
        'show_icon' => true,
        'tooltip' => 'Falar no WhatsApp',
        'visibility' => 'all',
        'mobile_bar' => false,
    ],

    'section' => [
        'enabled' => true,
        'eyebrow' => 'Atendimento',
        'title' => 'Precisa falar com a gente?',
        'subtitle' => 'Chame pelo WhatsApp para tirar duvidas ou iniciar uma conversa.',
        'text' => 'A equipe responde pelo canal direto configurado para esta landing page.',
        'card' => true,
    ],

    'tracking' => [
        'enabled' => false,
        'event_name' => 'whatsapp_click',
    ],

    'style' => [
        'use_brand_color' => false,
        'brand_color' => '#25D366',
        'brand_text_color' => '#ffffff',
    ],
];
