<?php

namespace Template\LandingSeo\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Template\LandingSeo\Config\SeoConfig;

class SeoPage extends Model
{
    protected $fillable = [
        'page_key',
        'path',
        'route_name',
        'title',
        'description',
        'canonical_url',
        'image_url',
        'favicon_url',
        'robots',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'twitter_card',
        'schema',
        'sitemap_enabled',
        'sitemap_changefreq',
        'sitemap_priority',
        'is_active',
    ];

    protected $attributes = [
        'sitemap_enabled' => true,
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'schema' => 'array',
            'sitemap_enabled' => 'boolean',
            'sitemap_priority' => 'float',
            'is_active' => 'boolean',
        ];
    }

    public function getTable()
    {
        return SeoConfig::fromConfig()->databaseTable();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForSitemap(Builder $query): Builder
    {
        return $query->active()->where('sitemap_enabled', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('path')
            ->orderBy('page_key');
    }
}
