<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Urutan seeder penting karena ada foreign key dependency
     */
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,   // Settings dulu
            UserSeeder::class,      // Users dengan semua role
            JournalSeeder::class,   // Journals
            IssueSeeder::class,     // Issues per journal
            ArticleSeeder::class,   // Artikel dummy
        ]);
    }
}
