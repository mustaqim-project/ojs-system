@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Reviewer Portal'],['label'=>'Dashboard','href'=>route('reviewer.dashboard')],['label'=>'Review Assignments']]"/>
    <h1 class="ds-page-title">Review Assignments</h1>
    <p class="ds-page-subtitle">Manage your {{ $reviews->total() }} assigned manuscripts for peer review</p>
  </div>
</div>

{{-- Filter Tabs --}}
<div class="ds-ftabs mb-4" data-aos="fade-up" style="flex-wrap:wrap;">
  @php $cur = request('status', ''); @endphp
  <a href="{{ route('reviewer.reviews.index') }}" class="ds-ftab {{ !$cur ? 'active' : '' }}">All</a>
  <a href="{{ route('reviewer.reviews.index',['status'=>'pending']) }}" class="ds-ftab {{ $cur==='pending'?'active':'' }}">Pending Action</a>
  <a href="{{ route('reviewer.reviews.index',['status'=>'in_progress']) }}" class="ds-ftab {{ $cur==='in_progress'?'active':'' }}">In Progress</a>
  <a href="{{ route('reviewer.reviews.index',['status'=>'completed']) }}" class="ds-ftab {{ $cur==='completed'?'active':'' }}">Completed</a>
  <a href="{{ route('reviewer.reviews.index',['status'=>'declined']) }}" class="ds-ftab {{ $cur==='declined'?'active':'' }}">Declined</a>
</div>

<div class="ds-card" data-aos="fade-up" data-aos-delay="200">
  <div class="table-responsive">
    <table class="ds-table">
      <thead>
        <tr>
          <th>Manuscript</th>
          <th>Journal</th>
          <th>Status</th>
          <th>Recommendation</th>
          <th>Due Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($reviews as $review)
        <tr>
          <td>
            <a href="{{ route('reviewer.reviews.show', $review) }}"
               style="font-weight:600;color:var(--text-main);text-decoration:none;max-width:300px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
               title="{{ $review->article->title }}">{{ $review->article->title }}</a>
            <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">{{ $review->article->author->name }}</div>
          </td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $review->article->journal->abbreviation }}</td>
          <td>
            @php
            $sc = [
              'pending'    => ['bg'=>'var(--warning-bg)','color'=>'var(--warning)'],
              'in_progress'=> ['bg'=>'var(--info-bg)','color'=>'var(--info)'],
              'completed'  => ['bg'=>'var(--success-bg)','color'=>'var(--success)'],
              'accepted'   => ['bg'=>'var(--success-bg)','color'=>'var(--success)'],
              'declined'   => ['bg'=>'var(--danger-bg)','color'=>'var(--danger)'],
            ];
            $style = $sc[$review->status] ?? ['bg'=>'var(--bg-app)','color'=>'var(--text-muted)'];
            @endphp
            <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $style['bg'] }};color:{{ $style['color'] }};">
              {{ ucfirst(str_replace('_',' ',$review->status)) }}
            </span>
          </td>
          <td>
            @if($review->recommendation)
              <x-status-badge :status="$review->recommendation" :label="$review->recommendation_label"/>
            @else
              <span style="font-size:12px;color:var(--text-muted);">—</span>
            @endif
          </td>
          <td>
            @if($review->due_date)
              @php $ov = $review->due_date->isPast() && $review->status !== 'completed'; @endphp
              <span style="font-size:13px;color:{{ $ov?'var(--danger)':'var(--text-main)' }};font-weight:{{ $ov?'700':'500' }};">
                {{ $review->due_date->format('d M Y') }}
                @if($ov) <i class="bi bi-exclamation-circle-fill ms-1"></i> @endif
              </span>
            @else
              <span style="font-size:12px;color:var(--text-muted);">—</span>
            @endif
          </td>
          <td>
            <a href="{{ route('reviewer.reviews.show', $review) }}" class="ds-btn ds-btn-out ds-btn-sm">
              {{ $review->status === 'pending' ? 'Respond' : 'Detail' }}
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6">
            <x-ui.empty-state icon="bi-clipboard" title="No review assignments" description="You have no manuscripts assigned for peer review yet."/>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($reviews->hasPages())
  <div style="padding:16px 24px;border-top:1px solid var(--border);background:var(--bg-app);">
    {{ $reviews->withQueryString()->links() }}
  </div>
  @endif
</div>

@endsection
