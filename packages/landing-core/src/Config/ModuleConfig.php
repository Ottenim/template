<?php

namespace Template\LandingCore\Config;

use Illuminate\Support\Arr;
use Template\LandingCore\Support\Coerce;

/**
 * Base para a config tipada de cada módulo.
 *
 * O "plug" é o mecanismo (resolver config + coerção segura por tipo), não o
 * formato: cada módulo subclasse declara seus próprios acessadores com nomes
 * que revelam intenção. As chaves passam a existir num único lugar, então um
 * typo vira erro de teste/IDE em vez de bug silencioso em produção.
 */
abstract class ModuleConfig
{
    /**
     * @param  array<string, mixed>  $data
     */
    final public function __construct(protected array $data)
    {
        //
    }

    /**
     * Lê o snapshot atual da config do módulo (ex.: config('landing-contact')).
     */
    public static function fromConfig(): static
    {
        return new static((array) config(static::key(), []));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        return new static($data);
    }

    /**
     * Chave raiz da config do módulo (ex.: 'landing-contact').
     */
    abstract public static function key(): string;

    protected function string(string $path, string $default = ''): string
    {
        return Coerce::string(Arr::get($this->data, $path), $default);
    }

    protected function nullableString(string $path): ?string
    {
        return Coerce::nullableString(Arr::get($this->data, $path));
    }

    protected function bool(string $path, bool $default = false): bool
    {
        return Coerce::bool(Arr::get($this->data, $path), $default);
    }

    protected function int(string $path, int $default = 0): int
    {
        return Coerce::int(Arr::get($this->data, $path), $default);
    }

    /**
     * @param  array<int|string, mixed>  $default
     * @return array<int|string, mixed>
     */
    protected function list(string $path, array $default = []): array
    {
        $value = Arr::get($this->data, $path);

        if ($value === null) {
            return $default;
        }

        return (array) $value;
    }
}
