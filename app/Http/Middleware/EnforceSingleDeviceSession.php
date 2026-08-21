<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceSingleDeviceSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip enforcement on guest & auth routes
        if ($request->is('/', 'login', 'register', 'logout', 'auth/*')) {
            return $next($request);
        }

        if (Auth::check()) {
            $user = User::find(Auth::id());
            if (!$user) {
                return $next($request);
            }

            $currentSessionId = $request->session()->getId();

            // If session was just authenticated, sync session ID to DB
            if ($request->session()->get('just_authenticated')) {
                $user->update(['current_session_id' => $currentSessionId]);
                $request->session()->forget('just_authenticated');
            } elseif (empty($user->current_session_id)) {
                // Auto-sync empty session ID
                $user->update(['current_session_id' => $currentSessionId]);
            } elseif ($user->current_session_id !== $currentSessionId) {
                // Kick out previous device session
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'login' => 'Your account was logged in from another device. Simultaneous logins on multiple devices are disabled.',
                ]);
            }
        }

        return $next($request);
    }
}
