<?php

namespace Template\LandingCore\Support;

class AssetRegistry
{
    protected array $styles = [];

    protected array $scripts = [];

    public function __construct(array $assets = [])
    {
        foreach ($assets['styles'] ?? [] as $name => $asset) {
            $this->registerStyle($name, $asset);
        }

        foreach ($assets['scripts'] ?? [] as $name => $asset) {
            $this->registerScript($name, $asset);
        }
    }

    public function registerStyle(string $name, string|array $asset): static
    {
        $this->styles[$name] = $this->normalizeAsset($name, $asset);

        return $this;
    }

    public function registerScript(string $name, string|array $asset): static
    {
        $this->scripts[$name] = $this->normalizeAsset($name, $asset);

        return $this;
    }

    public function styles(): array
    {
        return $this->styles;
    }

    public function scripts(): array
    {
        return $this->scripts;
    }

    public function all(): array
    {
        return [
            'styles' => $this->styles,
            'scripts' => $this->scripts,
        ];
    }

    protected function normalizeAsset(string $name, string|array $asset): array
    {
        if (is_string($asset)) {
            return [
                'name' => $name,
                'url' => $asset,
                'attributes' => [],
            ];
        }

        return [
            'name' => $name,
            'url' => $asset['url'] ?? $asset['path'] ?? '',
            'attributes' => $asset['attributes'] ?? [],
            ...$asset,
        ];
    }
}
