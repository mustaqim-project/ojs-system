@props([
    'label'       => null,
    'required'    => false,
    'hint'        => null,
    'error'       => null,
    'id'          => null,
])
<div {{ $attributes->merge(['class' => 'mb-5']) }}>
    @if($label)
    <label @if($id) for="{{ $id }}" @endif class="ds-lbl" style="display:block;font-size:13px;font-weight:500;color:var(--text-main);margin-bottom:6px;">
        {{ $label }}@if($required)<span class="req" style="color:var(--danger);margin-left:2px;">*</span>@endif
    </label>
    @endif

    {{ $slot }}

    @if($error)
    <p class="ds-f-err" style="font-size:12px;color:var(--danger);margin-top:5px;display:flex;align-items:center;gap:4px;">
        <i class="bi bi-exclamation-circle"></i> {{ $error }}
    </p>
    @elseif($hint)
    <p class="ds-f-hint" style="font-size:12px;color:var(--text-muted);margin-top:4px;">{{ $hint }}</p>
    @endif
</div>
