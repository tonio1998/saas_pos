<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UpdateLastActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!auth()->check()) {
            return $response;
        }

        $userId = auth()->id();

        $cacheKey = 'last_activity_'.$userId;

        if (!Cache::has($cacheKey)) {

            Cache::put(
                $cacheKey,
                true,
                now()->addMinutes(5)
            );

            try {

                DB::table('users')
                    ->where('id', $userId)
                    ->update([
                        'last_activity_at' => now(),
                    ]);

            } catch (\Throwable $e) {

                logger()->error('LAST ACTIVITY UPDATE FAILED', [
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return $response;
    }
}
