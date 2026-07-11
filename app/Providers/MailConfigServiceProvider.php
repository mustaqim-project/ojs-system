<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        try {
            // Check if table exists to prevent artisan commands from failing before migration
            if (Schema::hasTable('api_integrations')) {
                // Check if SMTP is enabled
                if (ApiIntegration::isEnabled('smtp')) {
                    $config = ApiIntegration::getProvider('smtp');

                    if (!empty($config['host'])) {
                        config([
                            'mail.mailers.smtp.host'       => $config['host'],
                            'mail.mailers.smtp.port'       => $config['port'],
                            'mail.mailers.smtp.encryption' => $config['encryption'],
                            'mail.mailers.smtp.username'   => $config['username'],
                            'mail.mailers.smtp.password'   => $config['password'],
                            'mail.from.address'            => $config['from_address'],
                            'mail.from.name'               => $config['from_name'],
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            // Log if there's any issue so it doesn't break the application
            Log::warning('Could not load SMTP config from database: ' . $e->getMessage());
        }
    }
}
