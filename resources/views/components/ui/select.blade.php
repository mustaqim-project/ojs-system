@props([
    'value' => '',
    'name' => null,
    'options' => [],
    'placeholder' => 'Select an option',
    'required' => false,
    'disabled' => false,
    'error' => null,
    'class' => '',
])

@php
$baseClasses = 'block w-full rounded-lg border-gray-300 shadow-sm text-sm transition-all duration-150';
$stateClasses = $error 
    ? 'border-red-500 focus:border-red-500 focus:ring-red-500' 
    : 'focus:border-[#0F4C81] focus:ring-[#0F4C81]';
$disabledClasses = $disabled ? 'bg-gray-100 cursor-not-allowed opacity-60' : 'bg-white';
@endphp

<select 
    name="{{ $name }}"
    {{ $required ? 'required' : '' }}
    {{ $disabled ? 'disabled' : '' }}
    class="{{ $baseClasses }} {{ $stateClasses }} {{ $disabledClasses }} {{ $class }}"
>
    @if($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif
    @foreach($options as $key => $label)
        <option value="{{ $key }}" {{ old($name, $value) == $key ? 'selected' : '' }}>
            {{ $label }}
        </option>
    @endforeach
</select>
