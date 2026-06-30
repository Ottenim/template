<?php

namespace Template\LandingContact\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Template\LandingContact\Config\ContactConfig;
use Template\LandingContact\Support\ContactFields;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $config = app(ContactConfig::class);
        $fields = app(ContactFields::class);
        $rules = [
            'source_page' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'url', 'max:2048'],
        ];

        foreach (ContactFields::SUPPORTED_FIELDS as $field) {
            if (! $fields->isEnabled($field)) {
                $rules[$field] = ['prohibited'];

                continue;
            }

            $rules[$field] = $this->rulesForField($field, $fields->get($field));
        }

        if ($config->privacyConsentEnabled()) {
            $rules['privacy_consent'] = $config->privacyConsentRequired()
                ? ['accepted']
                : ['nullable'];
        }

        if ($config->honeypotEnabled()) {
            $rules[$config->honeypotField()] = ['prohibited'];
        }

        return $rules;
    }

    public function validatedContactData(): array
    {
        $fields = app(ContactFields::class);
        $validated = $this->validated();
        $data = [];

        foreach ($fields->enabledNames() as $field) {
            $data[$field] = $validated[$field] ?? null;
        }

        $data['privacy_consent'] = $this->boolean('privacy_consent');
        $data['source_page'] = $validated['source_page'] ?? null;
        $data['source_url'] = $validated['source_url'] ?? null;

        return $data;
    }

    protected function rulesForField(string $name, array $field): array
    {
        $rules = [$field['required'] ? 'required' : 'nullable'];

        return match ($name) {
            'name', 'company', 'interest' => [
                ...$rules,
                'string',
                'max:255',
                ...$this->interestRules($name, $field),
            ],
            'email' => [
                ...$rules,
                'email',
                'max:255',
            ],
            'phone' => [
                ...$rules,
                'string',
                'max:50',
            ],
            'message' => [
                ...$rules,
                'string',
                'max:5000',
            ],
            default => [
                ...$rules,
                'string',
                'max:255',
            ],
        };
    }

    protected function interestRules(string $name, array $field): array
    {
        if ($name !== 'interest' || $field['options'] === []) {
            return [];
        }

        return [Rule::in(array_column($field['options'], 'value'))];
    }
}
