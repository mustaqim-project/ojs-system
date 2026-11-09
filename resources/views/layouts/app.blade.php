<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @php
        try {
            $siteName = \App\Models\Setting::get('site_name', config('app.name', 'OJS System'));
            $siteDesc = \App\Models\Setting::get('seo_meta_description', \App\Models\Setting::get('site_description', 'Platform Penerbitan Jurnal Ilmiah'));
        } catch (\Throwable $e) {
            $siteName = config('app.name', 'OJS System');
            $siteDesc = 'Platform Penerbitan Jurnal Ilmiah';
        }
        
        $pageTitle = trim($__env->yieldContent('title'));
        if (empty($pageTitle)) {
            $pageTitle = $title ?? ($page['title'] ?? '');
        }
        $fullTitle = $pageTitle ? $pageTitle . ' — ' . $siteName : $siteName;

        $pageDesc = trim($__env->yieldContent('meta_description'));
        if (empty($pageDesc)) {
            $pageDesc = $description ?? ($page['meta_description'] ?? $siteDesc);
        }
        $pageDesc = Str::limit(strip_tags($pageDesc), 200);

        // Resolve Open Graph Image (Logo / Cover)
        $customOgImage = trim($__env->yieldContent('og_image'));
        $rawImage = null;

        try {
            if (!empty($customOgImage)) {
                $rawImage = $customOgImage;
            } elseif (!empty($og_image)) {
                $rawImage = $og_image;
            } elseif (isset($article) && $article instanceof \App\Models\Article && !empty($article->journal?->cover_image)) {
                $rawImage = $article->journal->cover_image;
            } elseif (isset($journal) && $journal instanceof \App\Models\Journal && !empty($journal->cover_image)) {
                $rawImage = $journal->cover_image;
            } elseif (!empty(\App\Models\Setting::get('site_logo'))) {
                $rawImage = \App\Models\Setting::get('site_logo');
            } else {
                $firstCover = \App\Models\Journal::where('is_active', true)->whereNotNull('cover_image')->where('cover_image', '!=', '')->value('cover_image');
                $rawImage = $firstCover ?: \App\Models\Setting::get('site_favicon');
            }
        } catch (\Throwable $e) {
            $rawImage = null;
        }

        $shareImageUrl = '';
        if ($rawImage) {
            $shareImageUrl = (str_starts_with($rawImage, 'http://') || str_starts_with($rawImage, 'https://'))
                ? $rawImage
                : asset($rawImage);
        }

        // Detect mime type for Open Graph
        $imageType = 'image/jpeg';
        if (str_ends_with(strtolower($shareImageUrl), '.png')) {
            $imageType = 'image/png';
        } elseif (str_ends_with(strtolower($shareImageUrl), '.webp')) {
            $imageType = 'image/webp';
        } elseif (str_ends_with(strtolower($shareImageUrl), '.svg')) {
            $imageType = 'image/svg+xml';
        }

        $ogType = trim($__env->yieldContent('og_type')) ?: 'website';
        $currentUrl = url()->current();
    @endphp

    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $pageDesc }}" />
    <meta name="keywords" content="{{ $page['extra']['meta_keywords'] ?? \App\Models\Setting::get('seo_meta_keywords', '') }}" />
    <link rel="canonical" href="{{ $currentUrl }}" />

    {{-- Open Graph / Facebook / WhatsApp / LinkedIn / Telegram --}}
    <meta property="og:type" content="{{ $ogType }}" />
    <meta property="og:site_name" content="{{ $siteName }}" />
    <meta property="og:title" content="{{ $pageTitle ?: $siteName }}" />
    <meta property="og:description" content="{{ $pageDesc }}" />
    <meta property="og:url" content="{{ $currentUrl }}" />
    @if(!empty($shareImageUrl))
        <meta property="og:image" content="{{ $shareImageUrl }}" />
        <meta property="og:image:secure_url" content="{{ $shareImageUrl }}" />
        <meta property="og:image:type" content="{{ $imageType }}" />
        <meta property="og:image:alt" content="{{ $pageTitle ?: $siteName }}" />
        <link rel="image_src" href="{{ $shareImageUrl }}" />
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:url" content="{{ $currentUrl }}" />
    <meta name="twitter:title" content="{{ $pageTitle ?: $siteName }}" />
    <meta name="twitter:description" content="{{ $pageDesc }}" />
    @if(!empty($shareImageUrl))
        <meta name="twitter:image" content="{{ $shareImageUrl }}" />
    @endif

    @stack('meta')

    @if (\App\Models\Setting::get('site_favicon'))
        <link rel="icon" href="{{ asset(\App\Models\Setting::get('site_favicon')) }}">
    @endif

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300&display=swap"
        rel="stylesheet">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Animate.css --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    {{-- AOS Animation --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Core CSS is in ojs.css -->
    <link rel="stylesheet" href="{{ asset('css/ojs.css') }}">
    @stack('styles')
</head>

<body class="d-flex flex-column min-vh-100">

    {{-- Reading Progress Bar --}}
    <div class="reading-progress-container">
        <div class="reading-progress-bar" id="readingProgressBar"></div>
    </div>

    {{-- PUBLIC NAVIGATION (BOOTSTRAP 5 NATIVE) --}}
    <nav class="navbar navbar-dark navbar-expand-lg sticky-top pub-navbar"
        style="background: #0a3161; border-bottom: 1px solid rgba(255,255,255,0.1); padding: 12px 0; z-index: 1030;">
        <div class="container" style="max-width: 1400px;">
            {{-- Brand --}}
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('public.home') }}"
                style="font-weight: 700; color: #F8FAFC;">
                @if (\App\Models\Setting::get('site_logo'))
                    <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}"
                        alt="{{ \App\Models\Setting::get('site_name', 'OJS System') }}"
                        style="height: 38px; width: auto; object-fit: contain;">
                @else
                    <div
                        style="width: 34px; height: 34px; background: linear-gradient(135deg, var(--primary), #3b82f6); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 16px; box-shadow: 0 4px 12px rgba(37,99,235,0.2); flex-shrink:0;">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                    <span
                        style="background: linear-gradient(to right, #ffffff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-size: 15px;">{{ \App\Models\Setting::get('site_name', 'OJS System') }}</span>
                @endif
            </a>

            {{-- Mobile Toggler --}}
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false"
                aria-label="Toggle navigation"
                style="min-height: 44px; min-width: 44px;">
                <i class="bi bi-list" style="font-size: 26px; color: #F8FAFC;"></i>
            </button>

            {{-- Navbar Content --}}
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0" style="gap: 4px; align-items: center;">
                    @foreach ($global_navigations as $nav)
                        @if(str_contains(strtolower($nav->url), 'indexing') || str_contains(strtolower($nav->title), 'indexing') || str_contains(strtolower($nav->title), 'indeks'))
                            @continue
                        @endif
                        @if ($nav->children->count() > 0)
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle"
                                    href="{{ $nav->url === '#' ? '#' : url($nav->url) }}" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false"
                                    style="font-weight: 500; font-size: 14px; padding: 10px 12px; border-radius: 6px; color: #E2E8F0; min-height: 44px; display: flex; align-items: center;">
                                    {{ $nav->title }}
                                </a>
                                <ul class="dropdown-menu shadow-sm"
                                    style="border:1px solid var(--border); border-radius: var(--radius-md); font-size: 14px;">
                                    @foreach ($nav->children as $child)
                                        @if(str_contains(strtolower($child->url), 'indexing') || str_contains(strtolower($child->title), 'indexing') || str_contains(strtolower($child->title), 'indeks'))
                                            @continue
                                        @endif
                                        <li><a class="dropdown-item"
                                                href="{{ url($child->url) }}" style="min-height: 44px; display: flex; align-items: center;">{{ $child->title }}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url($nav->url) }}"
                                    style="font-weight: 500; font-size: 14px; padding: 10px 12px; border-radius: 6px; color: #E2E8F0; min-height: 44px; display: flex; align-items: center;">
                                    {{ $nav->title }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>

                {{-- Actions --}}
                <div class="d-flex align-items-center flex-wrap gap-2 mt-3 mt-lg-0">

                    <a href="{{ route('public.search') }}" class="btn btn-light shadow-sm"
                        style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #F8FAFC; font-weight: 500; padding: 8px 14px; border-radius: 8px; min-height: 44px;"
                        title="Cari">
                        <i class="bi bi-search me-1"></i> Cari
                    </a>

                    @auth
                        <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="btn btn-light shadow-sm"
                            style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #F8FAFC; font-weight: 500; padding: 8px 14px; border-radius: 8px; min-height: 44px;">
                            <i class="bi bi-grid me-1"></i> Dasbor
                        </a>
                        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn btn-danger shadow-sm"
                                style="font-weight: 500; padding: 8px 14px; border-radius: 8px; min-height: 44px;">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light shadow-sm"
                            style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #F8FAFC; font-weight: 500; padding: 8px 14px; border-radius: 8px; min-height: 44px;">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-primary shadow-sm"
                            style="font-weight: 600; padding: 8px 18px; border-radius: 8px; min-height: 44px;">Kirim Artikel <i
                                class="bi bi-arrow-right ms-1"></i></a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- FLASH MESSAGES --}}
    @if (session('success'))
        <div class="pub-flash pub-flash-suc">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button onclick="this.parentElement.remove()"
                style="margin-left:auto;background:none;border:none;cursor:pointer;color:inherit;font-size:16px;">✕</button>
        </div>
    @endif
    @if (session('error'))
        <div class="pub-flash pub-flash-err">
            <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
            <button onclick="this.parentElement.remove()"
                style="margin-left:auto;background:none;border:none;cursor:pointer;color:inherit;font-size:16px;">✕</button>
        </div>
    @endif

    <main class="flex-grow-1" style="position: relative; z-index: 1;">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="pub-footer animate__animated animate__fadeIn" role="contentinfo"
        style="position: relative; z-index: 2; padding: 60px 0 20px; border-top: 1px solid rgba(255,255,255,0.1); background: #0a3161; clear: both; margin-top: auto;">
        <div class="container" style="max-width: 1400px;">
            <div class="row g-5">
                <div class="col-12 col-lg-5">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
                        @if (\App\Models\Setting::get('site_logo'))
                            <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}"
                                alt="{{ \App\Models\Setting::get('site_name', 'OJS System') }}"
                                style="height: 44px; width: auto; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));">
                        @else
                            <div
                                style="width:36px;height:36px;background:linear-gradient(135deg, var(--primary), #3b82f6);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;box-shadow: 0 4px 12px rgba(37,99,235,0.2);">
                                <i class="bi bi-journal-bookmark-fill"></i>
                            </div>
                            <div
                                style="font-size: 1.35rem; font-weight: 800; color: #F8FAFC; letter-spacing: -0.02em;">
                                {{ \App\Models\Setting::get('site_name', 'OJS System') }}</div>
                        @endif
                    </div>
                    <p
                        style="font-size: 15px; color: #94A3B8; line-height: 1.8; max-width: 420px; margin-bottom: 0; font-weight: 400;">
                        {{ \App\Models\Setting::get('site_description', 'Platform penerbitan ilmiah akses terbuka yang diakui secara internasional.') }}
                    </p>
                </div>

                <div class="col-6 col-lg-2">
                    <div
                        style="font-size: 12px; font-weight: 700; color: #E2E8F0; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 24px;">
                        Eksplorasi</div>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <a href="{{ route('public.home') }}" class="footer-link"
                            style="padding: 0; font-weight: 500;">Beranda</a>
                        <a href="{{ route('public.journals.index') }}" class="footer-link"
                            style="padding: 0; font-weight: 500;">Jurnal</a>
                        <a href="{{ route('public.articles.index') }}" class="footer-link"
                            style="padding: 0; font-weight: 500;">Artikel</a>
                        <a href="{{ route('public.search') }}" class="footer-link"
                            style="padding: 0; font-weight: 500;">Pencarian</a>
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <div
                        style="font-size: 12px; font-weight: 700; color: #E2E8F0; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 24px;">
                        Penulis</div>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <a href="{{ route('register') }}" class="footer-link"
                            style="padding: 0; font-weight: 500;">Kirim Naskah</a>
                        <a href="{{ route('login') }}" class="footer-link"
                            style="padding: 0; font-weight: 500;">Portal Penulis</a>
                        <a href="{{ route('public.author-guidelines') }}" class="footer-link"
                            style="padding: 0; font-weight: 500;">Panduan Penulis</a>
                        <a href="{{ route('public.ethics') }}" class="footer-link"
                            style="padding: 0; font-weight: 500;">Etika Publikasi</a>
                    </div>
                </div>

                <div class="col-12 col-lg-3">
                    <div
                        style="font-size: 12px; font-weight: 700; color: #E2E8F0; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 24px;">
                        Hubungi Kami</div>
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @php
                            $footerEmail = \App\Models\Setting::get('site_email') ?: (\App\Models\Journal::where('is_active', true)->value('email') ?: '');
                        @endphp
                        @if($footerEmail)
                            <div style="display: flex; gap: 14px; align-items: flex-start;">
                                <i class="bi bi-envelope" style="color: #64748B; font-size: 18px; margin-top: 1px;"></i>
                                <span
                                    style="font-size: 14px; color: #94A3B8; line-height: 1.6; font-weight: 500;">{{ $footerEmail }}</span>
                            </div>
                        @endif
                        @if (\App\Models\Setting::get('contact_phone'))
                            <div style="display: flex; gap: 14px; align-items: flex-start;">
                                <i class="bi bi-telephone"
                                    style="color: #64748B; font-size: 18px; margin-top: 1px;"></i>
                                <span
                                    style="font-size: 14px; color: #94A3B8; line-height: 1.6; font-weight: 500;">{{ \App\Models\Setting::get('contact_phone') }}</span>
                            </div>
                        @endif
                        @if (\App\Models\Setting::get('contact_address'))
                            <div style="display: flex; gap: 14px; align-items: flex-start;">
                                <i class="bi bi-geo-alt"
                                    style="color: #64748B; font-size: 18px; margin-top: 1px;"></i>
                                <span
                                    style="font-size: 14px; color: #94A3B8; line-height: 1.6; font-weight: 500;">{{ \App\Models\Setting::get('contact_address') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <hr style="border-color: #1e293b; margin: 48px 0 24px; opacity: 1;" />

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3"
                style="padding-bottom: 12px;">
                <span
                    style="font-size: 13px; color: #64748B; font-weight: 500;">{{ $global_settings['footer_text'] ?? '&copy; ' . date('Y') . ' ' . \App\Models\Setting::get('site_name', 'OJS System') . '. Hak cipta dilindungi undang-undang.' }}</span>
                <span
                    style="font-size: 13px; color: #64748B; font-weight: 500; display: flex; align-items: center; gap: 6px;">
                    Dibuat oleh <a href="https://cooca.id" target="_blank" rel="noopener noreferrer"
                        style="color: #94A3B8; text-decoration: none; border-bottom: 1px solid rgba(148,163,184,0.3); padding-bottom: 1px; transition: all 0.2s;"
                        onmouseover="this.style.color='#F8FAFC'; this.style.borderColor='rgba(248,250,252,0.5)';"
                        onmouseout="this.style.color='#94A3B8'; this.style.borderColor='rgba(148,163,184,0.3)';">Cooca.id</a>
                </span>
            </div>
        </div>
    </footer>

    {{-- Back to Top Button --}}
    <button id="backToTop" class="back-to-top" title="Go to top">
        <i class="bi bi-arrow-up"></i>
    </button>

    {{-- Global Toast Container --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
        <div id="globalToast" class="toast align-items-center text-white border-0" role="alert"
            aria-live="assertive" aria-atomic="true" style="background:var(--success);">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">
                    Berhasil!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Tutup"></button>
            </div>
        </div>
    </div>

    @stack('modals')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Ensure any Bootstrap modal is placed at <body> level to avoid stacking context traps
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.modal').forEach(function (modalEl) {
                if (modalEl.parentElement !== document.body) {
                    document.body.appendChild(modalEl);
                }
            });
        });
        document.addEventListener('show.bs.modal', function (event) {
            if (event.target && event.target.parentElement !== document.body) {
                document.body.appendChild(event.target);
            }
        });

        // Initialize AOS
        AOS.init({
            duration: 400,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50
        });



        // Reading Progress Bar
        window.addEventListener('scroll', () => {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            document.getElementById("readingProgressBar").style.width = scrolled + "%";

            // Back to top button visibility
            const btt = document.getElementById('backToTop');
            if (winScroll > 300) {
                btt.classList.add('show');
            } else {
                btt.classList.remove('show');
            }
        });

        // Back to Top functionality
        document.getElementById('backToTop').addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
    @stack('scripts')
    @yield('scripts')
</body>

</html>
