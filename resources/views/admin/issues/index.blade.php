{{-- admin/issues/index.blade.php --}}
@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Issues']]"/>
    <h1 class="ds-page-title">Issue Management</h1>
    <p class="ds-page-subtitle">Manage journal volumes and issues</p>
  </div>
  <a href="{{ route('admin.issues.create') }}" class="ds-btn ds-btn-pri">
    <i class="bi bi-plus-lg"></i> Add Issue
  </a>
</div>

<div class="ds-card" data-aos="fade-up" data-aos-delay="200">
  <div class="table-responsive">
    <table class="ds-table">
      <thead>
        <tr>
          <th>Journal</th>
          <th>Volume / Number</th>
          <th>Year</th>
          <th>Status</th>
          <th>Published Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($issues as $issue)
        <tr>
          <td>
            <div style="font-weight:600;color:var(--text-main);">{{ $issue->journal->abbreviation ?? $issue->journal->title }}</div>
            <div style="font-size:12px;color:var(--text-muted);">{{ $issue->journal->title }}</div>
          </td>
          <td style="font-weight:600;color:var(--text-main);">Vol. {{ $issue->volume }} No. {{ $issue->number }}</td>
          <td style="font-size:14px;color:var(--text-muted);">{{ $issue->year }}</td>
          <td>
            @if($issue->status === 'published')
              <x-status-badge status="published" label="Published"/>
            @else
              <x-status-badge status="draft" label="Draft"/>
            @endif
          </td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $issue->published_date?->format('d M Y') ?? '—' }}</td>
          <td>
            @if($issue->status === 'draft')
              <form method="POST" action="{{ route('admin.issues.publish',$issue) }}" style="display:inline;">
                @csrf @method('PATCH')
                <button type="submit" class="ds-btn ds-btn-suc ds-btn-sm"
                        onclick="return confirm('Publish this issue? This will make it publicly visible.')">
                  <i class="bi bi-send-check"></i> Publish
                </button>
              </form>
            @else
              <span style="font-size:12px;color:var(--success);font-weight:600;"><i class="bi bi-check-circle me-1"></i>Published</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6">
            <x-ui.empty-state icon="bi-collection" title="No issues yet" description="Create the first issue to start accepting articles.">
              <a href="{{ route('admin.issues.create') }}" class="ds-btn ds-btn-pri" style="display:inline-flex;">
                <i class="bi bi-plus-lg"></i> Add Issue
              </a>
            </x-ui.empty-state>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($issues->hasPages())
  <div style="padding:16px 24px;border-top:1px solid var(--border);background:var(--bg-app);">
    {{ $issues->links() }}
  </div>
  @endif
</div>

@endsection
