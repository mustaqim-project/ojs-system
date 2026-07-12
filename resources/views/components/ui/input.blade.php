@props(['error' => false, 'size' => 'md'])
@php
$cls = 'form-control '
     . match($size) {
         'sm' => 'form-control-sm',
         'lg' => 'form-control-lg',
         default => '',
       }
     . ($error ? ' is-invalid' : '');
@endphp
<input {{ $attributes->merge(['class' => $cls]) }}>
