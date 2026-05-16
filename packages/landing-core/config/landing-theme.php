<?php

return [
    'active' => env('LANDING_THEME', 'default'),

    'themes' => [
        'default' => [
            'font' => [
                'sans' => 'Inter, ui-sans-serif, system-ui, sans-serif',
                'heading' => 'Inter, ui-sans-serif, system-ui, sans-serif',
            ],

            'colors' => [
                'primary' => '#2563eb',
                'on_primary' => '#ffffff',
                'secondary' => '#64748b',
                'background' => '#ffffff',
                'surface' => '#f8fafc',
                'text' => '#0f172a',
                'muted' => '#64748b',
                'border' => '#e2e8f0',
                'danger' => '#dc2626',
                'success' => '#16a34a',
                'warning' => '#ca8a04',
            ],

            'radius' => [
                'sm' => '0.375rem',
                'md' => '0.75rem',
                'lg' => '1rem',
                'xl' => '1.5rem',
            ],

            'spacing' => [
                'section_y' => '5rem',
                'container' => '1120px',
                'content_gap' => '2rem',
            ],

            'shadow' => [
                'card' => '0 10px 30px rgba(15, 23, 42, 0.08)',
                'button' => '0 10px 20px rgba(37, 99, 235, 0.18)',
                'focus' => '0 0 0 3px rgba(37, 99, 235, 0.18)',
            ],
        ],

        'dark' => [
            'colors' => [
                'primary' => '#60a5fa',
                'on_primary' => '#07111f',
                'secondary' => '#94a3b8',
                'background' => '#0f172a',
                'surface' => '#111827',
                'text' => '#f8fafc',
                'muted' => '#cbd5e1',
                'border' => '#334155',
            ],

            'shadow' => [
                'card' => '0 18px 45px rgba(0, 0, 0, 0.28)',
                'button' => '0 10px 24px rgba(96, 165, 250, 0.20)',
                'focus' => '0 0 0 3px rgba(96, 165, 250, 0.24)',
            ],
        ],
    ],

    'components' => [
        'section' => 'lp-section',
        'container' => 'lp-container',
        'section_header' => 'lp-section-header',
        'section_content' => 'lp-section-content',
        'card' => 'lp-card',
        'heading' => 'lp-heading',
        'subheading' => 'lp-subheading',
        'muted' => 'lp-muted',
        'button' => 'lp-button',
        'button_primary' => 'lp-button lp-button-primary',
        'button_secondary' => 'lp-button lp-button-secondary',
        'input' => 'lp-input',
        'label' => 'lp-label',
    ],
];
