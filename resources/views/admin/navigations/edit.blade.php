@extends('layouts.dashboard')
@section('title', 'Edit Menu Navigasi')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Menu Navigasi','href'=>route('admin.navigations.index')],['label'=>'Edit: '.$navigation->title]]"/>
    <h1 class="ds-page-title">Edit Menu Navigasi</h1>
    <p class="ds-page-subtitle">Lokasi: {{ $navigation->location }} &middot; Parent: {{ $navigation->parent?->title ?? '—' }}</p>
  </div>
</div>

<div style="max-width:640px;">
  <div class="ds-card" data-aos="fade-up">
    <div class="p-4">
      <form method="POST" action="{{ route('admin.navigations.update', $navigation) }}">
        @csrf @method('PUT')

        <div class="mb-3">
          <label class="form-label" style="font-size:13px;font-weight:600;">Judul</label>
          <input type="text" name="title" value="{{ old('title', $navigation->title) }}" class="form-control" required>
          @error('title')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
          <label class="form-label" style="font-size:13px;font-weight:600;">URL atau Route</label>
          <input type="text" name="url" value="{{ old('url', $navigation->url) }}" class="form-control" placeholder="/about  atau  route::admin.users.index">
        </div>

        <div class="row g-2 mb-3">
          <div class="col-6">
            <label class="form-label" style="font-size:13px;font-weight:600;">Lokasi</label>
            <select name="location" class="form-select" required>
              <option value="header" {{ old('location', $navigation->location) === 'header' ? 'selected' : '' }}>Header (Situs)</option>
              <option value="sidebar" {{ old('location', $navigation->location) === 'sidebar' ? 'selected' : '' }}>Sidebar (Dashboard)</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label" style="font-size:13px;font-weight:600;">Induk (Opsional)</label>
            <select name="parent_id" class="form-select">
              <option value="">— Root —</option>
              @foreach($parents as $parent)
                @if($parent->id !== $navigation->id)
                  <option value="{{ $parent->id }}" {{ old('parent_id', $navigation->parent_id) == $parent->id ? 'selected' : '' }}>
                    [{{ $parent->location }}] {{ $parent->title }}
                  </option>
                @endif
              @endforeach
            </select>
          </div>
        </div>

        <div class="row g-2 mb-3">
          <div class="col-4">
            <label class="form-label" style="font-size:13px;font-weight:600;">Urutan</label>
            <input type="number" name="order" value="{{ old('order', $navigation->order) }}" class="form-control" min="0">
          </div>
          <div class="col-8">
            <label class="form-label" style="font-size:13px;font-weight:600;">Ikon (Bootstrap)</label>
            <input type="text" name="icon" value="{{ old('icon', $navigation->icon) }}" class="form-control" placeholder="bi-grid-1x2">
          </div>
        </div>

        <div class="row g-2 mb-3">
          <div class="col-6">
            <label class="form-label" style="font-size:13px;font-weight:600;">Route Aktif</label>
            <input type="text" name="route_name" value="{{ old('route_name', $navigation->route_name) }}" class="form-control" placeholder="admin.users">
          </div>
          <div class="col-6">
            <label class="form-label" style="font-size:13px;font-weight:600;">Permission</label>
            <input type="text" name="permission" value="{{ old('permission', $navigation->permission) }}" class="form-control" placeholder="users.view">
          </div>
        </div>

        <div class="form-check form-switch mb-4">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" id="navActive"
            {{ old('is_active', $navigation->is_active) ? 'checked' : '' }}>
          <label class="form-check-label" for="navActive" style="font-size:13px;">Aktif</label>
        </div>

        <div style="display:flex;gap:12px;">
          <button type="submit" class="ds-btn ds-btn-pri"><i class="bi bi-save"></i> Simpan Perubahan</button>
          <a href="{{ route('admin.navigations.index') }}" class="ds-btn ds-btn-out">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection