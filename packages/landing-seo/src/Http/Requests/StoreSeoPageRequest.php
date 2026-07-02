<?php

namespace Template\LandingSeo\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Template\LandingSeo\Config\SeoConfig;
use Template\LandingSeo\Support\SeoUrl;

class StoreSeoPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $table = SeoConfig::fromConfig()->databaseTable();

        return [
            'page_key' => ['required', 'string', 'max:120', Rule::unique($table, 'page_key')],
            'path' => ['nullable', 'string', 'max:2048'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => $this->urlRules(),
            'image_url' => $this->urlRules(),
            'favicon_url' => $this->urlRules(),
            'robots' => ['nullable', 'string', 'max:80'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => $this->urlRules(),
            'og_type' => ['nullable', 'string', 'max:80'],
            'twitter_title' => ['nullable', 'string', 'max:255'],
            'twitter_description' => ['nullable', 'string', 'max:500'],
            'twitter_image' => $this->urlRules(),
            'twitter_card' => ['nullable', 'string', 'max:80'],
            'schema' => [
                'nullable',
                'string',
                'max:10000',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ($value !== null && $value !== '' && json_decode((string) $value, true) === null && json_last_error() !== JSON_ERROR_NONE) {
                        $fail('O campo :attribute deve conter um JSON valido.');
                    }
                },
            ],
            'sitemap_enabled' => ['nullable', 'boolean'],
            'sitemap_changefreq' => ['nullable', Rule::in(['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'])],
            'sitemap_priority' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function urlRules(): array
    {
        return [
            'nullable',
            'string',
            'max:2048',
            function (string $attribute, mixed $value, Closure $fail): void {
                if (! SeoUrl::isSafe($value)) {
                    $fail('O campo :attribute contem uma URL insegura.');
                }
            },
        ];
    }
}
