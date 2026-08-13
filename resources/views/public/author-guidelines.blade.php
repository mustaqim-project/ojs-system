@extends('layouts.app')

@section('content')

{{-- Hero Section --}}
<section class="section section-alt pb-0" style="padding-top: 100px;">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="section-tag">Panduan</div>
                <h1 class="hero-title">{!! $page['title'] ?? 'Panduan <span class="accent">Penulis</span>' !!}</h1>
                <p class="hero-desc">{{ $page['meta_description'] ?? 'Semua yang perlu Anda ketahui untuk mempersiapkan dan mengirimkan naskah Anda.' }}</p>
            </div>
        </div>
    </div>
</section>

{{-- Content Section --}}
<section class="section pt-5 pb-5 mb-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                
                <div class="pub-card mb-4" data-aos="fade-up">
                    <div style="color:var(--text-muted);line-height:1.8;">{!! $page['body'] !!}</div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div style="position: sticky; top: 100px; z-index: 10;">
                    <div class="pub-card text-center" data-aos="fade-up" data-aos-delay="300">
                    <div style="width: 64px; height: 64px; background: rgba(37,99,235,0.1); color: var(--primary); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 24px;">
                        <i class="bi bi-cloud-arrow-up"></i>
                    </div>
                    <h4 class="mb-3" style="font-weight: 700; font-size: 18px;">Siap Mengajukan Naskah?</h4>
                    <p style="font-size: 14px; color: var(--text-muted); line-height: 1.6; margin-bottom: 24px;">Unduh template naskah resmi kami untuk memastikan format Anda benar sebelum dikirimkan.</p>
                    
                    <a href="{{ !empty($page['extra']['template_url']) ? $page['extra']['template_url'] : '#' }}"
                       class="btn btn-secondary w-100 mb-3" style="height: 44px;"
                       {{ !empty($page['extra']['template_url']) ? 'target="_blank"' : '' }}>
                        <i class="bi bi-file-word me-2"></i> Unduh Template (.docx)
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-primary w-100" style="height: 44px;">
                        Ajukan Naskah <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
