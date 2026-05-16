<?php

return [
    'enabled' => true,

    'route' => [
        'enabled' => true,
        'uri' => 'contact',
        'name' => 'landing-contact.submit',
        'middleware' => ['web'],
    ],

    'section' => [
        'enabled' => true,
        'eyebrow' => 'Contato',
        'title' => 'Entre em contato',
        'subtitle' => 'Preencha o formulario e retornaremos em breve.',
    ],

    'fields' => [
        'name' => true,
        'email' => true,
        'phone' => true,
        'company' => false,
        'interest' => false,
        'message' => true,
    ],

    'database' => [
        'table' => 'lp_contact_messages',
    ],

    'save_to_database' => true,

    'send_email' => [
        'enabled' => true,
        'to' => env('LANDING_CONTACT_MAIL_TO'),
        'subject' => 'Nova mensagem de contato',
    ],

    'redirect_after_submit' => null,

    'button' => [
        'label' => 'Solicitar contato',
    ],

    'messages' => [
        'success' => 'Mensagem enviada com sucesso. Retornaremos em breve.',
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
        'event_name' => 'contact_form_submit',
    ],
];
