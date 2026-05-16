<?php

namespace Tests\Unit\LandingCore;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Template\LandingCore\Support\MenuRegistry;

class MenuRegistryTest extends TestCase
{
    public function test_it_registers_menu_items_with_defaults(): void
    {
        $registry = new MenuRegistry([
            'dashboard' => 'Dashboard',
            'faq' => [
                'label' => 'FAQ',
                'enabled' => false,
                'route' => 'faq.index',
            ],
        ]);

        $this->assertSame('Dashboard', $registry->get('dashboard')['label']);
        $this->assertSame('Landing Page', $registry->get('dashboard')['group']);
        $this->assertSame('faq.index', $registry->get('faq')['route']);
        $this->assertSame(['dashboard'], array_keys($registry->enabled()));
    }

    public function test_it_rejects_menu_items_without_a_key(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Landing menu items must define a non-empty key.');

        (new MenuRegistry)->register(['key' => '']);
    }
}
