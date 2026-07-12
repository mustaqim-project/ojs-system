@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'API Integrations','href'=>route('admin.integrations.index')],['label'=>$providerMeta['label']]]"/>
    <div style="display:flex;align-items:center;gap:14px;margin-top:10px;">
      <div style="width:44px;height:44px;border-radius:10px;background:{{ $providerMeta['color'] ?? 'var(--primary)' }}18;color:{{ $providerMeta['color'] ?? 'var(--primary)' }};display:flex;align-items:center;justify-content:center;font-size:22px;">
        <i class="{{ $providerMeta['icon'] ?? 'bi-plug' }}"></i>
      </div>
      <div>
        <h1 class="ds-page-title" style="margin:0;">{{ $providerMeta['label'] }}</h1>
        <p class="ds-page-subtitle" style="margin:0;">{{ $providerMeta['description'] }}</p>
      </div>
    </div>
  </div>
  <div style="display:flex;gap:10px;">
    <button id="btnTest" class="ds-btn ds-btn-out" onclick="testConnection()">
      <i class="bi bi-wifi" id="testIcon"></i>
      <span id="testLabel">Test Connection</span>
    </button>
    @if($providerMeta['docs_url'] ?? false)
    <a href="{{ $providerMeta['docs_url'] }}" target="_blank" class="ds-btn ds-btn-ghost">
      <i class="bi bi-box-arrow-up-right"></i> Docs
    </a>
    @endif
  </div>
</div>

{{-- Test Result --}}
<div id="testResult" style="display:none;margin-bottom:20px;" class="ds-alert" data-aos="fade-up"></div>

<div style="max-width:700px;">
  <form method="POST" action="{{ route('admin.integrations.update', $provider) }}" novalidate>
    @csrf @method('PUT')

    {{-- Status --}}
    <div class="ds-card" data-aos="fade-up" data-aos-delay="100" style="margin-bottom:20px;">
      <div class="ds-card-hdr"><span class="ds-card-title">Integration Status</span></div>
      <div style="padding:16px 20px;">
        @php $currentStatus = $fields->first()?->status ?? 'inactive'; @endphp
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
          @foreach(['active'=>['Active','var(--success)','bi-check-circle-fill'],'inactive'=>['Inactive','var(--text-muted)','bi-slash-circle'],'testing'=>['Testing','var(--warning)','bi-bug']] as $s=>[$slabel,$scol,$sicon])
          <label id="status-label-{{ $s }}" onclick="selectStatus('{{ $s }}')"
                 style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:12px 16px;border-radius:8px;border:2px solid {{ $currentStatus===$s ? $scol : 'var(--border)' }};flex:1;min-width:120px;transition:all 0.15s;background:{{ $currentStatus===$s ? 'var(--bg-app)' : 'var(--bg-surface)' }};">
            <input type="radio" name="status" value="{{ $s }}" {{ $currentStatus===$s ? 'checked' : '' }} id="status-{{ $s }}" style="display:none;">
            <i class="{{ $sicon }}" style="color:{{ $scol }};font-size:18px;"></i>
            <span style="font-size:13px;font-weight:600;color:var(--text-main);">{{ $slabel }}</span>
          </label>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Fields --}}
    <div class="ds-card" data-aos="fade-up" data-aos-delay="200" style="margin-bottom:20px;">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Configure {{ $providerMeta['label'] }}</span>
      </div>
      <div style="padding:24px;">
        @foreach($fields as $field)
        <div style="margin-bottom:{{ $loop->last ? '0' : '20px' }};">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
            <label style="font-size:13px;font-weight:500;color:var(--text-main);">
              {{ $field->label }}@if($field->is_required)<span style="color:var(--danger);margin-left:2px;">*</span>@endif
            </label>
            @if($field->is_secret)
              <span style="font-size:10px;background:#FEF2F2;color:#C53030;padding:1px 8px;border-radius:10px;font-weight:700;border:1px solid #FECACA;font-family:monospace;">SECRET</span>
            @endif
          </div>

          @if($field->field_type === 'boolean')
            <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--bg-app);border:1px solid var(--border);border-radius:var(--radius-sm);">
              <input type="hidden" name="fields[{{ $field->key }}]" value="0"/>
              <x-ui.checkbox name="fields[{{ $field->key }}]" value="1" :checked="$field->value == '1'" label="{{ $field->value == '1' ? 'Enabled' : 'Disabled' }}"/>
            </div>
          @elseif($field->field_type === 'select')
            <x-ui.select name="fields[{{ $field->key }}]">
              @foreach($field->field_options ?? [] as $optVal => $optLabel)
                <option value="{{ $optVal }}" {{ $field->value === (string)$optVal ? 'selected' : '' }}>{{ $optLabel }}</option>
              @endforeach
            </x-ui.select>
          @elseif($field->field_type === 'textarea')
            <x-ui.textarea name="fields[{{ $field->key }}]" rows="4">{{ $field->is_secret ? '' : $field->value }}</x-ui.textarea>
          @elseif($field->is_secret)
            <x-ui.input type="password" name="fields[{{ $field->key }}]"
                        :placeholder="!empty($field->getRawOriginal('value')) ? '••••••••  (saved — leave blank to keep)' : 'Enter '.$field->label"
                        autocomplete="new-password"/>
            @if(!empty($field->getRawOriginal('value')))
            <div style="display:flex;align-items:center;gap:6px;margin-top:6px;">
              <x-ui.checkbox name="clear[{{ $field->key }}]" value="1"/>
              <label style="font-size:12px;color:var(--danger);cursor:pointer;">Remove this credential</label>
            </div>
            @endif
          @else
            <x-ui.input type="{{ $field->field_type === 'url' ? 'url' : 'text' }}"
                        name="fields[{{ $field->key }}]"
                        :value="old('fields.'.$field->key, $field->value)"
                        :placeholder="$field->label"
                        :error="$errors->has('fields.'.$field->key)"
                        :required="$field->is_required"/>
          @endif

          @if($field->description)
          <p style="font-size:12px;color:var(--text-muted);margin-top:5px;display:flex;align-items:center;gap:5px;">
            <i class="bi bi-info-circle" style="color:#A0AEC0;"></i> {{ $field->description }}
          </p>
          @endif
        </div>
        @endforeach
      </div>
    </div>

    <div style="display:flex;gap:12px;" class="" data-aos="fade-up" data-aos-delay="300">
      <button type="submit" class="ds-btn ds-btn-pri" style="height:42px;padding:0 24px;font-size:14px;">
        <i class="bi bi-floppy"></i> Save Configuration
      </button>
      <a href="{{ route('admin.integrations.index') }}" class="ds-btn ds-btn-out" style="height:42px;padding:0 20px;">Cancel</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
function selectStatus(val) {
  const colors = { active:'var(--success)', inactive:'var(--text-muted)', testing:'var(--warning)' };
  document.querySelectorAll('[id^="status-label-"]').forEach(el => {
    el.style.borderColor = 'var(--border)';
    el.style.background = 'var(--bg-surface)';
  });
  const lbl = document.getElementById('status-label-' + val);
  lbl.style.borderColor = colors[val] || 'var(--primary)';
  lbl.style.background = 'var(--bg-app)';
  document.getElementById('status-' + val).checked = true;
}

async function testConnection() {
  const btn = document.getElementById('btnTest');
  const icon = document.getElementById('testIcon');
  const label = document.getElementById('testLabel');
  const result = document.getElementById('testResult');

  btn.disabled = true;
  icon.className = 'bi bi-arrow-repeat';
  icon.style.animation = 'spin 0.7s linear infinite';
  label.textContent = 'Testing...';

  try {
    const res = await fetch('{{ route('admin.integrations.test', $provider) }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
        'Accept': 'application/json',
      }
    });
    const data = await res.json();

    result.style.display = 'flex';
    result.className = 'ds-alert fu ' + (data.success ? 'ds-alert-success' : 'ds-alert-danger');
    result.innerHTML = `<i class="bi bi-${data.success ? 'check-circle-fill' : 'x-circle-fill'}"></i> ${data.message}`;
    icon.className = 'bi bi-' + (data.success ? 'check-circle-fill' : 'x-circle-fill');
    icon.style.animation = '';
    label.textContent = data.success ? 'Connected!' : 'Failed';
  } catch(e) {
    result.style.display = 'flex';
    result.className = 'ds-alert fu ds-alert-danger';
    result.innerHTML = '<i class="bi bi-x-circle-fill"></i> Error: ' + e.message;
    icon.style.animation = '';
    label.textContent = 'Error';
  } finally {
    btn.disabled = false;
    setTimeout(() => {
      icon.className = 'bi bi-wifi';
      icon.style.animation = '';
      label.textContent = 'Test Connection';
    }, 5000);
  }
}
</script>
<style>
@keyframes spin { from{transform:rotate(0deg);}to{transform:rotate(360deg);} }
</style>
@endpush

@endsection
