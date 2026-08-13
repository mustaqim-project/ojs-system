@extends('layouts.app')
@section('title', $page['title'] ?? 'Syarat & Ketentuan')
@section('meta_description', $page['meta_description'] ?? 'Ketentuan layanan untuk menggunakan platform kami.')
@section('content')
<div class="container py-5" style="max-width:900px;">
    <div class="row justify-content-center">
        <div class="col-12">
            <h1 class="mb-2" style="font-weight:800;">{{ $page['title'] ?? 'Syarat &amp; Ketentuan' }}</h1>
            <p class="text-muted mb-4" style="font-size:14px;">Terakhir diperbarui: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('j F Y') }}</p>
            <div class="pub-card" style="padding:40px;color:var(--text-muted);line-height:1.9;font-size:15px;">
                {!! !empty($page['body']) ? $page['body'] : \App\Models\Setting::get('terms_conditions', '<p>Silakan perbarui syarat dan ketentuan di pengaturan admin.</p>') !!}
            </div>
        </div>
    </div>
</div>
@endsection
