@extends('layouts.dashboard')
@section('title', 'Kelola Menu Navigasi')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Menu Navigasi']]"/>
    <h1 class="ds-page-title">Kelola Menu Navigasi</h1>
    <p class="ds-page-subtitle">Menu header situs publik + sidebar dashboard (permission-driven)</p>
  </div>
</div>

<div class="row g-4">
  {{-- Create Form --}}
  <div class="col-lg-4" data-aos="fade-up">
    <div class="ds-card">
      <div class="ds-card-hdr">
        <h3 class="ds-section-title" style="margin:0;"><i class="bi bi-plus-circle me-2"></i>Tambah Menu Baru</h3>
      </div>
      <div class="p-3">
        <form action="{{ route('admin.navigations.store') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label class="form-label" style="font-size:13px;font-weight:600;">Judul</label>
            <input type="text" name="title" value="{{ old('title') }}" class="form-control" required placeholder="Mis. Dasbor">
            @error('title')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label" style="font-size:13px;font-weight:600;">URL atau Route</label>
            <input type="text" name="url" value="{{ old('url') }}" class="form-control" placeholder="/about  atau  route::admin.users.index">
            <div class="form-text" style="font-size:11px;">Gunakan <code>route::nama.route</code> untuk menautkan ke named route.</div>
          </div>
          <div class="row g-2">
            <div class="col-6 mb-3">
              <label class="form-label" style="font-size:13px;font-weight:600;">Lokasi</label>
              <select name="location" class="form-select" required>
                <option value="header" {{ old('location') === 'header' ? 'selected' : '' }}>Header (Situs)</option>
                <option value="sidebar" {{ old('location') === 'sidebar' ? 'selected' : '' }}>Sidebar (Dashboard)</option>
              </select>
            </div>
            <div class="col-6 mb-3">
              <label class="form-label" style="font-size:13px;font-weight:600;">Induk (Opsional)</label>
              <select name="parent_id" class="form-select">
                <option value="">— Root —</option>
                @foreach($parents as $parent)
                  <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                    [{{ $parent->location }}] {{ $parent->title }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="row g-2">
            <div class="col-6 mb-3">
              <label class="form-label" style="font-size:13px;font-weight:600;">Urutan</label>
              <input type="number" name="order" value="{{ old('order', 0) }}" class="form-control" min="0">
            </div>
            <div class="col-6 mb-3">
              <label class="form-label" style="font-size:13px;font-weight:600;">Ikon (Bootstrap)</label>
              <input type="text" name="icon" value="{{ old('icon') }}" class="form-control" placeholder="bi-grid-1x2">
            </div>
          </div>
          <div class="row g-2">
            <div class="col-6 mb-3">
              <label class="form-label" style="font-size:13px;font-weight:600;">Route Aktif</label>
              <input type="text" name="route_name" value="{{ old('route_name') }}" class="form-control" placeholder="admin.users">
              <div class="form-text" style="font-size:11px;">Awalan route untuk tanda aktif.</div>
            </div>
            <div class="col-6 mb-3">
              <label class="form-label" style="font-size:13px;font-weight:600;">Permission</label>
              <input type="text" name="permission" value="{{ old('permission') }}" class="form-control" placeholder="users.view">
              <div class="form-text" style="font-size:11px;">Kosongkan = semua user login.</div>
            </div>
          </div>
          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="navActive" checked>
            <label class="form-check-label" for="navActive" style="font-size:13px;">Aktif</label>
          </div>
          <button type="submit" class="ds-btn ds-btn-pri w-100">
            <i class="bi bi-save"></i> Simpan Menu
          </button>
        </form>
      </div>
    </div>
  </div>
{{-- List --}}
  <div class="col-lg-8" data-aos="fade-up" data-aos-delay="150">
    @foreach(['header' => 'Menu Header (Situs Publik)', 'sidebar' => 'Menu Sidebar (Dashboard)'] as $location => $sectionTitle)
      <div class="ds-card mb-4">
        <div class="ds-card-hdr">
          <h3 class="ds-section-title" style="margin:0;"><i class="bi bi-list-ul me-2"></i>{{ $sectionTitle }}</h3>
        </div>
        <div class="p-3">
          @php $sectionMenus = $navigations->where('location', $location); @endphp
          @forelse($sectionMenus as $nav)
            <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2 {{ $nav->is_active ? '' : 'opacity-50' }}">
              <div>
                <strong>{{ $nav->title }}</strong>
                <div style="font-size:11px;color:var(--text-muted);">
                  {{ $nav->url ?: ($nav->route_name ? 'route::' . $nav->route_name : '—') }}
                  @if($nav->permission)
                    <span class="ds-badge ms-1" style="background:var(--primary-light);color:var(--primary);font-size:10px;">{{ $nav->permission }}</span>
                  @endif
                </div>
              </div>
              <div class="d-flex gap-1" style="flex-shrink:0;">
                <a href="{{ route('admin.navigations.edit', $nav) }}" class="ds-btn ds-btn-out ds-btn-xs" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="{{ route('admin.navigations.update', $nav) }}" style="margin:0;">
                  @csrf @method('PUT')
                  <button type="submit" name="is_active" value="{{ $nav->is_active ? 0 : 1 }}" class="ds-btn ds-btn-xs {{ $nav->is_active ? 'ds-btn-ghost' : 'ds-btn-suc' }}" title="{{ $nav->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                    <i class="bi bi-{{ $nav->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                  </button>
                  <input type="hidden" name="title" value="{{ $nav->title }}">
                  <input type="hidden" name="url" value="{{ $nav->url }}">
                  <input type="hidden" name="route_name" value="{{ $nav->route_name }}">
                  <input type="hidden" name="permission" value="{{ $nav->permission }}">
                  <input type="hidden" name="icon" value="{{ $nav->icon }}">
                  <input type="hidden" name="parent_id" value="{{ $nav->parent_id }}">
                  <input type="hidden" name="order" value="{{ $nav->order }}">
                  <input type="hidden" name="location" value="{{ $nav->location }}">
                </form>
                <form method="POST" action="{{ route('admin.navigations.destroy', $nav) }}" style="margin:0;">
                  @csrf @method('DELETE')
                  <button type="submit" class="ds-btn ds-btn-danger ds-btn-xs" onclick="return confirm('Hapus menu ini dan semua sub-menu-nya?')" title="Hapus">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            </div>

            @if($nav->children->count() > 0)
              <div style="margin-left:22px;border-left:2px solid var(--border);padding-left:14px;">
                @foreach($nav->children as $child)
                  <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2 {{ $child->is_active ? '' : 'opacity-50' }}" style="background:var(--bg-app);">
                    <div>
                      <span class="me-1">↳</span><strong>{{ $child->title }}</strong>
                      <div style="font-size:11px;color:var(--text-muted);">
                        {{ $child->url ?: '—' }}
                        @if($child->permission)
                          <span class="ds-badge ms-1" style="background:var(--primary-light);color:var(--primary);font-size:10px;">{{ $child->permission }}</span>
                        @endif
                      </div>
                    </div>
                    <div class="d-flex gap-1" style="flex-shrink:0;">
                      <a href="{{ route('admin.navigations.edit', $child) }}" class="ds-btn ds-btn-out ds-btn-xs" title="Edit">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <form method="POST" action="{{ route('admin.navigations.destroy', $child) }}" style="margin:0;">
                        @csrf @method('DELETE')
                        <button type="submit" class="ds-btn ds-btn-danger ds-btn-xs" onclick="return confirm('Hapus sub-menu ini?')" title="Hapus">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          @empty
            <x-ui.empty-state icon="bi-list-ul" title="Belum ada menu" :description="'Tambahkan menu untuk lokasi ' . $location . ' di form kiri.'"/>
          @endforelse
        </div>
      </div>
    @endforeach
  </div>
</div>

@endsection