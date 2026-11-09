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
        /* Bootstrap Modal & Backdrop Stacking Fixes */
        .modal-backdrop {
            z-index: 1050 !important;
        }

        .modal {
            z-index: 1060 !important;
        }

        .modal-dialog {
            z-index: 1065 !important;
            pointer-events: auto !important;
        }

        .modal-content {
            pointer-events: auto !important;
        }

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
            color: #f8fafc;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            overflow-y: auto;
            border-right: 1px solid #1e293b;
            transition: transform 0.3s ease;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.04);
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 4px;
        }

        .sidebar-header {
            padding: 18px 18px;
            border-bottom: 1px solid #1e293b;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #0b1120;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .sidebar-brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #0284c7, #2563eb);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 19px;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
            flex-shrink: 0;
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
            gap: 1px;
            overflow: hidden;
        }

        .brand-title {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.08em;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            line-height: 1.2;
            white-space: nowrap;
        }

        .brand-subtitle {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }

        .sidebar-user-card {
            margin: 14px 14px 6px;
            background: rgba(30, 41, 59, 0.5);
            border-radius: 10px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
        }

        .sidebar-user-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #0284c7, #3b82f6);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
            flex-shrink: 0;
        }

        .sidebar-user-name {
            font-size: 13px;
            font-weight: 600;
            color: #f8fafc;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
            line-height: 1.3;
        }

        .sidebar-user-role {
            font-size: 11px;
            color: #38bdf8;
            font-weight: 500;
            text-transform: capitalize;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .sidebar-user-role::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #22c55e;
            display: inline-block;
        }

        .sidebar-menu {
            list-style: none;
            padding: 8px 10px 24px;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .sidebar-category {
            padding: 16px 12px 6px;
            font-size: 10.5px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 12px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: 'Plus Jakarta Sans', sans-serif;
            position: relative;
        }

        .sidebar-link i {
            font-size: 17px;
            min-width: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #f1f5f9;
        }

        .sidebar-link:hover i {
            color: #38bdf8;
        }

        .sidebar-link.active {
            background: rgba(2, 132, 199, 0.14);
            color: #38bdf8;
            font-weight: 600;
            border: 1px solid rgba(56, 189, 248, 0.25);
        }

        .sidebar-link.active i {
            color: #38bdf8;
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
                    selector: 'textarea.tinymce-editor, textarea#bodyEditor, textarea.rich-text',

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
                <div class="sidebar-brand-icon">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </div>
                <div class="sidebar-brand-text">
                    <span class="brand-title">OJS SYSTEM</span>
                    <span class="brand-subtitle">Platform Publikasi</span>
                </div>
            </div>

            <div class="sidebar-user-card">
                <div class="sidebar-user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div style="overflow: hidden; flex: 1;">
                    <div class="sidebar-user-name" title="{{ auth()->user()->name }}">
                        {{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">
                        {{ auth()->user()->role }}</div>
                </div>
            </div>

            <ul class="sidebar-menu">
                @include('layouts.partials.sidebar-menu')

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

    @stack('modals')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.0.0/countUp.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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
