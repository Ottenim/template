<?php

namespace Tests\Unit\LandingFaq;

use PHPUnit\Framework\TestCase;
use Template\LandingFaq\Config\FaqConfig;

class FaqConfigTest extends TestCase
{
    public function test_it_exposes_safe_defaults_when_config_is_empty(): void
    {
        $config = FaqConfig::fromArray([]);

        $this->assertTrue($config->enabled());
        $this->assertSame('accordion', $config->layout());
        $this->assertFalse($config->showCategories());
        $this->assertTrue($config->defaultOpenFirstItem());
        $this->assertNull($config->limit());
        $this->assertTrue($config->sectionEnabled());
        $this->assertNull($config->sectionEyebrow());
        $this->assertTrue($config->databaseEnabled());
        $this->assertSame('lp_faq_items', $config->databaseTable());
        $this->assertSame([], $config->items());
        $this->assertTrue($config->schemaEnabled());
        $this->assertFalse($config->adminEnabled());
        $this->assertSame('admin/faq', $config->adminPrefix());
        $this->assertSame(['web', 'auth'], $config->adminMiddleware());
        $this->assertSame(15, $config->adminPerPage());
    }

    public function test_it_reads_and_coerces_configured_values(): void
    {
        $config = FaqConfig::fromArray([
            'enabled' => 'false',
            'layout' => 'grid',
            'show_categories' => '1',
            'default_open_first_item' => 0,
            'limit' => '5',
            'section' => [
                'enabled' => 'false',
                'title' => '  Perguntas  ',
            ],
            'database' => [
                'enabled' => 'false',
                'table' => 'custom_faq',
            ],
            'admin' => [
                'enabled' => 'true',
                'prefix' => 'painel/faq',
                'middleware' => ['web', '', 'auth', '  '],
                'per_page' => '30',
            ],
            'schema' => [
                'enabled' => 'false',
            ],
        ]);

        $this->assertFalse($config->enabled());
        $this->assertSame('grid', $config->layout());
        $this->assertTrue($config->showCategories());
        $this->assertFalse($config->defaultOpenFirstItem());
        $this->assertSame(5, $config->limit());
        $this->assertFalse($config->sectionEnabled());
        $this->assertSame('Perguntas', $config->sectionTitle());
        $this->assertFalse($config->databaseEnabled());
        $this->assertSame('custom_faq', $config->databaseTable());
        $this->assertTrue($config->adminEnabled());
        $this->assertSame('painel/faq', $config->adminPrefix());
        $this->assertSame(['web', 'auth'], $config->adminMiddleware());
        $this->assertSame(30, $config->adminPerPage());
        $this->assertFalse($config->schemaEnabled());
    }

    public function test_limit_ignores_non_positive_values(): void
    {
        $this->assertNull(FaqConfig::fromArray(['limit' => 0])->limit());
        $this->assertNull(FaqConfig::fromArray(['limit' => -3])->limit());
        $this->assertSame(8, FaqConfig::fromArray(['limit' => '8'])->limit());
    }
}
