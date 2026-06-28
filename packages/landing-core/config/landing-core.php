<?php

return [
    'modules' => [
        'landing-core' => [
            'name' => 'landing-core',
            'label' => 'Landing Core',
            'enabled' => true,
            'description' => 'Base visual, registries, helpers, assets, and section rendering for landing modules.',
        ],
    ],

    'menus' => [],

    'assets' => [
        'styles' => [],
        'scripts' => [],
    ],

    'sections' => [],

    'observability' => [
        'request_id' => [
            'enabled' => true,
            'header' => 'X-Request-Id',
        ],
    ],
];
