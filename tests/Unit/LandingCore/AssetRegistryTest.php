<?php

namespace Tests\Unit\LandingCore;

use PHPUnit\Framework\TestCase;
use Template\LandingCore\Support\AssetRegistry;

class AssetRegistryTest extends TestCase
{
    public function test_it_normalizes_registered_styles_and_scripts(): void
    {
        $registry = new AssetRegistry([
            'styles' => [
                'core' => '/vendor/landing-core/core.css',
                'theme' => [
                    'path' => '/build/theme.css',
                    'attributes' => ['media' => 'screen'],
                ],
            ],
            'scripts' => [
                'app' => [
                    'url' => '/build/app.js',
                    'attributes' => ['defer' => true],
                ],
            ],
        ]);

        $this->assertSame('/vendor/landing-core/core.css', $registry->styles()['core']['url']);
        $this->assertSame([], $registry->styles()['core']['attributes']);
        $this->assertSame('/build/theme.css', $registry->styles()['theme']['url']);
        $this->assertSame(['media' => 'screen'], $registry->styles()['theme']['attributes']);
        $this->assertSame('/build/app.js', $registry->scripts()['app']['url']);
        $this->assertSame(['defer' => true], $registry->scripts()['app']['attributes']);
    }

    public function test_it_can_register_assets_after_construction(): void
    {
        $registry = new AssetRegistry;

        $registry
            ->registerStyle('landing', '/landing.css')
            ->registerScript('landing', '/landing.js');

        $this->assertSame('/landing.css', $registry->all()['styles']['landing']['url']);
        $this->assertSame('/landing.js', $registry->all()['scripts']['landing']['url']);
    }
}
