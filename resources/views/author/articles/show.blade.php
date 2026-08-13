@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Portal Penulis'],['label'=>'Naskah Saya','href'=>route('author.articles.index')],['label'=>Str::limit($article->title,45)]]"/>
    <h1 class="ds-page-title" style="font-size:19px;max-width:660px;line-height:1.4;">{{ $article->title }}</h1>
  </div>
  <x-status-badge :status="$article->status" :label="$article->status_label"/>
</div>

{{-- Action Alerts --}}
@if($article->status === 'revision_required')
<div class="ds-alert ds-alert-warn" data-aos="fade-up" style="margin-bottom:20px;">
  <i class="bi bi-exclamation-triangle-fill"></i>
  <div><strong>Revisi Diperlukan.</strong> {{ $article->editor_note ? 'Silakan lihat catatan editor di bawah.' : '' }}
    <a href="{{ route('author.articles.revision',$article) }}" style="color:inherit;font-weight:700;margin-left:8px;text-decoration:underline;">Unggah Revisi →</a>
  </div>
</div>
@endif
@if($article->needsPayment())
<div class="ds-alert ds-alert-info" data-aos="fade-up" style="margin-bottom:20px;">
  <i class="bi bi-credit-card-fill"></i>
  <div><strong>Artikel Diterima!</strong> Silakan lakukan pembayaran APC Anda untuk memproses publikasi.
    <a href="{{ route('author.payments.show',$article) }}" style="color:inherit;font-weight:700;margin-left:8px;text-decoration:underline;">Lihat Tagihan →</a>
  </div>
</div>
@endif

<div class="row g-3">
  <div class="col-12 col-lg-8">
    {{-- Article Info --}}
    <div class="ds-card" data-aos="fade-up" data-aos-delay="100">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Informasi Manuskrip</span>
        @if($article->manuscript_file)
          <a href="{{ asset($article->manuscript_file) }}" target="_blank" class="ds-btn ds-btn-out ds-btn-sm">
            <i class="bi bi-download"></i> Manuskrip
          </a>
        @endif
      </div>
      <div>
        @php
        $rows = [
          'Jurnal'    => $article->journal->title,
          'Bahasa'    => strtoupper($article->language ?? 'id'),
          'Dikirim'   => $article->submitted_at?->format('d M Y H:i'),
        ];
        if($article->accepted_at)  $rows['Diterima']  = $article->accepted_at->format('d M Y H:i');
        if($article->published_at) $rows['Diterbitkan'] = $article->published_at->format('d M Y H:i');
        if($article->doi) $rows['DOI'] = '<span style="font-family:monospace;color:var(--primary);">'.$article->doi.'</span>';
        @endphp
        @foreach($rows as $k => $v)
        <div style="display:grid;grid-template-columns:130px 1fr;gap:8px;padding:12px 24px;border-bottom:1px solid #F1F5F9;font-size:14px;">
          <span style="font-weight:500;color:var(--text-muted);">{{ $k }}</span>
          <span style="color:var(--text-main);">{!! $v !!}</span>
        </div>
        @endforeach
        {{-- Abstract --}}
        <div style="padding:16px 24px;border-bottom:1px solid #F1F5F9;">
          <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);margin-bottom:8px;">Abstrak</div>
          <p style="font-size:14px;color:var(--text-main);line-height:1.75;margin:0;">{{ $article->abstract }}</p>
        </div>
        {{-- Keywords --}}
        <div style="padding:16px 24px;">
          <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);margin-bottom:8px;">Kata Kunci</div>
          <div style="display:flex;flex-wrap:wrap;gap:6px;">
            @foreach($article->keywords_array as $kw)
              <span style="font-size:12px;background:#F1F5F9;color:#475569;padding:3px 10px;border-radius:20px;border:1px solid #E2E8F0;">{{ $kw }}</span>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- Editor Note --}}
    @if($article->editor_note)
    <div class="ds-alert ds-alert-warn" data-aos="fade-up" data-aos-delay="200" style="margin-top:16px;">
      <i class="bi bi-chat-left-text-fill"></i>
      <div><strong>Catatan Editor:</strong><br/> {{ $article->editor_note }}</div>
    </div>
    @endif

    {{-- Review Results --}}
    @if($article->reviews->where('status','completed')->count())
    <div class="ds-card" data-aos="fade-up" data-aos-delay="300" style="margin-top:16px;">
      <div class="ds-card-hdr"><span class="ds-card-title">Hasil Peninjauan Sejawat</span></div>
      <div>
        @foreach($article->reviews->where('status','completed') as $review)
        <div style="padding:20px 24px;border-bottom:1px solid #F1F5F9;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px;">
            <span style="font-size:12px;color:var(--text-muted);font-weight:600;">Penelaah {{ $loop->iteration }}</span>
            @if($review->recommendation)
              <x-status-badge :status="$review->recommendation" :label="$review->recommendation_label"/>
            @endif
          </div>
          @if($review->comments_to_author)
            <p style="font-size:14px;color:var(--text-main);line-height:1.7;margin:0;">{{ $review->comments_to_author }}</p>
          @endif
          @if($review->average_score)
            <div style="font-size:12px;color:var(--text-muted);margin-top:10px;">Skor Rata-rata: <strong style="color:var(--text-main);">{{ $review->average_score }}/5</strong></div>
          @endif
        </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Payment Summary --}}
    @if($article->payment)
    <div class="ds-card" data-aos="fade-up" data-aos-delay="400" style="margin-top:16px;">
      <div class="ds-card-hdr">
        <span class="ds-card-title">Pembayaran APC</span>
        <a href="{{ route('author.payments.show',$article) }}" class="ds-btn ds-btn-ghost ds-btn-sm">Lihat Tagihan <i class="bi bi-arrow-right ms-1"></i></a>
      </div>
      <div style="padding:20px 24px;display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
        <div>
          <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;color:var(--text-muted);margin-bottom:4px;">Kode Invoice</div>
          <div style="font-family:monospace;font-size:14px;font-weight:700;">{{ $article->payment->invoice_code }}</div>
        </div>
        <div style="width:1px;height:36px;background:var(--border);"></div>
        <div>
          <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;color:var(--text-muted);margin-bottom:4px;">Nominal</div>
          <div style="font-size:20px;font-weight:800;">{{ $article->payment->formatted_amount }}</div>
        </div>
        <div style="margin-left:auto;">
          <x-status-badge :status="$article->payment->status" :label="$article->payment->status_label"/>
        </div>
      </div>
    </div>
    @endif
  </div>

  {{-- Sidebar: Progress Timeline --}}
  <div class="col-12 col-lg-4">
    <div class="ds-card" data-aos="fade-up" data-aos-delay="200" style="position:sticky;top:80px;">
      <div class="ds-card-hdr"><span class="ds-card-title">Progres Manuskrip</span></div>
      <div style="padding:20px;">
        @php
        $stages = [
          ['key'=>'submitted',       'label'=>'Dikirim',       'sub'=>'Manuskrip diterima'],
          ['key'=>'under_review',    'label'=>'Ditinjau',    'sub'=>'Penilaian sejawat berlangsung'],
          ['key'=>'accepted',        'label'=>'Diterima',        'sub'=>'Disetujui oleh editor'],
          ['key'=>'waiting_payment', 'label'=>'Pembayaran',         'sub'=>'Menunggu pembayaran APC'],
          ['key'=>'paid',            'label'=>'Lunas',            'sub'=>'Pembayaran dikonfirmasi'],
          ['key'=>'published',       'label'=>'Diterbitkan',       'sub'=>'Artikel telah dipublikasi'],
        ];
        $order = ['submitted','under_review','revision_required','accepted','rejected','waiting_payment','payment_uploaded','paid','published'];
        $ci = array_search($article->status, $order);
        @endphp
        @foreach($stages as $i => $s)
        @php $si = array_search($s['key'],$order); $done = $ci > $si; $active = $ci === $si; @endphp
        <div style="display:flex;align-items:flex-start;gap:14px;padding-bottom:{{ $i < count($stages)-1 ? '18px' : '0' }};position:relative;">
          @if($i < count($stages)-1)
            <div style="position:absolute;left:15px;top:30px;bottom:0;width:1px;background:{{ $done ? 'var(--success)' : 'var(--border)' }};"></div>
          @endif
          <div style="width:30px;height:30px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;position:relative;z-index:1;
            {{ $done ? 'background:var(--success);color:#fff;' : ($active ? 'background:var(--primary);color:#fff;' : 'background:var(--bg-app);color:var(--text-muted);border:2px solid var(--border);') }}">
            @if($done)<i class="bi bi-check" style="font-size:14px;"></i>@elseif($active)<i class="bi bi-circle-fill" style="font-size:8px;"></i>@else{{ $i+1 }}@endif
          </div>
          <div>
            <div style="font-size:13px;font-weight:600;color:{{ $active ? 'var(--primary)' : ($done ? 'var(--text-main)' : 'var(--text-muted)') }};">{{ $s['label'] }}</div>
            <div style="font-size:12px;color:var(--text-muted);">{{ $s['sub'] }}</div>
          </div>
        </div>
        @endforeach

        @if($article->status === 'rejected')
        <div style="display:flex;align-items:flex-start;gap:14px;padding-top:18px;">
          <div style="width:30px;height:30px;border-radius:50%;background:var(--danger-bg);border:2px solid var(--danger);color:var(--danger);display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-x" style="font-size:14px;"></i>
          </div>
          <div>
            <div style="font-size:13px;font-weight:600;color:var(--danger);">Ditolak</div>
            <div style="font-size:12px;color:var(--text-muted);">Naskah tidak disetujui</div>
          </div>
        </div>
        @endif

        @if($article->status === 'revision_required')
          <a href="{{ route('author.articles.revision',$article) }}" class="ds-btn ds-btn-pri w-100 justify-content-center" style="margin-top:20px;background:var(--warning);border-color:var(--warning);">
            <i class="bi bi-pencil-square"></i> Unggah Revisi
          </a>
        @endif
        @if($article->needsPayment())
          <a href="{{ route('author.payments.show',$article) }}" class="ds-btn ds-btn-pri w-100 justify-content-center" style="margin-top:20px;background:#6B46C1;border-color:#6B46C1;">
            <i class="bi bi-credit-card"></i> Bayar Sekarang
          </a>
        @endif
      </div>
    </div>
  </div>
</div>

@endsection
