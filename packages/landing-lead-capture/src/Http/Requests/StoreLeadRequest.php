<?php

namespace Template\LandingLeadCapture\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Template\LandingCore\Support\Coerce;
use Template\LandingLeadCapture\Config\LeadCaptureConfig;
use Template\LandingLeadCapture\Support\LeadCaptureFields;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $config = app(LeadCaptureConfig::class);
        $fields = app(LeadCaptureFields::class);
        $rules = [
            'source_page' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'url', 'max:2048'],
            'source' => ['nullable', 'string', 'max:255'],
            'campaign' => ['nullable', 'string', 'max:255'],
            'tag' => ['nullable', 'string', 'max:255'],
        ];

        foreach (LeadCaptureFields::SUPPORTED_FIELDS as $field) {
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

    public function validatedLeadData(): array
    {
        $config = app(LeadCaptureConfig::class);
        $fields = app(LeadCaptureFields::class);
        $validated = $this->validated();
        $data = [];

        foreach ($fields->enabledNames() as $field) {
            $data[$field] = $validated[$field] ?? null;
        }

        $data['privacy_consent'] = $this->boolean('privacy_consent');
        $data['source'] = Coerce::nullableString($validated['source'] ?? $config->leadSource());
        $data['campaign'] = Coerce::nullableString($validated['campaign'] ?? $config->leadCampaign());
        $data['tag'] = Coerce::nullableString($validated['tag'] ?? $config->leadTag());
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
