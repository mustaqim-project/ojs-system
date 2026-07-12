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
