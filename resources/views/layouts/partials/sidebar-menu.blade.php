@php
    // Sidebar permission-driven: item diambil dari tabel `navigations`
    // (location = 'sidebar'), difilter per-permission via Gate/can().
    $sidebarMenus = \App\Models\Navigation::where('location', 'sidebar')
        ->where('is_active', true)
        ->whereNull('parent_id')
        ->orderBy('order')
        ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('order')])
        ->get();
@endphp

@php
    $user = auth()->user();
@endphp

@if ($sidebarMenus->isEmpty())
    {{-- Fallback otomatis jika tabel navigations di database belum di-seed --}}
    @if ($user->isAdmin())
        <li class="sidebar-category">Administrasi</li>
        <li><a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}"><i class="bi bi-grid-1x2"></i> Dasbor</a></li>
        <li><a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i class="bi bi-people"></i> Pengguna</a></li>
        <li><a href="{{ route('admin.roles.index') }}" class="sidebar-link {{ request()->routeIs('admin.roles*') ? 'active' : '' }}"><i class="bi bi-shield-lock"></i> Role & Permissions</a></li>
        <li><a href="{{ route('admin.navigations.index') }}" class="sidebar-link {{ request()->routeIs('admin.navigations*') ? 'active' : '' }}"><i class="bi bi-list-ul"></i> Menu Navigasi</a></li>
        <li><a href="{{ route('admin.journals.index') }}" class="sidebar-link {{ request()->routeIs('admin.journals*') ? 'active' : '' }}"><i class="bi bi-journals"></i> Jurnal</a></li>
        <li><a href="{{ route('admin.issues.index') }}" class="sidebar-link {{ request()->routeIs('admin.issues*') ? 'active' : '' }}"><i class="bi bi-collection"></i> Terbitan</a></li>
        <li><a href="{{ route('admin.articles.index') }}" class="sidebar-link {{ request()->routeIs('admin.articles*') ? 'active' : '' }}"><i class="bi bi-file-earmark-text"></i> Artikel</a></li>
        @php
            $pendingPaymentsCount = \App\Models\Payment::whereIn('status', ['uploaded', 'waiting_verification'])->count();
        @endphp
        <li>
            <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i> Pembayaran
                @if ($pendingPaymentsCount)
                    <span class="badge rounded-pill" style="background-color: #ef4444; font-size: 10px; margin-left: auto;">{{ $pendingPaymentsCount }}</span>
                @endif
            </a>
        </li>
        <li class="sidebar-category">Pengaturan</li>
        <li><a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}"><i class="bi bi-sliders"></i> Pengaturan</a></li>
        <li><a href="{{ route('admin.pages.index') }}" class="sidebar-link {{ request()->routeIs('admin.pages*') ? 'active' : '' }}"><i class="bi bi-file-earmark-richtext"></i> Halaman Situs</a></li>
        <li><a href="{{ route('admin.integrations.index') }}" class="sidebar-link {{ request()->routeIs('admin.integrations*') ? 'active' : '' }}"><i class="bi bi-plug"></i> Integrasi API</a></li>
    @endif

    @if ($user->isEditor() && ! $user->isAdmin())
        <li class="sidebar-category">Redaksi</li>
        <li><a href="{{ route('editor.dashboard') }}" class="sidebar-link {{ request()->routeIs('editor.dashboard*') ? 'active' : '' }}"><i class="bi bi-grid-1x2"></i> Dasbor</a></li>
        <li><a href="{{ route('editor.articles.index') }}" class="sidebar-link {{ request()->routeIs('editor.articles*') ? 'active' : '' }}"><i class="bi bi-file-earmark-text"></i> Naskah</a></li>
    @endif

    @if ($user->isReviewer())
        <li class="sidebar-category">Peninjau</li>
        <li><a href="{{ route('reviewer.dashboard') }}" class="sidebar-link {{ request()->routeIs('reviewer.dashboard*') ? 'active' : '' }}"><i class="bi bi-grid-1x2"></i> Dasbor</a></li>
        <li><a href="{{ route('reviewer.reviews.index') }}" class="sidebar-link {{ request()->routeIs('reviewer.reviews*') ? 'active' : '' }}"><i class="bi bi-clipboard-check"></i> Antrean Tinjauan</a></li>
    @endif

    @if ($user->isAuthor())
        <li class="sidebar-category">Penulis</li>
        <li><a href="{{ route('author.dashboard') }}" class="sidebar-link {{ request()->routeIs('author.dashboard*') ? 'active' : '' }}"><i class="bi bi-grid-1x2"></i> Dasbor</a></li>
        <li><a href="{{ route('author.articles.index') }}" class="sidebar-link {{ request()->routeIs('author.articles*') ? 'active' : '' }}"><i class="bi bi-file-earmark-text"></i> Kiriman Saya</a></li>
        <li><a href="{{ route('author.articles.create') }}" class="sidebar-link {{ request()->routeIs('author.articles.create*') ? 'active' : '' }}"><i class="bi bi-plus-circle"></i> Kirim Naskah</a></li>
    @endif
@else
@foreach ($sidebarMenus as $menu)
    @php
        $visibleChildren = $menu->children->filter(function ($child) use ($user) {
            // 1. Permission check (admin selalu diizinkan; fallback bila cache permission Spatie kosong)
            if ($child->permission && ! $user->isAdmin() && ! $user->can($child->permission)) {
                $target = $child->route_name ?? $child->url ?? '';
                $isAllowed = (str_starts_with($target, 'author.') && $user->isAuthor())
                    || (str_starts_with($target, 'reviewer.') && $user->isReviewer())
                    || (str_starts_with($target, 'editor.') && ($user->isEditor() || $user->isAdmin()));
                if (! $isAllowed) {
                    return false;
                }
            }

            // 2. Portal / Role context check to prevent route bleeding and 403 errors
            $target = $child->route_name ?? $child->url ?? '';
            if (str_starts_with($target, 'admin.') || str_contains($target, 'route::admin.')) {
                if (! $user->isAdmin()) return false;
            } elseif (str_starts_with($target, 'editor.') || str_contains($target, 'route::editor.')) {
                if (! $user->isEditor() && ! $user->isAdmin()) return false;
            } elseif (str_starts_with($target, 'reviewer.') || str_contains($target, 'route::reviewer.')) {
                if (! $user->isReviewer()) return false;
            } elseif (str_starts_with($target, 'author.') || str_contains($target, 'route::author.')) {
                if (! $user->isAuthor()) return false;
            }

            return true;
        });
    @endphp

    @if ($menu->children->count() > 0)
        {{-- Kategori + item child --}}
        @if ($visibleChildren->count() > 0)
            <li class="sidebar-category">{{ $menu->title }}</li>
            @foreach ($visibleChildren as $child)
                @php
                    $isPayments = $child->route_name === 'admin.payments';
                    $pendingCount = $isPayments ? \App\Models\Payment::whereIn('status', ['uploaded', 'waiting_verification'])->count() : 0;
                @endphp
                <li>
                    <a href="{{ nav_url($child) }}"
                        class="sidebar-link {{ nav_is_active($child) ? 'active' : '' }}">
                        <i class="bi {{ $child->icon ?: 'bi-arrow-right' }}"></i> {{ $child->title }}
                        @if ($pendingCount)
                            <span class="badge rounded-pill"
                                style="background-color: #ef4444; font-size: 10px; margin-left: auto;">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li>
            @endforeach
        @endif
    @else
        {{-- Item tunggal (bukan kategori) --}}
        @if (! $menu->permission || auth()->user()->can($menu->permission))
            <li class="sidebar-category">{{ $menu->title }}</li>
            <li>
                <a href="{{ nav_url($menu) }}"
                    class="sidebar-link {{ nav_is_active($menu) ? 'active' : '' }}">
                    <i class="bi {{ $menu->icon ?: 'bi-arrow-right' }}"></i> {{ $menu->title }}
                </a>
            </li>
        @endif
    @endif
@endforeach
@endif