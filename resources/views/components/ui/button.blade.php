@props([
    'variant'  => 'primary',   // primary | secondary | ghost | danger | success | warning | outline
    'size'     => 'md',        // xs | sm | md | lg
    'type'     => 'button',
    'href'     => null,
    'icon'     => null,        // bi class e.g. "bi-plus-lg"
    'iconRight'=> null,
    'loading'  => false,
    'disabled' => false,
    'full'     => false,
])

@php
$base = 'ds-btn';

$sizes = [
    'xs' => 'ds-btn-xs',
    'sm' => 'ds-btn-sm',
    'md' => '',
    'lg' => 'btn-lg', // Fallback to bootstrap if needed, or custom
];

$variants = [
    'primary'   => 'ds-btn-pri',
    'secondary' => 'ds-btn-out',
    'ghost'     => 'ds-btn-ghost',
    'outline'   => 'ds-btn-out',
    'danger'    => 'ds-btn-danger',
    'success'   => 'ds-btn-suc',
    'warning'   => 'btn-warning', // fallback to bootstrap
];

$classes = implode(' ', array_filter([
    $base,
    $sizes[$size] ?? $sizes['md'],
    $variants[$variant] ?? $variants['primary'],
    $full ? 'w-100 justify-content-center' : '',
    ($disabled || $loading) ? 'disabled' : '',
]));
@endphp

@if($href && !$disabled)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<i class="bi {{ $icon }}"></i>@endif
        @if($loading)<i class="bi bi-arrow-repeat animate__animated animate__spin"></i>@endif
        {{ $slot }}
        @if($iconRight)<i class="bi {{ $iconRight }}"></i>@endif
    </a>
@else
    <button type="{{ $type }}" {{ $disabled || $loading ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<i class="bi {{ $icon }}"></i>@endif
        @if($loading)<i class="bi bi-arrow-repeat animate__animated animate__spin"></i>@endif
        {{ $slot }}
        @if($iconRight)<i class="bi {{ $iconRight }}"></i>@endif
    </button>
@endif
