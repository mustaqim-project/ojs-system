{{-- Usage: <x-ui.breadcrumb :items="[['label'=>'Home','href'=>route('...')],['label'=>'Current']]"/> --}}
@props(['items' => []])
<nav aria-label="Breadcrumb">
  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:6px;list-style:none;padding:0;margin:0 0 8px 0;">
    @foreach($items as $i => $item)
      <li style="display:flex;align-items:center;gap:6px;">
        @if($i > 0)
          <span aria-hidden="true" style="color:var(--text-muted);font-size:11px;">›</span>
        @endif
        @if(isset($item['href']) && $i < count($items)-1)
          <a href="{{ $item['href'] }}" style="font-size:13px;color:var(--primary);font-weight:500;text-decoration:none;" class="hover:underline">
            {{ $item['label'] }}
          </a>
        @else
          <span style="font-size:13px;color:{{ $i === count($items)-1 ? 'var(--text-main)' : 'var(--text-muted)' }};font-weight:{{ $i === count($items)-1 ? '500' : '400' }};">
            {{ $item['label'] }}
          </span>
        @endif
      </li>
    @endforeach
  </ol>
</nav>
