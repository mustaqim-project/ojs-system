@props([
    'value' => '',
    'name' => null,
    'type' => 'text',
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'autofocus' => false,
    'autocomplete' => null,
    'icon' => null,
    'error' => null,
    'class' => '',
])

@php
$baseClasses = 'block w-full rounded-lg border-gray-300 shadow-sm text-sm transition-all duration-150';
$stateClasses = $error 
    ? 'border-red-500 focus:border-red-500 focus:ring-red-500' 
    : 'focus:border-[#0F4C81] focus:ring-[#0F4C81]';
$disabledClasses = $disabled ? 'bg-gray-100 cursor-not-allowed opacity-60' : 'bg-white';
$iconPadding = $icon ? 'pl-10' : '';
@endphp

<div class="relative">
    @if($icon)
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <x-dynamic-component component="heroicon::{{ $icon }}" class="h-5 w-5 text-gray-400" />
        </div>
    @endif
    
    <input 
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        {{ $autofocus ? 'autofocus' : '' }}
        autocomplete="{{ $autocomplete ?? 'off' }}"
        class="{{ $baseClasses }} {{ $stateClasses }} {{ $disabledClasses }} {{ $iconPadding }} {{ $class }}"
    />
</div>
