@props([
    'label' => null,
    'required' => false,
    'for' => null,
    'helpText' => null,
    'error' => null,
])

<div>
    @if($label)
        <label for="{{ $for }}" class="block text-sm font-medium text-gray-700 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-red-500" aria-label="required">*</span>
            @endif
        </label>
    @endif
    
    {{ $slot }}
    
    @if($error)
        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
            <x-dynamic-component component="heroicon::exclamation-circle" class="h-3 w-3" />
            {{ $error }}
        </p>
    @endif
    
    @if($helpText && !$error)
        <p class="mt-1.5 text-xs text-gray-500">{{ $helpText }}</p>
    @endif
</div>
