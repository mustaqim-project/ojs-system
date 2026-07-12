@extends('layouts.app')
@section('title', $page['title'] ?? 'Tim Redaksi')
@section('meta_description', $page['meta_description'] ?? '')

@section('content')

<section class="section section-alt pb-0" style="padding-top: 100px;">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="section-tag">Orang</div>
                <h1 class="hero-title">{!! $page['title'] ?? 'Tim <span class="accent">Redaksi</span>' !!}</h1>
                <p class="hero-desc">{{ $page['meta_description'] ?? 'Dewan redaksi kami yang beragam dan diakui secara internasional.' }}</p>
            </div>
        </div>
    </div>
</section>

<section class="section pt-5">
    <div class="container">

        @if(!empty($page['body']))
        <div class="row mb-5">
            <div class="col-12" data-aos="fade-up">
                <div class="pub-card" style="color:var(--text-muted);line-height:1.8;">
                    {!! $page['body'] !!}
                </div>
            </div>
        </div>
        @endif

        {{-- Editor in Chief --}}
        @php $eic = $page['extra']['editor_in_chief'] ?? null; @endphp
        @if($eic)
        <h3 class="mb-4" style="font-weight:700;color:var(--text-main);" data-aos="fade-up">Pemimpin Redaksi</h3>
        <div class="row mb-5">
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="pub-card text-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($eic['name']) }}&background=2563eb&color=fff&size=100"
                         alt="{{ $eic['name'] }}" class="rounded-circle mb-3" width="96" height="96">
                    <h4 style="font-weight:700;font-size:18px;margin-bottom:4px;">{{ $eic['name'] }}</h4>
                    <p style="font-size:14px;color:var(--primary);font-weight:500;margin-bottom:12px;">{{ $eic['affiliation'] ?? '' }}</p>
                    <div class="d-flex justify-content-center gap-2">
                        @if(!empty($eic['orcid']))
                        <a href="{{ $eic['orcid'] }}" class="badge badge-neutral text-decoration-none" target="_blank"><i class="bi bi-person-badge"></i> ORCID</a>
                        @else
                        <a href="https://orcid.org" class="badge badge-neutral text-decoration-none" target="_blank"><i class="bi bi-person-badge"></i> ORCID</a>
                        @endif
                        <a href="https://scholar.google.com" class="badge badge-neutral text-decoration-none" target="_blank"><i class="bi bi-mortarboard-fill"></i> Scholar</a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Section Editors --}}
        @php $editors = $page['extra']['section_editors'] ?? []; @endphp
        @if(count($editors))
        <h3 class="mb-4" style="font-weight:700;color:var(--text-main);" data-aos="fade-up">Editor Bagian</h3>
        <div class="row g-4 mb-5">
            @foreach($editors as $i => $ed)
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ ($i % 3) * 100 }}">
                <div class="pub-card text-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($ed['name']) }}&background=e2e8f0&color=475569&size=100"
                         alt="{{ $ed['name'] }}" class="rounded-circle mb-3" width="80" height="80">
                    <h4 style="font-weight:600;font-size:16px;margin-bottom:4px;">{{ $ed['name'] }}</h4>
                    <p style="font-size:13px;color:var(--primary);font-weight:500;margin-bottom:6px;">{{ $ed['affiliation'] ?? '' }}</p>
                    @if(!empty($ed['area']))<p style="font-size:12px;color:var(--text-muted);margin-bottom:12px;">{{ $ed['area'] }}</p>@endif
                    <div class="d-flex justify-content-center gap-2">
                        <a href="https://orcid.org" class="badge badge-neutral text-decoration-none" target="_blank"><i class="bi bi-person-badge"></i> ORCID</a>
                        <a href="https://scholar.google.com" class="badge badge-neutral text-decoration-none" target="_blank"><i class="bi bi-mortarboard-fill"></i> Scholar</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</section>

@endsection
