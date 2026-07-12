@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Site Pages']]"/>
    <h1 class="ds-page-title">Manage Site Pages</h1>
    <p class="ds-page-subtitle">Edit konten halaman publik yang tampil di website</p>
  </div>
</div>

@foreach($pages as $group => $items)
<div class="ds-card mb-4 fd{{ $loop->index + 1 }}" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
  <div class="ds-card-hdr">
    <span class="ds-card-title">{{ $group }}</span>
    <span class="ds-badge" style="background:var(--bg-app);color:var(--text-muted);font-size:12px;">{{ $items->count() }} pages</span>
  </div>
  <div class="table-responsive">
    <table class="ds-table">
      <thead>
        <tr>
          <th>Page</th>
          <th>Title</th>
          <th>Status</th>
          <th>Last Updated</th>
          <th style="width:120px;"></th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $pg)
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px;">
              <div style="width:36px;height:36px;border-radius:10px;background:var(--bg-app);display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:16px;">
                <i class="{{ $pg['icon'] }}"></i>
              </div>
              <div>
                <div style="font-weight:600;color:var(--text-main);">{{ $pg['label'] }}</div>
                <div style="font-size:12px;color:var(--text-muted);">/{{ $pg['slug'] }}</div>
              </div>
            </div>
          </td>
          <td style="color:var(--text-muted);font-size:14px;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
            {{ $pg['title'] }}
          </td>
          <td>
            @if($pg['from_db'])
              <span class="ds-badge ds-badge-success" style="font-size:11px;">Custom</span>
            @else
              <span class="ds-badge" style="background:#f1f5f9;color:#64748b;font-size:11px;">Default</span>
            @endif
          </td>
          <td style="color:var(--text-muted);font-size:13px;">
            {{ $pg['updated_at'] ? $pg['updated_at']->diffForHumans() : '—' }}
          </td>
          <td>
            <div style="display:flex;gap:6px;">
              <a href="{{ route('admin.pages.edit', $pg['slug']) }}" class="ds-btn ds-btn-pri ds-btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit
              </a>
              <a href="{{ url('/' . $pg['slug']) }}" target="_blank" class="ds-btn ds-btn-ghost ds-btn-sm" title="Preview">
                <i class="bi bi-eye"></i>
              </a>
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
