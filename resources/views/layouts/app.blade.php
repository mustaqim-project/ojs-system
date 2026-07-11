<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>{{ $title ?? config('app.name') }} — {{ \App\Models\Setting::get('site_name','OJS') }}</title>
  <meta name="description" content="{{ $description ?? \App\Models\Setting::get('site_description','') }}"/>
  
  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300&display=swap" rel="stylesheet">
  
  {{-- Icons --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>
  
  {{-- Tailwind CSS via CDN for rapid prototyping --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'system-ui', 'sans-serif'],
            serif: ['Merriweather', 'Georgia', 'serif'],
          },
          colors: {
            primary: {
              50: '#eff6ff',
              100: '#dbeafe',
              200: '#bfdbfe',
              300: '#93c5fd',
              400: '#60a5fa',
              500: '#3b82f6',
              600: '#2563eb',
              700: '#1d4ed8',
              800: '#1e40af',
              900: '#1e3a8a',
            },
            slate: {
              50: '#f8fafc',
              100: '#f1f5f9',
              200: '#e2e8f0',
              300: '#cbd5e1',
              400: '#94a3b8',
              500: '#64748b',
              600: '#475569',
              700: '#334155',
              800: '#1e293b',
              900: '#0f172a',
            }
          }
        }
      }
    }
  </script>
  
  <style>
    /* Design System Tokens */
    :root {
      --color-primary: #2563eb;
      --color-primary-hover: #1d4ed8;
      --color-success: #16a34a;
      --color-warning: #ca8a04;
      --color-danger: #dc2626;
      --color-info: #0891b2;
      --bg-app: #f8fafc;
      --bg-surface: #ffffff;
      --border: #e2e8f0;
      --text-main: #0f172a;
      --text-muted: #64748b;
      --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
      --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
      --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
      --radius-sm: 6px;
      --radius-md: 8px;
      --radius-lg: 12px;
      --nav-h: 64px;
    }
    
    body { 
      font-family: 'Inter', system-ui, sans-serif; 
      color: var(--text-main); 
      background: #fff;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    /* Navigation */
    .pub-nav {
      background: #fff;
      border-bottom: 1px solid var(--border);
      position: sticky; top: 0; z-index: 100;
      height: var(--nav-h);
    }
    .pub-nav-inner {
      max-width: 1280px; margin: 0 auto; padding: 0 40px;
      height: 100%; display: flex; align-items: center; gap: 40px;
    }
    .pub-brand {
      display: flex; align-items: center; gap: 12px;
      text-decoration: none; flex-shrink: 0;
    }
    .pub-brand-icon {
      width: 36px; height: 36px;
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-size: 18px;
      box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
    }
    .pub-brand-name {
      font-size: 16px; font-weight: 700;
      color: var(--text-main); letter-spacing: -0.02em;
    }
    .pub-nav-links { display: flex; gap: 4px; flex: 1; }
    .pub-nav-link {
      padding: 8px 14px; border-radius: 8px;
      font-size: 14px; font-weight: 500; color: var(--text-muted);
      text-decoration: none; transition: all 0.2s ease;
    }
    .pub-nav-link:hover { background: var(--bg-app); color: var(--text-main); }
    .pub-nav-link.active { background: #eff6ff; color: #2563eb; font-weight: 600; }
    .pub-nav-actions { display: flex; align-items: center; gap: 12px; margin-left: auto; }
    
    /* Buttons */
    .pub-btn {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 9px 18px; border-radius: 8px;
      font-size: 14px; font-weight: 600;
      text-decoration: none; transition: all 0.2s ease;
      cursor: pointer; border: none; font-family: inherit;
    }
    .pub-btn-ghost {
      background: transparent; color: var(--text-main);
      border: 1px solid var(--border);
    }
    .pub-btn-ghost:hover { background: var(--bg-app); border-color: #cbd5e1; }
    .pub-btn-primary {
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
      color: #fff;
      box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
    }
    .pub-btn-primary:hover { 
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
    }
    
    /* Flash Messages */
    .pub-flash {
      padding: 14px 32px; font-size: 14px; font-weight: 500;
      display: flex; align-items: center; gap: 12px;
    }
    .pub-flash-suc { 
      background: linear-gradient(to right, #f0fdf4, #dcfce7); 
      color: #166534; 
      border-bottom: 1px solid #bbf7d0; 
    }
    .pub-flash-err { 
      background: linear-gradient(to right, #fef2f2, #fee2e2); 
      color: #991b1b; 
      border-bottom: 1px solid #fecaca; 
    }

    /* Content Wrapper */
    .pub-wrap { max-width: 1280px; margin: 0 auto; padding: 64px 40px; }
    .pub-wrap-narrow { max-width: 800px; margin: 0 auto; padding: 64px 40px; }

    /* Article Reading Mode */
    .article-body {
      font-family: 'Merriweather', Georgia, serif;
      font-size: 18px; line-height: 1.8;
      color: var(--text-main);
    }
    .article-body h2 { font-size: 24px; font-weight: 700; margin: 40px 0 16px; }
    .article-body h3 { font-size: 20px; font-weight: 700; margin: 32px 0 12px; }
    .article-body p  { margin: 0 0 20px; }
    .article-abstract {
      font-family: 'Inter', sans-serif;
      font-size: 15px; line-height: 1.7;
      color: var(--text-muted);
      border-left: 4px solid #2563eb;
      padding: 20px 24px;
      background: linear-gradient(to right, #eff6ff, #f8fafc);
      border-radius: 0 8px 8px 0;
      margin: 28px 0;
    }

    /* Cards */
    .pub-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 28px;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pub-card:hover {
      box-shadow: var(--shadow-lg);
      border-color: #cbd5e1;
      transform: translateY(-2px);
    }
    .pub-article-title {
      font-size: 18px; font-weight: 700; color: var(--text-main);
      line-height: 1.45; text-decoration: none;
      display: block; margin-bottom: 10px;
    }
    .pub-article-title:hover { color: #2563eb; }
    .pub-article-meta {
      font-size: 13px; color: var(--text-muted);
      display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 14px;
    }
    .pub-keyword-pill {
      display: inline-block;
      padding: 4px 12px; border-radius: 20px;
      background: var(--bg-app); border: 1px solid var(--border);
      font-size: 12px; color: var(--text-muted);
      text-decoration: none; transition: all 0.15s;
      margin: 3px 3px 3px 0;
    }
    .pub-keyword-pill:hover { 
      background: #eff6ff; 
      border-color: #2563eb; 
      color: #2563eb; 
    }

    /* Footer */
    .pub-footer {
      background: linear-gradient(to bottom, #0f172a, #1e293b);
      color: #94a3b8; margin-top: 120px;
      border-top: 1px solid #1e293b;
    }
    .pub-footer-inner { max-width: 1280px; margin: 0 auto; padding: 80px 40px 48px; }
    .pub-footer-brand { font-size: 16px; font-weight: 700; color: #f1f5f9; letter-spacing: -0.02em; margin-bottom: 12px; }
    .pub-footer-desc { font-size: 14px; color: #94a3b8; line-height: 1.7; max-width: 360px; }
    .pub-footer-hdr { 
      font-size: 12px; font-weight: 700; letter-spacing: 0.08em; 
      text-transform: uppercase; color: #f1f5f9; margin-bottom: 20px; 
    }
    .pub-footer-link { 
      display: block; font-size: 14px; color: #94a3b8; 
      text-decoration: none; padding: 6px 0; transition: color 0.2s; 
    }
    .pub-footer-link:hover { color: #fff; }
    .pub-footer-divider { border-color: #1e293b; margin: 56px 0 32px; }
    .pub-footer-copy { font-size: 13px; color: #64748b; }

    /* Responsive */
    @media (max-width: 768px) {
      .pub-nav-links { display: none; }
      .pub-nav-inner { padding: 0 20px; gap: 16px; }
      .pub-wrap { padding: 32px 20px; }
      .pub-wrap-narrow { padding: 32px 20px; }
      .pub-footer-inner { padding: 56px 20px 40px; }
    }
    
    /* Animations */
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(12px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.4s ease-out forwards; }
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
  </style>
  @stack('styles')
</head>
<body>

{{-- PUBLIC NAVIGATION --}}
<nav class="pub-nav" role="navigation" aria-label="Main navigation">
  <div class="pub-nav-inner">
    <a href="{{ route('public.home') }}" class="pub-brand">
      <div class="pub-brand-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
      <div class="pub-brand-name">{{ \App\Models\Setting::get('site_name','OJS System') }}</div>
    </a>
    <div class="pub-nav-links">
      <a href="{{ route('public.home') }}" class="pub-nav-link {{ request()->routeIs('public.home') ? 'active':'' }}">Home</a>
      <a href="{{ route('public.journals.index') }}" class="pub-nav-link {{ request()->routeIs('public.journals*') ? 'active':'' }}">Journals</a>
      <a href="{{ route('public.articles.index') }}" class="pub-nav-link {{ request()->routeIs('public.articles*') ? 'active':'' }}">Articles</a>
      <a href="{{ route('public.search') }}" class="pub-nav-link {{ request()->routeIs('public.search') ? 'active':'' }}">Search</a>
    </div>
    <div class="pub-nav-actions">
      @auth
        <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="pub-btn-ghost"><i class="bi bi-grid me-1"></i>Dashboard</a>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
          @csrf
          <button type="submit" class="pub-btn-ghost" style="cursor:pointer;font-family:inherit;">Sign Out</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="pub-btn-ghost">Sign In</a>
        <a href="{{ route('register') }}" class="pub-btn-primary">Submit Manuscript</a>
      @endauth
    </div>
  </div>
</nav>

{{-- FLASH MESSAGES --}}
@if(session('success'))
  <div class="pub-flash pub-flash-suc">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:inherit;font-size:16px;">✕</button>
  </div>
@endif
@if(session('error'))
  <div class="pub-flash pub-flash-err">
    <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
    <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:inherit;font-size:16px;">✕</button>
  </div>
@endif

@yield('content')

{{-- FOOTER --}}
<footer class="pub-footer" role="contentinfo">
  <div class="pub-footer-inner">
    <div class="row g-4">
      <div class="col-12 col-md-4">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
          <div style="width:30px;height:30px;background:var(--primary);border-radius:6px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;">
            <i class="bi bi-journal-bookmark-fill"></i>
          </div>
          <div class="pub-footer-brand">{{ \App\Models\Setting::get('site_name','OJS System') }}</div>
        </div>
        <p class="pub-footer-desc">{{ \App\Models\Setting::get('site_description','An internationally recognized open-access scholarly publishing platform.') }}</p>
      </div>
      <div class="col-6 col-md-2">
        <div class="pub-footer-hdr">Navigate</div>
        <a href="{{ route('public.home') }}" class="pub-footer-link">Home</a>
        <a href="{{ route('public.journals.index') }}" class="pub-footer-link">Journals</a>
        <a href="{{ route('public.articles.index') }}" class="pub-footer-link">Articles</a>
        <a href="{{ route('public.search') }}" class="pub-footer-link">Search</a>
      </div>
      <div class="col-6 col-md-2">
        <div class="pub-footer-hdr">Authors</div>
        <a href="{{ route('register') }}" class="pub-footer-link">Submit Manuscript</a>
        <a href="{{ route('login') }}" class="pub-footer-link">Author Portal</a>
        <a href="#" class="pub-footer-link">Submission Guide</a>
        <a href="#" class="pub-footer-link">Publication Ethics</a>
      </div>
      <div class="col-12 col-md-4">
        <div class="pub-footer-hdr">Contact</div>
        <p style="font-size:14px;color:#9CA3AF;margin:0;">{{ \App\Models\Setting::get('site_email','editorial@journal.ac.id') }}</p>
        <p style="font-size:14px;color:#9CA3AF;margin:6px 0;">{{ \App\Models\Setting::get('contact_phone','') }}</p>
        <p style="font-size:14px;color:#9CA3AF;margin:6px 0;">{{ \App\Models\Setting::get('contact_address','') }}</p>
      </div>
    </div>
    <hr class="pub-footer-divider"/>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
      <span class="pub-footer-copy">&copy; {{ date('Y') }} {{ \App\Models\Setting::get('site_name','OJS System') }}. All rights reserved.</span>
      <span class="pub-footer-copy">Built with Laravel · Open Access Publishing</span>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
