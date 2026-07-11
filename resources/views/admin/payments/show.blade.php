@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr">
  <div>
    <div class="pg-crumb"><a href="{{ route('admin.payments.index') }}">Pembayaran</a><span>›</span><span class="cur">{{ $payment->invoice_code }}</span></div>
    <h2 class="pg-title">Detail Pembayaran</h2>
  </div>
  <span class="bx bx-{{ $payment->status }}" style="font-size:12px;padding:5px 14px;">{{ $payment->status_label }}</span>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-7">
    {{-- Invoice --}}
    <div class="inv-card fu fd1" style="margin-bottom:12px;">
      <div style="display:flex;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:8px;">
        <div><div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--sb-txt);margin-bottom:5px;">Invoice</div>
          <div class="inv-code">{{ $payment->invoice_code }}</div>
        </div>
        <span style="background:rgba(255,255,255,.1);color:#e6edf3;padding:5px 14px;border-radius:20px;font-size:11px;font-weight:600;border:1px solid rgba(255,255,255,.15);height:fit-content;">{{ $payment->status_label }}</span>
      </div>
      <div class="inv-amt">Rp {{ number_format($payment->amount,0,',','.') }}</div>
      <div class="row g-3">
        <div class="col-sm-4"><div class="inv-fl">Bank</div><div class="inv-fv">{{ $payment->bank_name }}</div></div>
        <div class="col-sm-4"><div class="inv-fl">No. Rekening</div><div class="inv-fv" style="font-family:'Courier New',monospace;font-size:15px;">{{ $payment->bank_account }}</div></div>
        <div class="col-sm-4"><div class="inv-fl">Atas Nama</div><div class="inv-fv">{{ $payment->bank_holder }}</div></div>
      </div>
    </div>

    {{-- Proof --}}
    @if($payment->proof_file)
    <div class="card-ojs fu fd2" style="margin-bottom:12px;">
      <div class="card-hdr">
        <span class="card-title"><i class="bi bi-image me-2" style="color:var(--acc);"></i>Bukti Pembayaran</span>
        <a href="{{ asset('storage/'.$payment->proof_file) }}" target="_blank" class="btn-o btn-out btn-sm"><i class="bi bi-box-arrow-up-right"></i> Buka</a>
      </div>
      <div style="padding:20px;">
        @php $ext=strtolower(pathinfo($payment->proof_file,PATHINFO_EXTENSION)); @endphp
        @if(in_array($ext,['jpg','jpeg','png']))
          <div style="text-align:center;background:var(--canvas);border-radius:8px;padding:12px;">
            <img src="{{ asset('storage/'.$payment->proof_file) }}" alt="Bukti" style="max-height:280px;max-width:100%;border-radius:8px;border:1px solid var(--brd);"/>
          </div>
        @else
          <div style="display:flex;align-items:center;gap:12px;padding:16px;background:var(--canvas);border-radius:8px;border:1px solid var(--brd);">
            <div style="width:44px;height:44px;background:#fef2f2;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;color:var(--red);flex-shrink:0;"><i class="bi bi-file-earmark-pdf-fill"></i></div>
            <div><div style="font-size:13px;font-weight:600;">Bukti Transfer (PDF)</div><a href="{{ asset('storage/'.$payment->proof_file) }}" target="_blank" style="font-size:12px;color:var(--acc);">Buka dokumen →</a></div>
          </div>
        @endif
        @if($payment->proof_notes)
        <div style="margin-top:12px;padding:12px;background:var(--acc-l);border-radius:8px;">
          <div style="font-size:11px;font-weight:700;color:var(--acc);margin-bottom:4px;">Catatan Author:</div>
          <div style="font-size:13px;color:var(--txt);">{{ $payment->proof_notes }}</div>
        </div>
        @endif
      </div>
    </div>
    @else
    <div class="card-ojs fu fd2" style="margin-bottom:12px;"><div class="empty-st" style="padding:32px;"><div class="empty-icon"><i class="bi bi-image"></i></div><div class="empty-title">Belum ada bukti</div><div class="empty-desc">Author belum upload bukti transfer.</div></div></div>
    @endif

    {{-- Admin notes if done --}}
    @if($payment->admin_notes && in_array($payment->status,['verified','rejected']))
    <div class="alert-o {{ $payment->status==='verified'?'a-suc':'a-err' }} fu fd3" style="margin-bottom:12px;">
      <i class="bi bi-{{ $payment->status==='verified'?'check-circle-fill':'x-circle-fill' }}"></i>
      <div><strong>Catatan Admin:</strong> {{ $payment->admin_notes }}
        @if($payment->verifiedBy)<div style="font-size:11px;margin-top:3px;opacity:.8;">Oleh: {{ $payment->verifiedBy->name }} · {{ $payment->verified_at?->format('d M Y H:i') }}</div>@endif
      </div>
    </div>
    @endif

    {{-- Action --}}
    @if($payment->status==='uploaded')
    <div class="row g-3 fu fd4">
      <div class="col-md-6">
        <div style="background:var(--green-bg);border:1px solid var(--green-b);border-radius:var(--r);padding:20px;">
          <h4 style="font-size:13px;font-weight:700;color:var(--green);margin-bottom:8px;"><i class="bi bi-check-circle-fill me-2"></i>Verifikasi</h4>
          <form method="POST" action="{{ route('admin.payments.verify',$payment) }}">
            @csrf
            <div class="f-group"><label class="lbl" style="font-size:11px;">Catatan Admin <span class="hint">(opsional)</span></label>
              <textarea name="admin_notes" class="txta" rows="2" placeholder="Catatan verifikasi..." style="min-height:60px;"></textarea></div>
            <button type="submit" onclick="return confirm('Verifikasi pembayaran ini?')" class="btn-o btn-suc w-100 justify-content-center"><i class="bi bi-check-circle"></i> Verifikasi & PAID</button>
          </form>
        </div>
      </div>
      <div class="col-md-6">
        <div style="background:var(--red-bg);border:1px solid var(--red-b);border-radius:var(--r);padding:20px;">
          <h4 style="font-size:13px;font-weight:700;color:var(--red);margin-bottom:8px;"><i class="bi bi-x-circle-fill me-2"></i>Tolak</h4>
          <form method="POST" action="{{ route('admin.payments.reject',$payment) }}">
            @csrf
            <div class="f-group"><label class="lbl" style="font-size:11px;">Alasan <span class="req">*</span></label>
              <textarea name="admin_notes" class="txta" rows="2" required placeholder="Alasan penolakan..." style="min-height:60px;"></textarea></div>
            <button type="submit" onclick="return confirm('Tolak bukti ini?')" class="btn-o btn-danger w-100 justify-content-center"><i class="bi bi-x-circle"></i> Tolak Bukti</button>
          </form>
        </div>
      </div>
    </div>
    @endif
  </div>

  <div class="col-12 col-lg-5">
    <div style="display:flex;flex-direction:column;gap:12px;position:sticky;top:80px;">
      {{-- Author --}}
      <div class="card-ojs fu fd1">
        <div class="card-hdr"><span class="card-title">Author</span></div>
        <div style="padding:16px 20px;">
          <div style="display:flex;align-items:center;gap:12px;padding:12px;background:var(--canvas);border-radius:8px;margin-bottom:0;">
            <div style="width:36px;height:36px;border-radius:9px;background:var(--acc);color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($payment->author->name,0,1)) }}</div>
            <div><div style="font-size:13px;font-weight:700;color:var(--txt);">{{ $payment->author->name }}</div>
              <div style="font-size:12px;color:var(--txt2);">{{ $payment->author->email }}</div>
              @if($payment->author->affiliation)<div style="font-size:11px;color:var(--txt3);">{{ $payment->author->affiliation }}</div>@endif
            </div>
          </div>
        </div>
      </div>

      {{-- Artikel --}}
      <div class="card-ojs fu fd2">
        <div class="card-hdr"><span class="card-title">Artikel</span>
          <a href="{{ route('admin.articles.show',$payment->article) }}" class="btn-o btn-ghost btn-sm">Detail <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div style="padding:14px 20px;">
          <div style="font-size:13px;font-weight:700;color:var(--txt);line-height:1.4;margin-bottom:4px;">{{ $payment->article->title }}</div>
          <div style="font-size:12px;color:var(--txt2);">{{ $payment->article->journal->title }}</div>
          <div style="margin-top:8px;"><span class="bx bx-{{ $payment->article->status }}" style="font-size:10px;">{{ $payment->article->status_label }}</span></div>
        </div>
      </div>

      {{-- Transaksi info --}}
      <div class="card-ojs fu fd3">
        <div class="card-hdr"><span class="card-title">Detail Transaksi</span></div>
        <div>
          <div class="info-row"><span class="info-key">Invoice</span><span class="info-val" style="font-size:12px;font-family:'Courier New',monospace;font-weight:700;">{{ $payment->invoice_code }}</span></div>
          <div class="info-row"><span class="info-key">Nominal</span><span class="info-val" style="font-size:16px;font-weight:800;">Rp {{ number_format($payment->amount,0,',','.') }}</span></div>
          <div class="info-row"><span class="info-key">Dibuat</span><span class="info-val" style="font-size:12px;">{{ $payment->created_at->format('d M Y H:i') }}</span></div>
          @if($payment->uploaded_at)<div class="info-row"><span class="info-key">Diupload</span><span class="info-val" style="font-size:12px;">{{ $payment->uploaded_at->format('d M Y H:i') }}</span></div>@endif
          @if($payment->verified_at)<div class="info-row"><span class="info-key">Diverifikasi</span><span class="info-val" style="font-size:12px;">{{ $payment->verified_at->format('d M Y H:i') }}</span></div>@endif
        </div>
      </div>

      {{-- Timeline --}}
      <div class="card-ojs fu fd4">
        <div class="card-hdr"><span class="card-title">Progress</span></div>
        <div style="padding:20px;">
          @php
          $steps=[['key'=>'pending','label'=>'Invoice Dibuat','sub'=>'Menunggu transfer'],['key'=>'uploaded','label'=>'Bukti Diunggah','sub'=>'Author upload bukti'],['key'=>'verified','label'=>'Terverifikasi','sub'=>'Admin verifikasi']];
          $order=['pending','uploaded','verified'];
          $ci=array_search($payment->status,$order);
          if($payment->status==='rejected')$ci=0;
          @endphp
          @foreach($steps as $i=>$s)
          @php $done=$ci>$i;$active=$ci===$i; @endphp
          <div class="tl-item">
            <div class="tl-dot {{ $done?'tl-done':($active?'tl-active':'tl-todo') }}">
              @if($done)<i class="bi bi-check" style="font-size:11px;"></i>@elseif($active)<div style="width:8px;height:8px;border-radius:50%;background:#fff;"></div>@else<span style="font-size:9px;">{{ $i+1 }}</span>@endif
            </div>
            <div><div class="tl-label" style="{{ $active?'color:var(--acc);':($done?'':'color:var(--txt3);') }}">{{ $s['label'] }}</div><div class="tl-sub">{{ $s['sub'] }}</div></div>
          </div>
          @endforeach
          @if($payment->status==='rejected')
          <div class="tl-item" style="padding-bottom:0;">
            <div class="tl-dot" style="background:var(--red-bg);border:2px solid var(--red);color:var(--red);"><i class="bi bi-x" style="font-size:12px;"></i></div>
            <div><div class="tl-label" style="color:var(--red);">Ditolak</div><div class="tl-sub">Bukti tidak valid</div></div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
