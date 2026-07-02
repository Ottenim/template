<?php

namespace Tests\Unit\LandingSeo;

use PHPUnit\Framework\TestCase;
use Template\LandingSeo\Config\SeoConfig;

class SeoConfigTest extends TestCase
{
    public function test_it_exposes_safe_defaults_when_config_is_empty(): void
    {
        $config = SeoConfig::fromArray([]);

        $this->assertTrue($config->enabled());
        $this->assertNull($config->defaultsTitle());
        $this->assertSame('%s', $config->defaultsTitleTemplate());
        $this->assertNull($config->defaultsDescription());
        $this->assertNull($config->defaultsCanonicalUrl());
        $this->assertNull($config->defaultsImage());
        $this->assertNull($config->defaultsFavicon());
        $this->assertSame('index,follow', $config->defaultsRobots());
        $this->assertNull($config->defaultsSiteName());
        $this->assertNull($config->defaultsLocale());
        $this->assertTrue($config->openGraphEnabled());
        $this->assertSame('website', $config->openGraphType());
        $this->assertTrue($config->twitterEnabled());
        $this->assertSame('summary_large_image', $config->twitterCard());
        $this->assertNull($config->twitterSite());
        $this->assertTrue($config->schemaEnabled());
        $this->assertSame('WebSite', $config->schemaType());
        $this->assertNull($config->schemaOrganizationName());
        $this->assertTrue($config->sitemapEnabled());
        $this->assertTrue($config->sitemapIncludeHome());
        $this->assertSame('weekly', $config->sitemapDefaultChangefreq());
        $this->assertSame(0.5, $config->sitemapDefaultPriority());
        $this->assertTrue($config->robotsTxtEnabled());
        $this->assertSame([], $config->robotsTxtRules());
        $this->assertTrue($config->robotsTxtIncludeSitemap());
        $this->assertTrue($config->databaseEnabled());
        $this->assertSame('lp_seo_pages', $config->databaseTable());
        $this->assertSame([], $config->pages());
        $this->assertFalse($config->adminEnabled());
        $this->assertSame('admin/seo', $config->adminPrefix());
        $this->assertSame(['web', 'auth'], $config->adminMiddleware());
        $this->assertSame(15, $config->adminPerPage());
    }

    public function test_it_reads_and_coerces_configured_values(): void
    {
        $config = SeoConfig::fromArray([
            'enabled' => 'false',
            'defaults' => [
                'title' => ' Landing ',
                'title_template' => '%s | Site',
                'description' => ' Descricao ',
                'canonical_url' => ' /home ',
                'image' => ' /share.jpg ',
                'favicon' => ' /favicon.ico ',
                'robots' => ' noindex,nofollow ',
                'site_name' => ' Site ',
                'locale' => ' pt-BR ',
            ],
            'open_graph' => [
                'enabled' => '0',
                'type' => 'article',
            ],
            'twitter' => [
                'enabled' => 'false',
                'card' => 'summary',
                'site' => ' @site ',
            ],
            'schema' => [
                'enabled' => '0',
                'type' => 'Article',
                'organization' => [
                    'name' => ' Org ',
                    'url' => ' https://example.test ',
                    'logo' => ' /logo.png ',
                ],
            ],
            'sitemap' => [
                'enabled' => 'false',
                'include_home' => '0',
                'default_changefreq' => 'daily',
                'default_priority' => '0.8',
            ],
            'robots_txt' => [
                'enabled' => '0',
                'rules' => [
                    ['user_agent' => '*', 'disallow' => ['/admin']],
                ],
                'include_sitemap' => 'false',
            ],
            'database' => [
                'enabled' => 'false',
                'table' => 'seo_pages',
            ],
            'pages' => [
                '/' => ['title' => 'Home'],
            ],
            'admin' => [
                'enabled' => 'true',
                'prefix' => 'painel/seo',
                'middleware' => ['web', '', 'auth', '  '],
                'per_page' => '30',
            ],
        ]);

        $this->assertFalse($config->enabled());
        $this->assertSame('Landing', $config->defaultsTitle());
        $this->assertSame('%s | Site', $config->defaultsTitleTemplate());
        $this->assertSame('Descricao', $config->defaultsDescription());
        $this->assertSame('/home', $config->defaultsCanonicalUrl());
        $this->assertSame('/share.jpg', $config->defaultsImage());
        $this->assertSame('/favicon.ico', $config->defaultsFavicon());
        $this->assertSame('noindex,nofollow', $config->defaultsRobots());
        $this->assertSame('Site', $config->defaultsSiteName());
        $this->assertSame('pt-BR', $config->defaultsLocale());
        $this->assertFalse($config->openGraphEnabled());
        $this->assertSame('article', $config->openGraphType());
        $this->assertFalse($config->twitterEnabled());
        $this->assertSame('summary', $config->twitterCard());
        $this->assertSame('@site', $config->twitterSite());
        $this->assertFalse($config->schemaEnabled());
        $this->assertSame('Article', $config->schemaType());
        $this->assertSame('Org', $config->schemaOrganizationName());
        $this->assertSame('https://example.test', $config->schemaOrganizationUrl());
        $this->assertSame('/logo.png', $config->schemaOrganizationLogo());
        $this->assertFalse($config->sitemapEnabled());
        $this->assertFalse($config->sitemapIncludeHome());
        $this->assertSame('daily', $config->sitemapDefaultChangefreq());
        $this->assertSame(0.8, $config->sitemapDefaultPriority());
        $this->assertFalse($config->robotsTxtEnabled());
        $this->assertSame([['user_agent' => '*', 'disallow' => ['/admin']]], $config->robotsTxtRules());
        $this->assertFalse($config->robotsTxtIncludeSitemap());
        $this->assertFalse($config->databaseEnabled());
        $this->assertSame('seo_pages', $config->databaseTable());
        $this->assertSame(['/' => ['title' => 'Home']], $config->pages());
        $this->assertTrue($config->adminEnabled());
        $this->assertSame('painel/seo', $config->adminPrefix());
        $this->assertSame(['web', 'auth'], $config->adminMiddleware());
        $this->assertSame(30, $config->adminPerPage());
    }
}
