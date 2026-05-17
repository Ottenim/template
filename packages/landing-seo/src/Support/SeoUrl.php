<?php

namespace Template\LandingSeo\Support;

class SeoUrl
{
    public static function normalize(mixed $value, bool $absolute = false): ?string
    {
        $url = self::nullableString($value);

        if ($url === null || ! self::isSafe($url)) {
            return null;
        }

        if (! $absolute || str_starts_with($url, '#')) {
            return $url;
        }

        if (str_starts_with($url, '//')) {
            return request()->getScheme().':'.$url;
        }

        if (preg_match('/^https?:\/\//i', $url) === 1) {
            return $url;
        }

        if (str_starts_with($url, '/')) {
            return rtrim((string) config('app.url'), '/').$url;
        }

        return rtrim((string) config('app.url'), '/').'/'.ltrim($url, '/');
    }

    public static function isSafe(mixed $value): bool
    {
        $url = self::nullableString($value);

        if ($url === null) {
            return true;
        }

        if (preg_match('/^[a-z][a-z0-9+.-]*:/i', $url) !== 1) {
            return true;
        }

        return preg_match('/^https?:\/\//i', $url) === 1;
    }

    public static function normalizePath(mixed $value): ?string
    {
        $path = self::nullableString($value);

        if ($path === null) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $path) === 1) {
            $parsed = parse_url($path);
            $path = ($parsed['path'] ?? '/').(isset($parsed['query']) ? '?'.$parsed['query'] : '');
        }

        if ($path === '') {
            return '/';
        }

        return str_starts_with($path, '/') ? $path : '/'.$path;
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
