<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiIntegration;
use App\Models\Setting;
use App\Providers\MailConfigServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        // Branding / General Settings
        Setting::firstOrCreate(
            ['key' => 'site_logo'],
            [
                'group' => 'general',
                'label' => 'Site Logo',
                'type' => 'image',
                'description' => 'Upload logo website (resolusi yang disarankan: 200x60px).',
                'value' => ''
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'site_favicon'],
            [
                'group' => 'general',
                'label' => 'Site Favicon',
                'type' => 'image',
                'description' => 'Upload favicon (resolusi yang disarankan: 32x32px atau 64x64px, .png/.ico).',
                'value' => ''
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'seo_meta_description'],
            [
                'group' => 'general',
                'label' => 'Global Meta Description',
                'type' => 'textarea',
                'description' => 'Deskripsi global website untuk keperluan SEO (akan digunakan jika halaman tidak memiliki deskripsi spesifik).',
                'value' => 'An internationally recognized open-access scholarly publishing platform.'
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'seo_meta_keywords'],
            [
                'group' => 'general',
                'label' => 'Global Meta Keywords',
                'type' => 'text',
                'description' => 'Kata kunci (keywords) global dipisahkan dengan koma (contoh: journal, research, science).',
                'value' => 'journal, publication, research, open access'
            ]
        );

        // ===== PENGATURAN EMAIL / SMTP SERVER =====
        Setting::firstOrCreate(
            ['key' => 'mail_enabled'],
            [
                'group'       => 'email',
                'label'       => 'Aktifkan Pengiriman Email SMTP',
                'type'        => 'boolean',
                'description' => 'Aktifkan pengiriman email notifikasi otomatis melalui server SMTP.',
                'value'       => '1',
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'mail_host'],
            [
                'group'       => 'email',
                'label'       => 'SMTP Host',
                'type'        => 'text',
                'description' => 'Alamat server SMTP (contoh: smtp.gmail.com, mail.namadomain.com, smtp.mailtrap.io).',
                'value'       => 'smtp.gmail.com',
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'mail_port'],
            [
                'group'       => 'email',
                'label'       => 'SMTP Port',
                'type'        => 'number',
                'description' => 'Port koneksi SMTP (587 untuk TLS/STARTTLS, 465 untuk SSL, atau 25).',
                'value'       => '587',
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'mail_encryption'],
            [
                'group'       => 'email',
                'label'       => 'SMTP Enkripsi',
                'type'        => 'select',
                'description' => 'Protokol enkripsi koneksi: tls, ssl, atau none.',
                'value'       => 'tls',
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'mail_username'],
            [
                'group'       => 'email',
                'label'       => 'SMTP Username',
                'type'        => 'text',
                'description' => 'Username atau alamat email akun SMTP untuk otentikasi.',
                'value'       => '',
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'mail_password'],
            [
                'group'       => 'email',
                'label'       => 'SMTP Password / App Password',
                'type'        => 'password',
                'description' => 'Password akun SMTP atau Google App Password (disarankan jika menggunakan 2FA).',
                'value'       => '',
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'mail_from_address'],
            [
                'group'       => 'email',
                'label'       => 'Alamat Email Pengirim (From Address)',
                'type'        => 'text',
                'description' => 'Alamat email pengirim resmi jurnal yang tertera pada kotak masuk penerima.',
                'value'       => 'noreply@jpstem.indonesiamadani.com',
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'mail_from_name'],
            [
                'group'       => 'email',
                'label'       => 'Nama Pengirim (From Name)',
                'type'        => 'text',
                'description' => 'Nama instansi atau redaksi yang tampil sebagai pengirim email.',
                'value'       => 'JPSTEM Editorial Office',
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'email_submission_notification'],
            [
                'group'       => 'email',
                'label'       => 'Notifikasi Email Tahap Submission',
                'type'        => 'boolean',
                'description' => 'Kirim email tanda terima naskah ke author dan pemberitahuan naskah baru ke editor.',
                'value'       => '1',
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'email_review_notification'],
            [
                'group'       => 'email',
                'label'       => 'Notifikasi Email Tahap Review & Keputusan',
                'type'        => 'boolean',
                'description' => 'Kirim email penugasan reviewer, hasil telaah, dan keputusan revisi/diterima/ditolak ke author.',
                'value'       => '1',
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'email_publish_notification'],
            [
                'group'       => 'email',
                'label'       => 'Notifikasi Email Tahap Publikasi',
                'type'        => 'boolean',
                'description' => 'Kirim email ucapan selamat dan tautan artikel resmi saat naskah diterbitkan.',
                'value'       => '1',
            ]
        );

        // Pastikan setting legal ada untuk Privacy Policy & Terms Conditions
        Setting::firstOrCreate(
            ['key' => 'privacy_policy'],
            [
                'group' => 'legal',
                'label' => 'Privacy Policy Content',
                'type' => 'longtext',
                'description' => 'Isi Privacy Policy (mendukung tag HTML).',
                'value' => '<h5>1. Introduction</h5><p>The names and email addresses entered in this journal site will be used exclusively for the stated purposes of this journal and will not be made available for any other purpose or to any other party.</p>'
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'terms_conditions'],
            [
                'group' => 'legal',
                'label' => 'Terms & Conditions Content',
                'type' => 'longtext',
                'description' => 'Isi Terms & Conditions (mendukung tag HTML).',
                'value' => '<h5>1. Acceptance of Terms</h5><p>By registering, accessing, or using this scholarly publishing platform, you agree to comply with and be bound by these Terms and Conditions.</p>'
            ]
        );

        $settings = Setting::all();
        $groups = $settings->groupBy('group');

        return view('admin.settings.index', compact('groups'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'settings'          => ['required', 'array'],
            'settings.*.key'    => ['required', 'string'],
            'settings.*.value'  => ['nullable'],
        ]);

        // Process text, number, select, and boolean settings
        if ($request->has('settings')) {
            foreach ($request->settings as $item) {
                // Jangan kosongkan password jika input password dibiarkan kosong
                if (str_contains($item['key'], 'password') && empty($item['value'])) {
                    $existing = Setting::where('key', $item['key'])->first();
                    if ($existing && !empty($existing->value)) {
                        continue;
                    }
                }

                Setting::set($item['key'], $item['value'] ?? '');
            }
        }

        // Process file uploads for image settings
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $key => $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/settings'), $filename);
                Setting::set($key, 'uploads/settings/' . $filename);
            }
        }

        // Refresh configuration immediately in memory
        MailConfigServiceProvider::applyConfig();

        // Sync with api_integrations if provider smtp exists
        $this->syncSmtpToApiIntegration();

        return back()->with('success', 'Pengaturan berhasil disimpan dan konfigurasi email telah diperbarui!');
    }

    /**
     * Kirim email uji coba untuk memverifikasi pengaturan SMTP
     */
    public function testEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        try {
            // Apply current database settings to mail configuration
            MailConfigServiceProvider::applyConfig();

            $toEmail = $request->test_email;
            $fromAddress = Setting::get('mail_from_address', config('mail.from.address'));
            $fromName    = Setting::get('mail_from_name', config('mail.from.name', 'OJS System'));
            $smtpHost    = Setting::get('mail_host', config('mail.mailers.smtp.host'));
            $smtpPort    = Setting::get('mail_port', config('mail.mailers.smtp.port'));

            Mail::raw(
                "Halo,\n\nEmail ini dikirimkan sebagai UJI COBA (Test Connection) konfigurasi SMTP dari Menu Pengaturan Sistem OJS.\n\n"
                . "Detail Konfigurasi SMTP:\n"
                . "- Server Host: {$smtpHost}:{$smtpPort}\n"
                . "- Pengirim: {$fromName} <{$fromAddress}>\n"
                . "- Waktu Uji Coba: " . now()->translatedFormat('d F Y H:i:s') . "\n\n"
                . "Jika Anda menerima pesan ini, konfigurasi SMTP Anda di menu Setting telah AKTIF dan siap mengirimkan notifikasi pada seluruh tahapan publikasi artikel (submit, review, hingga publish) secara otomatis.\n\n"
                . "Salam,\nTim Administrator OJS System",
                function ($message) use ($toEmail, $fromAddress, $fromName) {
                    if ($fromAddress) {
                        $message->from($fromAddress, $fromName);
                    }
                    $message->to($toEmail)->subject('[TEST SMTP] Uji Coba Pengiriman Email OJS Berhasil');
                }
            );

            return back()->with('success', "Email uji coba berhasil dikirim ke {$toEmail}! Silakan periksa inbox atau spam.");
        } catch (\Throwable $e) {
            return back()->with('error', "Gagal mengirim email uji coba: " . $e->getMessage());
        }
    }

    public function clearCache(Request $request): RedirectResponse
    {
        $action = $request->input('action', 'optimize_clear');

        try {
            if ($action === 'optimize') {
                Artisan::call('optimize');
                $output = Artisan::output();
                return back()->with('success', 'Sistem berhasil dioptimalkan (config, route, and views cached). ' . trim($output));
            }

            Artisan::call('optimize:clear');
            $output = Artisan::output();
            return back()->with('success', 'Seluruh cache sistem berhasil dibersihkan. ' . trim($output));
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memproses cache: ' . $e->getMessage());
        }
    }

    /**
     * Sinkronisasikan pengaturan SMTP ke tabel api_integrations agar konsisten
     */
    private function syncSmtpToApiIntegration(): void
    {
        try {
            if (Schema::hasTable('api_integrations')) {
                $mappings = [
                    'host'         => Setting::get('mail_host'),
                    'port'         => Setting::get('mail_port'),
                    'username'     => Setting::get('mail_username'),
                    'password'     => Setting::get('mail_password'),
                    'encryption'   => Setting::get('mail_encryption'),
                    'from_address' => Setting::get('mail_from_address'),
                    'from_name'    => Setting::get('mail_from_name'),
                ];

                foreach ($mappings as $key => $val) {
                    if ($val !== null && $val !== '') {
                        ApiIntegration::where('provider', 'smtp')->where('key', $key)->update(['value' => $val]);
                    }
                }

                $enabled = Setting::get('mail_enabled', true);
                ApiIntegration::where('provider', 'smtp')->update([
                    'status' => $enabled ? 'active' : 'inactive'
                ]);
            }
        } catch (\Throwable $e) {
            // Ignore sync errors
        }
    }
}
