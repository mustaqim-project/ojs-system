@php
    // Picker permission dikelompokkan per modul (prefix sebelum titik pertama).
    $checked = is_array($selected ?? null) ? $selected : [];
@endphp

<div data-aos="fade-up" data-aos-delay="150">
  <div class="ds-card">
    <div class="ds-card-hdr">
      <h3 class="ds-section-title" style="margin:0;"><i class="bi bi-shield-lock me-2"></i>Permissions</h3>
    </div>
    <div class="p-4">
      @forelse($permissions as $module => $modulePermissions)
        <div class="mb-4">
          <h4 style="font-size:13px;font-weight:700;text-transform:capitalize;color:var(--primary);margin-bottom:8px;">
            <i class="bi bi-collection me-1"></i>{{ $module === '' ? 'Misc' : $module }}
          </h4>
          <div class="d-flex flex-wrap gap-2">
            @foreach($modulePermissions as $permission)
              <label class="border rounded px-2 py-1" style="font-size:12px;cursor:pointer;background:var(--bg-app);display:inline-flex;align-items:center;gap:6px;">
                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                  {{ in_array($permission->name, $checked) ? 'checked' : '' }}>
                {{ $permission->name }}
              </label>
            @endforeach
          </div>
        </div>
      @empty
        <x-ui.empty-state icon="bi-shield-lock" title="Belum ada permission" description="Seed RolePermissionSeeder terlebih dahulu."/>
      @endforelse
      <div class="form-text" style="font-size:11px;">Hanya permission yang tercentang yang diberikan ke role ini.</div>
    </div>
  </div>
</div>