{{-- errors/403.blade.php --}}
@extends('layouts.app')
@section('content')
<div style="min-height:60vh;display:flex;align-items:center;justify-content:center;padding:60px 24px;">
  <div style="text-align:center;max-width:400px;">
    <div style="font-size:80px;font-weight:800;color:#e2e8f0;line-height:1;letter-spacing:-.05em;margin-bottom:16px;">403</div>
    <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:8px;">Akses Ditolak</h1>
    <p style="font-size:14px;color:#64748b;margin-bottom:28px;line-height:1.7;">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
    <div style="display:flex;gap:10px;justify-content:center;">
      <a href="javascript:history.back()" style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:7px;font-size:13px;font-weight:600;background:#f1f5f9;color:#0f172a;border:1px solid #e2e8f0;text-decoration:none;">← Kembali</a>
      <a href="/" style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:7px;font-size:13px;font-weight:600;background:#2563eb;color:#fff;text-decoration:none;">Ke Beranda</a>
    </div>
  </div>
</div>
@endsection
