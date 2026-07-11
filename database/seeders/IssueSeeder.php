<?php

namespace Database\Seeders;

use App\Models\Issue;
use App\Models\Journal;
use Illuminate\Database\Seeder;

class IssueSeeder extends Seeder
{
    public function run(): void
    {
        $journals = Journal::all();

        foreach ($journals as $journal) {
            // Buat 2 issue per jurnal
            Issue::updateOrCreate(
                ['journal_id' => $journal->id, 'volume' => 1, 'number' => 1],
                [
                    'journal_id'     => $journal->id,
                    'title'          => "Volume 1, Nomor 1, Januari-Maret 2024",
                    'volume'         => 1,
                    'number'         => 1,
                    'year'           => 2024,
                    'description'    => 'Edisi perdana jurnal.',
                    'published_date' => '2024-03-31',
                    'status'         => 'published',
                ]
            );

            Issue::updateOrCreate(
                ['journal_id' => $journal->id, 'volume' => 1, 'number' => 2],
                [
                    'journal_id'     => $journal->id,
                    'title'          => "Volume 1, Nomor 2, April-Juni 2024",
                    'volume'         => 1,
                    'number'         => 2,
                    'year'           => 2024,
                    'description'    => 'Edisi kedua.',
                    'published_date' => '2024-06-30',
                    'status'         => 'published',
                ]
            );

            Issue::updateOrCreate(
                ['journal_id' => $journal->id, 'volume' => 2, 'number' => 1],
                [
                    'journal_id'     => $journal->id,
                    'title'          => "Volume 2, Nomor 1, Januari-Maret 2025",
                    'volume'         => 2,
                    'number'         => 1,
                    'year'           => 2025,
                    'description'    => 'Edisi terbaru.',
                    'published_date' => null,
                    'status'         => 'draft',
                ]
            );
        }

        $this->command->info('Issues seeded!');
    }
}
