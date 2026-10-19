@extends('layouts.dashboard')
@section('title', 'Manajemen Permission')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Role & Permissions'],['label'=>'Permissions']]"/>
    <h1 class="ds-page-title">Manajemen Permission</h1>
    <p class="ds-page-subtitle">{{ $permissions->count() }} permissions terdaftar &middot; format <code>resource.action</code></p>
  </div>
  <a href="{{ route('admin.permissions.create') }}" class="ds-btn ds-btn-pri">
    <i class="bi bi-plus-circle-fill"></i> Tambah Permission
  </a>
</div>

@foreach($grouped as $module => $modulePermissions)
  <div class="ds-card mb-4" data-aos="fade-up" data-aos-delay="100">
    <div class="ds-card-hdr">
      <h3 class="ds-section-title" style="margin:0;text-transform:capitalize;">
        <i class="bi bi-collection me-2"></i>{{ $module === '' ? 'Misc' : $module }}
        <span class="ds-badge ds-badge-neutral ms-2">{{ $modulePermissions->count() }}</span>
      </h3>
    </div>
    <div class="table-responsive">
      <table class="ds-table">
        <thead>
          <tr>
            <th>Permission</th>
            <th>Guard</th>
            <th>Digunakan Role</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($modulePermissions as $permission)
          <tr>
            <td>
              <span style="font-weight:600;font-size:13px;">{{ $permission->name }}</span>
              @if(in_array($permission->name, ['dashboard.view','users.view','roles.view','permissions.view','menus.view']))
                <span class="ds-badge" style="background:#EEF2FF;color:#4F46E5;font-size:10px;">panel</span>
              @endif
            </td>
            <td style="font-size:12px;color:var(--text-muted);">{{ $permission->guard_name }}</td>
            <td><span class="ds-badge ds-badge-neutral">{{ $permission->roles_count }} role</span></td>
            <td>
              <div style="display:flex;gap:6px;">
                <a href="{{ route('admin.permissions.edit', $permission) }}" class="ds-btn ds-btn-out ds-btn-xs" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
                @if($permission->roles_count === 0)
                <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}" style="margin:0;">
                  @csrf @method('DELETE')
                  <button type="submit" class="ds-btn ds-btn-danger ds-btn-xs" onclick="return confirm('Hapus permission {{ $permission->name }}?')" title="Hapus">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endforeach

@endsection