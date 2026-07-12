@props(['error' => false, 'rows' => 4])
@php
$cls = 'form-control ' . ($error ? ' is-invalid' : '');
@endphp
<textarea rows="{{ $rows }}" {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</textarea>
