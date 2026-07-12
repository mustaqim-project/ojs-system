@props(['label' => null, 'hint' => null])
<label class="inline-flex items-start gap-2.5 cursor-pointer group">
    <input type="checkbox" {{ $attributes->merge(['class' => 'mt-0.5 w-4 h-4 rounded border-[#E2E8F0] text-[#0F4C81] cursor-pointer accent-[#0F4C81] shrink-0']) }}>
    @if($label)
    <span style="font-size:14px;color:var(--text-main);line-height:1.5;">
        {{ $label }}
        @if($hint)
        <span style="display:block;font-size:12px;color:var(--text-muted);margin-top:1px;">{{ $hint }}</span>
        @endif
    </span>
    @elseif($slot->isNotEmpty())
    <span style="font-size:14px;color:var(--text-main);line-height:1.5;">{{ $slot }}</span>
    @endif
</label>
