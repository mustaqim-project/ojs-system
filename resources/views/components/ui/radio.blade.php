@props([
    'label' => null,
    'name' => null,
    'checked' => false,
    'disabled' => false,
    'helpText' => null,
])

<label class="flex items-center gap-3 cursor-pointer {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
    <input 
        type="radio" 
        name="{{ $name }}"
        {{ $checked ? 'checked' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        class="h-4 w-4 border-gray-300 text-[#0F4C81] focus:ring-[#0F4C81]"
    />
    @if($label)
        <span class="text-sm text-gray-700">{{ $label }}</span>
    @endif
    
    @if($helpText)
        <span class="text-xs text-gray-500">{{ $helpText }}</span>
    @endif
</label>
