<?php

namespace Template\LandingFaq\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FaqItem extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'category',
        'sort_order',
        'is_active',
    ];

    protected $attributes = [
        'sort_order' => 0,
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getTable()
    {
        return config('landing-faq.database.table', 'lp_faq_items');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
            ->orderBy('question');
    }
}
