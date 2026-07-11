<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>{{ $title ?? 'Sign In' }} — {{ \App\Models\Setting::get('site_name', config('app.name')) }}</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>
  <link rel="stylesheet" href="{{ asset('css/ojs.css') }}"/>
  <style>
    /* Guest layout uses ojs.css tokens — no duplication needed */
    body { background: var(--bg-app); min-height: 100vh; display: flex; flex-direction: column; }

    .auth-bg {
      flex: 1; display: flex; align-items: center; justify-content: center;
      padding: 40px 16px;
    }
    .auth-container { width: 100%; max-width: 440px; }

    .auth-logo { text-align: center; margin-bottom: 32px; }
    .auth-logo-icon {
      width: 48px; height: 48px;
      background: var(--primary); border-radius: var(--radius-md);
      display: inline-flex; align-items: center; justify-content: center;
      color: #fff; font-size: 22px; margin-bottom: 16px;
      box-shadow: 0 0 0 8px var(--primary-light);
    }
    .auth-logo-title { display: block; font-size: 17px; font-weight: 700; color: var(--text-main); letter-spacing: -0.01em; }
    .auth-logo-sub { display: block; font-size: 13px; color: var(--text-muted); margin-top: 3px; }

    .auth-card {
      background: var(--bg-surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-lg);
      overflow: hidden;
    }
    .auth-card-header { padding: 28px 28px 0; }
    .auth-card-body { padding: 24px 28px 28px; }
    .auth-title { font-size: 20px; font-weight: 700; color: var(--text-main); letter-spacing: -0.02em; margin: 0 0 4px 0; }
    .auth-sub { font-size: 14px; color: var(--text-muted); margin: 0 0 24px; }

    .auth-footer { text-align: center; padding: 20px; font-size: 13px; color: var(--text-muted); }
    .auth-footer a { color: var(--text-muted); font-weight: 500; text-decoration: none; }
    .auth-footer a:hover { color: var(--primary); }

    /* Form components */
    .lbl { display: block; font-size: 13px; font-weight: 500; color: var(--text-main); margin-bottom: 6px; }
    .lbl .req { color: var(--danger); margin-left: 2px; }
    .inp, .sel {
      width: 100%; padding: 9px 12px;
      border: 1px solid var(--border); border-radius: var(--radius-sm);
      font-family: inherit; font-size: 14px; color: var(--text-main);
      background: var(--bg-surface);
      transition: border-color 0.15s, box-shadow 0.15s; outline: none;
    }
    .inp:focus, .sel:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(15,76,129,0.12);
    }
    .inp::placeholder { color: var(--text-muted); font-size: 13px; }
    .inp.is-invalid { border-color: var(--danger); box-shadow: 0 0 0 3px rgba(197,48,48,0.1); }
    .f-err { font-size: 12px; color: var(--danger); margin-top: 5px; display: flex; align-items: center; gap: 4px; }

    .btn-auth {
      width: 100%; padding: 10px 16px; border-radius: var(--radius-sm);
      background: var(--primary); color: #fff; border: none;
      font-size: 14px; font-weight: 600; cursor: pointer;
      font-family: inherit; transition: all 0.15s;
      display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-auth:hover { background: var(--primary-hover); }

    .demo-box {
      background: var(--bg-app); border: 1px solid var(--border);
      border-radius: var(--radius-sm); padding: 12px 16px; margin-top: 20px;
    }
    .demo-title {
      font-size: 11px; font-weight: 600; text-transform: uppercase;
      letter-spacing: 0.06em; color: var(--text-muted); margin-bottom: 8px;
    }
    .demo-row { display: flex; justify-content: space-between; font-size: 12px; color: var(--text-muted); padding: 2px 0; }
    .demo-row span { font-weight: 500; color: var(--text-main); }

    /* SSO buttons */
    .btn-sso {
      width: 100%; padding: 9px 16px; border-radius: var(--radius-sm);
      background: var(--bg-surface); color: var(--text-main);
      border: 1px solid var(--border);
      font-size: 14px; font-weight: 500; cursor: pointer;
      font-family: inherit; transition: all 0.15s;
      display: flex; align-items: center; justify-content: center; gap: 10px;
      text-decoration: none;
    }
    .btn-sso:hover { background: var(--bg-app); border-color: #CBD5E1; color: var(--text-main); }
  </style>
</head>
<body>
<div class="auth-bg">
  <div class="auth-container">
    <div class="auth-logo">
      <div class="auth-logo-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
      <span class="auth-logo-title">{{ \App\Models\Setting::get('site_name','OJS System') }}</span>
      <span class="auth-logo-sub">Scholarly Publishing Platform</span>
    </div>
    <div class="auth-card">
      @yield('content')
    </div>
    <div class="auth-footer">
      <a href="{{ route('public.home') }}">← Return to Journal</a>
      <span style="margin:0 10px;opacity:0.4;">·</span>
      <span>&copy; {{ date('Y') }} {{ \App\Models\Setting::get('site_name','OJS System') }}</span>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
