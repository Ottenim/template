<?php

namespace Template\LandingSeo\Config;

use Illuminate\Support\Arr;
use Template\LandingCore\Config\ModuleConfig;

/**
 * Config tipada do módulo de SEO. Centraliza as chaves config('landing-seo.*')
 * antes espalhadas no manager, componente Meta, rotas, provider, model,
 * requests, controller de admin e Blades.
 *
 * Validação e normalização de URL continuam em SeoUrl.
 */
class SeoConfig extends ModuleConfig
{
    public static function key(): string
    {
        return 'landing-seo';
    }

    public function enabled(): bool
    {
        return $this->bool('enabled', true);
    }

    public function defaultsTitle(): ?string
    {
        return $this->nullableString('defaults.title');
    }

    public function defaultsTitleTemplate(): string
    {
        return $this->string('defaults.title_template', '%s');
    }

    public function defaultsDescription(): ?string
    {
        return $this->nullableString('defaults.description');
    }

    public function defaultsCanonicalUrl(): ?string
    {
        return $this->nullableString('defaults.canonical_url');
    }

    public function defaultsImage(): ?string
    {
        return $this->nullableString('defaults.image');
    }

    public function defaultsFavicon(): ?string
    {
        return $this->nullableString('defaults.favicon');
    }

    public function defaultsRobots(): string
    {
        return $this->string('defaults.robots', 'index,follow');
    }

    public function defaultsSiteName(): ?string
    {
        return $this->nullableString('defaults.site_name');
    }

    public function defaultsLocale(): ?string
    {
        return $this->nullableString('defaults.locale');
    }

    public function openGraphEnabled(): bool
    {
        return $this->bool('open_graph.enabled', true);
    }

    public function openGraphType(): string
    {
        return $this->string('open_graph.type', 'website');
    }

    public function twitterEnabled(): bool
    {
        return $this->bool('twitter.enabled', true);
    }

    public function twitterCard(): string
    {
        return $this->string('twitter.card', 'summary_large_image');
    }

    public function twitterSite(): ?string
    {
        return $this->nullableString('twitter.site');
    }

    public function schemaEnabled(): bool
    {
        return $this->bool('schema.enabled', true);
    }

    public function schemaType(): string
    {
        return $this->string('schema.type', 'WebSite');
    }

    public function schemaOrganizationName(): ?string
    {
        return $this->nullableString('schema.organization.name');
    }

    public function schemaOrganizationUrl(): ?string
    {
        return $this->nullableString('schema.organization.url');
    }

    public function schemaOrganizationLogo(): ?string
    {
        return $this->nullableString('schema.organization.logo');
    }

    public function sitemapEnabled(): bool
    {
        return $this->bool('sitemap.enabled', true);
    }

    public function sitemapIncludeHome(): bool
    {
        return $this->bool('sitemap.include_home', true);
    }

    public function sitemapDefaultChangefreq(): string
    {
        return $this->string('sitemap.default_changefreq', 'weekly');
    }

    public function sitemapDefaultPriority(): float
    {
        $value = Arr::get($this->data, 'sitemap.default_priority');

        return is_numeric($value) ? (float) $value : 0.5;
    }

    public function robotsTxtEnabled(): bool
    {
        return $this->bool('robots_txt.enabled', true);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function robotsTxtRules(): array
    {
        return $this->list('robots_txt.rules', []);
    }

    public function robotsTxtIncludeSitemap(): bool
    {
        return $this->bool('robots_txt.include_sitemap', true);
    }

    public function databaseEnabled(): bool
    {
        return $this->bool('database.enabled', true);
    }

    public function databaseTable(): string
    {
        return $this->string('database.table', 'lp_seo_pages');
    }

    /**
     * @return array<int|string, mixed>
     */
    public function pages(): array
    {
        return $this->list('pages', []);
    }

    public function adminEnabled(): bool
    {
        return $this->bool('admin.enabled', false);
    }

    public function adminPrefix(): string
    {
        return $this->string('admin.prefix', 'admin/seo');
    }

    /**
     * @return array<int, string>
     */
    public function adminMiddleware(): array
    {
        return array_values(array_filter(
            array_map(
                fn (mixed $middleware): string => trim((string) $middleware),
                $this->list('admin.middleware', ['web', 'auth']),
            ),
            fn (string $middleware): bool => $middleware !== '',
        ));
    }

    public function adminPerPage(): int
    {
        return $this->int('admin.per_page', 15);
    }
}
