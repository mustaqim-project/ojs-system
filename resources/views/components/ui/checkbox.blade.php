@props([
    'label' => null,
    'name' => null,
    'checked' => false,
    'disabled' => false,
    'helpText' => null,
])

<label class="flex items-start gap-3 cursor-pointer {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
    <input 
        type="checkbox" 
        name="{{ $name }}"
        {{ $checked ? 'checked' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        class="mt-0.5 h-4 w-4 rounded border-gray-300 text-[#0F4C81] focus:ring-[#0F4C81]"
    />
    @if($label)
        <span class="text-sm text-gray-700">{{ $label }}</span>
    @endif
    
    @if($helpText)
        <span class="text-xs text-gray-500 mt-0.5">{{ $helpText }}</span>
    @endif
</label>
