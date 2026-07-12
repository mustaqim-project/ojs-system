{{-- admin/journals/index.blade.php --}}
@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Journals']]"/>
    <h1 class="ds-page-title">Journal Management</h1>
    <p class="ds-page-subtitle">{{ $journals->total() }} journals managed on this platform</p>
  </div>
  <a href="{{ route('admin.journals.create') }}" class="ds-btn ds-btn-pri">
    <i class="bi bi-plus-lg"></i> Add Journal
  </a>
</div>

<div class="ds-card" data-aos="fade-up" data-aos-delay="200">
  <div class="table-responsive">
    <table class="ds-table">
      <thead>
        <tr>
          <th>Journal</th>
          <th>ISSN</th>
          <th>Chief Editor</th>
          <th>Articles</th>
          <th>Frequency</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($journals as $j)
        <tr style="{{ $j->trashed() ? 'opacity:0.5;' : '' }}">
          <td>
            <div style="font-weight:700;font-size:14px;color:var(--text-main);">{{ $j->title }}</div>
            @if($j->abbreviation)
              <span style="font-size:11px;font-family:monospace;background:#F1F5F9;color:#475569;padding:2px 7px;border-radius:4px;display:inline-block;margin-top:3px;">{{ $j->abbreviation }}</span>
            @endif
          </td>
          <td style="font-family:monospace;font-size:13px;color:var(--text-muted);">{{ $j->issn_print ?? $j->issn_online ?? '—' }}</td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $j->editor?->name ?? '—' }}</td>
          <td>
            <span style="font-weight:600;font-size:14px;color:var(--primary);">{{ $j->articles_count }}</span>
          </td>
          <td style="font-size:13px;color:var(--text-muted);text-transform:capitalize;">{{ $j->frequency ?? '—' }}</td>
          <td>
            @if($j->trashed())
              <x-status-badge status="rejected" label="Deleted"/>
            @elseif($j->is_active)
              <x-status-badge status="active" label="Active"/>
            @else
              <x-status-badge status="inactive" label="Inactive"/>
            @endif
          </td>
          <td>
            @if(!$j->trashed())
              <a href="{{ route('admin.journals.edit',$j) }}" class="ds-btn ds-btn-out ds-btn-xs" title="Edit Journal">
                <i class="bi bi-pencil"></i>
              </a>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7">
            <x-ui.empty-state icon="bi-journals" title="No journals yet" description="Create the first journal to get started.">
              <a href="{{ route('admin.journals.create') }}" class="ds-btn ds-btn-pri" style="display:inline-flex;">
                <i class="bi bi-plus-lg"></i> Add Journal
              </a>
            </x-ui.empty-state>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($journals->hasPages())
  <div style="padding:16px 24px;border-top:1px solid var(--border);background:var(--bg-app);">
    {{ $journals->links() }}
  </div>
  @endif
</div>

@endsection
