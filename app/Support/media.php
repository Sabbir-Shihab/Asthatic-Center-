<?php

require_once __DIR__.'/locale.php';

use Illuminate\Support\Str;
use App\Models\SiteSetting;

if (! function_exists('storage_relative')) {
    function storage_relative(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return $path;
        }

        $storedPath = $path;
        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            $storedPath = parse_url($path, PHP_URL_PATH) ?? '';
        }

        if (Str::startsWith($storedPath, '/storage/')) {
            return ltrim(substr($storedPath, strlen('/storage/')), '/');
        }

        return $path;
    }
}

if (! function_exists('media_url')) {
    function media_url(?string $path): string
    {
        if (! $path) return '';

        $relative = storage_relative($path);
        if ($relative !== $path) {
            return url('storage/'.ltrim((string) $relative, '/'));
        }

        if (Str::startsWith($path, ['http://', 'https://', '//', 'data:'])) return $path;

        return url('storage/'.ltrim($path, '/'));
    }
}

if (! function_exists('site_setting')) {
    function site_setting(string $key, ?string $default = null): ?string
    {
        static $settings = null;
        if ($settings === null) {
            $settings = SiteSetting::query()->get()->keyBy('key');
        }

        return $settings->get($key)?->value ?? $default;
    }
}
