<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>{{ $title ?? 'Sign In' }} — {{ \App\Models\Setting::get('site_name', config('app.name', 'OJS')) }}</title>
  
  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300&display=swap" rel="stylesheet">
  
  {{-- Icons --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  {{-- Tailwind CSS 4 --}}
  <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
  
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    
    .auth-bg {
      flex: 1; 
      display: flex; 
      align-items: center; 
      justify-content: center;
      padding: 40px 16px;
    }
    
    .auth-container { 
      width: 100%; 
      max-width: 460px; 
    }
    
    .auth-logo { 
      text-align: center; 
      margin-bottom: 32px; 
    }
    
    .auth-logo-icon {
      width: 56px; 
      height: 56px;
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
      border-radius: 14px;
      display: inline-flex; 
      align-items: center; 
      justify-content: center;
      color: #fff; 
      font-size: 26px; 
      margin-bottom: 16px;
      box-shadow: 0 8px 24px rgba(37, 99, 235, 0.25);
    }
    
    .auth-logo-title { 
      display: block; 
      font-size: 18px; 
      font-weight: 700; 
      color: #0f172a; 
      letter-spacing: -0.02em; 
    }
    
    .auth-logo-sub { 
      display: block; 
      font-size: 14px; 
      color: #64748b; 
      margin-top: 4px; 
    }
    
    .auth-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 16px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
      overflow: hidden;
    }
    
    .auth-card-header { 
      padding: 32px 32px 0; 
    }
    
    .auth-card-body { 
      padding: 28px 32px 32px; 
    }
    
    .auth-title { 
      font-size: 22px; 
      font-weight: 700; 
      color: #0f172a; 
      letter-spacing: -0.02em; 
      margin: 0 0 6px 0; 
    }
    
    .auth-sub { 
      font-size: 14px; 
      color: #64748b; 
      margin: 0 0 28px; 
    }
    
    .auth-footer { 
      text-align: center; 
      padding: 20px; 
      font-size: 13px; 
      color: #64748b; 
    }
    
    .auth-footer a { 
      color: #2563eb; 
      font-weight: 500; 
      text-decoration: none; 
    }
    
    .auth-footer a:hover { 
      text-decoration: underline; 
    }
    
    /* Form components */
    .lbl { 
      display: block; 
      font-size: 13px; 
      font-weight: 500; 
      color: #334155; 
      margin-bottom: 6px; 
    }
    
    .lbl .req { 
      color: #ef4444; 
      margin-left: 2px; 
    }
    
    .inp, .sel {
      width: 100%; 
      padding: 10px 14px;
      border: 1px solid #e2e8f0; 
      border-radius: 10px;
      font-family: inherit; 
      font-size: 14px; 
      color: #0f172a;
      background: #fff;
      transition: all 0.15s; 
      outline: none;
    }
    
    .inp:focus, .sel:focus {
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    
    .inp::placeholder { 
      color: #94a3b8; 
      font-size: 13px; 
    }
    
    .inp.is-invalid { 
      border-color: #ef4444; 
      box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1); 
    }
    
    .f-err { 
      font-size: 12px; 
      color: #ef4444; 
      margin-top: 5px; 
      display: flex; 
      align-items: center; 
      gap: 4px; 
    }
    
    .btn-auth {
      width: 100%; 
      padding: 11px 18px; 
      border-radius: 10px;
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); 
      color: #fff; 
      border: none;
      font-size: 14px; 
      font-weight: 600; 
      cursor: pointer;
      font-family: inherit; 
      transition: all 0.2s;
      display: flex; 
      align-items: center; 
      justify-content: center; 
      gap: 8px;
    }
    
    .btn-auth:hover { 
      background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
      box-shadow: 0 4px 16px rgba(37, 99, 235, 0.3);
      transform: translateY(-1px);
    }
    
    .demo-box {
      background: #f8fafc; 
      border: 1px solid #e2e8f0;
      border-radius: 10px; 
      padding: 14px 16px; 
      margin-top: 20px;
    }
    
    .demo-title {
      font-size: 11px; 
      font-weight: 600; 
      text-transform: uppercase;
      letter-spacing: 0.06em; 
      color: #64748b; 
      margin-bottom: 10px;
    }
    
    .demo-row { 
      display: flex; 
      justify-content: space-between; 
      font-size: 12px; 
      color: #64748b; 
      padding: 3px 0; 
    }
    
    .demo-row span { 
      font-weight: 500; 
      color: #0f172a; 
    }
    
    /* SSO buttons */
    .btn-sso {
      width: 100%; 
      padding: 10px 16px; 
      border-radius: 10px;
      background: #fff; 
      color: #334155;
      border: 1px solid #e2e8f0;
      font-size: 14px; 
      font-weight: 500; 
      cursor: pointer;
      font-family: inherit; 
      transition: all 0.15s;
      display: flex; 
      align-items: center; 
      justify-content: center; 
      gap: 10px;
      text-decoration: none;
    }
    
    .btn-sso:hover { 
      background: #f8fafc; 
      border-color: #cbd5e1; 
      color: #0f172a; 
    }
    
    /* Divider */
    .divider {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 24px 0;
      color: #94a3b8;
      font-size: 12px;
    }
    
    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #e2e8f0;
    }
    
    /* Alert */
    .alert {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 12px 14px;
      border-radius: 10px;
      font-size: 13px;
      margin-bottom: 20px;
    }
    
    .alert-error {
      background: #fef2f2;
      color: #dc2626;
      border: 1px solid #fee2e2;
    }
    
    .alert-success {
      background: #ecfdf5;
      color: #059669;
      border: 1px solid #d1fae5;
    }
    
    .alert i {
      font-size: 16px;
      flex-shrink: 0;
    }
  </style>
  
  @stack('styles')
</head>

<body>
<div class="auth-bg">
  <div class="auth-container">
    <div class="auth-logo">
      <div class="auth-logo-icon">
        <i class="bi bi-journal-bookmark-fill"></i>
      </div>
      <span class="auth-logo-title">{{ \App\Models\Setting::get('site_name', 'OJS System') }}</span>
      <span class="auth-logo-sub">Scholarly Publishing Platform</span>
    </div>
    
    <div class="auth-card">
      @yield('content')
    </div>
    
    <div class="auth-footer">
      <a href="{{ route('public.home') }}">← Return to Journal</a>
      <span style="margin:0 10px;opacity:0.4;">·</span>
      <span>&copy; {{ date('Y') }} {{ \App\Models\Setting::get('site_name', 'OJS System') }}</span>
    </div>
  </div>
</div>

@stack('scripts')
</body>
</html>
