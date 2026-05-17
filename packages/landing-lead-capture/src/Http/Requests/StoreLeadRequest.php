<?php

namespace Template\LandingLeadCapture\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Template\LandingLeadCapture\Support\LeadCaptureFields;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
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

        if ((bool) config('landing-lead-capture.privacy_consent.enabled', true)) {
            $rules['privacy_consent'] = (bool) config('landing-lead-capture.privacy_consent.required', true)
                ? ['accepted']
                : ['nullable'];
        }

        if ((bool) config('landing-lead-capture.anti_spam.honeypot', true)) {
            $rules[$this->honeypotField()] = ['prohibited'];
        }

        return $rules;
    }

    public function validatedLeadData(): array
    {
        $fields = app(LeadCaptureFields::class);
        $validated = $this->validated();
        $data = [];

        foreach ($fields->enabledNames() as $field) {
            $data[$field] = $validated[$field] ?? null;
        }

        $data['privacy_consent'] = $this->boolean('privacy_consent');
        $data['source'] = $this->nullableString($validated['source'] ?? config('landing-lead-capture.lead.source'));
        $data['campaign'] = $this->nullableString($validated['campaign'] ?? config('landing-lead-capture.lead.campaign'));
        $data['tag'] = $this->nullableString($validated['tag'] ?? config('landing-lead-capture.lead.tag'));
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

    protected function honeypotField(): string
    {
        $field = trim((string) config('landing-lead-capture.anti_spam.honeypot_field', 'website'));

        return $field === '' ? 'website' : $field;
    }

    protected function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
