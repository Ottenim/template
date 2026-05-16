<?php

namespace Template\LandingCore\Theme;

use Illuminate\Support\Arr;

class Theme
{
    public function __construct(
        protected string $name,
        protected array $tokens,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function all(): array
    {
        return $this->tokens;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->tokens, $key, $default);
    }

    public function cssVariables(): array
    {
        return array_merge(
            $this->variablesForGroup('font', 'font'),
            $this->variablesForGroup('colors', 'color'),
            $this->variablesForGroup('radius', 'radius'),
            $this->spacingVariables(),
            $this->variablesForGroup('shadow', 'shadow'),
        );
    }

    protected function variablesForGroup(string $tokenGroup, string $cssGroup): array
    {
        $variables = [];

        foreach ($this->get($tokenGroup, []) as $key => $value) {
            $variables['--lp-'.$cssGroup.'-'.$this->cssKey($key)] = $value;
        }

        return $variables;
    }

    protected function spacingVariables(): array
    {
        $variables = [];

        foreach ($this->get('spacing', []) as $key => $value) {
            $name = match ($key) {
                'section_y' => '--lp-section-y',
                'container' => '--lp-container',
                default => '--lp-spacing-'.$this->cssKey($key),
            };

            $variables[$name] = $value;
        }

        return $variables;
    }

    protected function cssKey(string $key): string
    {
        return str_replace('_', '-', $key);
    }
}
