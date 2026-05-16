<?php

namespace Tests\Unit\LandingCore;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Template\LandingCore\Support\ModuleRegistry;

class ModuleRegistryTest extends TestCase
{
    public function test_it_registers_modules_with_defaults_and_filters_enabled_modules(): void
    {
        $registry = new ModuleRegistry([
            'landing-core' => [
                'label' => 'Landing Core',
                'description' => 'Core infrastructure',
            ],
            'faq' => [
                'enabled' => false,
            ],
            'contact' => 'Contact Form',
        ]);

        $this->assertTrue($registry->has('landing-core'));
        $this->assertSame('Landing Core', $registry->get('landing-core')['label']);
        $this->assertSame('Core infrastructure', $registry->get('landing-core')['description']);
        $this->assertSame('Contact Form', $registry->get('contact')['label']);
        $this->assertTrue($registry->isEnabled('landing-core'));
        $this->assertFalse($registry->isEnabled('faq'));
        $this->assertSame(['landing-core', 'contact'], array_keys($registry->enabled()));
    }

    public function test_it_can_forget_registered_modules(): void
    {
        $registry = new ModuleRegistry([
            'landing-core' => 'Landing Core',
        ]);

        $registry->forget('landing-core');

        $this->assertFalse($registry->has('landing-core'));
        $this->assertFalse($registry->isEnabled('landing-core'));
    }

    public function test_it_rejects_modules_without_a_name(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Landing modules must define a non-empty name.');

        (new ModuleRegistry)->register(['name' => '']);
    }
}
