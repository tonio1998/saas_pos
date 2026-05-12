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

        $schoolYears = Cache::rememberForever(
            'school_years',
            function () {

                return SchoolYear::query()
                    ->orderByDesc('AYFrom')
                    ->get([
                        'id',
                        'AYFrom',
                        'AYTo'
                    ]);

            }
        );

        $semesters = Cache::rememberForever(
            'semesters',
            function () {

                return Semesters::query()
                    ->where('IsActive',1)
                    ->orderBy('SemesterOrder')
                    ->get([
                        'id',
                        'SemesterName',
                        'SemesterOrder'
                    ]);
            }
        );

        View::share(
            'schoolSettings',
            $schoolSettings
        );

        View::share(
            'schoolYears',
            $schoolYears
        );

        View::share(
            'semesters',
            $semesters
        );
    }
}
