<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateLastActivity
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('MIDDLEWARE HIT');

        Log::info('AUTH CHECK', [
            'check' => auth()->check(),
            'id' => auth()->id(),
        ]);

        if (auth()->check()) {

            $updated = DB::table('users')
                ->where('id', auth()->id())
                ->update([
                    'last_activity_at' => now(),
                ]);

            Log::info('UPDATED RESULT', [
                'updated' => $updated,
            ]);
        }

        return $next($request);
    }
}
