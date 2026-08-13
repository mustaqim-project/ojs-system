<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title ?? 'Dasbor' }} — {{ config('app.name', 'OJS') }}</title>

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Animate.css --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    {{-- AOS Animation --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    {{-- Custom Enterprise OJS CSS --}}
    <link rel="stylesheet" href="{{ asset('css/ojs.css') }}">

    <style>
        /* Add minor fixes specifically for integrating Bootstrap and custom layout */
        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-w, 280px);
            background: #111827;
            color: #F3F4F6;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            overflow-y: auto;
            border-right: 1px solid #1F2937;
            transition: transform 0.3s ease;
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-w, 280px);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: calc(100% - var(--sidebar-w, 280px));
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        .topbar {
            height: var(--topbar-h, 64px);
            background: var(--bg-surface, #ffffff);
            border-bottom: 1px solid var(--border, #e2e8f0);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .page-content {
            padding: 24px;
            flex: 1;
            width: 100%;
            max-width: 100%;
        }

        /* Sidebar Menu Premium Styling */
        .sidebar {
            width: var(--sidebar-w, 280px);
            background: #0f172a;
            /* Deep slate */
            color: #f8fafc;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            overflow-y: auto;
            border-right: 1px solid #1e293b;
            transition: transform 0.3s ease;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.02);
        }

        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid #1e293b;
            display: flex;
            align-items: center;
            gap: 14px;
            background: rgba(15, 23, 42, 0.95);
            position: sticky;
            top: 0;
            z-index: 2;
            backdrop-filter: blur(8px);
        }

        .sidebar-user-card {
            margin: 16px 20px 8px;
            background: #1e293b;
            border-radius: 12px;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .sidebar-menu {
            list-style: none;
            padding: 8px 12px 24px;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sidebar-category {
            padding: 24px 16px 8px;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: 10px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: 'Plus Jakarta Sans', sans-serif;
            position: relative;
            overflow: hidden;
        }

        .sidebar-link i {
            font-size: 18px;
            transition: transform 0.2s ease, color 0.2s ease;
            opacity: 0.8;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.04);
            color: #f8fafc;
        }

        .sidebar-link:hover i {
            transform: scale(1.1);
            color: #38bdf8;
            opacity: 1;
        }

        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(56, 189, 248, 0.1) 0%, transparent 100%);
            color: #38bdf8;
            font-weight: 600;
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 10%;
            height: 80%;
            width: 3px;
            background: #38bdf8;
            border-radius: 0 4px 4px 0;
        }

        .sidebar-link.active i {
            color: #38bdf8;
            opacity: 1;
        }

        @media (max-width: 1023px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .ds-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1035;
                backdrop-filter: blur(4px);
            }

            .ds-overlay.show {
                display: block;
            }
        }
    </style>
    {{-- TinyMCE Free --}}
    <script src="https://cdn.tiny.cloud/1/7o263mkoo1n6fgu9o0m6ecqeb7vh1gqfepr6a1m4j9dvdsns/tinymce/8/tinymce.min.js"
        referrerpolicy="origin" crossorigin="anonymous"></script>

    <script>
        tinymce.init({
                    selector: 'textarea',

                    plugins: [
                        'anchor',
                        'autolink',
                        'charmap',
                        'codesample',
                        'emoticons',
                        'link',
                        'lists',
                        'media',
                        'searchreplace',
                        'table',
                        'visualblocks',
                        'wordcount'
                    ],

                    toolbar: 'undo redo | ' +
                        'blocks fontfamily fontsize | ' +
                        'bold italic underline strikethrough | ' +
                        'link media table | ' +
                        'align lineheight | ' +
                        'bullist numlist | ' +
                        'outdent indent | ' +
                        'emoticons charmap codesample | ' +
                        'removeformat',

                    menubar: 'file edit view insert format tools table help',

                    height: 500,

                    branding: false,

                    promotion: false,

                    resize: true,

                    statusbar: true,

                    elementpath: true,

                    content_style: `
                body {
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 14px;
                    line-height: 1.6;
                    padding: 10px;
                }
            `,

            toolbar_mode: 'sliding',

            relative_urls: false,

            remove_script_host: false,

            convert_urls: false
        });
    </script>
    @stack('styles')
</head>

<body>
    <div class="ds-overlay" id="ds-overlay" onclick="closeSB()"></div>

    <div class="wrapper">
        {{-- SIDEBAR --}}
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <i class="bi bi-journal-bookmark-fill" style="font-size: 26px; color: #38bdf8;"></i>
                <div style="line-height: 1.2;">
                    <div
                        style="font-size: 16px; color: #fff; font-weight: 700; font-family: 'Plus Jakarta Sans', sans-serif;">
                        {{ \App\Models\Setting::get('site_name', 'OJS') }}</div>
                    <div style="font-size: 11px; color: #94a3b8; font-weight: 500;">Platform Publikasi</div>
                </div>
            </div>

            <div class="sidebar-user-card">
                <div
                    style="width: 36px; height: 36px; background: linear-gradient(135deg, #0ea5e9, #2563eb); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.2);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div style="overflow: hidden; flex: 1;">
                    <div
                        style="font-size: 13px; font-weight: 600; color: #f8fafc; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; font-family: 'Plus Jakarta Sans', sans-serif;">
                        {{ auth()->user()->name }}</div>
                    <div style="font-size: 11px; color: #94a3b8; font-weight: 500; text-transform: capitalize;">
                        {{ auth()->user()->role }}</div>
                </div>
            </div>

            <ul class="sidebar-menu">
                @if (auth()->user()->isAdmin())
                    <li class="sidebar-category">Administrasi</li>
                    <li><a href="{{ route('admin.dashboard') }}"
                            class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i
                                class="bi bi-grid-1x2"></i> Dasbor</a></li>
                    <li><a href="{{ route('admin.users.index') }}"
                            class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i
                                class="bi bi-people"></i> Pengguna</a></li>
                    <li><a href="{{ route('admin.journals.index') }}"
                            class="sidebar-link {{ request()->routeIs('admin.journals*') ? 'active' : '' }}"><i
                                class="bi bi-journals"></i> Jurnal</a></li>
                    <li><a href="{{ route('admin.issues.index') }}"
                            class="sidebar-link {{ request()->routeIs('admin.issues*') ? 'active' : '' }}"><i
                                class="bi bi-collection"></i> Terbitan</a></li>
                    <li><a href="{{ route('admin.articles.index') }}"
                            class="sidebar-link {{ request()->routeIs('admin.articles*') ? 'active' : '' }}"><i
                                class="bi bi-file-earmark-text"></i> Artikel</a></li>
                    <li>
                        <a href="{{ route('admin.payments.index') }}"
                            class="sidebar-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
                            <i class="bi bi-credit-card"></i> Pembayaran
                            @php $pc = \App\Models\Payment::where('status','uploaded')->count(); @endphp
                            @if ($pc)
                                <span class="badge rounded-pill"
                                    style="background-color: #ef4444; font-size: 10px; margin-left: auto;">{{ $pc }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="sidebar-category">Pengaturan</li>
                    <li><a href="{{ route('admin.settings.index') }}"
                            class="sidebar-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}"><i
                                class="bi bi-sliders"></i> Pengaturan</a></li>
                    <li><a href="{{ route('admin.pages.index') }}"
                            class="sidebar-link {{ request()->routeIs('admin.pages*') ? 'active' : '' }}"><i
                                class="bi bi-file-earmark-richtext"></i> Halaman Situs</a></li>
                    <li><a href="{{ route('admin.integrations.index') }}"
                            class="sidebar-link {{ request()->routeIs('admin.integrations*') ? 'active' : '' }}"><i
                                class="bi bi-plug"></i> Integrasi API</a></li>
                @endif

                @if (auth()->user()->isEditor())
                    <li class="sidebar-category">Redaksi</li>
                    <li><a href="{{ route('editor.dashboard') }}"
                            class="sidebar-link {{ request()->routeIs('editor.dashboard') ? 'active' : '' }}"><i
                                class="bi bi-grid-1x2"></i> Dasbor</a></li>
                    <li><a href="{{ route('editor.articles.index') }}"
                            class="sidebar-link {{ request()->routeIs('editor.articles*') ? 'active' : '' }}"><i
                                class="bi bi-file-earmark-text"></i> Naskah</a></li>
                @endif

                @if (auth()->user()->isReviewer())
                    <li class="sidebar-category">Peninjau</li>
                    <li><a href="{{ route('reviewer.dashboard') }}"
                            class="sidebar-link {{ request()->routeIs('reviewer.dashboard') ? 'active' : '' }}"><i
                                class="bi bi-grid-1x2"></i> Dasbor</a></li>
                    <li><a href="{{ route('reviewer.reviews.index') }}"
                            class="sidebar-link {{ request()->routeIs('reviewer.reviews*') ? 'active' : '' }}"><i
                                class="bi bi-clipboard-check"></i> Antrean Tinjauan</a></li>
                @endif

                @if (auth()->user()->isAuthor())
                    <li class="sidebar-category">Penulis</li>
                    <li><a href="{{ route('author.dashboard') }}"
                            class="sidebar-link {{ request()->routeIs('author.dashboard') ? 'active' : '' }}"><i
                                class="bi bi-grid-1x2"></i> Dasbor</a></li>
                    <li><a href="{{ route('author.articles.index') }}"
                            class="sidebar-link {{ request()->routeIs('author.articles.index') ? 'active' : '' }}"><i
                                class="bi bi-file-earmark-text"></i> Kiriman Saya</a></li>
                    <li><a href="{{ route('author.articles.create') }}"
                            class="sidebar-link {{ request()->routeIs('author.articles.create') ? 'active' : '' }}"><i
                                class="bi bi-plus-circle"></i> Kirim Naskah</a></li>
                @endif

                <li style="margin: 16px 16px 8px; border-top: 1px solid #1e293b;"></li>
                <li><a href="{{ route('public.home') }}" class="sidebar-link" target="_blank"><i
                            class="bi bi-arrow-up-right-square"></i> Lihat Jurnal</a></li>

                <li>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 w-100">
                        @csrf
                        <button type="submit" class="sidebar-link"
                            style="width: 100%; border: none; background: transparent; text-align: left; margin: 0;">
                            <i class="bi bi-box-arrow-left"></i> Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </aside>

        {{-- MAIN CONTENT AREA --}}
        <div class="main-content">
            {{-- TOPBAR --}}
            <header class="topbar">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link d-lg-none text-dark p-0 me-3" onclick="openSB()"
                        aria-label="Open menu">
                        <i class="bi bi-list" style="font-size: 24px; color: var(--text-main);"></i>
                    </button>
                    <div>
                        <h1 class="m-0"
                            style="font-size: 18px; font-weight: 700; color: var(--text-main, #1e293b);">
                            {{ $title ?? 'Dasbor' }}</h1>
                        @if (isset($breadcrumbs))
                            <nav aria-label="breadcrumb" style="margin-top:2px;">
                                <ol class="breadcrumb m-0" style="font-size: 12px; font-weight: 500;">
                                    @foreach ($breadcrumbs as $bc)
                                        @if (!$loop->last)
                                            <li class="breadcrumb-item"><a href="{{ $bc['url'] }}"
                                                    style="color:var(--text-muted); text-decoration:none;">{{ $bc['label'] }}</a>
                                            </li>
                                        @else
                                            <li class="breadcrumb-item active" aria-current="page"
                                                style="color:var(--primary);">{{ $bc['label'] }}</li>
                                        @endif
                                    @endforeach
                                </ol>
                            </nav>
                        @endif
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">


                    <div class="dropdown">
                        <a href="#" class="text-secondary position-relative" data-bs-toggle="dropdown"
                            aria-expanded="false" style="font-size: 18px;">
                            <i class="bi bi-bell"></i>
                            @if (auth()->user()->unreadNotifications->count() > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm"
                            style="width: 320px; padding: 0; border-radius: 8px; border: 1px solid var(--border, #e2e8f0);">
                            <li
                                style="padding: 12px 16px; border-bottom: 1px solid var(--border, #e2e8f0); background: var(--bg-app, #f8fafc); display: flex; justify-content: space-between; align-items: center;">
                                <span
                                    style="font-weight: 600; font-size: 14px; color: var(--text-main, #1e293b);">Notifikasi</span>
                                @if (auth()->user()->unreadNotifications->count() > 0)
                                    <a href="{{ route('notifications.markAll') }}"
                                        style="font-size: 12px; color: var(--primary, #0f4c81); text-decoration: none; font-weight: 500;">Tandai
                                        semua dibaca</a>
                                @endif
                            </li>
                            <div style="max-height: 300px; overflow-y: auto;">
                                @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                                    <li>
                                        <a class="dropdown-item d-flex gap-3 align-items-start"
                                            href="{{ route('notifications.read', $notification->id) }}"
                                            style="padding: 12px 16px; border-bottom: 1px solid #f1f5f9; white-space: normal;">
                                            <div
                                                style="width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgba(15, 76, 129, 0.1); color: var(--primary, #0f4c81);">
                                                <i class="bi {{ $notification->data['icon'] ?? 'bi-bell-fill' }}"></i>
                                            </div>
                                            <div>
                                                <div
                                                    style="font-size: 13px; font-weight: 500; color: var(--text-main, #1e293b); line-height: 1.4;">
                                                    {{ $notification->data['message'] ?? 'Notifikasi baru' }}</div>
                                                <div
                                                    style="font-size: 11px; color: var(--text-muted, #64748b); margin-top: 4px;">
                                                    {{ $notification->created_at->diffForHumans() }}</div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li style="padding: 24px 16px; text-align: center;">
                                        <i class="bi bi-bell-slash text-muted"
                                            style="font-size: 20px; display: block; margin-bottom: 8px;"></i>
                                        <span class="text-muted" style="font-size: 13px;">Tidak ada notifikasi
                                            baru</span>
                                    </li>
                                @endforelse
                            </div>
                        </ul>
                    </div>

                    <div style="width: 1px; height: 24px; background: var(--border, #e2e8f0);"></div>

                    <div class="dropdown">
                        <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown" role="button"
                            aria-expanded="false" style="cursor: pointer;">
                            <div
                                style="width: 32px; height: 32px; border-radius: 8px; background: var(--primary, #0f4c81); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm"
                            style="border: 1px solid var(--border, #e2e8f0); border-radius: 8px;">
                            <li
                                style="padding: 8px 16px; border-bottom: 1px solid var(--border, #e2e8f0); margin-bottom: 4px;">
                                <div style="font-weight: 600; font-size: 14px; color: var(--text-main, #1e293b);">
                                    {{ auth()->user()->name }}</div>
                                <div style="font-size: 12px; color: var(--text-muted, #64748b);">
                                    {{ auth()->user()->email }}</div>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"
                                    style="font-size: 14px;"><i class="bi bi-person me-2"></i>Profil</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"
                                        style="font-size: 14px;">
                                        <i class="bi bi-box-arrow-left me-2"></i>Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            {{-- PAGE CONTENT --}}
            <main class="page-content animate__animated animate__fadeIn"
                style="animation-duration: 0.6s; animation-delay: 0.2s; animation-fill-mode: both;">
                @if (session('success'))
                    <div class="alert alert-success d-flex align-items-center" role="alert"
                        style="border-radius: 8px; font-size: 14px;">
                        <i class="bi bi-check-circle-fill me-2" style="font-size: 16px;"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger d-flex align-items-center" role="alert"
                        style="border-radius: 8px; font-size: 14px;">
                        <i class="bi bi-x-circle-fill me-2" style="font-size: 16px;"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert" style="border-radius: 8px; font-size: 14px;">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-exclamation-triangle-fill me-2" style="font-size: 16px;"></i>
                            <strong>Terdapat kesalahan:</strong>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                        <ul class="mb-0 ps-4">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.0.0/countUp.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 600,
            once: true,
            offset: 30
        });


        function openSB() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('ds-overlay').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeSB() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('ds-overlay').classList.remove('show');
            document.body.style.overflow = '';
        }

        // Sidebar active states and hover logic is now entirely handled by pure CSS
        // The inline JS that manually modified styles was removed for a cleaner architecture.
    </script>
    @stack('scripts')
</body>

</html>
