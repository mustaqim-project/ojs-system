@extends('layouts.app')
@section('title', $page['title'] ?? 'Hubungi Kami')
@section('meta_description', $page['meta_description'] ?? '')

@section('content')

<section class="section section-alt pb-0" style="padding-top: 100px;">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="section-tag">Hubungi Kami</div>
                <h1 class="hero-title">{!! $page['title'] ?? 'Hubungi <span class="accent">Kami</span>' !!}</h1>
                <p class="hero-desc">{{ $page['meta_description'] ?? 'Punya pertanyaan tentang proses publikasi kami? Kami di sini untuk membantu.' }}</p>
            </div>
        </div>
    </div>
</section>

<section class="section pt-5">
    <div class="container">
        <div class="row g-5">

            {{-- Contact Information --}}
            <div class="col-lg-5" data-aos="fade-up" data-aos-delay="100">
                <h3 class="mb-4" style="font-weight: 700;">Tetap Terhubung</h3>
                @if(!empty($page['body']))
                    <div class="mb-4" style="color:var(--text-muted);line-height:1.8;">{!! $page['body'] !!}</div>
                @else
                    <p class="text-muted mb-5">Kantor redaksi kami buka Senin–Jumat, 09.00 hingga 17.00 (GMT+7). Kami berusaha merespons dalam waktu 48 jam.</p>
                @endif

                @if(!empty($page['extra']['address']))
                <div class="d-flex mb-4">
                    <div style="width:48px;height:48px;background:rgba(37,99,235,0.1);color:var(--primary);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;margin-right:16px;">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div>
                        <h5 style="font-weight:600;font-size:16px;margin-bottom:4px;">Alamat</h5>
                        <p style="color:var(--text-muted);font-size:14px;margin:0;">{{ $page['extra']['address'] }}</p>
                    </div>
                </div>
                @endif

                @if(!empty($page['extra']['email']))
                <div class="d-flex mb-4">
                    <div style="width:48px;height:48px;background:rgba(37,99,235,0.1);color:var(--primary);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;margin-right:16px;">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <div>
                        <h5 style="font-weight:600;font-size:16px;margin-bottom:4px;">Email</h5>
                        <p style="color:var(--text-muted);font-size:14px;margin:0;">
                            <a href="mailto:{{ $page['extra']['email'] }}" class="text-decoration-none">{{ $page['extra']['email'] }}</a>
                        </p>
                    </div>
                </div>
                @endif

                @if(!empty($page['extra']['phone']))
                <div class="d-flex mb-4">
                    <div style="width:48px;height:48px;background:rgba(37,99,235,0.1);color:var(--primary);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;margin-right:16px;">
                        <i class="bi bi-telephone-fill"></i>
                    </div>
                    <div>
                        <h5 style="font-weight:600;font-size:16px;margin-bottom:4px;">Telepon</h5>
                        <p style="color:var(--text-muted);font-size:14px;margin:0;">{{ $page['extra']['phone'] }}</p>
                    </div>
                </div>
                @endif

                @if(!empty($page['extra']['maps_embed_url']))
                <div class="mt-4" style="border-radius:12px;overflow:hidden;border:1px solid var(--border);">
                    <iframe src="{{ $page['extra']['maps_embed_url'] }}" width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
                @endif
            </div>

            {{-- Contact Form --}}
            <div class="col-lg-7" data-aos="fade-up" data-aos-delay="200">
                <div class="pub-card contact-form-card" style="padding: 40px;">
                    <h4 class="mb-4" style="font-weight: 700;">Kirimkan Pesan</h4>
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-weight:600;font-size:14px;">Nama Anda</label>
                                <input type="text" class="form-control" placeholder="Dr. Budi Santoso">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-weight:600;font-size:14px;">Alamat Email</label>
                                <input type="email" class="form-control" placeholder="budi@universitas.ac.id">
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-weight:600;font-size:14px;">Subjek</label>
                                <select class="form-control">
                                    <option>Pertanyaan Umum</option>
                                    <option>Masalah Pengiriman</option>
                                    <option>Proses Tinjauan</option>
                                    <option>Pertanyaan Pengindeksan</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-weight:600;font-size:14px;">Pesan</label>
                                <textarea class="form-control" rows="5" placeholder="Bagaimana kami bisa membantu Anda?"></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="button" class="btn btn-primary w-100" style="height:48px;font-weight:600;">Kirim Pesan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
