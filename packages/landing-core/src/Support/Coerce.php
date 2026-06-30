<?php

namespace Template\LandingCore\Support;

/**
 * Coerção de valores "stringly-typed" para tipos seguros, com fallback.
 *
 * Consolida a lógica que antes estava duplicada em ContactFields,
 * AnalyticsManager e nos componentes Blade. Usado tanto pela config tipada
 * (ModuleConfig) quanto por componentes que recebem atributos soltos.
 */
class Coerce
{
    public static function bool(mixed $value, bool $default = false): bool
    {
        if ($value === null) {
            return $default;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
        }

        return (bool) $value;
    }

    public static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    public static function string(mixed $value, string $default = ''): string
    {
        return self::nullableString($value) ?? $default;
    }

    public static function int(mixed $value, int $default = 0): int
    {
        if (is_int($value)) {
            return $value;
        }

        return is_numeric($value) ? (int) $value : $default;
    }
}
