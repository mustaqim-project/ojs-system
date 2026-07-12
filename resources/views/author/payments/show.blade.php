@extends('layouts.dashboard')
@section('content')

<div class="ds-page-hdr" data-aos="fade-up">
  <div>
    <x-ui.breadcrumb :items="[['label'=>'Author Portal'],['label'=>'My Submissions','href'=>route('author.articles.index')],['label'=>'Detail','href'=>route('author.articles.show',$article)],['label'=>'Payment']]"/>
    <h1 class="ds-page-title">APC Payment Invoice</h1>
    <p class="ds-page-subtitle">Complete your Article Processing Charge (APC) payment to proceed to publication.</p>
  </div>
</div>

<div style="max-width:740px;">
  
  {{-- Invoice Card --}}
  <div class="ds-card" data-aos="fade-up" data-aos-delay="100" style="background:linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);color:white;margin-bottom:20px;box-shadow:0 10px 25px -5px rgba(37,99,235,0.4);">
    <div style="padding:24px;">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;">
        <div>
          <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:rgba(255,255,255,0.7);margin-bottom:6px;">Invoice Number</div>
          <div style="font-family:monospace;font-size:18px;font-weight:700;letter-spacing:0.05em;">{{ $payment->invoice_code }}</div>
        </div>
        <div style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.2);padding:6px 14px;border-radius:20px;font-size:12px;font-weight:600;backdrop-filter:blur(4px);">
          {{ $payment->status_label }}
        </div>
      </div>
      
      <div style="margin-bottom:24px;">
        <div style="font-size:12px;font-weight:500;color:rgba(255,255,255,0.8);margin-bottom:4px;">Amount Due</div>
        <div style="font-size:36px;font-weight:800;letter-spacing:-0.02em;line-height:1;">Rp {{ number_format($payment->amount,0,',','.') }}</div>
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:20px;padding-top:20px;border-top:1px solid rgba(255,255,255,0.1);">
        <div>
          <div style="font-size:11px;color:rgba(255,255,255,0.7);margin-bottom:4px;">Bank Name</div>
          <div style="font-size:14px;font-weight:600;">{{ $payment->bank_name }}</div>
        </div>
        <div>
          <div style="font-size:11px;color:rgba(255,255,255,0.7);margin-bottom:4px;">Account Number</div>
          <div style="font-family:monospace;font-size:16px;font-weight:700;letter-spacing:0.05em;">{{ $payment->bank_account }}</div>
        </div>
        <div>
          <div style="font-size:11px;color:rgba(255,255,255,0.7);margin-bottom:4px;">Account Holder</div>
          <div style="font-size:14px;font-weight:600;">{{ $payment->bank_holder }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Article Ref --}}
  <div class="ds-card" data-aos="fade-up" data-aos-delay="200" style="margin-bottom:20px;">
    <div style="padding:16px 20px;display:flex;align-items:center;gap:16px;">
      <div style="width:40px;height:40px;border-radius:10px;background:var(--bg-app);color:var(--text-muted);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
        <i class="bi bi-file-earmark-text"></i>
      </div>
      <div>
        <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;">Payment For Article</div>
        <div style="font-size:14px;font-weight:700;color:var(--text-main);line-height:1.4;">{{ $article->title }}</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">{{ $article->journal->title }}</div>
      </div>
    </div>
  </div>

  {{-- Status Alerts --}}
  @if($payment->status === 'rejected' && $payment->admin_notes)
  <div class="ds-alert ds-alert-danger" data-aos="fade-up" data-aos-delay="300" style="margin-bottom:20px;">
    <i class="bi bi-x-circle-fill"></i>
    <div>
      <strong>Proof Rejected:</strong> {{ $payment->admin_notes }}<br/>
      <small style="font-size:12px;display:block;margin-top:4px;">Please re-upload a valid proof of payment.</small>
    </div>
  </div>
  @endif

  @if($payment->status === 'verified')
  <div class="ds-alert ds-alert-success" data-aos="fade-up" data-aos-delay="300" style="margin-bottom:20px;">
    <i class="bi bi-check-circle-fill"></i>
    <div>
      <strong>Payment Verified!</strong> Your APC payment has been confirmed. The editor will proceed with publishing your article.
      <div style="font-size:11px;margin-top:6px;opacity:0.9;">Verified on: {{ $payment->verified_at?->format('d M Y H:i') }}</div>
    </div>
  </div>
  @endif

  @if($payment->status === 'uploaded')
  <div class="ds-alert ds-alert-info" data-aos="fade-up" data-aos-delay="300" style="margin-bottom:20px;">
    <i class="bi bi-hourglass-split"></i>
    <div>
      <strong>Pending Verification.</strong> Your proof of payment has been uploaded successfully and is currently being reviewed by the admin team.
    </div>
  </div>
  @endif

  {{-- Upload Form --}}
  @if(in_array($payment->status, ['pending','rejected']))
  <div class="ds-section" data-aos="fade-up" data-aos-delay="300">
    <div class="ds-section-hdr">
      <h3 class="ds-section-title"><i class="bi bi-cloud-upload me-2" style="color:var(--primary);"></i>Upload Proof of Payment</h3>
    </div>
    <div class="ds-section-body">
      
      <div style="background:var(--bg-app);border:1px dashed var(--border);border-radius:10px;padding:20px 24px;margin-bottom:24px;">
        <div style="font-size:13px;font-weight:700;color:var(--text-main);margin-bottom:10px;display:flex;align-items:center;gap:8px;">
          <i class="bi bi-info-circle" style="color:var(--primary);"></i> Payment Instructions:
        </div>
        <ol style="font-size:13px;color:var(--text-muted);margin:0;padding-left:18px;line-height:1.8;">
          <li>Transfer exactly <strong>Rp {{ number_format($payment->amount,0,',','.') }}</strong> to the bank account listed above.</li>
          <li>Save or screenshot the transfer receipt.</li>
          <li>Upload the receipt using the form below.</li>
          <li>Wait for admin verification (typically 1-2 business days).</li>
        </ol>
      </div>

      <form method="POST" action="{{ route('author.payments.upload', $article) }}" enctype="multipart/form-data" novalidate>
        @csrf
        <x-ui.form-field label="Proof of Transfer" required :error="$errors->first('proof_file')" hint="Accepted formats: JPG, PNG, PDF. Maximum size: 5MB.">
          <input type="file" name="proof_file" accept=".jpg,.jpeg,.png,.pdf" required
                 class="{{ $errors->has('proof_file') ? 'is-invalid' : '' }}"
                 style="display:block;width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-surface);font-size:13px;color:var(--text-main);cursor:pointer;"/>
        </x-ui.form-field>
        
        <x-ui.form-field label="Additional Notes" hint="Optional. E.g., Sender's name, transfer date, source bank.">
          <x-ui.textarea name="proof_notes" rows="2" placeholder="Sender name, bank name, etc...">{{ old('proof_notes') }}</x-ui.textarea>
        </x-ui.form-field>

        <div style="margin-top:24px;">
          <button type="submit" class="ds-btn ds-btn-pri w-100 justify-content-center" style="height:44px;font-size:15px;background:#6B46C1;border-color:#6B46C1;">
            <i class="bi bi-upload"></i> Submit Proof of Payment
          </button>
        </div>
      </form>

    </div>
  </div>
  @endif

</div>

@endsection
