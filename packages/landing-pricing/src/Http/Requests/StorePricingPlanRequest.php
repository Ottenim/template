<?php

namespace Template\LandingPricing\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Template\LandingPricing\Support\PricingUrl;

class StorePricingPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['nullable', 'string', 'max:80'],
            'currency' => ['nullable', 'string', 'max:20'],
            'billing_period_label' => ['nullable', 'string', 'max:40'],
            'features' => ['nullable', 'string', 'max:2000'],
            'cta_label' => ['nullable', 'string', 'max:80'],
            'cta_url' => [
                'nullable',
                'string',
                'max:2048',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! PricingUrl::isSafe($value)) {
                        $fail('O campo :attribute contem uma URL insegura.');
                    }
                },
            ],
            'note' => ['nullable', 'string', 'max:500'],
            'badge' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
