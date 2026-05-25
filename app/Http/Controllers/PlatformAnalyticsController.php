<?php

namespace App\Http\Controllers;

use App\Models\LoginActivity;
use App\Models\School;
use App\Models\SuspiciousActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PlatformAnalyticsController extends Controller
{
    public function index()
    {
        return view('pages.sa.platform-analytics.index');
    }

    public function overviewData()
    {
        return Cache::remember(
            'platform.analytics.overview',
            now()->addMinutes(5),
            function () {

                $totalUsers = User::count();

                $activeUsers = User::where('last_activity_at', '>=', now()->subMinutes(10))
                    ->count();

                $totalSchools = School::count();

                $activeSessions = DB::table('sessions')
                    ->count();

                $suspiciousActivities = SuspiciousActivity::whereDate(
                    'created_at',
                    today()
                )->count();

                $todayLogins = LoginActivity::whereDate(
                    'created_at',
                    today()
                )->count();

                return response()->json([
                    'total_users' => number_format($totalUsers),
                    'active_users' => number_format($activeUsers),
                    'total_schools' => number_format($totalSchools),
                    'active_sessions' => number_format($activeSessions),
                    'suspicious_activities' => number_format($suspiciousActivities),
                    'today_logins' => number_format($todayLogins),
                ]);
            }
        );
    }

    public function loginTrends()
    {
        $data = Cache::remember(
            'platform.analytics.login-trends',
            now()->addMinutes(5),
            function () {

                return LoginActivity::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as total')
                )
                    ->where('created_at', '>=', now()->subDays(14))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
            }
        );

        return response()->json($data);
    }

    public function securityTrends()
    {
        $data = Cache::remember(
            'platform.analytics.security-trends',
            now()->addMinutes(5),
            function () {

                return SuspiciousActivity::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as total')
                )
                    ->where('created_at', '>=', now()->subDays(14))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
            }
        );

        return response()->json($data);
    }

    public function deviceAnalytics()
    {
        $data = Cache::remember(
            'platform.analytics.device-analytics',
            now()->addMinutes(10),
            function () {

                return LoginActivity::select(
                    'browser',
                    DB::raw('COUNT(*) as total')
                )
                    ->whereNotNull('browser')
                    ->groupBy('browser')
                    ->orderByDesc('total')
                    ->limit(8)
                    ->get();
            }
        );

        return response()->json($data);
    }

    public function schoolAnalytics()
    {
        $data = Cache::remember(
            'platform.analytics.school-analytics',
            now()->addMinutes(10),
            function () {

                return School::select(
                    'school.SchoolName',
                    DB::raw('COUNT(users.id) as users_count')
                )
                    ->leftJoin('users', 'users.school_id', '=', 'school.id')
                    ->groupBy('school.id', 'school.SchoolName')
                    ->orderByDesc('users_count')
                    ->limit(10)
                    ->get();
            }
        );

        return response()->json($data);
    }
}
