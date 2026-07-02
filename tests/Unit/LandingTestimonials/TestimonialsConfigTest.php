<?php

namespace Tests\Unit\LandingTestimonials;

use PHPUnit\Framework\TestCase;
use Template\LandingTestimonials\Config\TestimonialsConfig;

class TestimonialsConfigTest extends TestCase
{
    public function test_it_exposes_safe_defaults_when_config_is_empty(): void
    {
        $config = TestimonialsConfig::fromArray([]);

        $this->assertTrue($config->enabled());
        $this->assertSame('grid', $config->layout());
        $this->assertSame(3, $config->columns());
        $this->assertTrue($config->showAvatar());
        $this->assertFalse($config->showRating());
        $this->assertTrue($config->showCompany());
        $this->assertTrue($config->showLogo());
        $this->assertNull($config->limit());
        $this->assertTrue($config->sectionEnabled());
        $this->assertNull($config->sectionTitle());
        $this->assertTrue($config->databaseEnabled());
        $this->assertSame('lp_testimonials', $config->databaseTable());
        $this->assertSame([], $config->items());
        $this->assertFalse($config->adminEnabled());
        $this->assertSame('admin/testimonials', $config->adminPrefix());
        $this->assertSame(['web', 'auth'], $config->adminMiddleware());
        $this->assertSame(15, $config->adminPerPage());
    }

    public function test_it_reads_and_coerces_configured_values(): void
    {
        $config = TestimonialsConfig::fromArray([
            'enabled' => 'false',
            'layout' => 'carousel',
            'columns' => '2',
            'show_avatar' => '0',
            'show_rating' => 'true',
            'limit' => '4',
            'section' => [
                'enabled' => 'false',
                'title' => '  Depoimentos  ',
            ],
            'database' => [
                'enabled' => 'false',
                'table' => 'custom_testimonials',
            ],
            'admin' => [
                'enabled' => 'true',
                'prefix' => 'painel/depoimentos',
                'middleware' => ['web', '', 'auth'],
                'per_page' => '25',
            ],
        ]);

        $this->assertFalse($config->enabled());
        $this->assertSame('carousel', $config->layout());
        $this->assertSame(2, $config->columns());
        $this->assertFalse($config->showAvatar());
        $this->assertTrue($config->showRating());
        $this->assertSame(4, $config->limit());
        $this->assertFalse($config->sectionEnabled());
        $this->assertSame('Depoimentos', $config->sectionTitle());
        $this->assertFalse($config->databaseEnabled());
        $this->assertSame('custom_testimonials', $config->databaseTable());
        $this->assertTrue($config->adminEnabled());
        $this->assertSame('painel/depoimentos', $config->adminPrefix());
        $this->assertSame(['web', 'auth'], $config->adminMiddleware());
        $this->assertSame(25, $config->adminPerPage());
    }

    public function test_limit_ignores_non_positive_values(): void
    {
        $this->assertNull(TestimonialsConfig::fromArray(['limit' => null])->limit());
        $this->assertNull(TestimonialsConfig::fromArray(['limit' => 0])->limit());
        $this->assertNull(TestimonialsConfig::fromArray(['limit' => -1])->limit());
        $this->assertSame(10, TestimonialsConfig::fromArray(['limit' => '10'])->limit());
        $this->assertSame(6, TestimonialsConfig::fromArray(['limit' => 6])->limit());
    }
}
