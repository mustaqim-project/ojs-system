<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ============================================================
        // GLOBAL AUTHORIZATION BYPASS (backward-compat safety net)
        // ------------------------------------------------------------
        // Pengguna dengan role Spatie 'super-admin' / 'system-admin' ATAU
        // kolom legacy `users.role = 'admin'` diizinkan melakukan semua
        // aksi (Gate::check / can()). Permission middleware tetap memfilter
        // per-resource untuk role lain (editor/reviewer/author/finance, dsb).
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            // Guest / user non-Eloquent → biarkan pipeline default menanganinya.
            if (! $user || ! method_exists($user, 'hasRole')) {
                return null;
            }

            if ($user->hasRole(['super-admin', 'system-admin', 'admin']) || ($user->role ?? null) === 'admin') {
                return true;
            }

            return null; // Lanjut ke Gate::before milik Spatie / policy
        });

        // Register Socialite Providers listener
        \Illuminate\Support\Facades\Event::listen(
            \SocialiteProviders\Manager\SocialiteWasCalled::class,
            \SocialiteProviders\Orcid\OrcidExtendSocialite::class
        );

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $view->with('global_navigations', \App\Models\Navigation::whereNull('parent_id')->where('is_active', true)->where('location', 'header')->orderBy('order')->with(['children' => function($q) {
                $q->where('is_active', true)->orderBy('order');
            }])->get());
            
            // Setting values helper map
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $settings = \Illuminate\Support\Facades\Cache::remember('global_settings', 60, function() {
                    return \App\Models\Setting::pluck('value', 'key')->toArray();
                });
                $view->with('global_settings', $settings);
            }
        });
    }
}
