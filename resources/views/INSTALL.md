# 🎨 OJS System — Bootstrap 5 Premium UI Upgrade

## File yang Disertakan (41 file)

```
ojs-bs5/
├── ojs.css                          ← Design system CSS (WAJIB)
│
├── layouts/
│   ├── dashboard.blade.php          ← Layout dashboard semua role
│   ├── app.blade.php                ← Layout public website
│   └── guest.blade.php             ← Layout login/register
│
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
│
├── public/
│   ├── home.blade.php              ← Landing page premium
│   ├── search.blade.php
│   ├── articles/index.blade.php
│   ├── articles/show.blade.php
│   ├── journals/index.blade.php
│   └── journals/show.blade.php
│
├── admin/
│   ├── dashboard.blade.php
│   ├── users/{index,create,edit}.blade.php
│   ├── journals/{index,create,edit}.blade.php
│   ├── issues/{index,create}.blade.php
│   ├── articles/{index,show}.blade.php
│   ├── payments/{index,show}.blade.php
│   └── settings/index.blade.php
│
├── author/
│   ├── dashboard.blade.php
│   ├── articles/{index,create,show,revision}.blade.php
│   └── payments/show.blade.php
│
├── editor/
│   ├── dashboard.blade.php
│   └── articles/{index,show}.blade.php
│
├── reviewer/
│   ├── dashboard.blade.php
│   └── reviews/{index,show}.blade.php
│
├── components/
│   └── status-badge.blade.php
│
└── errors/
    ├── 403.blade.php
    └── 404.blade.php
```

---

## 🚀 Langkah Instalasi

### 1. Copy ojs.css ke project Laravel

```bash
cp ojs-bs5/ojs.css your-laravel-project/public/css/ojs.css
```

### 2. Copy semua Blade views

```bash
# Copy semua file view ke resources/views
cp -r ojs-bs5/layouts/*     your-laravel-project/resources/views/layouts/
cp -r ojs-bs5/auth/*        your-laravel-project/resources/views/auth/
cp -r ojs-bs5/public/*      your-laravel-project/resources/views/public/
cp -r ojs-bs5/admin/*       your-laravel-project/resources/views/admin/
cp -r ojs-bs5/author/*      your-laravel-project/resources/views/author/
cp -r ojs-bs5/editor/*      your-laravel-project/resources/views/editor/
cp -r ojs-bs5/reviewer/*    your-laravel-project/resources/views/reviewer/
cp -r ojs-bs5/components/*  your-laravel-project/resources/views/components/
cp -r ojs-bs5/errors/*      your-laravel-project/resources/views/errors/
```

### 3. Tidak perlu install NPM

Semua dependency sudah melalui CDN:
- Bootstrap 5.3.3
- Bootstrap Icons 1.11.3
- Google Fonts (Plus Jakarta Sans)

### 4. Jalankan project

```bash
php artisan serve
```

---

## 🎨 Design System

### CSS Variables (ojs.css)

```css
:root {
  --acc:    #2563eb;   /* Primary blue */
  --canvas: #f4f6f9;   /* Page background */
  --surf:   #ffffff;   /* Card surface */
  --brd:    #e2e8f0;   /* Border color */
  --txt:    #0f172a;   /* Text primary */
  --txt2:   #475569;   /* Text secondary */
  --green:  #16a34a;   /* Success */
  --red:    #dc2626;   /* Danger */
}
```

### Komponen Utama

| Class | Deskripsi |
|-------|-----------|
| `.btn-o.btn-pri` | Button primary blue |
| `.btn-o.btn-out` | Button outline |
| `.btn-o.btn-ghost` | Button ghost |
| `.btn-o.btn-suc` | Button success green |
| `.btn-o.btn-danger` | Button danger red |
| `.btn-o.btn-sm` | Button small |
| `.btn-o.btn-lg` | Button large |
| `.card-ojs` | Card container |
| `.card-hdr` | Card header |
| `.card-body-p` | Card body with padding |
| `.tbl` | Table modern |
| `.bx` | Badge base |
| `.bx-published` | Status badge published |
| `.bx-submitted` | Status badge submitted |
| `.inp` | Input field |
| `.sel` | Select field |
| `.txta` | Textarea |
| `.lbl` | Form label |
| `.f-section` | Form section card |
| `.alert-o.a-suc` | Alert success |
| `.alert-o.a-err` | Alert error |
| `.alert-o.a-warn` | Alert warning |
| `.alert-o.a-info` | Alert info |
| `.stat-card` | Statistic card |
| `.ftabs` `.ftab` | Filter tabs |
| `.empty-st` | Empty state |
| `.tl-item` | Timeline item |
| `.inv-card` | Invoice dark card |
| `.fu.fd1` `.fd2` etc | Fade-up animations |

### Sidebar & Layout

- Sidebar width: `--sw: 248px`
- Topbar height: `--th: 60px`
- Layout: `margin-left: var(--sw); margin-top: var(--th)`
- Responsive: Sidebar collapse at `768px`

---

## ✅ Fitur Design

- ✅ **Plus Jakarta Sans** — premium typeface
- ✅ **Dark Sidebar** — editorial dark ink aesthetic
- ✅ **Fade-up animations** — staggered page load
- ✅ **Hover effects** — cards, rows, buttons
- ✅ **Interactive star rating** — reviewer form
- ✅ **Search highlight** — highlight keyword di hasil
- ✅ **Invoice dark card** — premium payment UI
- ✅ **Progress bars** — reviewer completion rate
- ✅ **Timeline component** — article status tracking
- ✅ **Responsive** — mobile sidebar collapse
- ✅ **Modal components** — publish & verify
- ✅ **Empty states** — semua halaman kosong
- ✅ **Status badges** — semua 10 status workflow
