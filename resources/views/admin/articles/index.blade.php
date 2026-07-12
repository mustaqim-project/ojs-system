@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'All Articles']]"/>
    <h1 class="ds-page-title">Article Management</h1>
    <p class="ds-page-subtitle">{{ $articles->total() }} manuscripts in the system</p>
  </div>
</div>

{{-- Status Filter Tabs --}}
<div class="ds-ftabs mb-4" data-aos="fade-up" style="flex-wrap:wrap;">
  <a href="{{ route('admin.articles.index') }}" class="ds-ftab {{ !$status ? 'active' : '' }}">All</a>
  @foreach([
    'submitted'       => 'New',
    'under_review'    => 'Under Review',
    'revision_required'=> 'Revision',
    'accepted'        => 'Accepted',
    'waiting_payment' => 'Awaiting Payment',
    'payment_uploaded'=> 'Payment Pending',
    'paid'            => 'Paid',
    'published'       => 'Published',
    'rejected'        => 'Rejected',
  ] as $k => $l)
  <a href="{{ route('admin.articles.index',['status'=>$k]) }}" class="ds-ftab {{ $status === $k ? 'active' : '' }}">{{ $l }}</a>
  @endforeach
</div>

<div class="ds-card" data-aos="fade-up" data-aos-delay="200">
  <div class="table-responsive">
    <table class="ds-table">
      <thead>
        <tr>
          <th>Title</th>
          <th>Journal</th>
          <th>Author</th>
          <th>Status</th>
          <th>Payment</th>
          <th>Submitted</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($articles as $a)
        <tr>
          <td>
            <a href="{{ route('admin.articles.show',$a) }}"
               style="font-weight:600;color:var(--text-main);text-decoration:none;max-width:240px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
               title="{{ $a->title }}">
              {{ $a->title }}
            </a>
          </td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $a->journal->abbreviation ?? '—' }}</td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $a->author->name }}</td>
          <td><x-status-badge :status="$a->status" :label="$a->status_label"/></td>
          <td>
            @if($a->payment)
              <x-status-badge :status="$a->payment->status" :label="$a->payment->status_label"/>
            @else
              <span style="font-size:12px;color:var(--text-muted);">—</span>
            @endif
          </td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $a->submitted_at?->format('d M Y') }}</td>
          <td>
            <a href="{{ route('admin.articles.show',$a) }}" class="ds-btn ds-btn-out ds-btn-xs">
              View <i class="bi bi-arrow-right ms-1"></i>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7">
            <x-ui.empty-state icon="bi-file-earmark-text" title="No articles found" description="No manuscripts match the current filter."/>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($articles->hasPages())
  <div style="padding:16px 24px;border-top:1px solid var(--border);background:var(--bg-app);">
    {{ $articles->withQueryString()->links() }}
  </div>
  @endif
</div>

@endsection
