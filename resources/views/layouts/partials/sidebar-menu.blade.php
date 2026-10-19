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

@foreach ($sidebarMenus as $menu)
    @php
        $user = auth()->user();
        $visibleChildren = $menu->children->filter(function ($child) use ($user) {
            // 1. Permission check
            if ($child->permission && ! $user->can($child->permission)) {
                return false;
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