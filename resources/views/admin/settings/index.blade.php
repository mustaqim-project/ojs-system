@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Pengaturan']]"/>
    <h1 class="ds-page-title">Pengaturan Sistem</h1>
    <p class="ds-page-subtitle">Konfigurasi global untuk platform OJS</p>
  </div>
</div>

  @php
  $groupMeta = [
    'general' => ['icon'=>'bi-gear-fill',         'label'=>'Pengaturan Umum',         'color'=>'var(--info)'],
    'payment' => ['icon'=>'bi-credit-card-fill',   'label'=>'Pengaturan Pembayaran / APC',   'color'=>'var(--success)'],
    'review'  => ['icon'=>'bi-clipboard-check-fill','label'=>'Konfigurasi Peninjauan',    'color'=>'#6B46C1'],
    'email'   => ['icon'=>'bi-envelope-fill',      'label'=>'Pengaturan Email',           'color'=>'var(--warning)'],
    'legal'   => ['icon'=>'bi-file-earmark-text-fill','label'=>'Pengaturan Legal',        'color'=>'var(--danger)'],
  ];
  @endphp

<div class="ds-card fd1" data-aos="fade-up">
  <div class="ds-card-hdr p-0 border-bottom" style="background: var(--bg-surface);">
    <ul class="nav nav-tabs border-0" id="settingsTab" role="tablist" style="padding-top: 15px; padding-left: 20px;">
      @foreach($groups as $groupKey => $settings)
      @php $gl = $groupMeta[$groupKey] ?? ['icon'=>'bi-circle','label'=>ucfirst($groupKey),'color'=>'var(--primary)']; @endphp
      <li class="nav-item" role="presentation">
        <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $groupKey }}" data-bs-toggle="tab" data-bs-target="#pane-{{ $groupKey }}" type="button" role="tab" style="font-weight: 600; font-size: 14px; padding: 12px 20px; color: var(--text-main); border: none; border-bottom: 3px solid {{ $loop->first ? 'var(--primary)' : 'transparent' }}; border-radius: 0; background: transparent;" onclick="document.querySelectorAll('#settingsTab .nav-link').forEach(el => el.style.borderBottomColor = 'transparent'); this.style.borderBottomColor = 'var(--primary)';">
          <span style="color:{{ $gl['color'] }}"><i class="{{ $gl['icon'] }} me-2"></i></span> {{ $gl['label'] }}
        </button>
      </li>
      @endforeach
    </ul>
  </div>
  
  <div class="tab-content" id="settingsTabContent">
    @foreach($groups as $groupKey => $settings)
    @php $gl = $groupMeta[$groupKey] ?? ['icon'=>'bi-circle','label'=>ucfirst($groupKey),'color'=>'var(--primary)']; @endphp
    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pane-{{ $groupKey }}" role="tabpanel">
      <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" novalidate class="m-0">
        @csrf
        <div style="padding:24px;">
          <div class="row g-3">
            @foreach($settings as $idx => $setting)
            @php
              $isWide = ($setting->type === 'textarea' || $setting->type === 'longtext') || (str_contains($setting->key, 'description') || str_contains($setting->key, 'address'));
              $isFull = $setting->type === 'boolean';
            @endphp
            <div class="{{ $isWide ? 'col-12' : 'col-12 col-md-6' }}">
              <input type="hidden" name="settings[{{ $groupKey }}_{{ $idx }}][key]" value="{{ $setting->key }}"/>
              @if($setting->type === 'textarea')
                <x-ui.form-field :label="$setting->label ?? $setting->key" :hint="$setting->description ?? null">
                  <x-ui.textarea name="settings[{{ $groupKey }}_{{ $idx }}][value]" rows="3">{{ $setting->value }}</x-ui.textarea>
                </x-ui.form-field>
              @elseif($setting->type === 'longtext')
                <x-ui.form-field :label="$setting->label ?? $setting->key" :hint="$setting->description ?? null">
                  <x-ui.textarea name="settings[{{ $groupKey }}_{{ $idx }}][value]" rows="10">{{ $setting->value }}</x-ui.textarea>
                </x-ui.form-field>
              @elseif($setting->type === 'boolean')
                <div style="padding:12px 14px;background:var(--bg-app);border:1px solid var(--border);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:space-between;">
                  <div>
                    <div style="font-size:13px;font-weight:600;color:var(--text-main);">{{ $setting->label ?? $setting->key }}</div>
                    @if($setting->description)<div style="font-size:12px;color:var(--text-muted);margin-top:2px;">{{ $setting->description }}</div>@endif
                  </div>
                  <div style="display:flex;align-items:center;gap:8px;">
                    <input type="hidden" name="settings[{{ $groupKey }}_{{ $idx }}][value]" value="0"/>
                    <input type="checkbox" id="s_{{ $setting->key }}" name="settings[{{ $groupKey }}_{{ $idx }}][value]" value="1"
                           {{ $setting->value ? 'checked' : '' }}
                           style="width:17px;height:17px;accent-color:var(--primary);cursor:pointer;"/>
                    <label for="s_{{ $setting->key }}" style="font-size:13px;color:var(--text-muted);cursor:pointer;margin:0;">Aktif</label>
                  </div>
                </div>
              @elseif($setting->type === 'number')
                <x-ui.form-field :label="$setting->label ?? $setting->key" :hint="$setting->description ?? null">
                  @if($groupKey === 'payment' && str_contains($setting->key, 'amount'))
                    <div style="position:relative;">
                      <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:12px;font-weight:600;color:var(--text-muted);">Rp</span>
                      <x-ui.input type="number" name="settings[{{ $groupKey }}_{{ $idx }}][value]" :value="$setting->value" style="padding-left:38px;"/>
                    </div>
                  @else
                    <x-ui.input type="number" name="settings[{{ $groupKey }}_{{ $idx }}][value]" :value="$setting->value"/>
                  @endif
                </x-ui.form-field>
              @elseif($setting->type === 'image')
                <x-ui.form-field :label="$setting->label ?? $setting->key" :hint="$setting->description ?? null">
                  @if($setting->value)
                    <div class="mb-2">
                      <img src="{{ asset($setting->value) }}" alt="Preview" style="max-height: 80px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-surface); padding: 4px;">
                    </div>
                  @endif
                  <input type="file" name="files[{{ $setting->key }}]" class="form-control" accept="image/*" style="font-size: 14px;">
                  <input type="hidden" name="settings[{{ $groupKey }}_{{ $idx }}][key]" value="{{ $setting->key }}"/>
                  <input type="hidden" name="settings[{{ $groupKey }}_{{ $idx }}][value]" value="{{ $setting->value }}"/>
                </x-ui.form-field>
              @else
                <x-ui.form-field :label="$setting->label ?? $setting->key" :hint="$setting->description ?? null">
                  <x-ui.input type="text" name="settings[{{ $groupKey }}_{{ $idx }}][value]" :value="$setting->value"/>
                </x-ui.form-field>
              @endif
            </div>
            @endforeach
          </div>
        </div>
        <div style="padding: 16px 24px; border-top: 1px solid var(--border); background: var(--bg-surface); border-bottom-left-radius: inherit; border-bottom-right-radius: inherit; display: flex; justify-content: flex-end;">
          <button type="submit" class="ds-btn ds-btn-pri" style="height:38px;font-size:14px;padding:0 24px;">
            <i class="bi bi-floppy-fill me-2"></i> Simpan {{ $gl['label'] }}
          </button>
        </div>
      </form>
    </div>
    @endforeach
  </div>
</div>

@endsection
