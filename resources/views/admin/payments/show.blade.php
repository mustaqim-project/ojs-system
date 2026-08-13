@extends('layouts.dashboard')
@section('content')

@php
  $indonesianStatusLabel = match($payment->status) {
    'pending' => 'Menunggu Pembayaran',
    'uploaded' => 'Menunggu Verifikasi',
    'verified' => 'Diverifikasi',
    'rejected' => 'Ditolak',
    default => $payment->status_label
  };
@endphp

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Admin'],['label'=>'Pembayaran','href'=>route('admin.payments.index')],['label'=>$payment->invoice_code]]"/>
    <h1 class="ds-page-title">Detail Pembayaran</h1>
  </div>
  <x-status-badge :status="$payment->status" :label="$indonesianStatusLabel"/>
</div>

<div class="row g-3">
  {{-- Main --}}
  <div class="col-12 col-lg-7">

    {{-- Invoice Card --}}
    <div style="background:linear-gradient(135deg,var(--primary) 0%,#1a3a6b 100%);border-radius:12px;padding:28px;color:#fff;margin-bottom:16px;" class="" data-aos="fade-up" data-aos-delay="100">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
        <div>
          <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;opacity:0.6;margin-bottom:6px;">Invoice</div>
          <div style="font-family:monospace;font-size:20px;font-weight:800;letter-spacing:0.02em;">{{ $payment->invoice_code }}</div>
        </div>
        <span style="background:rgba(255,255,255,0.12);color:#fff;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:600;border:1px solid rgba(255,255,255,0.2);">{{ $indonesianStatusLabel }}</span>
      </div>
      <div style="font-size:32px;font-weight:800;letter-spacing:-0.02em;margin-bottom:20px;">Rp {{ number_format($payment->amount,0,',','.') }}</div>
      <div class="row g-3">
        <div class="col-sm-4">
          <div style="font-size:11px;opacity:0.6;margin-bottom:3px;">Bank</div>
          <div style="font-size:14px;font-weight:600;">{{ $payment->bank_name }}</div>
        </div>
        <div class="col-sm-4">
          <div style="font-size:11px;opacity:0.6;margin-bottom:3px;">No. Rekening</div>
          <div style="font-family:monospace;font-size:14px;font-weight:600;">{{ $payment->bank_account }}</div>
        </div>
        <div class="col-sm-4">
          <div style="font-size:11px;opacity:0.6;margin-bottom:3px;">Nama Pemilik</div>
          <div style="font-size:14px;font-weight:600;">{{ $payment->bank_holder }}</div>
        </div>
      </div>
    </div>

    {{-- Proof of Payment --}}
    @if($payment->proof_file)
    <div class="ds-card" data-aos="fade-up" data-aos-delay="200" style="margin-bottom:16px;">
      <div class="ds-card-hdr">
        <span class="ds-card-title"><i class="bi bi-image me-2" style="color:var(--primary);"></i>Bukti Pembayaran</span>
        <a href="{{ asset($payment->proof_file) }}" target="_blank" class="ds-btn ds-btn-out ds-btn-sm">
          <i class="bi bi-box-arrow-up-right"></i> Buka
        </a>
      </div>
      <div style="padding:20px;">
        @php $ext = strtolower(pathinfo($payment->proof_file, PATHINFO_EXTENSION)); @endphp
        @if(in_array($ext, ['jpg','jpeg','png']))
          <div style="text-align:center;background:var(--bg-app);border-radius:8px;padding:12px;">
            <img src="{{ asset($payment->proof_file) }}" alt="Proof" style="max-height:300px;max-width:100%;border-radius:8px;border:1px solid var(--border);"/>
          </div>
        @else
          <div style="display:flex;align-items:center;gap:12px;padding:16px;background:var(--bg-app);border-radius:8px;border:1px solid var(--border);">
            <div style="width:44px;height:44px;background:#FEF2F2;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;color:var(--danger);flex-shrink:0;">
              <i class="bi bi-file-earmark-pdf-fill"></i>
            </div>
            <div>
              <div style="font-size:13px;font-weight:600;color:var(--text-main);">Bukti Pembayaran (PDF)</div>
              <a href="{{ asset($payment->proof_file) }}" target="_blank" style="font-size:12px;color:var(--primary);">Buka dokumen →</a>
            </div>
          </div>
        @endif
        @if($payment->proof_notes)
        <div style="margin-top:12px;padding:12px;background:var(--info-bg);border-radius:8px;">
          <div style="font-size:11px;font-weight:700;color:var(--info);margin-bottom:4px;">Catatan Penulis:</div>
          <div style="font-size:13px;color:var(--text-main);">{{ $payment->proof_notes }}</div>
        </div>
        @endif
      </div>
    </div>
    @else
    <div class="ds-card" data-aos="fade-up" data-aos-delay="200" style="margin-bottom:16px;">
      <x-ui.empty-state icon="bi-image" title="Belum ada bukti pembayaran" description="Penulis belum mengunggah bukti kuitansi transfer."/>
    </div>
    @endif

    {{-- Admin Note (if processed) --}}
    @if($payment->admin_notes && in_array($payment->status, ['verified','rejected']))
    <div class="ds-alert {{ $payment->status === 'verified' ? 'ds-alert-success' : 'ds-alert-danger' }}" data-aos="fade-up" data-aos-delay="300" style="margin-bottom:16px;">
      <i class="bi bi-{{ $payment->status === 'verified' ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
      <div>
        <strong>Catatan Admin:</strong> {{ $payment->admin_notes }}
        @if($payment->verifiedBy)
          <div style="font-size:12px;opacity:0.8;margin-top:4px;">Oleh {{ $payment->verifiedBy->name }} · {{ $payment->verified_at?->format('d M Y H:i') }}</div>
        @endif
      </div>
    </div>
    @endif

    {{-- Verify / Reject Actions --}}
    @if($payment->status === 'uploaded')
    <div class="row g-3" data-aos="fade-up" data-aos-delay="400">
      <div class="col-md-6">
        <div style="background:var(--success-bg);border:1px solid var(--success);border-radius:10px;padding:20px;">
          <h4 style="font-size:13px;font-weight:700;color:var(--success);margin-bottom:12px;"><i class="bi bi-check-circle-fill me-2"></i>Setujui Pembayaran</h4>
          <form method="POST" action="{{ route('admin.payments.verify',$payment) }}">
            @csrf
            <x-ui.form-field label="Catatan Admin (opsional)">
              <x-ui.textarea name="admin_notes" rows="2" placeholder="Catatan verifikasi..."></x-ui.textarea>
            </x-ui.form-field>
            <button type="submit" onclick="return confirm('Setujui dan tandai LUNAS?')" class="ds-btn ds-btn-suc w-100 justify-content-center">
              <i class="bi bi-check-circle"></i> Verifikasi & Tandai Lunas
            </button>
          </form>
        </div>
      </div>
      <div class="col-md-6">
        <div style="background:var(--danger-bg);border:1px solid var(--danger);border-radius:10px;padding:20px;">
          <h4 style="font-size:13px;font-weight:700;color:var(--danger);margin-bottom:12px;"><i class="bi bi-x-circle-fill me-2"></i>Tolak Pembayaran</h4>
          <form method="POST" action="{{ route('admin.payments.reject',$payment) }}">
            @csrf
            <x-ui.form-field label="Alasan Penolakan" required>
              <x-ui.textarea name="admin_notes" rows="2" required placeholder="Tulis alasan penolakan..."></x-ui.textarea>
            </x-ui.form-field>
            <button type="submit" onclick="return confirm('Tolak pembayaran ini?')" class="ds-btn ds-btn-danger w-100 justify-content-center">
              <i class="bi bi-x-circle"></i> Tolak
            </button>
          </form>
        </div>
      </div>
    </div>
    @endif
  </div>

  {{-- Sidebar --}}
  <div class="col-12 col-lg-5">
    <div style="position:sticky;top:80px;display:flex;flex-direction:column;gap:16px;">
      {{-- Author Card --}}
      <div class="ds-card" data-aos="fade-up" data-aos-delay="100">
        <div class="ds-card-hdr"><span class="ds-card-title">Penulis</span></div>
        <div style="padding:16px 20px;">
          <div style="display:flex;align-items:center;gap:12px;padding:12px;background:var(--bg-app);border-radius:8px;">
            <div style="width:36px;height:36px;border-radius:9px;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0;">
              {{ strtoupper(substr($payment->author->name,0,1)) }}
            </div>
            <div>
              <div style="font-size:13px;font-weight:700;color:var(--text-main);">{{ $payment->author->name }}</div>
              <div style="font-size:12px;color:var(--text-muted);">{{ $payment->author->email }}</div>
              @if($payment->author->affiliation)
                <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">{{ $payment->author->affiliation }}</div>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- Article Card --}}
      <div class="ds-card" data-aos="fade-up" data-aos-delay="200">
        <div class="ds-card-hdr">
          <span class="ds-card-title">Artikel</span>
          <a href="{{ route('admin.articles.show',$payment->article) }}" class="ds-btn ds-btn-ghost ds-btn-sm">Detail <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div style="padding:16px 20px;">
          <div style="font-size:13px;font-weight:700;color:var(--text-main);line-height:1.4;margin-bottom:6px;">{{ $payment->article->title }}</div>
          <div style="font-size:12px;color:var(--text-muted);margin-bottom:8px;">{{ $payment->article->journal->title }}</div>
          @php
            $indonesianArticleStatus = match($payment->article->status) {
              'submitted' => 'Mengajukan',
              'screening' => 'Skrining',
              'reviewing' => 'Ditinjau',
              'accepted' => 'Diterima',
              'declined' => 'Ditolak',
              'paid' => 'Lunas',
              'published' => 'Diterbitkan',
              default => $payment->article->status
            };
          @endphp
          <x-status-badge :status="$payment->article->status" :label="$indonesianArticleStatus"/>
        </div>
      </div>

      {{-- Transaction Details --}}
      <div class="ds-card" data-aos="fade-up" data-aos-delay="300">
        <div class="ds-card-hdr"><span class="ds-card-title">Rincian Transaksi</span></div>
        <div>
          @php
          $txDetails = [
            'Invoice' => '<span style="font-family:monospace;font-weight:700;">'.$payment->invoice_code.'</span>',
            'Nominal'  => '<strong style="font-size:16px;">Rp '.number_format($payment->amount,0,',','.').'</strong>',
            'Dibuat' => $payment->created_at->format('d M Y H:i'),
          ];
          if($payment->uploaded_at) $txDetails['Diunggah'] = $payment->uploaded_at->format('d M Y H:i');
          if($payment->verified_at) $txDetails['Diverifikasi'] = $payment->verified_at->format('d M Y H:i');
          @endphp
          @foreach($txDetails as $k => $v)
          <div style="display:grid;grid-template-columns:90px 1fr;gap:8px;padding:11px 20px;border-bottom:1px solid #F1F5F9;font-size:13px;">
            <span style="color:var(--text-muted);">{{ $k }}</span>
            <span style="color:var(--text-main);">{!! $v !!}</span>
          </div>
          @endforeach
        </div>
      </div>

      {{-- Progress Timeline --}}
      <div class="ds-card" data-aos="fade-up" data-aos-delay="400">
        <div class="ds-card-hdr"><span class="ds-card-title">Progres Pembayaran</span></div>
        <div style="padding:20px;">
          @php
          $steps = [
            ['key'=>'pending',  'label'=>'Invoice Dibuat', 'sub'=>'Menunggu transfer bank'],
            ['key'=>'uploaded', 'label'=>'Bukti Diunggah',  'sub'=>'Bukti transfer berhasil dikirim'],
            ['key'=>'verified', 'label'=>'Diverifikasi',   'sub'=>'Pembayaran telah dikonfirmasi'],
          ];
          $order = ['pending','uploaded','verified'];
          $ci = array_search($payment->status, $order);
          if($payment->status === 'rejected') $ci = 0;
          @endphp
          @foreach($steps as $i => $s)
          @php $done = $ci > $i; $active = $ci === $i; @endphp
          <div style="display:flex;align-items:flex-start;gap:14px;padding-bottom:{{ $i < count($steps)-1 ? '20px' : '0' }};position:relative;">
            @if($i < count($steps)-1)
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
          @if($payment->status === 'rejected')
          <div style="display:flex;align-items:flex-start;gap:14px;padding-top:20px;">
            <div style="width:30px;height:30px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:var(--danger-bg);border:2px solid var(--danger);color:var(--danger);">
              <i class="bi bi-x" style="font-size:14px;"></i>
            </div>
            <div>
              <div style="font-size:13px;font-weight:600;color:var(--danger);">Ditolak</div>
              <div style="font-size:12px;color:var(--text-muted);">Bukti tidak sesuai</div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
