<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Seed default settings untuk sistem OJS
     */
    public function run(): void
    {
        $settings = [
            // ===== GENERAL SETTINGS =====
            [
                'key'         => 'site_name',
                'value'       => 'Portal Jurnal Ilmiah',
                'group'       => 'general',
                'label'       => 'Nama Website',
                'type'        => 'text',
                'description' => 'Nama website yang ditampilkan di header',
            ],
            [
                'key'         => 'site_description',
                'value'       => 'Platform publikasi jurnal ilmiah bereputasi',
                'group'       => 'general',
                'label'       => 'Deskripsi Website',
                'type'        => 'textarea',
                'description' => 'Deskripsi singkat website',
            ],
            [
                'key'         => 'site_email',
                'value'       => 'editor@jurnalilmiah.id',
                'group'       => 'general',
                'label'       => 'Email Website',
                'type'        => 'text',
                'description' => 'Email kontak utama',
            ],
            [
                'key'         => 'contact_phone',
                'value'       => '+62-21-12345678',
                'group'       => 'general',
                'label'       => 'Telepon Kontak',
                'type'        => 'text',
                'description' => '',
            ],
            [
                'key'         => 'contact_address',
                'value'       => 'Jl. Ilmu Pengetahuan No. 1, Jakarta 12345',
                'group'       => 'general',
                'label'       => 'Alamat',
                'type'        => 'textarea',
                'description' => '',
            ],

            // ===== PUBLIC SITE SETTINGS =====
            [
                'key'         => 'hero_title',
                'value'       => 'Advance Your Research with Open Access Publishing',
                'group'       => 'general',
                'label'       => 'Hero Title',
                'type'        => 'text',
                'description' => 'Teks utama pada hero banner',
            ],
            [
                'key'         => 'hero_subtitle',
                'value'       => 'Join our global community of researchers and publish your work in our peer-reviewed, high-impact journals.',
                'group'       => 'general',
                'label'       => 'Hero Subtitle',
                'type'        => 'textarea',
                'description' => 'Teks deskripsi di bawah hero title',
            ],
            [
                'key'         => 'hero_button_text',
                'value'       => 'Submit Manuscript',
                'group'       => 'general',
                'label'       => 'Hero Button Text',
                'type'        => 'text',
                'description' => 'Teks pada tombol call to action hero',
            ],
            [
                'key'         => 'hero_button_link',
                'value'       => '/register',
                'group'       => 'general',
                'label'       => 'Hero Button Link',
                'type'        => 'text',
                'description' => 'Link tujuan tombol hero',
            ],
            [
                'key'         => 'footer_text',
                'value'       => '© 2026 OJS System. All rights reserved. Empowering academic research through open access.',
                'group'       => 'general',
                'label'       => 'Footer Text',
                'type'        => 'textarea',
                'description' => 'Teks pada bagian footer',
            ],

            // ===== PAYMENT SETTINGS =====
            [
                'key'         => 'apc_amount',
                'value'       => '500000',
                'group'       => 'payment',
                'label'       => 'Biaya APC (Article Processing Charge)',
                'type'        => 'number',
                'description' => 'Biaya yang harus dibayar author setelah artikel diterima (dalam Rupiah)',
            ],
            [
                'key'         => 'apc_currency',
                'value'       => 'IDR',
                'group'       => 'payment',
                'label'       => 'Mata Uang',
                'type'        => 'text',
                'description' => 'Kode mata uang (IDR, USD, dll)',
            ],
            [
                'key'         => 'bank_name',
                'value'       => 'Bank BCA',
                'group'       => 'payment',
                'label'       => 'Nama Bank',
                'type'        => 'text',
                'description' => 'Bank tujuan transfer',
            ],
            [
                'key'         => 'bank_account',
                'value'       => '1234567890',
                'group'       => 'payment',
                'label'       => 'Nomor Rekening',
                'type'        => 'text',
                'description' => 'Nomor rekening bank',
            ],
            [
                'key'         => 'bank_holder',
                'value'       => 'Yayasan Jurnal Ilmiah Indonesia',
                'group'       => 'payment',
                'label'       => 'Nama Pemilik Rekening',
                'type'        => 'text',
                'description' => 'Nama pemilik rekening bank',
            ],

            // ===== REVIEW SETTINGS =====
            [
                'key'         => 'review_due_days',
                'value'       => '14',
                'group'       => 'review',
                'label'       => 'Batas Waktu Review (hari)',
                'type'        => 'number',
                'description' => 'Jumlah hari untuk reviewer menyelesaikan review',
            ],
            [
                'key'         => 'max_file_size_mb',
                'value'       => '10',
                'group'       => 'review',
                'label'       => 'Ukuran Maksimal File (MB)',
                'type'        => 'number',
                'description' => 'Ukuran maksimal file upload manuskrip',
            ],

            // ===== EMAIL SETTINGS =====
            [
                'key'         => 'email_submission_notification',
                'value'       => '1',
                'group'       => 'email',
                'label'       => 'Notifikasi Email Submit Artikel',
                'type'        => 'boolean',
                'description' => 'Kirim email saat ada artikel baru masuk',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Settings seeded successfully!');
    }
}
