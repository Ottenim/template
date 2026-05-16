<?php

namespace Template\LandingContact\Support;

class ContactFields
{
    public const SUPPORTED_FIELDS = [
        'name',
        'email',
        'phone',
        'company',
        'interest',
        'message',
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

    public function isRequired(string $name): bool
    {
        $field = $this->get($name);

        return $field['enabled'] && $field['required'];
    }

    protected function fields(): array
    {
        return $this->fields ?? config('landing-contact.fields', []);
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

        $field['enabled'] = $this->boolValue($field['enabled'] ?? true, true);
        $field['required'] = $this->boolValue($field['required'] ?? false, false);
        $field['label'] = $this->stringValue($field['label'] ?? null, $default['label']);
        $field['type'] = $this->stringValue($field['type'] ?? null, $default['type']);
        $field['placeholder'] = $this->nullableString($field['placeholder'] ?? null);
        $field['autocomplete'] = $this->nullableString($field['autocomplete'] ?? null);
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
                'placeholder' => null,
                'autocomplete' => 'name',
                'options' => [],
            ],
            'email' => [
                'enabled' => true,
                'required' => true,
                'label' => 'Email',
                'type' => 'email',
                'placeholder' => null,
                'autocomplete' => 'email',
                'options' => [],
            ],
            'phone' => [
                'enabled' => true,
                'required' => false,
                'label' => 'Telefone',
                'type' => 'tel',
                'placeholder' => null,
                'autocomplete' => 'tel',
                'options' => [],
            ],
            'company' => [
                'enabled' => false,
                'required' => false,
                'label' => 'Empresa',
                'type' => 'text',
                'placeholder' => null,
                'autocomplete' => 'organization',
                'options' => [],
            ],
            'interest' => [
                'enabled' => false,
                'required' => false,
                'label' => 'Interesse',
                'type' => 'select',
                'placeholder' => 'Selecione uma opcao',
                'autocomplete' => null,
                'options' => [],
            ],
            'message' => [
                'enabled' => true,
                'required' => true,
                'label' => 'Mensagem',
                'type' => 'textarea',
                'placeholder' => null,
                'autocomplete' => null,
                'options' => [],
                'rows' => 5,
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

    protected function boolValue(mixed $value, bool $default): bool
    {
        if ($value === null) {
            return $default;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
        }

        return (bool) $value;
    }

    protected function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function stringValue(mixed $value, string $default): string
    {
        return $this->nullableString($value) ?? $default;
    }
}
