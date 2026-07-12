{{--
  Usage:
  <x-ui.empty-state
    icon="bi-file-earmark-text"
    title="No articles yet"
    description="Submit your first manuscript to get started."
  >
    <x-ui.button icon="bi-plus-lg" href="{{ route('...') }}">New Submission</x-ui.button>
  </x-ui.empty-state>
--}}
@props([
    'icon'        => 'bi-inbox',
    'title'       => 'Nothing here yet',
    'description' => null,
])
<div style="text-align:center;padding:64px 24px;" {{ $attributes }}>
    <div style="width:56px;height:56px;border-radius:14px;background:var(--bg-app);border:1px solid var(--border);display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
        <i class="bi {{ $icon }}" style="font-size:24px;color:var(--text-muted);"></i>
    </div>
    <h3 style="font-size:15px;font-weight:700;color:var(--text-main);margin:0 0 6px;">{{ $title }}</h3>
    @if($description)
    <p style="font-size:14px;color:var(--text-muted);margin:0 auto 24px;max-width:360px;line-height:1.6;">{{ $description }}</p>
    @endif
    @if($slot->isNotEmpty())
    <div>{{ $slot }}</div>
    @endif
</div>
