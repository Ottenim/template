<?php

namespace Template\LandingCore\Support;

use Illuminate\Support\Str;
use InvalidArgumentException;

class MenuRegistry
{
    protected array $items = [];

    public function __construct(array $items = [])
    {
        foreach ($items as $key => $item) {
            $this->register(is_array($item) ? ['key' => $key, ...$item] : [
                'key' => $key,
                'label' => (string) $item,
            ]);
        }
    }

    public function register(array $item): static
    {
        $key = $item['key'] ?? $item['name'] ?? null;

        if (! is_string($key) || $key === '') {
            throw new InvalidArgumentException('Landing menu items must define a non-empty key.');
        }

        $this->items[$key] = [
            'key' => $key,
            'label' => Str::headline($key),
            'enabled' => true,
            'group' => 'Landing Page',
            ...$item,
        ];

        return $this;
    }

    public function all(): array
    {
        return $this->items;
    }

    public function enabled(): array
    {
        return array_filter($this->items, fn (array $item) => $item['enabled'] ?? true);
    }

    public function get(string $key): ?array
    {
        return $this->items[$key] ?? null;
    }
}
