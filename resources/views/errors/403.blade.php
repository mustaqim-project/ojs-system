@extends('layouts.app')
@section('content')
<div style="min-height:70vh;display:flex;align-items:center;justify-content:center;padding:80px 24px;background:var(--bg-app);">
  <div style="text-align:center;max-width:480px;background:var(--bg-surface);padding:48px 40px;border-radius:var(--radius-lg);border:1px solid var(--border);box-shadow:var(--shadow-lg);">
    <div style="width:80px;height:80px;border-radius:24px;background:var(--danger-bg);color:var(--danger);display:flex;align-items:center;justify-content:center;font-size:36px;margin:0 auto 24px;box-shadow:0 4px 12px rgba(239,68,68,0.15);">
      <i class="bi bi-shield-lock"></i>
    </div>
    
    <div style="font-size:72px;font-weight:900;color:var(--border);line-height:1;letter-spacing:-0.05em;margin-bottom:16px;">403</div>
    
    <h1 style="font-size:24px;font-weight:800;color:var(--text-main);margin-bottom:12px;letter-spacing:-0.02em;">Akses Ditolak</h1>
    <p style="font-size:15px;color:var(--text-muted);margin-bottom:32px;line-height:1.7;">
      Anda tidak memiliki izin yang diperlukan untuk melihat halaman ini. Silakan pastikan Anda masuk dengan peran akun yang benar.
    </p>
    
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
      <button onclick="window.history.back()" class="ds-btn ds-btn-out" style="height:44px;padding:0 24px;">
        <i class="bi bi-arrow-left"></i> Kembali
      </button>
      <a href="{{ route('public.home') }}" class="ds-btn ds-btn-pri" style="height:44px;padding:0 24px;">
        <i class="bi bi-house"></i> Beranda
      </a>
    </div>
  </div>
</div>
@endsection
