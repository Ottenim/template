<?php

namespace Template\LandingCore\Support;

use Illuminate\Support\Str;
use InvalidArgumentException;

class ModuleRegistry
{
    protected array $modules = [];

    public function __construct(array $modules = [])
    {
        foreach ($modules as $key => $module) {
            $this->register(is_array($module) ? ['name' => $key, ...$module] : [
                'name' => $key,
                'label' => (string) $module,
            ]);
        }
    }

    public function register(array $module): static
    {
        $name = $module['name'] ?? $module['key'] ?? null;

        if (! is_string($name) || $name === '') {
            throw new InvalidArgumentException('Landing modules must define a non-empty name.');
        }

        $this->modules[$name] = [
            'name' => $name,
            'label' => Str::headline($name),
            'enabled' => true,
            ...$module,
        ];

        return $this;
    }

    public function all(): array
    {
        return $this->modules;
    }

    public function enabled(): array
    {
        return array_filter($this->modules, fn (array $module) => $module['enabled'] ?? true);
    }

    public function get(string $name): ?array
    {
        return $this->modules[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return isset($this->modules[$name]);
    }

    public function isEnabled(string $name): bool
    {
        return (bool) ($this->get($name)['enabled'] ?? false);
    }

    public function forget(string $name): static
    {
        unset($this->modules[$name]);

        return $this;
    }
}
