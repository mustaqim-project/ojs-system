@extends('layouts.app')
@section('content')
    {{-- ── HERO SECTION ── --}}
    <section class="hero" style="position:relative; overflow:hidden;">
        <div class="container" style="max-width:1400px; position:relative; z-index:2;">
            <div class="row align-items-center g-5">
                <div class="col-12 col-lg-7">
                    <div class="hero-tag mb-3" data-aos="fade-up" data-aos-delay="100">
                        <i class="bi bi-patch-check-fill text-primary"></i> Penerbit Akademik Terkemuka
                    </div>
                    <h1 class="hero-title" data-aos="fade-up" data-aos-delay="200">
                        {!! $global_settings['hero_title'] ??
                            'Majukan Pengetahuan.<br>Terbitkan dengan <span class="accent">Unggul.</span>' !!}
                    </h1>
                    <p class="hero-desc" data-aos="fade-up" data-aos-delay="300"
                        style="font-size:1.1rem; color:var(--text-muted); max-width:600px; margin:24px 0;">
                        {{ $global_settings['hero_subtitle'] ?? $siteDescription ?: 'Platform penerbitan ilmiah tingkat perusahaan yang menawarkan tinjauan sejawat yang ketat, alur kerja yang transparan, dan jangkauan global untuk peneliti dan akademisi.' }}
                    </p>

                    <div class="d-flex flex-wrap gap-3 mt-4" data-aos="fade-up" data-aos-delay="400">
                        <a href="{{ $global_settings['hero_button_link'] ?? route('register') }}"
                            class="btn btn-primary shadow"
                            style="padding:12px 28px; font-weight:600; font-size:16px; border-radius:8px;">
                            {{ $global_settings['hero_button_text'] ?? 'Kirim Naskah' }} <i
                                class="bi bi-arrow-right ms-2"></i>
                        </a>
                        <a href="{{ route('public.articles.index') }}" class="btn btn-light shadow-sm"
                            style="padding:12px 28px; font-weight:600; font-size:16px; border-radius:8px; border:1px solid var(--border);">
                            Jelajahi Artikel <i class="bi bi-journal-text ms-2"></i>
                        </a>
                    </div>

                    <div class="mt-5" data-aos="fade-up" data-aos-delay="500">
                        <form action="{{ route('public.search') }}" method="GET" class="hero-search-form d-flex"
                            style="position:relative;">
                            <i class="bi bi-search"
                                style="position:absolute; left:16px; top:50%; transform:translateY(-50%); color:var(--text-muted); pointer-events:none; z-index:1;"></i>
                            <input type="text" name="q" class="form-control form-control-lg shadow-sm"
                                placeholder="Cari artikel, penulis, atau kata kunci..."
                                style="padding-left:44px; border-radius:8px; font-size:15px; border-color:var(--border);">
                            <button type="submit" class="btn btn-primary"
                                style="position:absolute; right:6px; top:6px; bottom:6px; border-radius:6px; font-weight:600;">Cari</button>
                        </form>
                    </div>
                </div>

                <div class="col-12 col-lg-5 d-none d-lg-block" data-aos="zoom-in" data-aos-delay="300">
                    <div style="position:relative; width:100%; height:500px;">
                        <div
                            style="position:absolute; inset:0; background:radial-gradient(circle at center, var(--primary-light) 0%, transparent 70%); opacity:0.3; border-radius:50%;">
                        </div>
                        {{-- Animated Abstract Graphic --}}
                        <div style="position:absolute; right:20px; top:50px; width:340px; background:var(--bg-app); border:1px solid var(--border); border-radius:var(--radius-lg); padding:24px; box-shadow:0 20px 40px rgba(0,0,0,0.08); z-index:2;"
                            class="hover-shadow">
                            <div
                                style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:12px;">
                                <span
                                    style="font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">Publikasi
                                    Terbaru</span>
                                <span class="badge bg-success" style="font-size:10px;">Ditinjau Sejawat</span>
                            </div>
                            @if ($latestArticles->isNotEmpty())
                                @php $featured = $latestArticles->first(); @endphp
                                <h5 style="font-weight:800; font-size:18px; line-height:1.4; margin-bottom:12px;">
                                    {{ Str::limit($featured->title, 70) }}</h5>
                                <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
                                    <div
                                        style="width:32px; height:32px; background:var(--primary-light); color:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:14px;">
                                        {{ substr($featured->author->name ?? 'A', 0, 1) }}
                                    </div>
                                    <div>
                                        <div style="font-size:13px; font-weight:600;">
                                            {{ $featured->author->name ?? 'Penulis Tidak Diketahui' }}</div>
                                        <div style="font-size:11px; color:var(--text-muted);">Diterbitkan:
                                            {{ $featured->published_at ? $featured->published_at->locale('id')->translatedFormat('d M Y') : '—' }}
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('public.articles.show', $featured->slug ?? '') }}"
                                    class="btn btn-sm btn-outline-primary w-100"
                                    style="border-radius:6px; font-weight:600;">Baca Artikel</a>
                            @else
                                <p class="text-muted small">Belum ada artikel yang diterbitkan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── STATISTICS (COUNTUP) ── --}}
    <section class="section" style="background:var(--bg-surface); border-bottom:1px solid var(--border);">
        <div class="container" style="max-width:1200px;">
            <div class="row g-4 text-center">
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                    <h2 class="display-5" style="font-weight:800; color:var(--primary);"
                        data-countup="{{ $totalPublished }}">{{ $totalPublished }}</h2>
                    <p class="stat-label-text"
                        style="font-weight:600; color:var(--text-muted); font-size:15px; text-transform:uppercase; letter-spacing:1px;">
                        Artikel Diterbitkan</p>
                </div>
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                    <h2 class="display-5" style="font-weight:800; color:var(--primary);"
                        data-countup="{{ $totalAuthors }}">{{ $totalAuthors }}</h2>
                    <p class="stat-label-text"
                        style="font-weight:600; color:var(--text-muted); font-size:15px; text-transform:uppercase; letter-spacing:1px;">
                        Penulis Aktif</p>
                </div>
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                    <h2 class="display-5" style="font-weight:800; color:var(--primary);"
                        data-countup="{{ $totalReviewers }}">{{ $totalReviewers }}</h2>
                    <p class="stat-label-text"
                        style="font-weight:600; color:var(--text-muted); font-size:15px; text-transform:uppercase; letter-spacing:1px;">
                        Mitra Bestari Global</p>
                </div>
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="400">
                    <h2 class="display-5" style="font-weight:800; color:var(--primary);"
                        data-countup="{{ $avgFirstDecisionDays }}">{{ $avgFirstDecisionDays }}</h2>
                    <p class="stat-label-text"
                        style="font-weight:600; color:var(--text-muted); font-size:15px; text-transform:uppercase; letter-spacing:1px;">
                        Waktu hingga Keputusan Pertama</p>
                </div>
            </div>
        </div>
    </section>





    {{-- ── JARINGAN JURNAL ── --}}
    <section class="section" style="background:var(--bg-app); border-top:1px solid var(--border);">
        <div class="container" style="max-width:1100px;">
            <div class="text-center mb-5" data-aos="fade-up">
                <div class="section-tag">Ekosistem</div>
                <h2 class="section-title">Jaringan Jurnal Kami</h2>
                <p class="section-desc mx-auto">Kami terhubung dengan berbagai jurnal ilmiah bereputasi di bawah Indonesia
                    Madani. Jelajahi portal jurnal lainnya dalam jaringan kami.</p>
            </div>

            @php
                $networkJournals = \App\Models\Setting::get('network_journals');
                if (empty($networkJournals)) {
                    $otherJournals = \App\Models\Journal::where('is_active', true)->get();
                    if ($otherJournals->count() > 1) {
                        $networkJournals = $otherJournals->map(fn($j) => [
                            'name' => $j->abbreviation ?: Str::limit($j->title, 15),
                            'title' => $j->title,
                            'description' => $j->description ? Str::limit(strip_tags($j->description), 95) : 'Publikasi ilmiah terakreditasi.',
                            'url' => route('public.journals.show', $j->slug),
                            'logo' => $j->cover_image ? asset($j->cover_image) : null,
                            'domain' => $j->slug,
                        ])->toArray();
                    }
                }
                if (empty($networkJournals)) {
                    $networkJournals = [
                        [
                            'name' => 'JHAM',
                            'title' => 'Jurnal Humaniora dan Agama Madani',
                            'description' => 'Publikasi ilmiah bidang humaniora & keagamaan.',
                            'url' => 'https://jham.indonesiamadani.com/',
                            'logo' => 'https://jham.indonesiamadani.com/uploads/settings/1786621030_WhatsApp%20Image%202026-07-12%20at%2013.34.49(2).jpeg',
                            'domain' => 'jham.indonesiamadani.com',
                        ],
                        [
                            'name' => 'JPSTEM',
                            'title' => 'Jurnal Pendidikan Sains, Teknologi, Engineering & Matematika',
                            'description' => 'Inovasi riset STEM Indonesia.',
                            'url' => 'https://jpstem.indonesiamadani.com/',
                            'logo' => 'https://jpstem.indonesiamadani.com/uploads/settings/1786625429_WhatsApp%20Image%202026-07-12%20at%2013.34.49(1).jpeg',
                            'domain' => 'jpstem.indonesiamadani.com',
                        ],
                        [
                            'name' => 'JPMM',
                            'title' => 'Jurnal Pengabdian Dan Pemberdayaan Masyarakat',
                            'description' => 'Pengabdian dan pemberdayaan masyarakat berbasis ilmu pengetahuan & teknologi.',
                            'url' => 'https://jpmm.indonesiamadani.com/',
                            'logo' => 'https://jpmm.indonesiamadani.com/uploads/settings/1786623829_WhatsApp%20Image%202026-07-12%20at%2013.34.49.jpeg',
                            'domain' => 'jpmm.indonesiamadani.com',
                        ],
                    ];
                }
            @endphp

            <div class="row g-4 justify-content-center">
                @foreach($networkJournals as $i => $net)
                <div class="col-12 col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                    <a href="{{ $net['url'] }}" target="_blank" rel="noopener noreferrer" style="text-decoration:none;">
                        <div class="pub-card h-100 text-center"
                            style="padding:32px 24px; transition:transform 0.3s, box-shadow 0.3s;"
                            onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 20px 48px rgba(37,99,235,0.15)';"
                            onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='';">
                            <div style="width:96px; height:96px; border-radius:16px; overflow:hidden; margin:0 auto 20px; box-shadow:0 8px 24px rgba(0,0,0,0.12); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; background:var(--bg-surface);">
                                @if(!empty($net['logo']))
                                    <img src="{{ $net['logo'] }}" alt="{{ $net['name'] }} Logo" style="width:100%; height:100%; object-fit:cover;">
                                @else
                                    <i class="bi bi-journal-bookmark-fill" style="font-size:40px; color:var(--primary);"></i>
                                @endif
                            </div>
                            <h5 style="font-weight:800; font-size:17px; color:var(--text-main); margin-bottom:8px;">{{ $net['name'] }}</h5>
                            <p style="font-size:13px; color:var(--text-muted); margin-bottom:20px; line-height:1.6;">{{ $net['description'] }}</p>
                            <div style="font-size:12px; font-weight:700; color:var(--primary); background:var(--primary-light); display:inline-block; padding:5px 14px; border-radius:20px;">
                                {{ $net['domain'] ?? $net['name'] }} <i class="bi bi-box-arrow-up-right ms-1" style="font-size:10px;"></i>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>


    {{-- ── CURRENT ISSUE & LATEST ARTICLES ── --}}
    <section class="section" style="background:var(--bg-app);">
        <div class="container" style="max-width:1400px;">

            <div class="d-flex justify-content-between align-items-end mb-5" data-aos="fade-up">
                <div>
                    <div class="section-tag">Rilis Baru</div>
                    <h2 class="section-title mb-0">Artikel Terbaru</h2>
                </div>
                <a href="{{ route('public.articles.index') }}" class="btn btn-light"
                    style="font-weight:600; border-radius:20px; padding:8px 20px;">Lihat Semua <i
                        class="bi bi-arrow-right ms-1"></i></a>
            </div>

            <div class="row g-4">
                @forelse($latestArticles as $index => $article)
                    <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <div class="pub-card d-flex flex-column" style="height:100%; position:relative; padding:24px;">
                            <div
                                style="font-size:12px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px;">
                                Artikel Penelitian</div>
                            <h4 style="font-weight:700; font-size:18px; line-height:1.4; margin-bottom:12px;">
                                <a href="{{ route('public.articles.show', $article->slug ?? '') }}"
                                    style="text-decoration:none; color:var(--text-main);"
                                    class="hover-primary">{{ Str::limit($article->title, 75) }}</a>
                            </h4>
                            <p
                                style="color:var(--text-muted); font-size:14px; line-height:1.6; margin-bottom:20px; flex-grow:1;">
                                {{ Str::limit(strip_tags($article->abstract), 120) }}
                            </p>

                            <div style="display:flex; flex-direction:column; gap:16px;">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div
                                        style="width:36px; height:36px; background:var(--bg-surface); border:1px solid var(--border); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; color:var(--text-main);">
                                        {{ substr($article->author->name ?? 'A', 0, 1) }}
                                    </div>
                                    <div style="font-size:13px; font-weight:600; color:var(--text-main);">
                                        {{ $article->author->name ?? 'Penulis Tidak Diketahui' }}
                                        <div style="color:var(--text-muted); font-weight:400; font-size:11px;">Afiliasi
                                            Penulis</div>
                                    </div>
                                </div>

                                <div
                                    style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:12px;">
                                    <div style="display:flex; gap:16px; font-size:12px; color:var(--text-muted);">
                                        <span title="Dilihat"><i class="bi bi-eye me-1"></i>
                                            {{ number_format($article->views_count ?? 0, 0, ',', '.') }}</span>
                                        <span title="Diunduh"><i class="bi bi-download me-1"></i>
                                            {{ number_format($article->downloads_count ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    <a href="{{ route('public.articles.show', $article->slug ?? '') }}"
                                        style="font-size:13px; font-weight:700; color:var(--primary); text-decoration:none;">Baca
                                        Selengkapnya <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-5">
                        <i class="bi bi-journal-x" style="font-size:48px;"></i>
                        <p class="mt-3">Belum ada artikel yang diterbitkan.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </section>


    {{-- ── JURNAL KAMI ── --}}
    <section class="section" style="background:var(--bg-surface); border-top:1px solid var(--border);">
        <div class="container" style="max-width:1200px;">
            <div class="text-center mb-5" data-aos="fade-up">
                <div class="section-tag">Publikasi</div>
                <h2 class="section-title">Jurnal Kami</h2>
                <p class="section-desc mx-auto">Temukan jurnal ilmiah kami yang bereputasi dan terpercaya di berbagai
                    bidang keilmuan.</p>
            </div>

            <div class="row g-4">
                @forelse($journals as $index => $journal)
                    <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $index * 80 }}">
                        <a href="{{ route('public.journals.show', $journal->slug) }}" style="text-decoration:none;">
                            <div class="pub-card h-100"
                                style="padding:0; overflow:hidden; transition:transform 0.3s, box-shadow 0.3s; cursor:pointer;"
                                onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 40px rgba(0,0,0,0.12)';"
                                onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='';">
                                {{-- Cover --}}
                                <div
                                    style="position:relative; height:160px; overflow:hidden; background:linear-gradient(135deg, var(--primary-light) 0%, rgba(37,99,235,0.04) 100%);">
                                    @if ($journal->cover_image)
                                        <img src="{{ asset($journal->cover_image) }}" alt="{{ $journal->title }}"
                                            style="width:100%; height:100%; object-fit:cover;">
                                    @else
                                        <div
                                            style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:60px; color:var(--primary); opacity:0.25;">
                                            <i class="bi bi-journal-bookmark-fill"></i>
                                        </div>
                                    @endif
                                    @if ($journal->abbreviation)
                                        <span
                                            style="position:absolute; top:12px; left:12px; background:rgba(255,255,255,0.92); backdrop-filter:blur(8px); color:var(--primary); font-size:11px; font-weight:800; font-family:monospace; padding:4px 10px; border-radius:6px; letter-spacing:0.5px; box-shadow:0 2px 8px rgba(0,0,0,0.08);">{{ $journal->abbreviation }}</span>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div style="padding:20px;">
                                    <h5
                                        style="font-weight:700; font-size:16px; color:var(--text-main); margin-bottom:8px; line-height:1.4;">
                                        {{ Str::limit($journal->title, 60) }}</h5>
                                    @if ($journal->description)
                                        <p
                                            style="font-size:13px; color:var(--text-muted); margin-bottom:14px; line-height:1.6;">
                                            {{ Str::limit(strip_tags($journal->description), 90) }}</p>
                                    @endif
                                    <div
                                        style="display:flex; align-items:center; justify-content:space-between; font-size:12px; color:var(--text-muted); border-top:1px solid var(--border); padding-top:12px;">
                                        <span><i class="bi bi-file-earmark-text me-1" style="color:var(--primary);"></i>
                                            {{ $journal->published_articles_count }} Artikel</span>
                                        <span style="color:var(--primary); font-weight:700;">Lihat Jurnal <i
                                                class="bi bi-arrow-right"></i></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-5">
                        <i class="bi bi-journal-x" style="font-size:48px;"></i>
                        <p class="mt-3">Belum ada jurnal yang tersedia.</p>
                    </div>
                @endforelse
            </div>

            @if ($journals->count() > 0)
                <div class="text-center mt-5" data-aos="fade-up">
                    <a href="{{ route('public.journals.index') }}" class="btn btn-outline-primary"
                        style="font-weight:700; border-radius:20px; padding:10px 28px;">
                        Lihat Semua Jurnal <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- ── WORKFLOW TIMELINE ── --}}
    <section class="section" style="background:var(--bg-app);">
        <div class="container" style="max-width:1000px;">
            <div class="text-center mb-5" data-aos="fade-up">
                <div class="section-tag">Proses</div>
                <h2 class="section-title">Alur Kerja Publikasi</h2>
                <p class="section-desc mx-auto">Proses 4 langkah kami yang transparan dari pengajuan hingga publikasi.</p>
            </div>

            <div class="row g-4 position-relative">
                <!-- Line connecting steps (Desktop only) -->
                <div class="d-none d-lg-block"
                    style="position:absolute; top:24px; left:10%; right:10%; height:2px; background:var(--border); z-index:1;">
                </div>

                @php
                    $workflow = [
                        [
                            'icon' => 'bi-cloud-arrow-up',
                            'title' => '1. Pengajuan Naskah',
                            'desc' => 'Penulis mengajukan naskah melalui sistem OJS.',
                        ],
                        [
                            'icon' => 'bi-search',
                            'title' => '2. Pemeriksaan & Penelaahan',
                            'desc' => 'Pemeriksaan plagiarisme dan penelaahan sejawat ganda-buta (double-blind).',
                        ],
                        [
                            'icon' => 'bi-pencil-square',
                            'title' => '3. Revisi',
                            'desc' => 'Penulis merevisi naskah berdasarkan umpan balik mitra bestari.',
                        ],
                        [
                            'icon' => 'bi-globe',
                            'title' => '4. Publikasi',
                            'desc' =>
                                'Penyuntingan naskah (copyediting), tata letak (layouting), dan penerbitan global.',
                        ],
                    ];
                @endphp

                @foreach ($workflow as $index => $step)
                    <div class="col-12 col-md-6 col-lg-3 text-center position-relative" style="z-index:2;"
                        data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <div
                            style="width:50px; height:50px; background:white; border:2px solid var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:20px; color:var(--primary); box-shadow:0 4px 10px rgba(0,0,0,0.05);">
                            <i class="{{ $step['icon'] }}"></i>
                        </div>
                        <h5 style="font-weight:700; font-size:16px;">{{ $step['title'] }}</h5>
                        <p style="font-size:13px; color:var(--text-muted);">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>



    {{-- ── NEWSLETTER & AJAKAN PENGAJUAN NASKAH ── --}}
    <section class="section text-center" style="background:var(--primary); color:white;">
        <div class="container" style="max-width:800px;" data-aos="zoom-in">
            <h2 style="font-weight:800; margin-bottom:16px;">Siap Mempublikasikan Penelitian Anda?</h2>
            <p style="font-size:18px; opacity:0.9; margin-bottom:32px;">Bergabunglah dengan ribuan penulis yang telah
                mempublikasikan penelitian inovatif mereka bersama kami. Pengajuan naskah kini terbuka untuk terbitan
                berikutnya.</p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="{{ route('register') }}" class="btn btn-light"
                    style="font-weight:700; font-size:16px; padding:12px 32px; border-radius:8px; color:var(--primary);">Ajukan
                    Naskah</a>
                <a href="{{ route('public.author-guidelines') }}" class="btn btn-outline-light"
                    style="font-weight:600; font-size:16px; padding:12px 32px; border-radius:8px;">Baca Panduan</a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // CountUp animation script
        document.addEventListener('DOMContentLoaded', () => {
            const counters = document.querySelectorAll('[data-countup]');

            const animateCount = (el) => {
                const rawAttr = el.getAttribute('data-countup') || '';
                const target = parseInt(rawAttr.replace(/[^0-9]/g, ''), 10) || 0;
                const suffix = rawAttr.replace(/[0-9]/g, '');
                if (target <= 0) {
                    el.innerText = '0' + suffix;
                    return;
                }
                const duration = 1200;
                const totalFrames = Math.round(duration / 16);
                let frame = 0;

                const timer = setInterval(() => {
                    frame++;
                    const progress = frame / totalFrames;
                    // easeOutQuad
                    const current = Math.min(target, Math.round(target * (progress * (2 - progress))));
                    el.innerText = current + suffix;
                    if (frame >= totalFrames) {
                        clearInterval(timer);
                        el.innerText = target + suffix;
                    }
                }, 16);
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCount(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            counters.forEach(c => observer.observe(c));
        });
    </script>
@endpush
