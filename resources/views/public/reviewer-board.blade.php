@extends('layouts.app')
@section('title', $page['title'] ?? 'Dewan Penelaah')
@section('meta_description', $page['meta_description'] ?? '')

@section('content')

<section class="section section-alt pb-0" style="padding-top: 100px;">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="section-tag">Mitra Bestari</div>
                <h1 class="hero-title">{!! $page['title'] ?? 'Dewan <span class="accent">Penelaah</span>' !!}</h1>
                <p class="hero-desc">{{ $page['meta_description'] ?? 'Kami dengan penuh syukur mengakui para penelaah terhormat kami.' }}</p>
            </div>
        </div>
    </div>
</section>

<section class="section pt-5">
    <div class="container">

        @if(!empty($page['body']))
        <div class="row mb-5">
            <div class="col-12" data-aos="fade-up">
                <div class="pub-card" style="color:var(--text-muted);line-height:1.8;">{!! $page['body'] !!}</div>
            </div>
        </div>
        @endif

        @php
            $reviewers = $dbReviewers ?? collect();
        @endphp
        <div class="row g-4">
            @forelse($reviewers as $i => $reviewer)
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ ($i % 4) * 80 }}">
                <div class="pub-card text-center h-100 d-flex flex-column justify-content-center" style="padding: 20px;">
                    <h4 style="font-weight:600;font-size:14px;margin-bottom:6px;color:var(--text-main);">{{ $reviewer->name }}</h4>
                    <p style="font-size:12px;color:var(--primary);font-weight:500;margin-bottom:0;">{{ $reviewer->affiliation ?? 'Lembaga Akademik' }}</p>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-5">
                <i class="bi bi-people" style="font-size:48px;"></i>
                <p class="mt-3">Belum ada mitra bestari (reviewer) terdaftar di sistem.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-5 text-center" data-aos="fade-up">
            <h4 class="mb-3" style="font-weight: 700;">Tertarik untuk menjadi penelaah?</h4>
            <p class="text-muted mb-4" style="max-width:600px;margin:0 auto;">Bergabunglah dengan komunitas pakar internasional kami dan berkontribusi untuk memajukan sains.</p>
            <a href="{{ route('register') }}" class="btn btn-primary" style="height:48px;padding:0 32px;font-size:16px;border-radius:30px;">
                Daftar sebagai Penelaah <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>

    </div>
</section>

@endsection
