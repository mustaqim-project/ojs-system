<?php

namespace Database\Seeders;

use App\Models\Navigation;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing
        Navigation::truncate();

        $this->seedHeaderMenu();
        $this->seedSidebarMenu();

        $this->command->info('Navigations seeded (header + sidebar permission-driven).');
    }

    /**
     * Menu header situs publik (front-end).
     */
    private function seedHeaderMenu(): void
    {
        // 1. Home
        Navigation::create([
            'title' => 'Home',
            'url' => '/#home',
            'order' => 1,
            'location' => 'header',
        ]);

        // 2. About (Parent)
        $about = Navigation::create([
            'title' => 'About',
            'url' => '#',
            'order' => 2,
            'location' => 'header',
        ]);
        
        $about->children()->createMany([
            ['title' => 'About Journal', 'url' => '/about', 'order' => 1],
            ['title' => 'Focus & Scope', 'url' => '/focus-and-scope', 'order' => 2],
            ['title' => 'Journal Policies', 'url' => '/journal-policies', 'order' => 3],
        ]);

        // 3. Editorial (Parent)
        $editorial = Navigation::create([
            'title' => 'Editorial',
            'url' => '#',
            'order' => 3,
            'location' => 'header',
        ]);

        $editorial->children()->createMany([
            ['title' => 'Editorial Team', 'url' => '/editorial-team', 'order' => 1],
            ['title' => 'Reviewer Board', 'url' => '/reviewer-board', 'order' => 2],
        ]);

        // 4. Browse (Parent)
        $browse = Navigation::create([
            'title' => 'Browse',
            'url' => '#',
            'order' => 4,
            'location' => 'header',
        ]);

        $browse->children()->createMany([
            ['title' => 'Current Issue', 'url' => '/current-issue', 'order' => 1],
            ['title' => 'Archive', 'url' => '/archive', 'order' => 2],
            ['title' => 'All Articles', 'url' => '/articles', 'order' => 3],
            ['title' => 'All Journals', 'url' => '/journals', 'order' => 4],
        ]);

        // 5. Guidelines (Parent)
        $guidelines = Navigation::create([
            'title' => 'Guidelines',
            'url' => '#',
            'order' => 5,
            'location' => 'header',
        ]);

        $guidelines->children()->createMany([
            ['title' => 'Author Guidelines', 'url' => '/author-guidelines', 'order' => 1],
            ['title' => 'Publication Ethics', 'url' => '/publication-ethics', 'order' => 2],
            ['title' => 'Peer Review Process', 'url' => '/peer-review-process', 'order' => 3],
        ]);

        // 6. Information (Parent)
        $info = Navigation::create([
            'title' => 'Information',
            'url' => '#',
            'order' => 6,
            'location' => 'header',
        ]);

        $info->children()->createMany([
            ['title' => 'Announcements', 'url' => '/announcements', 'order' => 1],
            ['title' => 'Call for Papers', 'url' => '/call-for-papers', 'order' => 2],
            ['title' => 'Indexing', 'url' => '/indexing', 'order' => 3],
            ['title' => 'Contact Us', 'url' => '/contact', 'order' => 4],
        ]);
    }

    /**
     * Menu sidebar dashboard (permission-driven).
     *
     * Konvensi:
     *  - Parent  = kategori menu (tanpa url).
     *  - Child   = item menu; `url` boleh berisi `route::admin.users.index`
     *              (direferensikan ke named route) atau URL literal.
     *  - `route_name` = pola aktifasi route (mis. `admin.users` → menu
     *              ikut aktif untuk admin.users.* / admin.users.edit, dst.).
     *  - `permission` = permission yang dibutuhkan; null = semua user login.
     *  - `location` = 'sidebar' membedakan dari menu header situs publik.
     */
    private function seedSidebarMenu(): void
    {
        $categories = [
            [
                'title' => 'Administrasi',
                'order' => 1,
                'items' => [
                    ['title' => 'Dasbor', 'url' => 'route::admin.dashboard', 'route_name' => 'admin.dashboard', 'permission' => 'dashboard.view', 'icon' => 'bi-grid-1x2', 'order' => 1],
                    ['title' => 'Pengguna', 'url' => 'route::admin.users.index', 'route_name' => 'admin.users', 'permission' => 'users.view', 'icon' => 'bi-people', 'order' => 2],
                    ['title' => 'Role & Permissions', 'url' => 'route::admin.roles.index', 'route_name' => 'admin.roles', 'permission' => 'roles.view', 'icon' => 'bi-shield-lock', 'order' => 3],
                    ['title' => 'Menu Navigasi', 'url' => 'route::admin.navigations.index', 'route_name' => 'admin.navigations', 'permission' => 'menus.view', 'icon' => 'bi-list-ul', 'order' => 4],
                    ['title' => 'Jurnal', 'url' => 'route::admin.journals.index', 'route_name' => 'admin.journals', 'permission' => 'journal.view', 'icon' => 'bi-journals', 'order' => 5],
                    ['title' => 'Terbitan', 'url' => 'route::admin.issues.index', 'route_name' => 'admin.issues', 'permission' => 'issue.manage', 'icon' => 'bi-collection', 'order' => 6],
                    ['title' => 'Artikel', 'url' => 'route::admin.articles.index', 'route_name' => 'admin.articles', 'permission' => 'submission.view', 'icon' => 'bi-file-earmark-text', 'order' => 7],
                    ['title' => 'Pembayaran', 'url' => 'route::admin.payments.index', 'route_name' => 'admin.payments', 'permission' => 'invoice.view', 'icon' => 'bi-credit-card', 'order' => 8],
                ],
            ],
            [
                'title' => 'Pengaturan',
                'order' => 2,
                'items' => [
                    ['title' => 'Pengaturan', 'url' => 'route::admin.settings.index', 'route_name' => 'admin.settings', 'permission' => 'system-settings.view', 'icon' => 'bi-sliders', 'order' => 1],
                    ['title' => 'Halaman Situs', 'url' => 'route::admin.pages.index', 'route_name' => 'admin.pages', 'permission' => 'cms.view', 'icon' => 'bi-file-earmark-richtext', 'order' => 2],
                    ['title' => 'Integrasi API', 'url' => 'route::admin.integrations.index', 'route_name' => 'admin.integrations', 'permission' => 'api.tokens.manage', 'icon' => 'bi-plug', 'order' => 3],
                ],
            ],
            [
                'title' => 'Redaksi',
                'order' => 3,
                'items' => [
                    ['title' => 'Dasbor', 'url' => 'route::editor.dashboard', 'route_name' => 'editor.dashboard', 'permission' => 'decision.view', 'icon' => 'bi-grid-1x2', 'order' => 1],
                    ['title' => 'Naskah', 'url' => 'route::editor.articles.index', 'route_name' => 'editor.articles', 'permission' => 'submission.view', 'icon' => 'bi-file-earmark-text', 'order' => 2],
                ],
            ],
            [
                'title' => 'Peninjau',
                'order' => 4,
                'items' => [
                    ['title' => 'Dasbor', 'url' => 'route::reviewer.dashboard', 'route_name' => 'reviewer.dashboard', 'permission' => 'review.view', 'icon' => 'bi-grid-1x2', 'order' => 1],
                    ['title' => 'Antrean Tinjauan', 'url' => 'route::reviewer.reviews.index', 'route_name' => 'reviewer.reviews', 'permission' => 'review.view', 'icon' => 'bi-clipboard-check', 'order' => 2],
                ],
            ],
            [
                'title' => 'Penulis',
                'order' => 5,
                'items' => [
                    ['title' => 'Dasbor', 'url' => 'route::author.dashboard', 'route_name' => 'author.dashboard', 'permission' => 'submission.view', 'icon' => 'bi-grid-1x2', 'order' => 1],
                    ['title' => 'Kiriman Saya', 'url' => 'route::author.articles.index', 'route_name' => 'author.articles', 'permission' => 'submission.view', 'icon' => 'bi-file-earmark-text', 'order' => 2],
                    ['title' => 'Kirim Naskah', 'url' => 'route::author.articles.create', 'route_name' => 'author.articles.create', 'permission' => 'submission.create', 'icon' => 'bi-plus-circle', 'order' => 3],
                ],
            ],
        ];

        foreach ($categories as $category) {
            $parent = Navigation::create([
                'title' => $category['title'],
                'url' => null,
                'order' => $category['order'],
                'location' => 'sidebar',
            ]);

            foreach ($category['items'] as $item) {
                $parent->children()->create([
                    'title' => $item['title'],
                    'url' => $item['url'],
                    'route_name' => $item['route_name'],
                    'permission' => $item['permission'],
                    'icon' => $item['icon'],
                    'order' => $item['order'],
                    'location' => 'sidebar',
                ]);
            }
        }
    }
}
