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

        // Ensure upload directory exists
        if (!file_exists(public_path('upload'))) {
            mkdir(public_path('upload'), 0777, true);
        }

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
                'cover_image'  => $this->generateJournalCover('Jurnal Ilmu Komputer dan Teknologi Informasi', 'JIKTI', 'journal_jikti.webp'),
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
                'cover_image'  => $this->generateJournalCover('Jurnal Teknik dan Rekayasa', 'JTR', 'journal_jtr.webp'),
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
                'cover_image'  => $this->generateJournalCover('Jurnal Ekonomi dan Bisnis', 'JEB', 'journal_jeb.webp'),
            ],
        ];

        foreach ($journals as $journal) {
            Journal::updateOrCreate(['slug' => $journal['slug']], $journal);
        }

        $this->command->info('Journals seeded with WebP cover images!');
    }

    private function generateJournalCover(string $title, string $abbreviation, string $filename): string
    {
        $width = 400;
        $height = 500;
        $image = imagecreatetruecolor($width, $height);

        // Define a beautiful color palette based on abbreviation
        $hash = md5($abbreviation);
        $r = hexdec(substr($hash, 0, 2)) % 80 + 30; // dark rich colors
        $g = hexdec(substr($hash, 2, 2)) % 80 + 30;
        $b = hexdec(substr($hash, 4, 2)) % 80 + 30;

        $bgColor = imagecolorallocate($image, $r, $g, $b);
        imagefill($image, 0, 0, $bgColor);

        // Draw some decorative elements
        $borderColor = imagecolorallocate($image, min($r + 30, 255), min($g + 30, 255), min($b + 30, 255));
        imagerectangle($image, 20, 20, $width - 20, $height - 20, $borderColor);

        // Highlight accent bar
        $accentColor = imagecolorallocate($image, min($r + 60, 255), min($g + 60, 255), min($b + 120, 255));
        imagefilledrectangle($image, 40, 40, $width - 40, 48, $accentColor);

        // Text color
        $textColor = imagecolorallocate($image, 255, 255, 255);
        $subTextColor = imagecolorallocate($image, 220, 220, 220);

        // Use built-in GD font
        $font = 5;
        
        // Draw abbreviation
        $textWidth = imagefontwidth($font) * strlen($abbreviation);
        $x = ($width - $textWidth) / 2;
        imagestring($image, $font, $x, 150, $abbreviation, $textColor);

        // Draw "JOURNAL"
        $jText = "JOURNAL";
        $jTextWidth = imagefontwidth($font) * strlen($jText);
        $jx = ($width - $jTextWidth) / 2;
        imagestring($image, $font, $jx, 120, $jText, $subTextColor);

        // Draw spine/ribbon on the left
        $ribbonColor = imagecolorallocate($image, max($r - 15, 0), max($g - 15, 0), max($b - 15, 0));
        imagefilledrectangle($image, 20, 20, 40, $height - 20, $ribbonColor);

        $dir = public_path('upload');
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $filePath = $dir . '/' . $filename;
        imagewebp($image, $filePath, 80);
        imagedestroy($image);

        return 'upload/' . $filename;
    }
}
