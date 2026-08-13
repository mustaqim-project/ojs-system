<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleanDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('article_galleys')->truncate();
        DB::table('article_dois')->truncate();
        DB::table('review_responses')->truncate();
        DB::table('review_assignments')->truncate();
        DB::table('review_rounds')->truncate();
        DB::table('editorial_decisions')->truncate();
        DB::table('production_tasks')->truncate();
        DB::table('submission_versions')->truncate();
        DB::table('submission_files')->truncate();
        DB::table('receipts')->truncate();
        DB::table('refunds')->truncate();
        DB::table('payments')->truncate();
        DB::table('invoices')->truncate();
        DB::table('apc_fees')->truncate();
        DB::table('reviews')->truncate();
        DB::table('issue_article')->truncate();
        DB::table('articles')->truncate();
        DB::table('issues')->truncate();
        DB::table('volumes')->truncate();
        DB::table('journals')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Journals, issues, volumes, dan articles berhasil dihapus.');
    }
}
