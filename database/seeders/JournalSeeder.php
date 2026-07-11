<?php

namespace Database\Seeders;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Database\Seeder;

class JournalSeeder extends Seeder
{
    public function run(): void
    {
        $editor = User::where('role', 'editor')->first();

        $journals = [
            [
                'title'        => 'Jurnal Ilmu Komputer dan Teknologi Informasi',
                'slug'         => 'jikti',
                'abbreviation' => 'JIKTI',
                'description'  => 'Jurnal yang memuat hasil penelitian di bidang ilmu komputer, rekayasa perangkat lunak, kecerdasan buatan, dan teknologi informasi.',
                'issn_print'   => '2301-1234',
                'issn_online'  => '2301-5678',
                'publisher'    => 'Fakultas Ilmu Komputer',
                'subject_area' => 'Computer Science, Information Technology',
                'frequency'    => 'quarterly',
                'is_active'    => true,
                'editor_id'    => $editor?->id,
            ],
            [
                'title'        => 'Jurnal Teknik dan Rekayasa',
                'slug'         => 'jtr',
                'abbreviation' => 'JTR',
                'description'  => 'Jurnal ilmiah yang memuat hasil penelitian di bidang teknik sipil, mesin, elektro, dan rekayasa lainnya.',
                'issn_print'   => '2302-1111',
                'issn_online'  => '2302-2222',
                'publisher'    => 'Fakultas Teknik',
                'subject_area' => 'Engineering',
                'frequency'    => 'bimonthly',
                'is_active'    => true,
                'editor_id'    => $editor?->id,
            ],
            [
                'title'        => 'Jurnal Ekonomi dan Bisnis',
                'slug'         => 'jeb',
                'abbreviation' => 'JEB',
                'description'  => 'Jurnal ilmiah di bidang ekonomi, manajemen, akuntansi dan bisnis.',
                'issn_print'   => '2303-3333',
                'issn_online'  => '2303-4444',
                'publisher'    => 'Fakultas Ekonomi dan Bisnis',
                'subject_area' => 'Economics, Business',
                'frequency'    => 'quarterly',
                'is_active'    => true,
                'editor_id'    => $editor?->id,
            ],
        ];

        foreach ($journals as $journal) {
            Journal::updateOrCreate(['slug' => $journal['slug']], $journal);
        }

        $this->command->info('Journals seeded!');
    }
}
