@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr">
  <div>
    <div class="pg-crumb">
      <a href="{{ route('admin.integrations.index') }}">Integrasi API</a>
      <span>›</span><span class="cur">{{ $providerMeta['label'] }}</span>
    </div>
    <div style="display:flex;align-items:center;gap:12px;margin-top:6px;">
      <div style="width:40px;height:40px;border-radius:10px;background:{{ $providerMeta['color'] ?? '#2563eb' }}18;color:{{ $providerMeta['color'] ?? '#2563eb' }};display:flex;align-items:center;justify-content:center;font-size:20px;">
        <i class="{{ $providerMeta['icon'] ?? 'bi-plug' }}"></i>
      </div>
      <div>
        <h2 class="pg-title" style="margin:0;">{{ $providerMeta['label'] }}</h2>
        <p class="pg-desc" style="margin:0;">{{ $providerMeta['description'] }}</p>
      </div>
    </div>
  </div>
  <div class="d-flex gap-2 align-items-center">
    {{-- Test Koneksi --}}
    <button id="btnTest" class="btn-o btn-out" onclick="testConnection()">
      <i class="bi bi-wifi" id="testIcon"></i>
      <span id="testLabel">Test Koneksi</span>
    </button>
    @if($providerMeta['docs_url'] ?? false)
    <a href="{{ $providerMeta['docs_url'] }}" target="_blank" class="btn-o btn-ghost">
      <i class="bi bi-box-arrow-up-right"></i> Docs
    </a>
    @endif
  </div>
</div>

{{-- Test Result Alert --}}
<div id="testResult" style="display:none;" class="alert-o fu mb-3"></div>

<div style="max-width:680px;">
  <form method="POST" action="{{ route('admin.integrations.update', $provider) }}">
    @csrf
    @method('PUT')

    {{-- Status Provider --}}
    <div class="card-ojs fu fd1 mb-3">
      <div class="card-hdr">
        <span class="card-title">Status Integrasi</span>
      </div>
      <div style="padding:16px 20px;">
        <div style="display:flex;align-items:center;gap:16px;">
          @php
            $currentStatus = $fields->first()?->status ?? 'inactive';
          @endphp
          @foreach(['active' => ['Aktif','green','bi-check-circle-fill'], 'inactive' => ['Nonaktif','gray','bi-slash-circle'], 'testing' => ['Testing','yellow','bi-bug']] as $s => [$slabel, $scolor, $sicon])
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 14px;border-radius:8px;border:2px solid {{ $currentStatus===$s ? 'var(--acc)' : 'var(--brd)' }};flex:1;transition:all .15s;"
                 id="status-label-{{ $s }}" onclick="selectStatus('{{ $s }}')">
            <input type="radio" name="status" value="{{ $s }}" {{ $currentStatus===$s ? 'checked' : '' }} style="display:none;" id="status-{{ $s }}">
            <i class="{{ $sicon }}" style="color:{{ $s==='active'?'var(--green)':($s==='testing'?'#d97706':'var(--txt3)') }};font-size:18px;"></i>
            <span style="font-size:13px;font-weight:600;color:#0f172a;">{{ $slabel }}</span>
          </label>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Fields --}}
    <div class="card-ojs fu fd2">
      <div class="card-hdr">
        <span class="card-title">Konfigurasi {{ $providerMeta['label'] }}</span>
      </div>
      <div style="padding:20px;">
        @foreach($fields as $field)
        <div class="f-group" style="{{ $loop->last ? 'margin-bottom:0;' : '' }}">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
            <label class="lbl" style="margin:0;">
              {{ $field->label }}
              @if($field->is_required)<span class="req">*</span>@endif
            </label>
            <div style="display:flex;gap:6px;align-items:center;">
              @if($field->is_secret)
              <span style="font-size:10px;background:#fef2f2;color:#dc2626;padding:1px 7px;border-radius:10px;font-weight:700;border:1px solid #fecaca;">SECRET</span>
              @endif
              @if($field->field_type === 'boolean')
              <span style="font-size:10px;color:#94a3b8;">Toggle</span>
              @endif
            </div>
          </div>

          @if($field->field_type === 'boolean')
            {{-- Toggle switch --}}
            <div style="display:flex;align-items:center;gap:12px;">
              <label class="toggle-switch" style="position:relative;display:inline-block;width:44px;height:24px;">
                <input type="checkbox" name="fields[{{ $field->key }}]" value="1"
                  {{ $field->value == '1' ? 'checked' : '' }}
                  style="opacity:0;width:0;height:0;">
                <span class="toggle-slider" style="position:absolute;cursor:pointer;inset:0;background:#cbd5e1;border-radius:24px;transition:.3s;">
                  <span style="position:absolute;content:'';height:18px;width:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s;"></span>
                </span>
              </label>
              <span style="font-size:12px;color:#64748b;">{{ $field->value == '1' ? 'Aktif' : 'Nonaktif' }}</span>
            </div>

          @elseif($field->field_type === 'select')
            <select name="fields[{{ $field->key }}]" class="sel">
              @foreach($field->field_options ?? [] as $optVal => $optLabel)
              <option value="{{ $optVal }}" {{ $field->value === (string)$optVal ? 'selected' : '' }}>{{ $optLabel }}</option>
              @endforeach
            </select>

          @elseif($field->field_type === 'textarea')
            <textarea name="fields[{{ $field->key }}]" class="txta" rows="4">{{ $field->is_secret ? '' : $field->value }}</textarea>

          @elseif($field->is_secret)
            {{-- Password field — tampilkan placeholder jika sudah ada nilai --}}
            <div style="position:relative;">
              <input type="password" name="fields[{{ $field->key }}]" class="inp"
                     placeholder="{{ !empty($field->getRawOriginal('value')) ? '••••••••  (sudah tersimpan — kosongkan untuk tidak mengubah)' : 'Masukkan ' . $field->label }}"
                     autocomplete="new-password"/>
              @if(!empty($field->getRawOriginal('value')))
              <div style="display:flex;align-items:center;gap:6px;margin-top:6px;">
                <input type="checkbox" name="clear[{{ $field->key }}]" id="clear_{{ $field->key }}" value="1">
                <label for="clear_{{ $field->key }}" style="font-size:11px;color:#dc2626;cursor:pointer;">Hapus credential ini</label>
              </div>
              @endif
            </div>

          @else
            <input type="{{ $field->field_type === 'url' ? 'url' : 'text' }}"
                   name="fields[{{ $field->key }}]"
                   class="inp {{ $errors->has('fields.'.$field->key) ? 'is-invalid' : '' }}"
                   value="{{ old('fields.'.$field->key, $field->value) }}"
                   placeholder="{{ $field->label }}"
                   {{ $field->is_required ? 'required' : '' }}/>
          @endif

          @if($field->description)
          <div class="f-hint-txt" style="margin-top:5px;">
            <i class="bi bi-info-circle me-1" style="color:#94a3b8;"></i>{{ $field->description }}
          </div>
          @endif
        </div>
        @endforeach
      </div>
    </div>

    <div class="d-flex gap-3 mt-3 fu fd3">
      <button type="submit" class="btn-o btn-pri btn-lg">
        <i class="bi bi-floppy"></i> Simpan Konfigurasi
      </button>
      <a href="{{ route('admin.integrations.index') }}" class="btn-o btn-out btn-lg">Batal</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
// Radio status selector
function selectStatus(val) {
  document.querySelectorAll('[id^="status-label-"]').forEach(el => {
    el.style.borderColor = 'var(--brd)';
  });
  document.getElementById('status-label-' + val).style.borderColor = 'var(--acc)';
  document.getElementById('status-' + val).checked = true;
}

// Test Connection
async function testConnection() {
  const btn   = document.getElementById('btnTest');
  const icon  = document.getElementById('testIcon');
  const label = document.getElementById('testLabel');
  const result= document.getElementById('testResult');

  btn.disabled = true;
  icon.className = 'bi bi-arrow-repeat spin';
  label.textContent = 'Testing...';

  try {
    const res  = await fetch('{{ route('admin.integrations.test', $provider) }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
        'Accept': 'application/json',
      }
    });
    const data = await res.json();

    result.style.display = 'flex';
    result.className = 'alert-o fu mb-3 ' + (data.success ? 'a-suc' : 'a-err');
    result.innerHTML = `<i class="bi bi-${data.success ? 'check-circle-fill' : 'x-circle-fill'}"></i> ${data.message}`;

    icon.className = 'bi bi-' + (data.success ? 'check-circle-fill' : 'x-circle-fill');
    label.textContent = data.success ? 'Berhasil!' : 'Gagal';
  } catch(e) {
    result.style.display = 'flex';
    result.className = 'alert-o fu mb-3 a-err';
    result.innerHTML = '<i class="bi bi-x-circle-fill"></i> Error: ' + e.message;
    icon.className = 'bi bi-x-circle';
    label.textContent = 'Error';
  } finally {
    btn.disabled = false;
    setTimeout(() => {
      icon.className = 'bi bi-wifi';
      label.textContent = 'Test Koneksi';
    }, 4000);
  }
}
</script>
<style>
.spin { animation: spin .7s linear infinite; display:inline-block; }
@keyframes spin { from { transform:rotate(0deg); } to { transform:rotate(360deg); } }

.toggle-switch input:checked + .toggle-slider { background: var(--acc); }
.toggle-switch input:checked + .toggle-slider span { transform: translateX(20px); }
.toggle-slider span {
  position:absolute;height:18px;width:18px;left:3px;bottom:3px;
  background:#fff;border-radius:50%;transition:.3s;
}
</style>
@endpush
@endsection
