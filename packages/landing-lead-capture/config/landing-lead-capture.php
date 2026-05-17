<?php

return [
    'enabled' => true,

    'variant' => 'inline',

    'route' => [
        'enabled' => true,
        'uri' => 'lead-capture',
        'name' => 'landing-lead-capture.submit',
        'middleware' => ['web'],
    ],

    'section' => [
        'enabled' => true,
        'eyebrow' => 'Newsletter',
        'benefit' => 'Receba conteudos e atualizacoes relevantes em primeira mao.',
    ],

    'cta' => [
        'title' => 'Receba novidades',
        'subtitle' => 'Cadastre-se para receber informacoes em primeira mao.',
        'button_label' => 'Quero receber',
    ],

    'fields' => [
        'name' => true,
        'email' => true,
        'phone' => false,
        'company' => false,
        'interest' => false,
    ],

    'lead' => [
        'source' => 'landing-page',
        'campaign' => null,
        'tag' => null,
    ],

    'database' => [
        'table' => 'lp_leads',
    ],

    'save_to_database' => true,

    'send_email' => [
        'enabled' => false,
        'to' => env('LANDING_LEAD_CAPTURE_MAIL_TO'),
        'subject' => 'Novo lead capturado',
    ],

    'redirect_after_submit' => null,

    'messages' => [
        'success' => 'Cadastro realizado com sucesso.',
    ],

    'privacy_consent' => [
        'enabled' => true,
        'required' => true,
        'label' => 'Li e aceito a politica de privacidade.',
    ],

    'anti_spam' => [
        'honeypot' => true,
        'honeypot_field' => 'website',
        'rate_limit' => true,
        'rate_limit_max_attempts' => 5,
        'rate_limit_decay_minutes' => 1,
    ],

    'tracking' => [
        'enabled' => false,
        'event_name' => 'lead_capture_submit',
    ],
];
