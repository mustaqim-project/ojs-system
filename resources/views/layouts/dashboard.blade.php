<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title ?? 'Dashboard' }} — {{ config('app.name', 'OJS') }}</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300&display=swap" rel="stylesheet">
    
    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    {{-- Tailwind CSS 4 via CDN --}}
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    
    {{-- Design Tokens --}}
    <style>
        :root {
            /* Colors */
            --color-primary: #2563eb;
            --color-primary-hover: #1d4ed8;
            --color-primary-light: #dbeafe;
            --color-secondary: #64748b;
            --color-success: #10b981;
            --color-success-bg: #ecfdf5;
            --color-warning: #f59e0b;
            --color-warning-bg: #fffbeb;
            --color-danger: #ef4444;
            --color-danger-bg: #fef2f2;
            --color-info: #06b6d4;
            --color-info-bg: #ecfeff;
            
            /* Grays */
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            
            /* Sidebar */
            --sidebar-width: 280px;
            --topbar-height: 64px;
            --sidebar-bg: #0f172a;
            --sidebar-border: #1e293b;
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #ffffff;
            --sidebar-hover: #1e293b;
            --sidebar-active: #1e40af;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gray-50);
            color: var(--gray-900);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .font-academic {
            font-family: 'Merriweather', serif;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--gray-300); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--gray-400); }
        
        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes pulse-subtle {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .animate-fadeInUp { animation: fadeInUp 0.4s ease-out both; }
        .animate-delay-1 { animation-delay: 0.05s; }
        .animate-delay-2 { animation-delay: 0.1s; }
        .animate-delay-3 { animation-delay: 0.15s; }
        .animate-delay-4 { animation-delay: 0.2s; }
        
        .gradient-brand {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }
        
        .gradient-subtle {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }
        
        /* Glass effect */
        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        
        /* Card hover effects */
        .card-hover {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.1);
        }
        
        /* Focus states */
        .focus-ring:focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }
    </style>
    
    @stack('styles')
</head>

<body class="antialiased">

      /* ── Sidebar ─────────────────────────────────────── */
      .ds-sidebar {
        position: fixed; top: 0; bottom: 0; left: 0;
        width: var(--sb-w);
        background: var(--sb-bg);
        border-right: 1px solid var(--sb-border);
        display: flex; flex-direction: column;
        z-index: 1040;
        transition: transform 0.28s cubic-bezier(0.4,0,0.2,1);
        overflow-y: auto;
        overflow-x: hidden;
      }
      .ds-sidebar::-webkit-scrollbar { width: 4px; }
      .ds-sidebar::-webkit-scrollbar-track { background: transparent; }
      .ds-sidebar::-webkit-scrollbar-thumb { background: #2D3748; border-radius: 4px; }

      .ds-brand {
        display: flex; align-items: center; gap: 12px;
        padding: 20px 20px 18px;
        border-bottom: 1px solid var(--sb-border);
        text-decoration: none;
        flex-shrink: 0;
      }
      .ds-brand-icon {
        width: 34px; height: 34px;
        background: var(--primary);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 16px; flex-shrink: 0;
      }
      .ds-brand-text { line-height: 1.2; }
      .ds-brand-name { font-size: 14px; font-weight: 700; color: #F3F4F6; letter-spacing: -0.01em; }
      .ds-brand-sub { font-size: 11px; color: var(--sb-txt); margin-top: 2px; }

      .ds-user {
        margin: 12px 12px 0;
        padding: 10px 12px;
        background: #1F2937;
        border-radius: 8px;
        display: flex; align-items: center; gap: 10px;
        flex-shrink: 0;
      }
      .ds-avatar {
        width: 32px; height: 32px;
        background: var(--primary);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; color: #fff; flex-shrink: 0;
      }
      .ds-user-name { font-size: 13px; font-weight: 600; color: #F3F4F6; line-height: 1.2; }
      .ds-user-role { font-size: 11px; color: var(--sb-txt); }

      .ds-nav { flex: 1; padding: 12px 0; }
      .ds-nav-section {
        padding: 16px 16px 4px;
        font-size: 11px; font-weight: 700;
        letter-spacing: 0.08em; text-transform: uppercase;
        color: #374151;
      }
      .ds-nav-link {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 12px; margin: 1px 10px;
        border-radius: 6px;
        color: var(--sb-txt);
        font-size: 14px; font-weight: 500;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
        border: none; background: none; width: calc(100% - 20px);
        cursor: pointer; font-family: inherit; text-align: left;
        position: relative;
      }
      .ds-nav-link i { font-size: 17px; width: 20px; text-align: center; flex-shrink: 0; }
      .ds-nav-link:hover { background: var(--sb-hover); color: var(--sb-txt-active); }
      .ds-nav-link.active { background: var(--sb-active-bg); color: var(--sb-txt-active); }
      .ds-nav-badge {
        margin-left: auto; font-size: 11px; padding: 2px 8px;
        background: rgba(15,76,129,0.4); color: #93BBFC;
        border-radius: 20px; font-weight: 600;
      }
      .ds-nav-link.active .ds-nav-badge { background: rgba(255,255,255,0.2); color: #fff; }
      .ds-divider { margin: 8px 12px; border-top: 1px solid var(--sb-border); }

      .ds-footer {
        padding: 12px 10px;
        border-top: 1px solid var(--sb-border);
        flex-shrink: 0;
      }

      /* ── Topbar ──────────────────────────────────────── */
      .ds-topbar {
        position: fixed; top: 0; right: 0;
        left: var(--sb-w);
        height: var(--tb-h);
        background: #fff;
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center;
        padding: 0 28px; gap: 12px;
        z-index: 1030;
      }
      .ds-topbar-title { font-size: 16px; font-weight: 700; color: var(--text-main); margin: 0; flex: 1; }
      .ds-tb-search {
        display: flex; align-items: center; gap: 8px;
        background: var(--bg-app); border: 1px solid var(--border);
        border-radius: 6px; padding: 7px 12px; width: 220px;
        transition: border-color 0.15s;
      }
      .ds-tb-search:focus-within { border-color: var(--primary); }
      .ds-tb-search input {
        border: none; background: transparent; font-size: 14px;
        outline: none; width: 100%; font-family: inherit; color: var(--text-main);
      }
      .ds-tb-search input::placeholder { color: var(--text-muted); }
      .ds-tb-search i { color: var(--text-muted); font-size: 14px; }
      .ds-icon-btn {
        width: 36px; height: 36px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        background: transparent; border: 1px solid transparent;
        cursor: pointer; color: var(--text-muted); font-size: 17px;
        transition: all 0.15s; text-decoration: none; position: relative;
      }
      .ds-icon-btn:hover { background: var(--bg-app); border-color: var(--border); color: var(--text-main); }
      .ds-notif-dot {
        position: absolute; top: 6px; right: 6px;
        width: 7px; height: 7px;
        background: var(--danger); border-radius: 50%; border: 2px solid #fff;
      }
      .ds-topbar-sep { width: 1px; height: 24px; background: var(--border); }
      .ds-tb-avatar {
        width: 34px; height: 34px; border-radius: 8px;
        background: var(--primary);
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; color: #fff;
        cursor: pointer; border: 2px solid transparent;
        transition: border-color 0.15s;
      }
      .ds-tb-avatar:hover { border-color: var(--primary); }

      /* ── Main Area ───────────────────────────────────── */
      .ds-main {
        margin-left: var(--sb-w);
        margin-top: var(--tb-h);
        min-height: calc(100vh - var(--tb-h));
        padding: 32px;
      }

      /* ── Page Header ─────────────────────────────────── */
      .ds-page-hdr {
        display: flex; justify-content: space-between; align-items: flex-start;
        gap: 16px; margin-bottom: 28px; flex-wrap: wrap;
      }
      .ds-breadcrumb {
        display: flex; align-items: center; gap: 6px;
        font-size: 13px; color: var(--text-muted);
        margin-bottom: 6px;
      }
      .ds-breadcrumb a { color: var(--primary); font-weight: 500; text-decoration: none; }
      .ds-breadcrumb a:hover { text-decoration: underline; }
      .ds-breadcrumb-sep { color: var(--text-muted); font-size: 11px; }
      .ds-page-title { font-size: 22px; font-weight: 700; color: var(--text-main); margin: 0; letter-spacing: -0.02em; }
      .ds-page-subtitle { font-size: 14px; color: var(--text-muted); margin: 4px 0 0 0; }

      /* ── Alerts ──────────────────────────────────────── */
      .ds-alert {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 14px 18px; border-radius: 8px;
        font-size: 14px; font-weight: 500;
        margin-bottom: 20px; border-left: 4px solid;
      }
      .ds-alert-suc { background: var(--success-bg); color: var(--success); border-color: var(--success); }
      .ds-alert-err { background: var(--danger-bg); color: var(--danger); border-color: var(--danger); }
      .ds-alert-warn { background: var(--warning-bg); color: var(--warning); border-color: var(--warning); }
      .ds-alert-info { background: var(--info-bg); color: var(--info); border-color: var(--info); }
      .ds-alert i { flex-shrink: 0; margin-top: 1px; font-size: 16px; }
      .ds-alert-close {
        margin-left: auto; background: none; border: none;
        cursor: pointer; color: inherit; font-size: 16px; padding: 0;
        opacity: 0.7; transition: opacity 0.15s;
      }
      .ds-alert-close:hover { opacity: 1; }

      /* ── Stat Cards ───────────────────────────────────── */
      .ds-stat {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 24px;
        transition: box-shadow 0.2s, transform 0.2s;
      }
      .ds-stat:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }
      .ds-stat-icon {
        width: 40px; height: 40px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; margin-bottom: 16px;
      }
      .ds-stat-val { font-size: 30px; font-weight: 700; color: var(--text-main); line-height: 1; }
      .ds-stat-lbl { font-size: 13px; color: var(--text-muted); font-weight: 500; margin-top: 6px; }

      /* ── Card ─────────────────────────────────────────── */
      .ds-card {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: 8px;
        box-shadow: var(--shadow-sm);
        margin-bottom: 24px;
        overflow: hidden;
      }
      .ds-card-hdr {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
      }
      .ds-card-title { font-size: 15px; font-weight: 600; color: var(--text-main); margin: 0; }
      .ds-card-body { padding: 24px; }

      /* ── Tables ───────────────────────────────────────── */
      .ds-table { width: 100%; border-collapse: collapse; font-size: 14px; }
      .ds-table thead th {
        padding: 11px 20px;
        background: var(--bg-app);
        color: var(--text-muted);
        font-size: 12px; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.05em;
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
      }
      .ds-table tbody td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: var(--text-main);
      }
      .ds-table tbody tr:last-child td { border-bottom: none; }
      .ds-table tbody tr { transition: background 0.1s; }
      .ds-table tbody tr:hover td { background: var(--bg-app); }

      /* ── Buttons ──────────────────────────────────────── */
      .ds-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 8px 16px; border-radius: 6px;
        font-size: 14px; font-weight: 500;
        border: 1px solid transparent;
        cursor: pointer; font-family: inherit;
        text-decoration: none; transition: all 0.15s;
        line-height: 1.4; white-space: nowrap;
      }
      .ds-btn i { font-size: 15px; }
      .ds-btn:focus-visible { outline: 2px solid var(--primary); outline-offset: 2px; }
      .ds-btn-pri { background: var(--primary); color: #fff; border-color: var(--primary); }
      .ds-btn-pri:hover { background: var(--primary-hover); border-color: var(--primary-hover); color: #fff; }
      .ds-btn-out { background: transparent; color: var(--text-main); border-color: var(--border); }
      .ds-btn-out:hover { background: var(--bg-app); color: var(--text-main); border-color: #CBD5E1; }
      .ds-btn-ghost { background: transparent; color: var(--text-muted); border-color: transparent; }
      .ds-btn-ghost:hover { background: var(--bg-app); color: var(--text-main); }
      .ds-btn-danger { background: var(--danger); color: #fff; border-color: var(--danger); }
      .ds-btn-danger:hover { background: #9B2C2C; }
      .ds-btn-suc { background: var(--success); color: #fff; }
      .ds-btn-suc:hover { background: #276749; }
      .ds-btn-sm { padding: 5px 12px; font-size: 13px; }
      .ds-btn-xs { padding: 3px 10px; font-size: 12px; }

      /* ── Badges ───────────────────────────────────────── */
      .ds-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 12px; font-weight: 500; white-space: nowrap;
      }
      .ds-badge::before {
        content: ''; width: 6px; height: 6px;
        border-radius: 50%; background: currentColor; flex-shrink: 0;
      }
      /* Status Variants */
      .ds-badge-submitted        { background: #EBF8FF; color: #2B6CB0; }
      .ds-badge-under_review     { background: #FEFCE8; color: #C05621; }
      .ds-badge-revision_required{ background: #FFF7ED; color: #C05621; }
      .ds-badge-accepted         { background: #F0FDF4; color: #2F855A; }
      .ds-badge-rejected         { background: #FEF2F2; color: #C53030; }
      .ds-badge-waiting_payment  { background: #FAF5FF; color: #6B46C1; }
      .ds-badge-payment_uploaded { background: #EBF8FF; color: #2B6CB0; }
      .ds-badge-paid             { background: #F0FDFA; color: #285E61; }
      .ds-badge-published        { background: #F0FDF4; color: #2F855A; }
      .ds-badge-draft            { background: var(--bg-app); color: var(--text-muted); border: 1px solid var(--border); }
      /* Role Variants */
      .ds-badge-admin    { background: #FEF2F2; color: #C53030; }
      .ds-badge-editor   { background: #EBF8FF; color: #2B6CB0; }
      .ds-badge-reviewer { background: #FAF5FF; color: #6B46C1; }
      .ds-badge-author   { background: #F0FDF4; color: #2F855A; }
      /* Generic */
      .ds-badge-success { background: var(--success-bg); color: var(--success); }
      .ds-badge-warning { background: var(--warning-bg); color: var(--warning); }
      .ds-badge-danger  { background: var(--danger-bg); color: var(--danger); }
      .ds-badge-info    { background: var(--info-bg); color: var(--info); }

      /* ── Forms ────────────────────────────────────────── */
      .ds-lbl {
        display: block; font-size: 13px; font-weight: 500;
        color: var(--text-main); margin-bottom: 6px;
      }
      .ds-lbl .req { color: var(--danger); margin-left: 2px; }
      .ds-inp, .ds-sel, .ds-txta {
        width: 100%; padding: 9px 12px;
        border: 1px solid var(--border); border-radius: 6px;
        font-family: inherit; font-size: 14px;
        color: var(--text-main); background: var(--bg-surface);
        transition: border-color 0.15s, box-shadow 0.15s; outline: none;
      }
      .ds-inp:focus, .ds-sel:focus, .ds-txta:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(15,76,129,0.12);
      }
      .ds-inp::placeholder, .ds-txta::placeholder { color: var(--text-muted); font-size: 13px; }
      .ds-inp.is-invalid { border-color: var(--danger); box-shadow: 0 0 0 3px rgba(197,48,48,0.1); }
      .ds-sel {
        appearance: none; -webkit-appearance: none;
        padding-right: 32px; cursor: pointer;
        background: var(--bg-surface) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%23718096' d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E") no-repeat right 10px center/10px;
      }
      .ds-txta { resize: vertical; min-height: 90px; }
      .ds-f-err { font-size: 12px; color: var(--danger); margin-top: 5px; display: flex; align-items: center; gap: 4px; }
      .ds-f-hint { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
      .ds-f-group { margin-bottom: 20px; }

      /* ── Form Sections ────────────────────────────────── */
      .ds-section {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 20px;
      }
      .ds-section-hdr {
        padding: 14px 20px;
        background: var(--bg-app);
        border-bottom: 1px solid var(--border);
      }
      .ds-section-title { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); margin: 0; }
      .ds-section-body { padding: 24px; }

      /* ── Info Rows ────────────────────────────────────── */
      .ds-info-row { display: flex; gap: 8px; padding: 12px 20px; border-bottom: 1px solid #f1f5f9; }
      .ds-info-row:last-child { border-bottom: none; }
      .ds-info-key { width: 160px; flex-shrink: 0; font-size: 13px; color: var(--text-muted); font-weight: 500; }
      .ds-info-val { font-size: 14px; color: var(--text-main); flex: 1; }

      /* ── Timeline ─────────────────────────────────────── */
      .ds-tl { padding: 4px 0; }
      .ds-tl-item { display: flex; gap: 12px; position: relative; padding-bottom: 20px; }
      .ds-tl-item:last-child { padding-bottom: 0; }
      .ds-tl-item:not(:last-child)::after {
        content: ''; position: absolute;
        left: 11px; top: 24px; bottom: 0;
        width: 1px; background: var(--border); z-index: 0;
      }
      .ds-tl-dot {
        width: 24px; height: 24px; border-radius: 50%;
        flex-shrink: 0; display: flex; align-items: center;
        justify-content: center; font-size: 12px;
        position: relative; z-index: 1;
      }
      .ds-tl-dot-done   { background: var(--success); color: #fff; }
      .ds-tl-dot-active { background: var(--primary); color: #fff; }
      .ds-tl-dot-todo   { background: var(--bg-surface); border: 2px solid var(--border); color: var(--text-muted); }
      .ds-tl-label { font-size: 14px; font-weight: 600; color: var(--text-main); padding-top: 2px; }
      .ds-tl-sub   { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

      /* ── Empty State ──────────────────────────────────── */
      .ds-empty { text-align: center; padding: 56px 24px; }
      .ds-empty-icon {
        width: 56px; height: 56px; border-radius: 14px;
        background: var(--bg-app); border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px; font-size: 24px; color: var(--text-muted);
      }
      .ds-empty-title { font-size: 15px; font-weight: 700; color: var(--text-main); margin-bottom: 6px; }
      .ds-empty-desc  { font-size: 14px; color: var(--text-muted); margin: 0 0 20px; }

      /* ── Filter Tabs ──────────────────────────────────── */
      .ds-ftabs { display: flex; gap: 4px; flex-wrap: wrap; }
      .ds-ftab {
        padding: 5px 14px; border-radius: 6px;
        font-size: 13px; font-weight: 500;
        border: 1px solid var(--border);
        background: var(--bg-surface); color: var(--text-muted);
        cursor: pointer; text-decoration: none; transition: all 0.15s;
      }
      .ds-ftab:hover { background: var(--bg-app); color: var(--text-main); }
      .ds-ftab.active { background: var(--primary); border-color: var(--primary); color: #fff; }

      /* ── Misc ─────────────────────────────────────────── */
      .ds-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.45); z-index: 1039;
      }
      .ds-overlay.show { display: block; }

      /* ── Responsive ───────────────────────────────────── */
      @media (max-width: 768px) {
        .ds-sidebar { transform: translateX(-100%); }
        .ds-sidebar.open { transform: translateX(0); box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .ds-topbar { left: 0; }
        .ds-main { margin-left: 0; padding: 16px; }
        .ds-tb-search { display: none !important; }
      }

      /* ── Dropdown Override ─────────────────────────────── */
      .dropdown-menu {
        border: 1px solid var(--border); border-radius: 10px;
        box-shadow: var(--shadow-lg); font-size: 14px; padding: 6px;
      }
      .dropdown-item { border-radius: 6px; padding: 8px 12px; }
      .dropdown-item:hover { background: var(--bg-app); }
      .dropdown-divider { border-color: var(--border); margin: 4px 0; }
      .modal-content { border: 1px solid var(--border); border-radius: 12px; box-shadow: var(--shadow-lg); }
      .modal-header { border-bottom: 1px solid var(--border); padding: 20px 24px; }
      .modal-title { font-size: 16px; font-weight: 700; }
      .modal-body { padding: 24px; }
      .modal-footer { border-top: 1px solid var(--border); padding: 16px 24px; }
      .pagination .page-link {
        border-radius: 6px !important; font-size: 13px; font-weight: 500;
        color: var(--text-muted); border-color: var(--border); padding: 6px 12px;
      }
      .pagination .page-link:hover { background: var(--bg-app); color: var(--text-main); }
      .pagination .page-item.active .page-link { background: var(--primary); border-color: var(--primary); color: #fff; }

      /* ── Animations ───────────────────────────────────── */
      @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
      .fu  { animation: fadeUp 0.3s ease both; }
      .fd1 { animation-delay: 0.04s; } .fd2 { animation-delay: 0.08s; }
      .fd3 { animation-delay: 0.12s; } .fd4 { animation-delay: 0.16s; }
      .fd5 { animation-delay: 0.20s; } .fd6 { animation-delay: 0.24s; }
    </style>
    @stack('styles')
</head>

<body>
    <div class="ds-overlay" id="ds-overlay" onclick="closeSB()"></div>

    {{-- SIDEBAR --}}
    <aside class="ds-sidebar" id="ds-sidebar">
        <a href="{{ route('public.home') }}" class="ds-brand">
            <div class="ds-brand-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
            <div class="ds-brand-text">
                <div class="ds-brand-name">{{ \App\Models\Setting::get('site_name', 'OJS') }}</div>
                <div class="ds-brand-sub">Publication Platform</div>
            </div>
        </a>

        <div class="ds-user">
            <div class="ds-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div style="overflow:hidden;min-width:0;">
                <div class="ds-user-name text-truncate">{{ auth()->user()->name }}</div>
                <div class="ds-user-role">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>

        <nav class="ds-nav">
            @if (auth()->user()->isAdmin())
                <div class="ds-nav-section">Administration</div>
                <a href="{{ route('admin.dashboard') }}" class="ds-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
                <a href="{{ route('admin.users.index') }}" class="ds-nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Users
                </a>
                <a href="{{ route('admin.journals.index') }}" class="ds-nav-link {{ request()->routeIs('admin.journals*') ? 'active' : '' }}">
                    <i class="bi bi-journals"></i> Journals
                </a>
                <a href="{{ route('admin.issues.index') }}" class="ds-nav-link {{ request()->routeIs('admin.issues*') ? 'active' : '' }}">
                    <i class="bi bi-collection"></i> Issues
                </a>
                <a href="{{ route('admin.articles.index') }}" class="ds-nav-link {{ request()->routeIs('admin.articles*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> Articles
                </a>
                <a href="{{ route('admin.payments.index') }}" class="ds-nav-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card"></i> Payments
                    @php $pc = \App\Models\Payment::where('status','uploaded')->count(); @endphp
                    @if($pc)<span class="ds-nav-badge">{{ $pc }}</span>@endif
                </a>
                <div class="ds-divider"></div>
                <div class="ds-nav-section">Settings</div>
                <a href="{{ route('admin.settings.index') }}" class="ds-nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                    <i class="bi bi-sliders"></i> Settings
                </a>
                <a href="{{ route('admin.integrations.index') }}" class="ds-nav-link {{ request()->routeIs('admin.integrations*') ? 'active' : '' }}">
                    <i class="bi bi-plug"></i> API Integrations
                </a>
            @endif
            @if (auth()->user()->isEditor())
                <div class="ds-nav-section">Editorial</div>
                <a href="{{ route('editor.dashboard') }}" class="ds-nav-link {{ request()->routeIs('editor.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
                <a href="{{ route('editor.articles.index') }}" class="ds-nav-link {{ request()->routeIs('editor.articles*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> Manuscripts
                </a>
            @endif
            @if (auth()->user()->isReviewer())
                <div class="ds-nav-section">Reviewer</div>
                <a href="{{ route('reviewer.dashboard') }}" class="ds-nav-link {{ request()->routeIs('reviewer.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
                <a href="{{ route('reviewer.reviews.index') }}" class="ds-nav-link {{ request()->routeIs('reviewer.reviews*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i> Review Queue
                </a>
            @endif
            @if (auth()->user()->isAuthor())
                <div class="ds-nav-section">Author</div>
                <a href="{{ route('author.dashboard') }}" class="ds-nav-link {{ request()->routeIs('author.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
                <a href="{{ route('author.articles.index') }}" class="ds-nav-link {{ request()->routeIs('author.articles.index') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> My Submissions
                </a>
                <a href="{{ route('author.articles.create') }}" class="ds-nav-link {{ request()->routeIs('author.articles.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle"></i> Submit Manuscript
                </a>
            @endif
            <div class="ds-divider"></div>
            <a href="{{ route('public.home') }}" class="ds-nav-link" target="_blank">
                <i class="bi bi-arrow-up-right-square"></i> View Journal
            </a>
        </nav>

        <div class="ds-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="ds-nav-link" style="color:#6B7280;">
                    <i class="bi bi-box-arrow-left"></i> Sign Out
                </button>
            </form>
        </div>
    </aside>

    {{-- TOPBAR --}}
    <header class="ds-topbar">
        <button class="ds-icon-btn d-md-none" onclick="openSB()" style="border:none;" aria-label="Open menu">
            <i class="bi bi-list"></i>
        </button>
        <h1 class="ds-topbar-title">{{ $title ?? 'Dashboard' }}</h1>
        <div style="display:flex;align-items:center;gap:10px;">
            <div class="ds-tb-search d-none d-md-flex">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Search..." aria-label="Global search" />
            </div>
            <a href="#" class="ds-icon-btn" aria-label="Notifications">
                <i class="bi bi-bell"></i>
                <span class="ds-notif-dot" aria-hidden="true"></span>
            </a>
            <div class="ds-topbar-sep"></div>
            <div class="dropdown">
                <div class="ds-tb-avatar" data-bs-toggle="dropdown" role="button" aria-label="User menu">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <div style="padding:12px 14px 10px;border-bottom:1px solid var(--border);margin-bottom:4px;">
                            <div style="font-weight:700;font-size:14px;color:var(--text-main);">{{ auth()->user()->name }}</div>
                            <div style="font-size:12px;color:var(--text-muted);">{{ auth()->user()->email }}</div>
                        </div>
                    </li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-left me-2"></i>Sign Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main class="ds-main">
        @if(session('success'))
            <div class="ds-alert ds-alert-suc">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
                <button class="ds-alert-close" onclick="this.parentElement.remove()" aria-label="Close">✕</button>
            </div>
        @endif
        @if(session('error'))
            <div class="ds-alert ds-alert-err">
                <i class="bi bi-x-circle-fill"></i>
                <span>{{ session('error') }}</span>
                <button class="ds-alert-close" onclick="this.parentElement.remove()" aria-label="Close">✕</button>
            </div>
        @endif
        @if($errors->any())
            <div class="ds-alert ds-alert-err">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>
                    @foreach($errors->all() as $e)
                        <div style="font-size:13px;">• {{ $e }}</div>
                    @endforeach
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openSB() {
            document.getElementById('ds-sidebar').classList.add('open');
            document.getElementById('ds-overlay').classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        function closeSB() {
            document.getElementById('ds-sidebar').classList.remove('open');
            document.getElementById('ds-overlay').classList.remove('show');
            document.body.style.overflow = '';
        }
    </script>
    @stack('scripts')
</body>
</html>
