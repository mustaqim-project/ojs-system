@extends('layouts.app')
@section('content')
<div style="min-height:70vh;display:flex;align-items:center;justify-content:center;padding:80px 24px;background:var(--bg-app);">
  <div style="text-align:center;max-width:480px;background:var(--bg-surface);padding:48px 40px;border-radius:var(--radius-lg);border:1px solid var(--border);box-shadow:var(--shadow-lg);">
    <div style="width:80px;height:80px;border-radius:24px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:36px;margin:0 auto 24px;box-shadow:0 4px 12px rgba(37,99,235,0.15);">
      <i class="bi bi-compass"></i>
    </div>
    
    <div style="font-size:72px;font-weight:900;color:var(--border);line-height:1;letter-spacing:-0.05em;margin-bottom:16px;">404</div>
    
    <h1 style="font-size:24px;font-weight:800;color:var(--text-main);margin-bottom:12px;letter-spacing:-0.02em;">Halaman Tidak Ditemukan</h1>
    <p style="font-size:15px;color:var(--text-muted);margin-bottom:32px;line-height:1.7;">
      Sumber daya akademik yang Anda cari tidak ada, telah dihapus, atau sedang tidak tersedia untuk sementara waktu.
    </p>
    
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
      <a href="{{ route('public.search') }}" class="ds-btn ds-btn-out" style="height:44px;padding:0 24px;">
        <i class="bi bi-search"></i> Cari
      </a>
      <a href="{{ route('public.home') }}" class="ds-btn ds-btn-pri" style="height:44px;padding:0 24px;">
        <i class="bi bi-house"></i> Beranda
      </a>
    </div>
  </div>
</div>
@endsection
