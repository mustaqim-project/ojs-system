@props(['variant' => 'primary', 'size' => 'md', 'type' => 'button', 'href' => null, 'icon' => null, 'iconRight' => null, 'disabled' => false, 'loading' => false, 'fullWidth' => false])

@php
$baseClasses = 'inline-flex items-center justify-center font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg';

$variants = [
    'primary' => 'bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800 focus:ring-blue-500 shadow-md hover:shadow-lg hover:-translate-y-0.5',
    'secondary' => 'bg-slate-600 text-white hover:bg-slate-700 focus:ring-slate-500',
    'outline' => 'bg-transparent text-slate-700 border border-slate-300 hover:bg-slate-50 focus:ring-slate-400',
    'ghost' => 'bg-transparent text-slate-600 hover:bg-slate-100 focus:ring-slate-300',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    'success' => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-xs gap-1.5',
    'md' => 'px-4 py-2 text-sm gap-2',
    'lg' => 'px-6 py-3 text-base gap-2.5',
];

$widthClass = $fullWidth ? 'w-full' : '';
$iconSize = $size === 'sm' ? 'h-3.5 w-3.5' : ($size === 'lg' ? 'h-5 w-5' : 'h-4 w-4');

$classes = "$baseClasses {$variants[$variant]} {$sizes[$size]} $widthClass";
@endphp

@if($href && !$disabled)
    <a href="{{ $href }}" class="{{ $classes }}">
        @if($icon && !$loading)
            <i class="bi bi-{{ $icon }} {{ $iconSize }}"></i>
        @endif
        @if($loading)
            <svg class="animate-spin {{ $iconSize }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif
        <span>{{ $slot }}</span>
        @if($iconRight && !$loading)
            <i class="bi bi-{{ $iconRight }} {{ $iconSize }}"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} class="{{ $classes }}">
        @if($icon && !$loading)
            <i class="bi bi-{{ $icon }} {{ $iconSize }}"></i>
        @endif
        @if($loading)
            <svg class="animate-spin {{ $iconSize }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif
        <span>{{ $slot }}</span>
        @if($iconRight && !$loading)
            <i class="bi bi-{{ $iconRight }} {{ $iconSize }}"></i>
        @endif
    </button>
@endif
