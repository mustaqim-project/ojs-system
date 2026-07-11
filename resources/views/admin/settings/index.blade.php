@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr"><div><h2 class="pg-title">Pengaturan Sistem</h2><p class="pg-desc">Konfigurasi global platform OJS</p></div></div>

<form method="POST" action="{{ route('admin.settings.update') }}">
  @csrf
  <div class="row g-3">
    @php $groupLabels=['general'=>['icon'=>'bi-gear','label'=>'Pengaturan Umum'],'payment'=>['icon'=>'bi-credit-card','label'=>'Pengaturan Pembayaran (APC)'],'review'=>['icon'=>'bi-clipboard-check','label'=>'Pengaturan Review'],'email'=>['icon'=>'bi-envelope','label'=>'Pengaturan Email']]; @endphp

    @foreach($groups as $groupKey => $settings)
    @php $gl=$groupLabels[$groupKey]??['icon'=>'bi-circle','label'=>ucfirst($groupKey)]; @endphp
    <div class="col-12 fu fd{{ $loop->index+1 }}">
      <div class="f-section">
        <div class="f-section-hdr" style="display:flex;align-items:center;gap:8px;">
          <i class="{{ $gl['icon'] }}" style="color:var(--acc);font-size:14px;"></i>
          <h3 class="f-section-title" style="margin:0;">{{ $gl['label'] }}</h3>
        </div>
        <div class="f-section-body">
          <div class="row g-3">
            @foreach($settings as $idx=>$setting)
            <div class="{{ in_array($setting->type,['textarea','text']) && str_contains($setting->key,'description') ? 'col-12' : ($setting->type==='boolean' ? 'col-12 col-md-6' : 'col-12 col-md-6') }}">
              <input type="hidden" name="settings[{{ $groupKey }}_{{ $idx }}][key]" value="{{ $setting->key }}"/>
              <label class="lbl">
                {{ $setting->label ?? $setting->key }}
                @if($setting->description)<span class="hint">— {{ $setting->description }}</span>@endif
              </label>
              @if($setting->type==='textarea')
                <textarea name="settings[{{ $groupKey }}_{{ $idx }}][value]" class="txta" rows="2">{{ $setting->value }}</textarea>
              @elseif($setting->type==='boolean')
                <div style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:var(--canvas);border:1px solid var(--brd);border-radius:var(--r2);">
                  <input type="hidden" name="settings[{{ $groupKey }}_{{ $idx }}][value]" value="0"/>
                  <input type="checkbox" id="s_{{ $setting->key }}" name="settings[{{ $groupKey }}_{{ $idx }}][value]" value="1" {{ $setting->value?'checked':'' }} style="width:16px;height:16px;accent-color:var(--acc);cursor:pointer;"/>
                  <label for="s_{{ $setting->key }}" style="font-size:13px;font-weight:500;cursor:pointer;margin:0;">Aktifkan</label>
                </div>
              @elseif($setting->type==='number')
                <div style="position:relative;">
                  <input class="inp" type="number" name="settings[{{ $groupKey }}_{{ $idx }}][value]" value="{{ $setting->value }}" style="padding-left:{{ $groupKey==='payment'&&str_contains($setting->key,'amount')?'48px':'12px' }};"/>
                  @if($groupKey==='payment'&&str_contains($setting->key,'amount'))<div style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:12px;font-weight:600;color:var(--txt2);">Rp</div>@endif
                </div>
              @else
                <input class="inp" type="text" name="settings[{{ $groupKey }}_{{ $idx }}][value]" value="{{ $setting->value }}"/>
              @endif
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    @endforeach

    <div class="col-12 fu">
      <div style="background:var(--surf);border:1px solid var(--brd);border-radius:var(--r);padding:20px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
        <button type="submit" class="btn-o btn-pri btn-lg"><i class="bi bi-floppy-fill"></i> Simpan Semua Pengaturan</button>
        <span style="font-size:12px;color:var(--txt3);">Pengaturan akan aktif setelah disimpan.</span>
      </div>
    </div>
  </div>
</form>
@endsection
