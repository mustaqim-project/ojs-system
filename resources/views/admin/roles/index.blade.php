@extends('layouts.dashboard')
@section('title', 'Manajemen Role')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Role & Permissions']]"/>
    <h1 class="ds-page-title">Manajemen Role</h1>
    <p class="ds-page-subtitle">{{ $roles->count() }} roles terdaftar &middot; permission-driven authorization</p>
  </div>
  <a href="{{ route('admin.roles.create') }}" class="ds-btn ds-btn-pri">
    <i class="bi bi-plus-circle-fill"></i> Tambah Role
  </a>
</div>

<div class="ds-card" data-aos="fade-up" data-aos-delay="150">
  <div class="table-responsive">
    <table class="ds-table">
      <thead>
        <tr>
          <th>Role</th>
          <th>Guard</th>
          <th>Permissions</th>
          <th>Users</th>
          <th>Dibuat</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($roles as $role)
        <tr>
          <td>
            <div style="font-weight:600;font-size:14px;color:var(--text-main);">
              {{ $role->name }}
              @if(in_array($role->name, ['super-admin','system-admin','admin','editor','author','reviewer']))
                <span class="ds-badge" style="background:#EEF2FF;color:#4F46E5;font-size:10px;">inti</span>
              @endif
            </div>
          </td>
          <td style="font-size:12px;color:var(--text-muted);">{{ $role->guard_name }}</td>
          <td>
            <span class="ds-badge ds-badge-neutral">{{ $role->permissions_count ?? $role->permissions->count() }} izin</span>
          </td>
          <td><span class="ds-badge ds-badge-success">{{ $userCounts[$role->id] ?? 0 }} user</span></td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $role->created_at->format('d M Y') }}</td>
          <td>
            <div style="display:flex;gap:6px;">
              <a href="{{ route('admin.roles.edit', $role) }}" class="ds-btn ds-btn-out ds-btn-xs" title="Edit Role">
                <i class="bi bi-pencil"></i>
              </a>
              @if(!in_array($role->name, ['super-admin','system-admin','admin','editor','author','reviewer']))
              <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" class="ds-btn ds-btn-danger ds-btn-xs" onclick="return confirm('Hapus role {{ $role->name }}?')" title="Hapus">
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

@endsection