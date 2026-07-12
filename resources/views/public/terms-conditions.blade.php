@extends('layouts.app')
@section('title', $page['title'] ?? 'Terms & Conditions')
@section('meta_description', $page['meta_description'] ?? 'Terms of service for using our platform.')
@section('content')
<div class="container py-5" style="max-width:900px;">
    <div class="row justify-content-center">
        <div class="col-12">
            <h1 class="mb-2" style="font-weight:800;">{{ $page['title'] ?? 'Terms &amp; Conditions' }}</h1>
            <p class="text-muted mb-4" style="font-size:14px;">Last updated: {{ date('F j, Y') }}</p>
            <div class="pub-card" style="padding:40px;color:var(--text-muted);line-height:1.9;font-size:15px;">
                {!! !empty($page['body']) ? $page['body'] : \App\Models\Setting::get('terms_conditions', '<p>Please update the terms and conditions in admin settings.</p>') !!}
            </div>
        </div>
    </div>
</div>
@endsection
