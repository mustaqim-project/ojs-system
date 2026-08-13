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
