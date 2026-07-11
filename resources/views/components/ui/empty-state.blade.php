@props([
    'title' => null,
    'description' => null,
    'icon' => 'inbox',
    'actionText' => null,
    'actionUrl' => null,
])

<div class="text-center py-12 px-6">
    <div class="mx-auto w-16 h-16 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center mb-4">
        <x-dynamic-component component="heroicon::{{ $icon }}" class="h-7 w-7 text-gray-400" />
    </div>
    
    @if($title)
        <h3 class="text-base font-semibold text-gray-900 mb-1">{{ $title }}</h3>
    @endif
    
    @if($description)
        <p class="text-sm text-gray-500 max-w-md mx-auto mb-6">{{ $description }}</p>
    @endif
    
    @if($actionText && $actionUrl)
        <a href="{{ $actionUrl }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-[#0F4C81] text-white text-sm font-medium rounded-lg hover:bg-[#0d4372] transition-colors">
            <x-dynamic-component component="heroicon::plus" class="h-4 w-4" />
            {{ $actionText }}
        </a>
    @endif
</div>
