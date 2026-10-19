@extends('layouts.dashboard')
@section('title', 'Manajemen Role & Hak Akses')
@section('content')

{{-- 1. PAGE HEADER / TOOLBAR --}}
<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'], ['label'=>'Role & Permissions']]"/>
    <h1 class="ds-page-title">Manajemen Role & Hak Akses</h1>
    <p class="ds-page-subtitle">Sistem RBAC granular dengan ruang lingkup khusus untuk Penulis (Author), Redaksi, dan Administrator</p>
  </div>
  <div style="display: flex; gap: 10px; align-items: center;">
    <a href="{{ route('admin.roles.create') }}" class="ds-btn ds-btn-pri" style="height: 38px; font-size: 13px;">
      <i class="bi bi-plus-circle-fill me-1"></i> Tambah Role Kustom
    </a>
  </div>
</div>

{{-- 2. KPI SUMMARY (Apple Material Standard) --}}
<div class="row g-3 mb-4" data-aos="fade-up">
  {{-- Card 1: Total Role --}}
  <div class="col-6 col-lg-3">
    <div style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 14px; padding: 18px; height: 100%; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
      <span style="font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Total Role</span>
      <div style="display: flex; align-items: baseline; justify-content: space-between; margin-top: 8px;">
        <span style="font-size: 26px; font-weight: 800; color: var(--text-main); font-variant-numeric: tabular-nums;">{{ $roles->count() }}</span>
        <span style="font-size: 11px; font-weight: 600; color: #4F46E5; background: rgba(79, 70, 229, 0.1); padding: 2px 8px; border-radius: 12px;">Inti & Kustom</span>
      </div>
    </div>
  </div>

  {{-- Card 2: Total Pengguna --}}
  <div class="col-6 col-lg-3">
    <div style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 14px; padding: 18px; height: 100%; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
      <span style="font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Pengguna Terdaftar</span>
      <div style="display: flex; align-items: baseline; justify-content: space-between; margin-top: 8px;">
        <span style="font-size: 26px; font-weight: 800; color: #10B981; font-variant-numeric: tabular-nums;">{{ $totalUsers }}</span>
        <span style="font-size: 11px; font-weight: 600; color: #10B981; background: rgba(16, 185, 129, 0.1); padding: 2px 8px; border-radius: 12px;">Aktif</span>
      </div>
    </div>
  </div>

  {{-- Card 3: Cakupan Permissions --}}
  <div class="col-6 col-lg-3">
    <div style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 14px; padding: 18px; height: 100%; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
      <span style="font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Cakupan Izin</span>
      <div style="display: flex; align-items: baseline; justify-content: space-between; margin-top: 8px;">
        <span style="font-size: 26px; font-weight: 800; color: #6366F1; font-variant-numeric: tabular-nums;">{{ $totalPermissions }}</span>
        <span style="font-size: 11px; font-weight: 600; color: #6366F1; background: rgba(99, 102, 241, 0.1); padding: 2px 8px; border-radius: 12px;">Hak Akses Granular</span>
      </div>
    </div>
  </div>

  {{-- Card 4: Keamanan & Isolasi Author --}}
  <div class="col-6 col-lg-3">
    <div style="background: var(--bg-surface); border: 1px solid var(--border); border-radius: 14px; padding: 18px; height: 100%; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
      <span style="font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Author Protection</span>
      <div style="display: flex; align-items: baseline; justify-content: space-between; margin-top: 8px;">
        <span style="font-size: 17px; font-weight: 800; color: #0284C7;">Terisolasi</span>
        <span style="font-size: 11px; font-weight: 600; color: #0284C7; background: rgba(2, 132, 199, 0.1); padding: 2px 8px; border-radius: 12px;">Khusus Author</span>
      </div>
    </div>
  </div>
</div>

{{-- 3. SPOTLIGHT KHUSUS: PERAN AUTHOR TERSENDIRI --}}
<div class="mb-4" data-aos="fade-up" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.06), rgba(2, 132, 199, 0.06)); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 14px; padding: 18px 22px;">
  <div style="display: flex; align-items: flex-start; gap: 14px;">
    <div style="width: 42px; height: 42px; border-radius: 10px; background: #10B981; color: white; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);">
      <i class="bi bi-pen-fill"></i>
    </div>
    <div style="flex: 1;">
      <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
        <h4 style="font-size: 15px; font-weight: 700; color: var(--text-main); margin: 0;">Ruang Lingkup Khusus Peran Penulis (Author)</h4>
        <span class="badge rounded-pill" style="background: #10B981; font-size: 10px; font-weight: 700; padding: 4px 10px;">Terisolasi dari Redaksi</span>
      </div>
      <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5; margin: 6px 0 0;">
        Penulis (Author) memiliki hak akses mandiri yang <strong>berdiri sendiri secara terpisah</strong> dari dewan editorial. Penulis hanya dapat mengirim naskah baru, merevisi naskah miliknya sendiri, melihat status peninjauan, dan mengunggah bukti pembayaran APC. Penulis <strong>tidak memiliki akses</strong> ke keputusan redaksi, penilaian rahasia reviewer, penugasan editor, atau modul manajemen jurnal lainnya.
      </p>
    </div>
  </div>
</div>

{{-- 4. SEGMENTED NAVIGATION TABS (Cooca Style) --}}
<div class="ds-card" data-aos="fade-up">
  <div class="ds-card-hdr p-0 border-bottom" style="background: var(--bg-surface);">
    <ul class="nav nav-tabs border-0" id="rbacTabs" role="tablist" style="padding-top: 14px; padding-left: 20px;">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-roles" data-bs-toggle="tab" data-bs-target="#pane-roles" type="button" role="tab" style="font-weight: 600; font-size: 14px; padding: 12px 20px; color: var(--text-main); border: none; border-bottom: 3px solid var(--primary); border-radius: 0; background: transparent;" onclick="setActiveTab(this)">
          <i class="bi bi-shield-check me-2" style="color: var(--primary);"></i> Daftar Role <span class="badge rounded-pill bg-primary ms-1" style="font-size: 11px;">{{ $roles->count() }}</span>
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-matrix" data-bs-toggle="tab" data-bs-target="#pane-matrix" type="button" role="tab" style="font-weight: 600; font-size: 14px; padding: 12px 20px; color: var(--text-main); border: none; border-bottom: 3px solid transparent; border-radius: 0; background: transparent;" onclick="setActiveTab(this)">
          <i class="bi bi-table me-2" style="color: #AF52DE;"></i> Matriks Hak Akses (Matrix)
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-members" data-bs-toggle="tab" data-bs-target="#pane-members" type="button" role="tab" style="font-weight: 600; font-size: 14px; padding: 12px 20px; color: var(--text-main); border: none; border-bottom: 3px solid transparent; border-radius: 0; background: transparent;" onclick="setActiveTab(this)">
          <i class="bi bi-people me-2" style="color: #10B981;"></i> Penugasan User ({{ $totalUsers }})
        </button>
      </li>
    </ul>
  </div>

  <div class="tab-content" id="rbacTabsContent">
    {{-- TAB 1: DAFTAR ROLE CARDS --}}
    <div class="tab-pane fade show active p-4" id="pane-roles" role="tabpanel">
      <div class="row g-3">
        @foreach($roles as $role)
          @php
            $isCore = in_array($role->name, ['super-admin', 'system-admin', 'admin', 'editor', 'author', 'reviewer']);
            $isAuthor = ($role->name === 'author');
            $userCount = $userCounts[$role->id] ?? ($legacyCounts[$role->name] ?? 0);
            $perms = $role->permissions;
          @endphp
          <div class="col-12 col-md-6 col-xl-4">
            <div style="background: var(--bg-app); border: 1px solid {{ $isAuthor ? 'rgba(16, 185, 129, 0.4)' : 'var(--border)' }}; border-radius: 12px; padding: 18px; height: 100%; display: flex; flex-direction: column; justify-content: space-between; position: relative;">
              <div>
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; margin-bottom: 10px;">
                  <div>
                    <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 6px;">
                      @if($isAuthor)
                        <i class="bi bi-pen-fill" style="color: #10B981; font-size: 15px;"></i>
                      @elseif($role->name === 'admin' || $role->name === 'super-admin')
                        <i class="bi bi-shield-shaded" style="color: var(--primary); font-size: 15px;"></i>
                      @elseif($role->name === 'editor')
                        <i class="bi bi-journal-check" style="color: #6366F1; font-size: 15px;"></i>
                      @elseif($role->name === 'reviewer')
                        <i class="bi bi-clipboard-check" style="color: #8B5CF6; font-size: 15px;"></i>
                      @else
                        <i class="bi bi-person-badge" style="color: var(--text-muted); font-size: 15px;"></i>
                      @endif
                      {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                    </h3>
                    <span style="font-size: 12px; color: var(--text-muted);">Guard: <code>{{ $role->guard_name }}</code></span>
                  </div>
                  <div>
                    @if($isAuthor)
                      <span class="ds-badge" style="background: rgba(16, 185, 129, 0.12); color: #10B981; font-weight: 700;">Khusus Penulis</span>
                    @elseif($isCore)
                      <span class="ds-badge" style="background: #EEF2FF; color: #4F46E5;">Role Inti</span>
                    @else
                      <span class="ds-badge ds-badge-neutral">Role Kustom</span>
                    @endif
                  </div>
                </div>

                {{-- Role Stats --}}
                <div style="display: flex; gap: 12px; font-size: 12px; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                  <span style="color: var(--text-main); font-weight: 600;">
                    <i class="bi bi-people me-1" style="color: #10B981;"></i> {{ $userCount }} Pengguna
                  </span>
                  <span style="color: var(--text-main); font-weight: 600;">
                    <i class="bi bi-key me-1" style="color: #6366F1;"></i> {{ $perms->count() }} Izin Terpasang
                  </span>
                </div>

                {{-- Granular Permissions Preview --}}
                <div style="margin-bottom: 16px;">
                  <span style="font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 6px;">Hak Akses Terpilih:</span>
                  <div style="display: flex; flex-wrap: wrap; gap: 4px; max-height: 85px; overflow-y: auto;">
                    @forelse($perms->take(6) as $p)
                      <span style="font-size: 11px; padding: 2px 7px; border-radius: 6px; background: var(--bg-surface); border: 1px solid var(--border); color: var(--text-main);">
                        {{ $p->name }}
                      </span>
                    @empty
                      <span style="font-size: 12px; color: var(--text-muted); font-style: italic;">Belum ada permission khusus yang di-assign.</span>
                    @endforelse
                    @if($perms->count() > 6)
                      <span style="font-size: 11px; padding: 2px 7px; border-radius: 6px; background: rgba(99, 102, 241, 0.1); color: #6366F1; font-weight: 600;">
                        +{{ $perms->count() - 6 }} lainnya
                      </span>
                    @endif
                  </div>
                </div>
              </div>

              {{-- Actions --}}
              <div style="display: flex; justify-content: flex-end; gap: 6px; padding-top: 10px; border-top: 1px solid var(--border);">
                <a href="{{ route('admin.roles.edit', $role) }}" class="ds-btn ds-btn-sec ds-btn-xs" title="Edit Permissions">
                  <i class="bi bi-pencil me-1"></i> Edit Izin
                </a>
                @if(!$isCore)
                  <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" style="margin:0;">
                    @csrf @method('DELETE')
                    <button type="submit" class="ds-btn ds-btn-danger ds-btn-xs" onclick="return confirm('Hapus role kustom {{ $role->name }}?')" title="Hapus Role">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- TAB 2: MATRIKS HAK AKSES (Cooca Permission Matrix Style) --}}
    <div class="tab-pane fade p-0" id="pane-matrix" role="tabpanel">
      <div style="padding: 16px 24px; background: var(--bg-surface); border-bottom: 1px solid var(--border);">
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">
          Tabel transparansi hak akses antar modul sistem. Membandingkan izin antara Administrator, Editor, Reviewer, dan <strong>Author (Penulis Mandiri)</strong>.
        </p>
      </div>

      <div class="table-responsive">
        <table class="ds-table m-0">
          <thead>
            <tr style="background: var(--bg-app);">
              <th style="min-width: 280px; width: 40%;">Modul & Hak Akses (Permission)</th>
              <th style="text-align: center; width: 15%;">Admin</th>
              <th style="text-align: center; width: 15%;">Editor</th>
              <th style="text-align: center; width: 15%;">Reviewer</th>
              <th style="text-align: center; width: 15%; background: rgba(16, 185, 129, 0.08); border-left: 1px solid rgba(16, 185, 129, 0.2); border-right: 1px solid rgba(16, 185, 129, 0.2);">
                <span style="color: #10B981; font-weight: 700;"><i class="bi bi-pen-fill me-1"></i> Author (Penulis)</span>
              </th>
            </tr>
          </thead>
          <tbody>
            @php
              $adminRole = $roles->firstWhere('name', 'admin');
              $editorRole = $roles->firstWhere('name', 'editor');
              $reviewerRole = $roles->firstWhere('name', 'reviewer');
              $authorRole = $roles->firstWhere('name', 'author');

              $adminPerms = $adminRole ? $adminRole->permissions->pluck('name')->flip()->toArray() : [];
              $editorPerms = $editorRole ? $editorRole->permissions->pluck('name')->flip()->toArray() : [];
              $reviewerPerms = $reviewerRole ? $reviewerRole->permissions->pluck('name')->flip()->toArray() : [];
              $authorPerms = $authorRole ? $authorRole->permissions->pluck('name')->flip()->toArray() : [];
            @endphp

            @foreach($permissionsByCategory as $categoryName => $perms)
              <tr style="background: rgba(0,0,0,0.02);">
                <td colspan="5" style="font-size: 12px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px; padding: 10px 24px;">
                  <i class="bi bi-folder2-open me-1"></i> {{ $categoryName }} ({{ $perms->count() }})
                </td>
              </tr>
              @foreach($perms as $p)
                @php
                  // Administrator bypasses via Gate::before in AppServiceProvider
                  $hasAdmin = isset($adminPerms[$p->name]) || true;
                  $hasEditor = isset($editorPerms[$p->name]);
                  $hasReviewer = isset($reviewerPerms[$p->name]);
                  $hasAuthor = isset($authorPerms[$p->name]);
                @endphp
                <tr>
                  <td style="padding-left: 32px;">
                    <div style="font-size: 13px; font-weight: 600; color: var(--text-main);">{{ $p->name }}</div>
                  </td>
                  <td style="text-align: center;">
                    <span style="color: #10B981; font-size: 16px; font-weight: bold;"><i class="bi bi-check-circle-fill"></i></span>
                  </td>
                  <td style="text-align: center;">
                    @if($hasEditor)
                      <span style="color: #10B981; font-size: 16px; font-weight: bold;"><i class="bi bi-check-circle-fill"></i></span>
                    @else
                      <span style="color: var(--text-muted); opacity: 0.4;">—</span>
                    @endif
                  </td>
                  <td style="text-align: center;">
                    @if($hasReviewer)
                      <span style="color: #10B981; font-size: 16px; font-weight: bold;"><i class="bi bi-check-circle-fill"></i></span>
                    @else
                      <span style="color: var(--text-muted); opacity: 0.4;">—</span>
                    @endif
                  </td>
                  <td style="text-align: center; background: rgba(16, 185, 129, 0.04); border-left: 1px solid rgba(16, 185, 129, 0.2); border-right: 1px solid rgba(16, 185, 129, 0.2);">
                    @if($hasAuthor)
                      <span style="color: #10B981; font-size: 16px; font-weight: bold;"><i class="bi bi-check-circle-fill"></i></span>
                    @else
                      <span style="color: var(--text-muted); opacity: 0.3;">—</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    {{-- TAB 3: PENUGASAN PENGGUNA (Members) --}}
    <div class="tab-pane fade p-0" id="pane-members" role="tabpanel">
      <div style="padding: 16px 24px; background: var(--bg-surface); border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
        <p style="font-size: 13px; color: var(--text-muted); margin: 0;">
          Daftar seluruh pengguna beserta role yang ditetapkan.
        </p>
        <a href="{{ route('admin.users.create') }}" class="ds-btn ds-btn-pri ds-btn-xs">
          <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna Baru
        </a>
      </div>

      <div class="table-responsive">
        <table class="ds-table m-0">
          <thead>
            <tr>
              <th>Pengguna</th>
              <th>Peran Utama</th>
              <th>Role Spatie</th>
              <th>Status</th>
              <th>Bergabung</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($members as $m)
              @php
                $isAuthor = ($m->role === 'author' || $m->hasRole('author'));
              @endphp
              <tr>
                <td>
                  <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:34px; height:34px; border-radius:8px; background: {{ $isAuthor ? 'rgba(16, 185, 129, 0.15)' : 'var(--primary-light)' }}; color: {{ $isAuthor ? '#10B981' : 'var(--primary)' }}; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700;">
                      {{ strtoupper(substr($m->name, 0, 1)) }}
                    </div>
                    <div>
                      <div style="font-weight:600; font-size:14px; color:var(--text-main);">{{ $m->name }}</div>
                      <div style="font-size:12px; color:var(--text-muted);">{{ $m->email }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="ds-badge" style="background: {{ $isAuthor ? 'rgba(16, 185, 129, 0.12)' : '#EEF2FF' }}; color: {{ $isAuthor ? '#10B981' : '#4F46E5' }}; font-weight: 600;">
                    {{ ucfirst($m->role) }}
                  </span>
                </td>
                <td>
                  <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                    @forelse($m->roles as $r)
                      <span class="badge rounded-pill bg-light text-dark border" style="font-size: 11px;">
                        {{ $r->name }}
                      </span>
                    @empty
                      <span style="font-size: 12px; color: var(--text-muted);">Legacy ({{ $m->role }})</span>
                    @endforelse
                  </div>
                </td>
                <td>
                  @if($m->is_active)
                    <span class="ds-badge ds-badge-success">Aktif</span>
                  @else
                    <span class="ds-badge ds-badge-neutral">Nonaktif</span>
                  @endif
                </td>
                <td style="font-size: 13px; color: var(--text-muted);">
                  {{ $m->created_at ? $m->created_at->format('d M Y') : '—' }}
                </td>
                <td>
                  <a href="{{ route('admin.users.edit', $m) }}" class="ds-btn ds-btn-sec ds-btn-xs" title="Ubah Role & Profil">
                    <i class="bi bi-pencil me-1"></i> Edit User
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div style="padding: 16px 24px; border-top: 1px solid var(--border);">
        {{ $members->links() }}
      </div>
    </div>
  </div>
</div>

<script>
function setActiveTab(button) {
  document.querySelectorAll('#rbacTabs .nav-link').forEach(el => el.style.borderBottomColor = 'transparent');
  button.style.borderBottomColor = 'var(--primary)';
}
</script>

@endsection