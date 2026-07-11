{{-- admin/users/index.blade.php --}}
@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr">
  <div><h2 class="pg-title">Kelola User</h2><p class="pg-desc">{{ $users->total() }} user terdaftar di sistem</p></div>
  <a href="{{ route('admin.users.create') }}" class="btn-o btn-pri"><i class="bi bi-plus-lg"></i> Tambah User</a>
</div>
<div class="ftabs mb-4 fu">
  <a href="{{ route('admin.users.index') }}" class="ftab {{ !$role?'active':'' }}">Semua</a>
  @foreach($roles as $r)
  <a href="{{ route('admin.users.index',['role'=>$r]) }}" class="ftab {{ $role===$r?'active':'' }}">{{ ucfirst($r) }}</a>
  @endforeach
</div>
<div class="card-ojs fu fd2">
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>User</th><th>Role</th><th>Institusi</th><th>Status</th><th>Bergabung</th><th>Aksi</th></tr></thead>
      <tbody>
        @forelse($users as $user)
        <tr class="{{ $user->trashed()?'opacity-50':'' }}">
          <td>
            <div style="display:flex;align-items:center;gap:10px;">
              <div style="width:32px;height:32px;border-radius:8px;background:var(--acc-l);color:var(--acc);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($user->name,0,1)) }}</div>
              <div><div class="cell-pri">{{ $user->name }}</div><div class="cell-sub">{{ $user->email }}</div></div>
            </div>
          </td>
          <td><span class="bx bx-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
          <td><span class="cell-mute">{{ Str::limit($user->affiliation??'—',28) }}</span></td>
          <td>
            @if($user->trashed())
              <span style="font-size:12px;color:var(--red);font-weight:600;"><i class="bi bi-trash me-1"></i>Dihapus</span>
            @elseif($user->is_active)
              <span style="font-size:12px;color:var(--green);font-weight:600;"><i class="bi bi-circle-fill" style="font-size:7px;"></i> Aktif</span>
            @else
              <span style="font-size:12px;color:var(--txt3);font-weight:600;"><i class="bi bi-circle" style="font-size:7px;"></i> Nonaktif</span>
            @endif
          </td>
          <td><span class="cell-mute">{{ $user->created_at->format('d M Y') }}</span></td>
          <td>
            @if(!$user->trashed())
            <div style="display:flex;gap:5px;align-items:center;">
              <a href="{{ route('admin.users.edit',$user) }}" class="btn-o btn-out btn-sm"><i class="bi bi-pencil"></i></a>
              @if($user->id!==auth()->id())
              <form method="POST" action="{{ route('admin.users.toggle-active',$user) }}">@csrf @method('PATCH')
                <button type="submit" class="btn-o {{ $user->is_active?'btn-ghost':'btn-suc' }} btn-sm" title="{{ $user->is_active?'Nonaktifkan':'Aktifkan' }}">
                  <i class="bi bi-{{ $user->is_active?'pause-circle':'play-circle' }}"></i>
                </button>
              </form>
              <form method="POST" action="{{ route('admin.users.destroy',$user) }}">@csrf @method('DELETE')
                <button type="submit" onclick="return confirm('Hapus user ini?')" class="btn-o btn-danger btn-sm"><i class="bi bi-trash"></i></button>
              </form>
              @endif
            </div>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6"><div class="empty-st"><div class="empty-icon"><i class="bi bi-people"></i></div><div class="empty-title">Tidak ada user</div></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-ftr">{{ $users->withQueryString()->links() }}</div>
</div>
@endsection
