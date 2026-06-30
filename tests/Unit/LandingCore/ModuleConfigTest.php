<?php

namespace Tests\Unit\LandingCore;

use PHPUnit\Framework\TestCase;
use Template\LandingCore\Config\ModuleConfig;

class StubModuleConfig extends ModuleConfig
{
    public static function key(): string
    {
        return 'stub';
    }

    public function flag(): bool
    {
        return $this->bool('flag', false);
    }

    public function nestedName(): string
    {
        return $this->string('nested.name', 'fallback');
    }

    public function maybe(): ?string
    {
        return $this->nullableString('maybe');
    }

    public function attempts(): int
    {
        return $this->int('attempts', 7);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function tags(): array
    {
        return $this->list('tags', ['default']);
    }
}

class ModuleConfigTest extends TestCase
{
    public function test_it_coerces_values_by_type_using_dot_paths(): void
    {
        $config = StubModuleConfig::fromArray([
            'flag' => 'true',
            'nested' => ['name' => '  Hello  '],
            'maybe' => '  value  ',
            'attempts' => '10',
            'tags' => ['a', 'b'],
        ]);

        $this->assertTrue($config->flag());
        $this->assertSame('Hello', $config->nestedName());
        $this->assertSame('value', $config->maybe());
        $this->assertSame(10, $config->attempts());
        $this->assertSame(['a', 'b'], $config->tags());
    }

    public function test_it_falls_back_to_defaults_for_missing_or_empty_values(): void
    {
        $config = StubModuleConfig::fromArray([
            'maybe' => '   ',
            'attempts' => 'not-a-number',
        ]);

        $this->assertFalse($config->flag());
        $this->assertSame('fallback', $config->nestedName());
        $this->assertNull($config->maybe());
        $this->assertSame(7, $config->attempts());
        $this->assertSame(['default'], $config->tags());
    }

    public function test_it_wraps_scalar_list_values_into_an_array(): void
    {
        $config = StubModuleConfig::fromArray(['tags' => 'single']);

        $this->assertSame(['single'], $config->tags());
    }
}
