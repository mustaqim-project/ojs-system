@props([
    'name' => null,
    'value' => '',
    'rows' => 4,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'class' => '',
])

@php
$baseClasses = 'block w-full rounded-lg border-gray-300 shadow-sm text-sm transition-all duration-150 resize-y';
$stateClasses = $error 
    ? 'border-red-500 focus:border-red-500 focus:ring-red-500' 
    : 'focus:border-[#0F4C81] focus:ring-[#0F4C81]';
$disabledClasses = $disabled ? 'bg-gray-100 cursor-not-allowed opacity-60' : 'bg-white';
@endphp

<textarea 
    name="{{ $name }}"
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    {{ $required ? 'required' : '' }}
    {{ $disabled ? 'disabled' : '' }}
    {{ $readonly ? 'readonly' : '' }}
    class="{{ $baseClasses }} {{ $stateClasses }} {{ $disabledClasses }} {{ $class }}"
>{{ old($name, $value) }}</textarea>
