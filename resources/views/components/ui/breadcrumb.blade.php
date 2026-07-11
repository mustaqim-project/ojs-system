@props([
    'items' => [], // Array of ['label' => '', 'href' => '']
    'currentPage' => null,
])

<nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-sm">
    <a href="{{ route('admin.dashboard') }}" 
       class="text-gray-500 hover:text-gray-700 transition-colors">
        <x-dynamic-component component="heroicon::home" class="h-4 w-4" />
    </a>
    
    @foreach($items as $index => $item)
        <span class="text-gray-400">/</span>
        @if($loop->last)
            <span class="text-gray-900 font-medium">{{ $item['label'] }}</span>
        @else
            <a href="{{ $item['href'] ?? '#' }}" 
               class="text-gray-500 hover:text-gray-700 transition-colors">
                {{ $item['label'] }}
            </a>
        @endif
    @endforeach
</nav>
