@extends('layouts.dashboard')
@section('content')
<div class="pg-hdr">
  <div>
    <div class="pg-crumb"><a href="{{ route('author.dashboard') }}">Dashboard</a><span>›</span><a href="{{ route('author.articles.show',$article) }}">Artikel</a><span>›</span><span class="cur">Pembayaran</span></div>
    <h2 class="pg-title">Invoice Pembayaran</h2>
  </div>
</div>

<div style="max-width:680px;">
  {{-- Invoice card --}}
  <div class="inv-card fu fd1" style="margin-bottom:16px;">
    <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:20px;">
      <div>
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--sb-txt);margin-bottom:6px;">Invoice</div>
        <div class="inv-code">{{ $payment->invoice_code }}</div>
      </div>
      @php $bc=['pending'=>'#ca8a04','uploaded'=>'#2563eb','verified'=>'#16a34a','rejected'=>'#dc2626'][$payment->status]??'#94a3b8'; @endphp
      <span style="background:rgba(255,255,255,.1);color:#e6edf3;padding:5px 14px;border-radius:20px;font-size:11px;font-weight:600;border:1px solid rgba(255,255,255,.15);">{{ $payment->status_label }}</span>
    </div>
    <div class="inv-amt">Rp {{ number_format($payment->amount,0,',','.') }}</div>
    <div class="row g-3">
      <div class="col-sm-4"><div class="inv-fl">Bank</div><div class="inv-fv">{{ $payment->bank_name }}</div></div>
      <div class="col-sm-4"><div class="inv-fl">No. Rekening</div><div class="inv-fv" style="font-family:'Courier New',monospace;font-size:16px;letter-spacing:.05em;">{{ $payment->bank_account }}</div></div>
      <div class="col-sm-4"><div class="inv-fl">Atas Nama</div><div class="inv-fv">{{ $payment->bank_holder }}</div></div>
    </div>
  </div>

  {{-- Article ref --}}
  <div class="card-ojs fu fd2" style="margin-bottom:16px;">
    <div style="padding:14px 20px;">
      <div style="font-size:11px;color:#94a3b8;margin-bottom:4px;">Untuk artikel:</div>
      <div style="font-size:13px;font-weight:700;color:#0f172a;">{{ $article->title }}</div>
      <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $article->journal->title }}</div>
    </div>
  </div>

  {{-- Rejected notice --}}
  @if($payment->status==='rejected' && $payment->admin_notes)
  <div class="alert-o a-err fu fd3" style="margin-bottom:16px;">
    <i class="bi bi-x-circle-fill"></i>
    <div><strong>Bukti Ditolak:</strong> {{ $payment->admin_notes }}<br/><small style="font-size:11px;opacity:.8;">Silakan upload ulang bukti pembayaran yang valid.</small></div>
  </div>
  @endif

  {{-- Verified notice --}}
  @if($payment->status==='verified')
  <div class="alert-o a-suc fu fd3" style="margin-bottom:16px;">
    <i class="bi bi-check-circle-fill"></i>
    <div><strong>Pembayaran Terverifikasi!</strong> Artikel Anda akan segera dipublish oleh editor.
      <div style="font-size:11px;margin-top:3px;opacity:.8;">Diverifikasi: {{ $payment->verified_at?->format('d M Y H:i') }}</div>
    </div>
  </div>
  @endif

  {{-- Uploaded waiting --}}
  @if($payment->status==='uploaded')
  <div class="alert-o a-info fu fd3" style="margin-bottom:16px;">
    <i class="bi bi-hourglass-split"></i>
    <div><strong>Menunggu Verifikasi Admin.</strong> Bukti pembayaran Anda sudah dikirim dan sedang diproses.</div>
  </div>
  @endif

  {{-- Upload form --}}
  @if(in_array($payment->status,['pending','rejected']))
  <div class="card-ojs fu fd3">
    <div class="card-hdr"><span class="card-title"><i class="bi bi-cloud-upload me-2" style="color:var(--acc);"></i>Upload Bukti Pembayaran</span></div>
    <div class="card-body-p">
      <div style="background:var(--canvas);border:1px dashed var(--brd);border-radius:10px;padding:20px;margin-bottom:16px;">
        <div style="font-size:13px;font-weight:600;color:#0f172a;margin-bottom:8px;">Petunjuk Transfer:</div>
        <ol style="font-size:12px;color:#64748b;margin:0;padding-left:16px;line-height:1.8;">
          <li>Transfer sebesar <strong>Rp {{ number_format($payment->amount,0,',','.') }}</strong> ke rekening di atas</li>
          <li>Simpan bukti transfer (screenshot atau struk)</li>
          <li>Upload bukti di form di bawah ini</li>
          <li>Tunggu verifikasi dari admin (1×24 jam)</li>
        </ol>
      </div>
      <form method="POST" action="{{ route('author.payments.upload',$article) }}" enctype="multipart/form-data">
        @csrf
        <div class="f-group">
          <label class="lbl">File Bukti Transfer <span class="req">*</span></label>
          <input class="file-inp {{ $errors->has('proof_file')?'is-invalid':'' }}" type="file" name="proof_file" accept=".jpg,.jpeg,.png,.pdf" required/>
          <div class="f-hint-txt"><i class="bi bi-info-circle me-1"></i>Format: JPG, PNG, PDF. Maks 5MB.</div>
          @error('proof_file')<div class="f-err">{{ $message }}</div>@enderror
        </div>
        <div class="f-group mb-0">
          <label class="lbl">Catatan Tambahan <span class="hint">(opsional)</span></label>
          <textarea name="proof_notes" class="txta" rows="2" placeholder="Nama pengirim, tanggal transfer, bank asal, dll..."></textarea>
        </div>
        <div style="margin-top:20px;">
          <button type="submit" class="btn-o btn-pri btn-lg w-100 justify-content-center">
            <i class="bi bi-upload"></i> Upload Bukti Pembayaran
          </button>
        </div>
      </form>
    </div>
  </div>
  @endif
</div>
@endsection
