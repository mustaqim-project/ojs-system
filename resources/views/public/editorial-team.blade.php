@extends('layouts.app')
@section('title', $page['title'] ?? 'Tim Redaksi')
@section('meta_description', $page['meta_description'] ?? '')

@section('content')

    <section class="section section-alt pb-0" style="padding-top: 100px;">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-8" data-aos="fade-up">
                    <div class="section-tag">Redaksi</div>
                    <h1 class="hero-title">{!! $page['title'] ?? 'Tim <span class="accent">Redaksi</span>' !!}</h1>
                    <p class="hero-desc">
                        {{ $page['meta_description'] ?? 'Dewan redaksi kami yang beragam dan diakui secara internasional.' }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="section pt-5">
        <div class="container">

            @if (!empty($page['body']))
                <div class="row mb-5">
                    <div class="col-12" data-aos="fade-up">
                        <div class="pub-card" style="color:var(--text-muted);line-height:1.8;">
                            {!! $page['body'] !!}
                        </div>
                    </div>
                </div>
            @endif

            {{-- Editor in Chief --}}
            @php
                $dbEditors = $dbEditors ?? collect();

                // Try to find an editor-in-chief
                $dbEic = $dbEditors->first(
                    fn($u) => $u->role === 'managing-editor' || (method_exists($u, 'roles') && $u->roles->pluck('name')->contains('managing-editor')),
                );
                if (!$dbEic && $dbEditors->isNotEmpty()) {
                    $dbEic = $dbEditors->first(); // fallback to first editor from DB
                }

                $eic = $dbEic
                    ? [
                        'name' => $dbEic->name,
                        'affiliation' => $dbEic->affiliation ?? 'Lembaga Redaksi',
                        'email' => $dbEic->email,
                        'orcid' => $dbEic->orcid ?? '',
                        'scholar' => $dbEic->google_scholar_url ?? '',
                    ]
                    : null;

                $sectionEditors = $dbEditors->filter(fn($u) => !$dbEic || $u->id !== $dbEic->id);
                $editors = $sectionEditors->isNotEmpty()
                    ? $sectionEditors
                        ->map(
                            fn($u) => [
                                'name' => $u->name,
                                'affiliation' => $u->affiliation ?? 'Universitas/Instansi',
                                'area' => $u->research_interest ?? '',
                                'orcid' => $u->orcid ?? '',
                                'scholar' => $u->google_scholar_url ?? '',
                            ],
                        )
                        ->toArray()
                    : [];
            @endphp

            @if ($eic)
                <h3 class="mb-4" style="font-weight:700;color:var(--text-main);" data-aos="fade-up">Pemimpin Redaksi</h3>
                <div class="row mb-5">
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="pub-card text-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($eic['name']) }}&background=2563eb&color=fff&size=100"
                                alt="{{ $eic['name'] }}" class="rounded-circle mb-3" width="96" height="96">
                            <h4 style="font-weight:700;font-size:18px;margin-bottom:4px;">{{ $eic['name'] }}</h4>
                            <p style="font-size:14px;color:var(--primary);font-weight:500;margin-bottom:12px;">
                                {{ $eic['affiliation'] ?? '' }}</p>
                            @if (!empty($eic['orcid']) || !empty($eic['scholar']))
                                <div class="d-flex justify-content-center gap-2">
                                    @if (!empty($eic['orcid']))
                                        <a href="{{ str_starts_with($eic['orcid'], 'http') ? $eic['orcid'] : 'https://orcid.org/' . $eic['orcid'] }}" class="badge badge-neutral text-decoration-none"
                                            target="_blank"><i class="bi bi-person-badge"></i> ORCID</a>
                                    @endif
                                    @if (!empty($eic['scholar']))
                                        <a href="{{ $eic['scholar'] }}" class="badge badge-neutral text-decoration-none"
                                            target="_blank"><i class="bi bi-mortarboard-fill"></i> Scholar</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Section Editors --}}
            @if (count($editors))
                <h3 class="mb-4" style="font-weight:700;color:var(--text-main);" data-aos="fade-up">Editor Bagian</h3>
                <div class="row g-4 mb-5">
                    @foreach ($editors as $i => $ed)
                        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ ($i % 3) * 100 }}">
                            <div class="pub-card text-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($ed['name']) }}&background=e2e8f0&color=475569&size=100"
                                    alt="{{ $ed['name'] }}" class="rounded-circle mb-3" width="80" height="80">
                                <h4 style="font-weight:600;font-size:16px;margin-bottom:4px;">{{ $ed['name'] }}</h4>
                                <p style="font-size:13px;color:var(--primary);font-weight:500;margin-bottom:6px;">
                                    {{ $ed['affiliation'] ?? '' }}</p>
                                @if (!empty($ed['area']))
                                    <p style="font-size:12px;color:var(--text-muted);margin-bottom:12px;">
                                        {{ $ed['area'] }}</p>
                                @endif
                                @if (!empty($ed['orcid']) || !empty($ed['scholar']))
                                    <div class="d-flex justify-content-center gap-2">
                                        @if (!empty($ed['orcid']))
                                            <a href="{{ str_starts_with($ed['orcid'], 'http') ? $ed['orcid'] : 'https://orcid.org/' . $ed['orcid'] }}" class="badge badge-neutral text-decoration-none"
                                                target="_blank"><i class="bi bi-person-badge"></i> ORCID</a>
                                        @endif
                                        @if (!empty($ed['scholar']))
                                            <a href="{{ $ed['scholar'] }}" class="badge badge-neutral text-decoration-none"
                                                target="_blank"><i class="bi bi-mortarboard-fill"></i> Scholar</a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if (!$eic && count($editors) === 0)
                <div class="pub-card text-center py-5" data-aos="fade-up">
                    <i class="bi bi-people" style="font-size:48px; color:var(--text-muted); opacity:0.6;"></i>
                    <h5 class="mt-3" style="font-weight:700; color:var(--text-main);">Informasi Tim Redaksi</h5>
                    <p class="text-muted" style="max-width:500px; margin:8px auto 0;">Daftar dewan redaksi saat ini sedang dalam proses pembaruan oleh pengelola jurnal.</p>
                </div>
            @endif

        </div>
    </section>

@endsection
