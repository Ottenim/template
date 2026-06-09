<?php

namespace Template\LandingCookieConsent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Template\LandingCookieConsent\Support\CookieConsentManager;

class StoreConsentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'consent_id' => ['nullable', 'string', 'max:100'],
            'version' => ['nullable', 'string', 'max:80'],
            'action' => ['required', 'string', Rule::in(['accept_all', 'reject_optional', 'save_preferences'])],
            'categories' => ['required', 'array'],
            'categories.*' => ['boolean'],
            'policy_url' => ['nullable', 'string', 'max:2048'],
            'url' => ['nullable', 'url', 'max:2048'],
            'accepted_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:accepted_at'],
        ];
    }

    public function validatedConsentData(): array
    {
        $validated = $this->validated();
        $configuredCategories = app(CookieConsentManager::class)->categories();
        $categories = [];

        foreach ($configuredCategories as $name => $category) {
            $categories[$name] = (bool) ($category['required'] ?? false)
                || filter_var(data_get($validated, "categories.{$name}", false), FILTER_VALIDATE_BOOLEAN);
        }

        return [
            'consent_id' => $validated['consent_id'] ?? null,
            'version' => $validated['version'] ?? null,
            'action' => $validated['action'],
            'categories' => $categories,
            'policy_url' => $validated['policy_url'] ?? null,
            'page_url' => $validated['url'] ?? null,
            'accepted_at' => $validated['accepted_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
        ];
    }
}
