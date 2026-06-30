<?php

namespace Template\LandingLeadCapture\Support;

use Template\LandingCore\Support\Coerce;
use Template\LandingLeadCapture\Config\LeadCaptureConfig;

class LeadCaptureFields
{
    public const SUPPORTED_FIELDS = [
        'name',
        'email',
        'phone',
        'company',
        'interest',
    ];

    public function __construct(
        protected ?array $fields = null,
    ) {
        //
    }

    public function all(): array
    {
        return collect(self::SUPPORTED_FIELDS)
            ->mapWithKeys(fn (string $name) => [$name => $this->get($name)])
            ->all();
    }

    public function enabled(): array
    {
        return array_filter($this->all(), fn (array $field) => $field['enabled']);
    }

    public function enabledNames(): array
    {
        return array_keys($this->enabled());
    }

    public function get(string $name): array
    {
        return $this->normalize($name, $this->fields()[$name] ?? null);
    }

    public function isEnabled(string $name): bool
    {
        return $this->get($name)['enabled'];
    }

    protected function fields(): array
    {
        return $this->fields ?? LeadCaptureConfig::fromConfig()->fields();
    }

    protected function normalize(string $name, mixed $config): array
    {
        $default = $this->defaults()[$name] ?? [
            'enabled' => false,
            'required' => false,
            'label' => ucfirst($name),
            'type' => 'text',
            'placeholder' => null,
            'autocomplete' => null,
            'options' => [],
        ];

        if (is_bool($config)) {
            return [
                ...$default,
                'enabled' => $config,
            ];
        }

        if (! is_array($config)) {
            return $default;
        }

        $field = [
            ...$default,
            ...$config,
        ];

        $field['enabled'] = Coerce::bool($field['enabled'] ?? true, true);
        $field['required'] = Coerce::bool($field['required'] ?? false, false);
        $field['label'] = Coerce::string($field['label'] ?? null, $default['label']);
        $field['type'] = Coerce::string($field['type'] ?? null, $default['type']);
        $field['placeholder'] = Coerce::nullableString($field['placeholder'] ?? null);
        $field['autocomplete'] = Coerce::nullableString($field['autocomplete'] ?? null);
        $field['options'] = $this->normalizeOptions($field['options'] ?? []);

        return $field;
    }

    protected function defaults(): array
    {
        return [
            'name' => [
                'enabled' => true,
                'required' => true,
                'label' => 'Nome',
                'type' => 'text',
                'placeholder' => 'Seu nome',
                'autocomplete' => 'name',
                'options' => [],
            ],
            'email' => [
                'enabled' => true,
                'required' => true,
                'label' => 'Email',
                'type' => 'email',
                'placeholder' => 'Seu email',
                'autocomplete' => 'email',
                'options' => [],
            ],
            'phone' => [
                'enabled' => false,
                'required' => false,
                'label' => 'Telefone',
                'type' => 'tel',
                'placeholder' => 'Seu telefone',
                'autocomplete' => 'tel',
                'options' => [],
            ],
            'company' => [
                'enabled' => false,
                'required' => false,
                'label' => 'Empresa',
                'type' => 'text',
                'placeholder' => 'Nome da empresa',
                'autocomplete' => 'organization',
                'options' => [],
            ],
            'interest' => [
                'enabled' => false,
                'required' => false,
                'label' => 'Interesse',
                'type' => 'select',
                'placeholder' => 'Selecione seu interesse',
                'autocomplete' => null,
                'options' => [],
            ],
        ];
    }

    protected function normalizeOptions(mixed $options): array
    {
        if (! is_array($options)) {
            return [];
        }

        return collect($options)
            ->map(function (mixed $label, string|int $value) {
                if (is_int($value)) {
                    $value = $label;
                }

                return [
                    'value' => (string) $value,
                    'label' => (string) $label,
                ];
            })
            ->filter(fn (array $option) => $option['value'] !== '' && $option['label'] !== '')
            ->values()
            ->all();
    }
}
