{{-- admin/payments/index.blade.php --}}
@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Payments']]"/>
    <h1 class="ds-page-title">Payment Verification</h1>
    <p class="ds-page-subtitle">{{ $payments->total() }} total transactions</p>
  </div>
</div>

{{-- Filter Tabs --}}
<div class="ds-ftabs mb-4" data-aos="fade-up">
  <a href="{{ route('admin.payments.index') }}" class="ds-ftab {{ !$status ? 'active' : '' }}">All</a>
  @foreach($statuses as $k => $l)
    <a href="{{ route('admin.payments.index',['status'=>$k]) }}" class="ds-ftab {{ $status === $k ? 'active' : '' }}">{{ $l }}</a>
  @endforeach
</div>

<div class="ds-card" data-aos="fade-up" data-aos-delay="200">
  <div class="table-responsive">
    <table class="ds-table">
      <thead>
        <tr>
          <th>Invoice</th>
          <th>Article</th>
          <th>Author</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($payments as $p)
        <tr>
          <td>
            <span style="font-family:monospace;font-size:13px;font-weight:700;color:var(--primary);">{{ $p->invoice_code }}</span>
          </td>
          <td>
            <div style="font-weight:600;font-size:13px;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--text-main);" title="{{ $p->article->title }}">{{ $p->article->title }}</div>
            <div style="font-size:12px;color:var(--text-muted);">{{ $p->article->journal->abbreviation }}</div>
          </td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $p->author->name }}</td>
          <td style="font-size:14px;font-weight:700;color:var(--text-main);">Rp {{ number_format($p->amount,0,',','.') }}</td>
          <td><x-status-badge :status="$p->status" :label="$p->status_label"/></td>
          <td style="font-size:13px;color:var(--text-muted);">{{ $p->created_at->format('d M Y') }}</td>
          <td>
            <a href="{{ route('admin.payments.show',$p) }}" class="ds-btn ds-btn-out ds-btn-xs">
              View <i class="bi bi-arrow-right ms-1"></i>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7">
            <x-ui.empty-state icon="bi-credit-card" title="No payment records" description="No transactions match the current filter."/>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($payments->hasPages())
  <div style="padding:16px 24px;border-top:1px solid var(--border);background:var(--bg-app);">
    {{ $payments->withQueryString()->links() }}
  </div>
  @endif
</div>

@endsection
