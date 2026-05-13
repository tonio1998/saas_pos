<?php

namespace App\Providers;

use App\Models\SchoolYear;
use App\Models\Semesters;
use App\Models\Settings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
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

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $schoolSettings = Cache::rememberForever(
            'school_settings',
            function () {

                return Settings::query()

                    ->with([
                        'principal',
                        'registrar'
                    ])

                    ->first();

            }
        );

        View::share(
            'schoolSettings',
            $schoolSettings
        );
    }
}
