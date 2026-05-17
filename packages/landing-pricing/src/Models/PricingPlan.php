<?php

namespace Template\LandingPricing\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',
        'billing_period_label',
        'features',
        'cta_label',
        'cta_url',
        'note',
        'badge',
        'sort_order',
        'is_featured',
        'is_active',
    ];

    protected $attributes = [
        'features' => '[]',
        'sort_order' => 0,
        'is_featured' => false,
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getTable()
    {
        return config('landing-pricing.database.table', 'lp_pricing_plans');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
            ->orderBy('name');
    }
}
