<?php

use App\Models\Setting;
use Illuminate\Support\Str;

if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('format_currency')) {
    function format_currency(float $amount, string $currency = 'IDR'): string
    {
        $symbol = match ($currency) {
            'IDR' => 'Rp ',
            'USD' => '$',
            'EUR' => '€',
            default => $currency . ' ',
        };

        return $symbol . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('tracking_code')) {
    function tracking_code(?\Illuminate\Database\Eloquent\Model $model = null): string
    {
        if ($model && $model->tracking_code) {
            return $model->tracking_code;
        }

        return 'TRK-' . strtoupper(Str::random(8));
    }
}

if (!function_exists('nav_url')) {
    /**
     * Resolve URL item navigasi.
     * Mendukung format `route::nama.route` (named route) atau URL literal.
     */
    function nav_url(\App\Models\Navigation $navigation): string
    {
        if ($navigation->url && str_starts_with($navigation->url, 'route::')) {
            $routeName = substr($navigation->url, 7);
            if (\Illuminate\Support\Facades\Route::has($routeName)) {
                return route($routeName);
            }

            return '#';
        }

        return $navigation->url ? url($navigation->url) : '#';
    }
}

if (!function_exists('nav_is_active')) {
    /**
     * Deteksi menu aktif berdasarkan `route_name` item navigasi.
     * Mendukung pola awalan (mis. `admin.users`) dan wildcard (mis. `admin.users*`).
     */
    function nav_is_active(\App\Models\Navigation $navigation): bool
    {
        $current = request()->route()?->getName();
        $pattern = $navigation->route_name;

        if (! $current || ! $pattern) {
            return false;
        }

        if (request()->routeIs($pattern)) {
            return true;
        }

        $base = rtrim($pattern, '*');

        if ($pattern !== $base) {
            return str_starts_with($current, $base);
        }

        return str_starts_with($current, $pattern . '.');
    }
}
