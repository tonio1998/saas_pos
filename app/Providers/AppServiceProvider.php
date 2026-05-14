<?php

namespace App\Providers;

use App\Models\Settings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

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

        $bgColor = $schoolSettings?->ThemeColor ?? '#ffffff';

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
    }

    private function isLightColor(string $hex): bool
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) === 3) {
            $hex =
                $hex[0] . $hex[0] .
                $hex[1] . $hex[1] .
                $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return $brightness > 155;
    }
}
