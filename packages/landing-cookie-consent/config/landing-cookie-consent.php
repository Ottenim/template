<?php

return [
    'enabled' => true,

    'storage_key' => 'landing_cookie_consent',

    'version' => '1',

    'policy_url' => '/politica-de-privacidade',

    'consent_lifetime_days' => 180,

    'categories' => [
        'necessary' => [
            'enabled' => true,
            'required' => true,
            'label' => 'Necessarios',
            'description' => 'Essenciais para seguranca, funcionamento do site e preferencias de privacidade.',
        ],

        'analytics' => [
            'enabled' => true,
            'required' => false,
            'label' => 'Analytics',
            'description' => 'Ajudam a entender visitas, desempenho e interacoes para melhorar a landing page.',
        ],

        'marketing' => [
            'enabled' => true,
            'required' => false,
            'label' => 'Marketing',
            'description' => 'Permitem medir campanhas, conversoes e personalizar comunicacoes comerciais.',
        ],

        'personalization' => [
            'enabled' => false,
            'required' => false,
            'label' => 'Personalizacao',
            'description' => 'Guardam preferencias para adaptar conteudos e experiencias futuras.',
        ],
    ],

    'banner' => [
        'enabled' => true,
        'position' => 'bottom',
        'layout' => 'bar',
        'title' => 'Preferencias de cookies',
        'message' => 'Usamos cookies para melhorar sua experiencia, analisar o uso do site e apoiar nossas campanhas.',
        'policy_label' => 'Saiba mais',
        'accept_all_label' => 'Aceitar todos',
        'reject_optional_label' => 'Recusar opcionais',
        'configure_label' => 'Configurar',
        'reopen_label' => 'Privacidade',
        'show_reopen_button' => true,
        'aria_label' => 'Aviso de cookies',
    ],

    'modal' => [
        'title' => 'Gerenciar preferencias de cookies',
        'description' => 'Escolha quais categorias opcionais podem ser usadas. Cookies necessarios permanecem ativos para o site funcionar corretamente.',
        'save_preferences_label' => 'Salvar preferencias',
        'accept_all_label' => 'Aceitar todos',
        'reject_optional_label' => 'Recusar opcionais',
        'close_label' => 'Fechar',
    ],

    'scripts' => [
        'selector' => 'script[type="text/plain"][data-landing-cookie-category], script[type="text/plain"][data-cookie-category]',
    ],

    'logging' => [
        'enabled' => true,
        'store_ip' => false,
        'store_user_agent' => true,

        'database' => [
            'enabled' => true,
            'table' => 'lp_cookie_consents',
        ],

        'route' => [
            'enabled' => true,
            'uri' => 'cookie-consent',
            'name' => 'landing-cookie-consent.store',
            'middleware' => ['web'],
            'rate_limit' => true,
            'rate_limit_max_attempts' => 30,
            'rate_limit_decay_minutes' => 1,
        ],
    ],

    'integrations' => [
        'analytics' => [
            'enabled' => true,
            'sync_config' => true,
            'default_granted' => false,
            'categories' => [
                'analytics' => 'analytics',
                'marketing' => 'marketing',
            ],
        ],
    ],
];
