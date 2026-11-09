<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use App\Models\ApiIntegration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        static::applyConfig();
    }

    /**
     * Terapkan konfigurasi SMTP langsung dari menu pengaturan database (bukan .env)
     */
    public static function applyConfig(): void
    {
        try {
            // 1. PRIORITAS UTAMA: Pengaturan dari Menu Setting Admin (tabel `settings`)
            if (Schema::hasTable('settings')) {
                $enabled = Setting::get('mail_enabled', true);
                $host    = Setting::get('mail_host');

                if ($enabled && !empty($host)) {
                    $port        = (int) Setting::get('mail_port', 587);
                    $username    = Setting::get('mail_username');
                    $password    = Setting::get('mail_password');
                    $encryption  = Setting::get('mail_encryption', 'tls');
                    $fromAddress = Setting::get('mail_from_address', 'noreply@jpstem.indonesiamadani.com');
                    $fromName    = Setting::get('mail_from_name', config('app.name', 'OJS System'));

                    config([
                        'mail.default'                 => 'smtp',
                        'mail.mailers.smtp.transport'  => 'smtp',
                        'mail.mailers.smtp.host'       => $host,
                        'mail.mailers.smtp.port'       => $port,
                        'mail.mailers.smtp.encryption' => ($encryption === 'none' || empty($encryption)) ? null : $encryption,
                        'mail.mailers.smtp.username'   => $username,
                        'mail.mailers.smtp.password'   => $password,
                        'mail.from.address'            => $fromAddress,
                        'mail.from.name'               => $fromName,
                    ]);

                    return;
                }
            }

            // 2. FALLBACK: Cek tabel `api_integrations` (provider 'smtp')
            if (Schema::hasTable('api_integrations')) {
                if (ApiIntegration::isEnabled('smtp')) {
                    $config = ApiIntegration::getProvider('smtp');

                    if (!empty($config['host'])) {
                        $encryption = $config['encryption'] ?? 'tls';
                        config([
                            'mail.default'                 => 'smtp',
                            'mail.mailers.smtp.transport'  => 'smtp',
                            'mail.mailers.smtp.host'       => $config['host'],
                            'mail.mailers.smtp.port'       => $config['port'] ?? 587,
                            'mail.mailers.smtp.encryption' => ($encryption === 'none' || empty($encryption)) ? null : $encryption,
                            'mail.mailers.smtp.username'   => $config['username'] ?? null,
                            'mail.mailers.smtp.password'   => $config['password'] ?? null,
                            'mail.from.address'            => $config['from_address'] ?? null,
                            'mail.from.name'               => $config['from_name'] ?? null,
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Log if there's any issue so it doesn't break artisan commands or web requests
            Log::warning('Could not load SMTP config from database: ' . $e->getMessage());
        }
    }
}
