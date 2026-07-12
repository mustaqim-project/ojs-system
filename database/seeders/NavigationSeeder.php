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

        // 1. Home
        Navigation::create([
            'title' => 'Home',
            'url' => '/#home', // or named route format if we implement logic for that later, for now we will store exact paths
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
}
