<?php

namespace Template\LandingTestimonials\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Template\LandingTestimonials\Config\TestimonialsConfig;

class Testimonial extends Model
{
    protected $fillable = [
        'name',
        'text',
        'role',
        'company',
        'avatar',
        'logo',
        'rating',
        'sort_order',
        'is_featured',
        'is_active',
    ];

    protected $attributes = [
        'sort_order' => 0,
        'is_featured' => false,
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getTable()
    {
        return TestimonialsConfig::fromConfig()->databaseTable();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('name');
    }
}
