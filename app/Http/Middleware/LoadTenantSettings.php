<?php

namespace App\Http\Middleware;

use App\Models\POS\POSTenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class LoadTenantSettings
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $tenantId = session('tenant_id');

        $tenantSettings = null;

        if ($tenantId) {

            $tenantSettings = Cache::remember(
                'tenant_settings_' . $tenantId,
                now()->addHours(1),
                fn () => POSTenant::query()
                    ->with('subscription')
                    ->find($tenantId)
            );
        }

        $bgColor = '#ffffff';

        $isLight = $this->isLightColor(
            $bgColor
        );

        View::share([

            'tenantSettings' => $tenantSettings,

            'themeVars' => [

                'bg' => $bgColor,

                'text' => '#1f2937',

                'hover' => '#f1f5f9',

                'active' => '#ecfdf5',

                'subtext' => '#64748b',

                'border' => '#e2e8f0',

            ]

        ]);

        return $next($request);
    }

    private function isLightColor(
        string $hex
    ): bool {

        $hex = ltrim($hex, '#');

        if (strlen($hex) !== 6) {
            return false;
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $brightness =
            (($r * 299) +
                ($g * 587) +
                ($b * 114)) / 1000;

        return $brightness > 155;
    }
}
