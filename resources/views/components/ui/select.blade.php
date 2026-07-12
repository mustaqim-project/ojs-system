@props(['error' => false, 'placeholder' => 'Select an option'])
@php
$cls = 'form-select ' . ($error ? ' is-invalid' : '');
@endphp
<select {{ $attributes->merge(['class' => $cls]) }}>
    @if($placeholder)
        <option value="" disabled selected>{{ $placeholder }}</option>
    @endif
    {{ $slot }}
</select>
