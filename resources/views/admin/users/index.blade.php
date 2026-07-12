{{-- admin/users/index.blade.php --}}
@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'User Management']]"/>
    <h1 class="ds-page-title">User Management</h1>
    <p class="ds-page-subtitle">{{ $users->total() }} users registered in the system</p>
  </div>
  <a href="{{ route('admin.users.create') }}" class="ds-btn ds-btn-pri">
    <i class="bi bi-person-plus-fill"></i> Add User
  </a>
</div>

{{-- Role Tabs --}}
<div class="ds-ftabs mb-4" data-aos="fade-up">
  <a href="{{ route('admin.users.index') }}" class="ds-ftab {{ !$role ? 'active' : '' }}">
    All <span style="margin-left:4px;font-size:11px;opacity:0.7;">({{ $users->total() }})</span>
  </a>
  @foreach($roles as $r)
  <a href="{{ route('admin.users.index',['role'=>$r]) }}" class="ds-ftab {{ $role === $r ? 'active' : '' }}">
    {{ ucfirst($r) }}
  </a>
  @endforeach
</div>

{{-- Table Card --}}
<div class="ds-card" data-aos="fade-up" data-aos-delay="200">
  <div class="table-responsive">
    <table class="ds-table">
      <thead>
        <tr>
          <th>User</th>
          <th>Role</th>
          <th>Institution</th>
          <th>ORCID</th>
          <th>Status</th>
          <th>Joined</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr style="{{ $user->trashed() ? 'opacity:0.5;' : '' }}">
          <td>
            <div style="display:flex;align-items:center;gap:12px;">
              <div style="width:34px;height:34px;border-radius:8px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;">
                {{ strtoupper(substr($user->name,0,1)) }}
              </div>
              <div>
                <div style="font-weight:600;font-size:14px;color:var(--text-main);">{{ $user->name }}</div>
                <div style="font-size:12px;color:var(--text-muted);">{{ $user->email }}</div>
              </div>
            </div>
          </td>
          <td><x-status-badge :status="$user->role" :label="ucfirst($user->role)"/></td>
          <td style="font-size:13px;color:var(--text-muted);">{{ Str::limit($user->affiliation ?? '—', 30) }}</td>
          <td><x-orcid-badge :orcid="$user->orcid"/></td>
          <td>
            @if($user->trashed())
              <span class="ds-badge" style="background:#FEF2F2;color:#C53030;"><i class="bi bi-trash" style="font-size:11px;"></i> Deleted</span>
            @elseif($user->is_active)
              <span class="ds-badge ds-badge-success">Active</span>
            @else
              <span class="ds-badge ds-badge-neutral">Inactive</span>
            @endif
          </td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $user->created_at->format('d M Y') }}</td>
          <td>
            @if(!$user->trashed())
            <div style="display:flex;gap:6px;">
              <a href="{{ route('admin.users.edit',$user) }}" class="ds-btn ds-btn-out ds-btn-xs" title="Edit User">
                <i class="bi bi-pencil"></i>
              </a>
              @if($user->id !== auth()->id())
              <form method="POST" action="{{ route('admin.users.toggle-active',$user) }}" style="margin:0;">
                @csrf @method('PATCH')
                <button type="submit" class="ds-btn ds-btn-xs {{ $user->is_active ? 'ds-btn-ghost' : 'ds-btn-suc' }}"
                        title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                  <i class="bi bi-{{ $user->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                </button>
              </form>
              <form method="POST" action="{{ route('admin.users.destroy',$user) }}" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" class="ds-btn ds-btn-danger ds-btn-xs"
                        onclick="return confirm('Permanently delete this user?')" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
              @endif
            </div>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7">
            <x-ui.empty-state icon="bi-people" title="No users found" description="Add the first user to get started.">
              <a href="{{ route('admin.users.create') }}" class="ds-btn ds-btn-pri" style="display:inline-flex;">
                <i class="bi bi-person-plus-fill"></i> Add User
              </a>
            </x-ui.empty-state>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($users->hasPages())
  <div style="padding:16px 24px;border-top:1px solid var(--border);background:var(--bg-app);">
    {{ $users->withQueryString()->links() }}
  </div>
  @endif
</div>

@endsection
