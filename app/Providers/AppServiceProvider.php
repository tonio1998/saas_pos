<?php

namespace App\Providers;

use App\Models\School;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        Gate::before(function ($user, $ability) {

            if ($user->hasRole('SA')) {
                return true;
            }

            return null;
        });

        View::composer('*', function () {

            $schoolId = session('school_id');

            $schoolSettings = null;

            if ($schoolId) {

                $cacheKey = 'school_settings_' . $schoolId;

                $schoolSettings = Cache::rememberForever(
                    $cacheKey,
                    function () use ($schoolId) {

                        $school = School::query()
                            ->with([
                                'principal',
                                'registrar'
                            ])
                            ->find($schoolId);

                        return $school
                            ? (object) $school->toArray()
                            : null;
                    }
                );
            }

            $bgColor = $schoolSettings?->ThemeColor
                ?? '#00674F';

            $isLight = $this->isLightColor($bgColor);

            View::share([

                'schoolSettings' => $schoolSettings,

                'themeVars' => [

                    'bg' => $bgColor,

                    'text' => $isLight
                        ? '#1f2937'
                        : '#ffffff',

                    'hover' => $isLight
                        ? 'rgba(0,0,0,0.06)'
                        : 'rgba(255,255,255,0.10)',

                    'active' => $isLight
                        ? 'rgba(0,0,0,0.10)'
                        : 'rgba(255,255,255,0.16)',

                    'subtext' => $isLight
                        ? '#4b5563'
                        : 'rgba(255,255,255,0.78)',

                    'border' => $isLight
                        ? 'rgba(0,0,0,0.06)'
                        : 'rgba(255,255,255,0.08)',
                ]
            ]);
        });
    }

    private function isLightColor(string $hex): bool
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) !== 6) {
            return false;
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return $brightness > 155;
    }
}
