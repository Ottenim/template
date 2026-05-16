<?php

namespace Template\LandingCore\Support;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use InvalidArgumentException;

class SectionRenderer
{
    protected array $sections = [];

    public function __construct(
        protected ModuleRegistry $modules,
        array $sections = [],
    ) {
        foreach ($sections as $key => $section) {
            $this->register($key, $section);
        }
    }

    public function register(string $name, string|array $section): static
    {
        $this->sections[$name] = $this->normalizeSection($section);

        return $this;
    }

    public function all(): array
    {
        return $this->sections;
    }

    public function render(string $name, array $data = []): HtmlString
    {
        $section = $this->sections[$name] ?? $this->sectionFromModule($name);

        if (! $section || ! ($section['enabled'] ?? true)) {
            return new HtmlString('');
        }

        $payload = array_replace($section['data'] ?? [], $data);

        if (isset($section['view'])) {
            return new HtmlString(view($section['view'], $payload)->render());
        }

        if (isset($section['component'])) {
            return new HtmlString($this->renderComponent($section['component'], $payload));
        }

        throw new InvalidArgumentException("Landing section [{$name}] must define a view or component.");
    }

    protected function sectionFromModule(string $name): ?array
    {
        $module = $this->modules->get($name);

        if (! $module || ! ($module['enabled'] ?? true)) {
            return null;
        }

        $section = $module['section'] ?? null;

        if ($section === null) {
            return null;
        }

        return $this->normalizeSection($section);
    }

    protected function normalizeSection(string|array $section): array
    {
        if (is_string($section)) {
            return [
                'view' => $section,
                'enabled' => true,
                'data' => [],
            ];
        }

        return [
            'enabled' => true,
            'data' => [],
            ...$section,
        ];
    }

    protected function renderComponent(string $component, array $data): string
    {
        $bindings = collect($data)
            ->keys()
            ->filter(fn (mixed $key) => is_string($key) && preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key))
            ->map(fn (string $key) => ':'.Str::kebab($key).'="$'.$key.'"')
            ->implode(' ');

        return Blade::render(
            trim('<x-dynamic-component :component="$component" '.$bindings.' />'),
            ['component' => $component, ...$data],
        );
    }
}
