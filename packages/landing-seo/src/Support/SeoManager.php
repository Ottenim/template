<?php

namespace Template\LandingSeo\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Template\LandingCore\Support\Coerce;
use Template\LandingSeo\Config\SeoConfig;
use Template\LandingSeo\Models\SeoPage;
use Throwable;

class SeoManager
{
    protected array $schemas = [];

    public function resolve(mixed $page = null, array $overrides = []): array
    {
        $data = [
            ...$this->defaults(),
            ...$this->contextPage($page),
            ...$this->normalizeInput($overrides),
        ];

        return $this->normalizeData($data);
    }

    public function registerSchema(array|string $schema): static
    {
        foreach ($this->schemaList($schema) as $entry) {
            $this->schemas[] = $entry;
        }

        return $this;
    }

    public function schemaJson(array $data): ?string
    {
        $config = SeoConfig::fromConfig();

        if (! Coerce::bool($data['schema_enabled'] ?? $config->schemaEnabled(), $config->schemaEnabled())) {
            return null;
        }

        $schemas = collect([
            $this->basicSchema($data),
            ...$this->schemaList($data['schema'] ?? null),
            ...$this->schemas,
        ])->filter()->values();

        if ($schemas->isEmpty()) {
            return null;
        }

        $payload = $schemas->count() === 1
            ? $schemas->first()
            : [
                '@context' => 'https://schema.org',
                '@graph' => $schemas->map(function (array $schema) {
                    unset($schema['@context']);

                    return $schema;
                })->all(),
            ];

        return json_encode(
            $payload,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        ) ?: null;
    }

    public function sitemapEntries(): Collection
    {
        $config = SeoConfig::fromConfig();
        $entries = collect();

        if ($config->sitemapIncludeHome()) {
            $entries->push($this->sitemapEntry([
                'path' => '/',
                'title' => $config->defaultsTitle(),
            ]));
        }

        $entries = $entries
            ->merge($this->configuredSitemapEntries())
            ->merge($this->databaseSitemapEntries())
            ->filter(fn (?array $entry) => $entry !== null)
            ->unique('loc')
            ->values();

        return $entries;
    }

    public function robotsText(): string
    {
        $config = SeoConfig::fromConfig();
        $lines = [];
        $rules = $config->robotsTxtRules();

        if ($rules === []) {
            $rules = [['user_agent' => '*', 'disallow' => [], 'allow' => []]];
        }

        foreach ($rules as $rule) {
            $lines[] = 'User-agent: '.(Coerce::nullableString(data_get($rule, 'user_agent')) ?? '*');

            foreach ((array) data_get($rule, 'allow', []) as $allow) {
                $lines[] = 'Allow: '.SeoUrl::normalizePath($allow);
            }

            $disallow = (array) data_get($rule, 'disallow', []);

            if ($disallow === []) {
                $lines[] = 'Disallow:';
            }

            foreach ($disallow as $path) {
                $lines[] = 'Disallow: '.SeoUrl::normalizePath($path);
            }

            $lines[] = '';
        }

        if ($config->robotsTxtIncludeSitemap() && $config->sitemapEnabled()) {
            $lines[] = 'Sitemap: '.rtrim((string) config('app.url'), '/').'/sitemap.xml';
        }

        return trim(implode(PHP_EOL, $lines)).PHP_EOL;
    }

    protected function contextPage(mixed $page): array
    {
        if ($page instanceof SeoPage) {
            return $this->fromSeoPage($page);
        }

        if ($page instanceof Arrayable) {
            return $this->normalizeInput($page->toArray());
        }

        if (is_array($page)) {
            return $this->normalizeInput($page);
        }

        $path = is_string($page) ? SeoUrl::normalizePath($page) : $this->currentPath();
        $routeName = is_string($page) ? null : $this->currentRouteName();

        return [
            'path' => $path,
            ...$this->configuredPage($path, $routeName),
            ...$this->databasePage($path, $routeName),
        ];
    }

    protected function configuredPage(?string $path, ?string $routeName): array
    {
        foreach (SeoConfig::fromConfig()->pages() as $key => $page) {
            if (! is_array($page)) {
                continue;
            }

            $keyPath = is_string($key) && str_starts_with($key, '/') ? $key : null;
            $keyRoute = is_string($key) && ! str_starts_with($key, '/') ? $key : null;
            $pagePath = SeoUrl::normalizePath(data_get($page, 'path', $keyPath));
            $pageRoute = Coerce::nullableString(data_get($page, 'route_name', $keyRoute));

            if (($path && $pagePath === $path) || ($routeName && ($pageRoute === $routeName || $key === $routeName))) {
                return $this->normalizeInput([
                    'page_key' => is_string($key) ? $key : ($pagePath ?? $routeName),
                    ...$page,
                    'path' => $pagePath,
                    'route_name' => $pageRoute,
                ]);
            }
        }

        return [];
    }

    protected function databasePage(?string $path, ?string $routeName): array
    {
        $config = SeoConfig::fromConfig();

        if (! $config->databaseEnabled()) {
            return [];
        }

        $table = $config->databaseTable();

        try {
            if (! Schema::hasTable($table)) {
                return [];
            }

            $page = SeoPage::query()
                ->active()
                ->where(function ($query) use ($path, $routeName) {
                    if ($path) {
                        $query->where('path', $path);
                    }

                    if ($routeName) {
                        $query->orWhere('route_name', $routeName);
                    }
                })
                ->first();

            return $page ? $this->fromSeoPage($page) : [];
        } catch (Throwable) {
            return [];
        }
    }

    protected function fromSeoPage(SeoPage $page): array
    {
        return $this->normalizeInput($page->toArray());
    }

    protected function defaults(): array
    {
        $config = SeoConfig::fromConfig();

        return [
            'title' => $config->defaultsTitle(),
            'title_template' => $config->defaultsTitleTemplate(),
            'description' => $config->defaultsDescription(),
            'canonical_url' => $config->defaultsCanonicalUrl(),
            'image_url' => $config->defaultsImage(),
            'favicon_url' => $config->defaultsFavicon(),
            'robots' => $config->defaultsRobots(),
            'site_name' => $config->defaultsSiteName(),
            'locale' => $config->defaultsLocale(),
            'og_type' => $config->openGraphType(),
            'twitter_card' => $config->twitterCard(),
            'twitter_site' => $config->twitterSite(),
            'open_graph_enabled' => $config->openGraphEnabled(),
            'twitter_enabled' => $config->twitterEnabled(),
            'schema_enabled' => $config->schemaEnabled(),
            'schema_type' => $config->schemaType(),
            'path' => $this->currentPath(),
            'route_name' => $this->currentRouteName(),
        ];
    }

    protected function normalizeInput(array $data): array
    {
        if (array_key_exists('canonical', $data) && ! array_key_exists('canonical_url', $data)) {
            $data['canonical_url'] = $data['canonical'];
        }

        if (array_key_exists('image', $data) && ! array_key_exists('image_url', $data)) {
            $data['image_url'] = $data['image'];
        }

        if (array_key_exists('favicon', $data) && ! array_key_exists('favicon_url', $data)) {
            $data['favicon_url'] = $data['favicon'];
        }

        return $data;
    }

    protected function normalizeData(array $data): array
    {
        $path = SeoUrl::normalizePath(data_get($data, 'path')) ?? $this->currentPath();
        $title = $this->formatTitle(
            Coerce::nullableString(data_get($data, 'title')),
            Coerce::nullableString(data_get($data, 'title_template')) ?? '%s',
        );
        $description = Coerce::nullableString(data_get($data, 'description'));
        $canonical = SeoUrl::normalize(data_get($data, 'canonical_url'), true)
            ?: SeoUrl::normalize($path, true)
            ?: $this->currentUrl();
        $image = SeoUrl::normalize(data_get($data, 'image_url'), true);
        $favicon = SeoUrl::normalize(data_get($data, 'favicon_url'), true);
        $ogTitle = Coerce::nullableString(data_get($data, 'og_title')) ?? $title;
        $ogDescription = Coerce::nullableString(data_get($data, 'og_description')) ?? $description;
        $ogImage = SeoUrl::normalize(data_get($data, 'og_image'), true) ?? $image;
        $twitterTitle = Coerce::nullableString(data_get($data, 'twitter_title')) ?? $ogTitle;
        $twitterDescription = Coerce::nullableString(data_get($data, 'twitter_description')) ?? $ogDescription;
        $twitterImage = SeoUrl::normalize(data_get($data, 'twitter_image'), true) ?? $ogImage;

        return [
            'page_key' => Coerce::nullableString(data_get($data, 'page_key')),
            'path' => $path,
            'route_name' => Coerce::nullableString(data_get($data, 'route_name')),
            'title' => $title,
            'description' => $description,
            'canonical_url' => $canonical,
            'image_url' => $image,
            'favicon_url' => $favicon,
            'robots' => Coerce::nullableString(data_get($data, 'robots')) ?? 'index,follow',
            'site_name' => Coerce::nullableString(data_get($data, 'site_name')),
            'locale' => Coerce::nullableString(data_get($data, 'locale')),
            'og_title' => $ogTitle,
            'og_description' => $ogDescription,
            'og_image' => $ogImage,
            'og_type' => Coerce::nullableString(data_get($data, 'og_type')) ?? 'website',
            'twitter_title' => $twitterTitle,
            'twitter_description' => $twitterDescription,
            'twitter_image' => $twitterImage,
            'twitter_card' => Coerce::nullableString(data_get($data, 'twitter_card')) ?? 'summary_large_image',
            'twitter_site' => Coerce::nullableString(data_get($data, 'twitter_site')),
            'schema' => data_get($data, 'schema'),
            'schema_type' => Coerce::nullableString(data_get($data, 'schema_type')) ?? 'WebSite',
            'open_graph_enabled' => Coerce::bool(data_get($data, 'open_graph_enabled'), true),
            'twitter_enabled' => Coerce::bool(data_get($data, 'twitter_enabled'), true),
            'schema_enabled' => Coerce::bool(data_get($data, 'schema_enabled'), true),
        ];
    }

    protected function basicSchema(array $data): ?array
    {
        $type = Coerce::nullableString($data['schema_type'] ?? null);

        if ($type === null) {
            return null;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'name' => $data['title'],
            'url' => $data['canonical_url'],
        ];

        if ($data['description']) {
            $schema['description'] = $data['description'];
        }

        if ($data['image_url']) {
            $schema['image'] = $data['image_url'];
        }

        $organization = $this->organizationSchema();

        if ($organization !== null) {
            $schema['publisher'] = $organization;
        }

        return $schema;
    }

    protected function organizationSchema(): ?array
    {
        $config = SeoConfig::fromConfig();
        $name = $config->schemaOrganizationName();

        if ($name === null) {
            return null;
        }

        $schema = [
            '@type' => 'Organization',
            'name' => $name,
        ];

        if ($url = SeoUrl::normalize($config->schemaOrganizationUrl(), true)) {
            $schema['url'] = $url;
        }

        if ($logo = SeoUrl::normalize($config->schemaOrganizationLogo(), true)) {
            $schema['logo'] = $logo;
        }

        return $schema;
    }

    protected function schemaList(mixed $schema): array
    {
        if ($schema === null || $schema === '') {
            return [];
        }

        if (is_string($schema)) {
            $schema = json_decode($schema, true);
        }

        if (! is_array($schema)) {
            return [];
        }

        if (array_is_list($schema)) {
            return collect($schema)
                ->filter(fn (mixed $entry) => is_array($entry))
                ->values()
                ->all();
        }

        return [$schema];
    }

    protected function configuredSitemapEntries(): Collection
    {
        return collect(SeoConfig::fromConfig()->pages())
            ->map(function (mixed $page, string|int $key) {
                if (! is_array($page)) {
                    return null;
                }

                if (($page['sitemap_enabled'] ?? true) === false) {
                    return null;
                }

                $keyPath = is_string($key) && str_starts_with($key, '/') ? $key : null;

                return $this->sitemapEntry([
                    'page_key' => is_string($key) ? $key : null,
                    ...$page,
                    'path' => data_get($page, 'path', $keyPath),
                ]);
            });
    }

    protected function databaseSitemapEntries(): Collection
    {
        $config = SeoConfig::fromConfig();

        if (! $config->databaseEnabled()) {
            return collect();
        }

        $table = $config->databaseTable();

        try {
            if (! Schema::hasTable($table)) {
                return collect();
            }

            return SeoPage::query()
                ->forSitemap()
                ->ordered()
                ->get()
                ->map(fn (SeoPage $page) => $this->sitemapEntry($page->toArray()));
        } catch (Throwable) {
            return collect();
        }
    }

    protected function sitemapEntry(array $page): ?array
    {
        $config = SeoConfig::fromConfig();
        $url = SeoUrl::normalize($page['canonical_url'] ?? null, true)
            ?? SeoUrl::normalize(SeoUrl::normalizePath($page['path'] ?? null), true);

        if ($url === null) {
            return null;
        }

        return [
            'loc' => $url,
            'lastmod' => $page['updated_at'] ?? null,
            'changefreq' => Coerce::nullableString($page['sitemap_changefreq'] ?? null)
                ?? $config->sitemapDefaultChangefreq(),
            'priority' => $page['sitemap_priority'] ?? $config->sitemapDefaultPriority(),
        ];
    }

    protected function formatTitle(?string $title, string $template): ?string
    {
        if ($title === null) {
            return null;
        }

        return str_contains($template, '%s') ? sprintf($template, $title) : $title;
    }

    protected function currentPath(): string
    {
        return SeoUrl::normalizePath(request()->getPathInfo()) ?? '/';
    }

    protected function currentRouteName(): ?string
    {
        return request()->route()?->getName();
    }

    protected function currentUrl(): string
    {
        return request()->url() ?: rtrim((string) config('app.url'), '/').$this->currentPath();
    }
}
