<?php

namespace Template\LandingPricing\Support;

use Template\LandingCore\Support\Coerce;

class PricingUrl
{
    public static function normalize(mixed $value): ?string
    {
        $url = Coerce::nullableString($value);

        if ($url === null || ! self::isSafe($url)) {
            return null;
        }

        return $url;
    }

    public static function isSafe(mixed $value): bool
    {
        $url = Coerce::nullableString($value);

        if ($url === null) {
            return true;
        }

        $normalized = strtolower(preg_replace('/[\x00-\x20]+/', '', $url) ?: $url);

        return ! str_starts_with($normalized, 'javascript:')
            && ! str_starts_with($normalized, 'data:')
            && ! str_starts_with($normalized, 'vbscript:');
    }
}
