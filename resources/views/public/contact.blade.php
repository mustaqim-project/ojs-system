@extends('layouts.app')
@section('title', $page['title'] ?? 'Contact Us')
@section('meta_description', $page['meta_description'] ?? '')

@section('content')

<section class="section section-alt pb-0" style="padding-top: 100px;">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="section-tag">Reach Out</div>
                <h1 class="hero-title">Contact <span class="accent">Us</span></h1>
                <p class="hero-desc">{{ $page['meta_description'] ?? 'Have a question about our publication process? We\'re here to help.' }}</p>
            </div>
        </div>
    </div>
</section>

<section class="section pt-5">
    <div class="container">
        <div class="row g-5">

            {{-- Contact Information --}}
            <div class="col-lg-5" data-aos="fade-up" data-aos-delay="100">
                <h3 class="mb-4" style="font-weight: 700;">Get in Touch</h3>
                @if(!empty($page['body']))
                    <div class="mb-4" style="color:var(--text-muted);line-height:1.8;">{!! $page['body'] !!}</div>
                @else
                    <p class="text-muted mb-5">Our editorial office is open Monday–Friday, 9:00 AM to 5:00 PM (GMT+7). We aim to respond within 48 hours.</p>
                @endif

                @if(!empty($page['extra']['address']))
                <div class="d-flex mb-4">
                    <div style="width:48px;height:48px;background:rgba(37,99,235,0.1);color:var(--primary);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;margin-right:16px;">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div>
                        <h5 style="font-weight:600;font-size:16px;margin-bottom:4px;">Address</h5>
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
                        <h5 style="font-weight:600;font-size:16px;margin-bottom:4px;">Phone</h5>
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
                <div class="pub-card" style="padding: 40px;">
                    <h4 class="mb-4" style="font-weight: 700;">Send us a Message</h4>
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-weight:600;font-size:14px;">Your Name</label>
                                <input type="text" class="form-control" placeholder="Dr. John Doe">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-weight:600;font-size:14px;">Email Address</label>
                                <input type="email" class="form-control" placeholder="john@university.edu">
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-weight:600;font-size:14px;">Subject</label>
                                <select class="form-control">
                                    <option>General Inquiry</option>
                                    <option>Submission Issue</option>
                                    <option>Review Process</option>
                                    <option>Indexing Query</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-weight:600;font-size:14px;">Message</label>
                                <textarea class="form-control" rows="5" placeholder="How can we help you?"></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="button" class="btn btn-primary w-100" style="height:48px;font-weight:600;">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
