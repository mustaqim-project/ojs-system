@extends('layouts.app')
@section('title', $page['title'] ?? 'Indeksasi & Abstraksi')
@section('meta_description', $page['meta_description'] ?? '')

@section('content')

<section class="section section-alt pb-0" style="padding-top: 100px;">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="section-tag">Indeksasi</div>
                <h1 class="hero-title">{!! $page['title'] ?? 'Indeksasi &amp; <span class="accent">Abstraksi</span>' !!}</h1>
                <p class="hero-desc">{{ $page['meta_description'] ?? 'Jurnal kami diindeks dalam basis data global terkemuka.' }}</p>
            </div>
        </div>
    </div>
</section>

<section class="section pt-5">
    <div class="container">

        @if(!empty($page['body']))
        <div class="row mb-4"><div class="col-12" data-aos="fade-up">
            <div style="color:var(--text-muted);line-height:1.8;">{!! $page['body'] !!}</div>
        </div></div>
        @endif

        @php
        $indexes = $page['extra']['indexes'] ?? [
            ['name'=>'Google Scholar', 'url'=>'https://scholar.google.com', 'icon'=>'bi-google',         'color'=>'#4285F4'],
            ['name'=>'Crossref',       'url'=>'https://crossref.org',       'icon'=>'bi-diagram-3-fill', 'color'=>'#F0AB00'],
            ['name'=>'DOAJ',           'url'=>'https://doaj.org',           'icon'=>'bi-journal-check',  'color'=>'#004d40'],
            ['name'=>'Scopus',         'url'=>'https://scopus.com',         'icon'=>'bi-bar-chart-fill', 'color'=>'#ff6600'],
            ['name'=>'SINTA',          'url'=>'https://sinta.kemdikbud.go.id','icon'=>'bi-award-fill',   'color'=>'#1E88E5'],
            ['name'=>'Garuda',         'url'=>'https://garuda.kemdikbud.go.id','icon'=>'bi-shield-check','color'=>'#2563eb'],
        ];
        $icons = ['bi-google','bi-diagram-3-fill','bi-journal-check','bi-bar-chart-fill','bi-award-fill','bi-shield-check'];
        $colors = ['#4285F4','#F0AB00','#004d40','#ff6600','#1E88E5','#2563eb'];
        @endphp

        <div class="row g-4 justify-content-center">
            @foreach($indexes as $i => $idx)
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ ($i % 3 + 1) * 100 }}">
                <a href="{{ $idx['url'] ?? '#' }}" target="_blank"
                   style="display:block;text-decoration:none;"
                   onmouseover="this.querySelector('.ic').style.transform='scale(1.15)'"
                   onmouseout="this.querySelector('.ic').style.transform='scale(1)'">
                    <div class="pub-card text-center h-100 d-flex flex-column align-items-center justify-content-center" style="transition:box-shadow 0.2s;">
                        <div class="ic" style="font-size:40px;color:{{ $idx['color'] ?? $colors[$i % count($colors)] }};margin-bottom:16px;transition:transform 0.3s cubic-bezier(0.175,0.885,0.32,1.275);">
                            <i class="{{ $idx['icon'] ?? $icons[$i % count($icons)] }}"></i>
                        </div>
                        <h4 style="font-weight:700;font-size:18px;margin-bottom:8px;color:var(--text-main);">{{ $idx['name'] }}</h4>
                        @if(!empty($idx['description']))
                        <p style="font-size:13px;color:var(--text-muted);margin:0;">{{ $idx['description'] }}</p>
                        @endif
                    </div>
                </a>
            </div>
            @endforeach
        </div>

    </div>
</section>

@endsection
