@extends('layouts.app')
@section('title', $page['title'] ?? 'Dewan Penelaah')
@section('meta_description', $page['meta_description'] ?? '')

@section('content')

<section class="section section-alt pb-0" style="padding-top: 100px;">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="section-tag">Orang-orang</div>
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

        @php $reviewers = $page['extra']['reviewers'] ?? []; @endphp
        <div class="row g-4">
            @forelse($reviewers as $i => $reviewer)
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ ($i % 4) * 80 }}">
                <div class="pub-card text-center" style="padding: 20px;">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($reviewer['name']) }}&background=e0e7ff&color=3730a3&size=80"
                         alt="{{ $reviewer['name'] }}" class="rounded-circle mb-3" width="64" height="64">
                    <h4 style="font-weight:600;font-size:14px;margin-bottom:4px;">{{ $reviewer['name'] }}</h4>
                    <p style="font-size:12px;color:var(--primary);font-weight:500;margin-bottom:6px;">{{ $reviewer['affiliation'] ?? '' }}</p>
                    @if(!empty($reviewer['area']))<p style="font-size:11px;color:var(--text-muted);margin-bottom:12px;">{{ $reviewer['area'] }}</p>@endif
                    <div class="d-flex justify-content-center gap-2">
                        <a href="https://orcid.org" class="badge badge-neutral text-decoration-none" target="_blank"><i class="bi bi-person-badge"></i> ORCID</a>
                        <a href="https://scholar.google.com" class="badge badge-neutral text-decoration-none" target="_blank"><i class="bi bi-mortarboard-fill"></i> Scholar</a>
                    </div>
                </div>
            </div>
            @empty
            {{-- Default placeholder reviewers when none in DB --}}
            @foreach(range(1, 8) as $i)
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ ($i % 4) * 80 }}">
                <div class="pub-card text-center" style="padding: 20px;">
                    <img src="https://ui-avatars.com/api/?name=Penelaah+{{ $i }}&background=e0e7ff&color=3730a3&size=80"
                         alt="Penelaah {{ $i }}" class="rounded-circle mb-3" width="64" height="64">
                    <h4 style="font-weight:600;font-size:14px;margin-bottom:4px;">Dr. Penelaah {{ $i }}</h4>
                    <p style="font-size:12px;color:var(--primary);font-weight:500;margin-bottom:12px;">Universitas Sains</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="https://orcid.org" class="badge badge-neutral text-decoration-none" target="_blank"><i class="bi bi-person-badge"></i> ORCID</a>
                        <a href="https://scholar.google.com" class="badge badge-neutral text-decoration-none" target="_blank"><i class="bi bi-mortarboard-fill"></i> Scholar</a>
                    </div>
                </div>
            </div>
            @endforeach
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
