<?php

namespace Template\LandingPricing\Support;

class PricingUrl
{
    public static function normalize(mixed $value): ?string
    {
        $url = self::nullableString($value);

        if ($url === null || ! self::isSafe($url)) {
            return null;
        }

        return $url;
    }

    public static function isSafe(mixed $value): bool
    {
        $url = self::nullableString($value);

        if ($url === null) {
            return true;
        }

        $normalized = strtolower(preg_replace('/[\x00-\x20]+/', '', $url) ?: $url);

        return ! str_starts_with($normalized, 'javascript:')
            && ! str_starts_with($normalized, 'data:')
            && ! str_starts_with($normalized, 'vbscript:');
    }

    protected static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
