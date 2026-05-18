<?php

declare(strict_types=1);

use App\Core\Lang;

if (!function_exists('__')) {
    function __(string $key, array $replace = []): string
    {
        return Lang::get($key, $replace);
    }
}

if (!function_exists('display_mapped_value')) {
    function display_mapped_value(string $prefix, mixed $value): string
    {
        $label = trim((string) $value);

        if ($label === '') {
            return '';
        }

        $slug = strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '_', $label));
        $slug = trim($slug, '_');

        if ($slug === '') {
            return $label;
        }

        $key = $prefix . '.' . $slug;
        $translated = __($key);

        return $translated === $key ? $label : $translated;
    }
}

if (!function_exists('display_category_name')) {
    function display_category_name(mixed $name): string
    {
        return display_mapped_value('category', $name);
    }
}

if (!function_exists('display_activity_title')) {
    function display_activity_title(mixed $title): string
    {
        return display_mapped_value('activity', $title);
    }
}
