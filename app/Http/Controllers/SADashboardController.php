<?php

namespace App\Http\Controllers;

use App\Models\POS\POSCustomers;
use App\Models\POS\POSProducts;
use App\Models\POS\POSSale;
use App\Models\POS\POSSubscription;
use App\Models\POS\POSTenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OwenIt\Auditing\Models\Audit;

class SADashboardController extends Controller
{
    public function index()
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin), 403);

        $today = now()->toDateString();

        $metrics = [
            'totalTenants'              => POSTenant::count(),
            'activeTenants'             => POSTenant::where('status', 'active')->count(),
            'paidSubscriptions'         => POSTenant::where('payment_status', 'paid')->count(),
            'pendingVerificationsCount' => POSTenant::where('payment_status', 'pending_verification')->count(),
            'trialTenants'              => POSTenant::where('payment_status', 'trial')->count(),
            'expiredTenants'            => POSTenant::whereNotNull('subscription_end')->where('subscription_end', '<', $today)->count(),
            'totalProducts'             => POSProducts::count(),
            'totalCustomers'            => POSCustomers::count(),
            'totalUsers'                => User::count(),
            'totalSalesVolume'          => (float) POSSale::sum('total_amount'),
            'todaySalesVolume'          => (float) POSSale::whereDate('created_at', $today)->sum('total_amount'),
            'dueSoonCount'              => POSTenant::whereNotNull('subscription_end')
                ->where('subscription_end', '>=', $today)
                ->where('subscription_end', '<=', now()->addDays(7)->endOfDay())
                ->count(),
            'due3DaysCount'             => POSTenant::whereNotNull('subscription_end')
                ->where('subscription_end', '>=', $today)
                ->where('subscription_end', '<=', now()->addDays(3)->endOfDay())
                ->count(),
        ];

        // Queue of tenants due soon (within 7 days or overdue)
        $tenantsDueSoon = POSTenant::with(['subscription'])
            ->whereNotNull('subscription_end')
            ->where('subscription_end', '<=', now()->addDays(7)->endOfDay())
            ->orderBy('subscription_end', 'asc')
            ->take(6)
            ->get();

        // Queue of subscriptions awaiting verification
        $pendingVerifications = POSTenant::with(['subscription', 'pendingPlan'])
            ->where('payment_status', 'pending_verification')
            ->orderByDesc('payment_submitted_at')
            ->take(10)
            ->get();

        // Recent tenants for CRM monitoring
        $recentTenants = POSTenant::with(['subscription', 'branches'])
            ->latest()
            ->take(8)
            ->get();

        // Plan distribution
        $plans = POSSubscription::where('status', 'active')->orderBy('sort_order')->get();
        $planBreakdown = [];
        foreach ($plans as $plan) {
            $planBreakdown[] = [
                'name'  => $plan->name,
                'price' => $plan->price,
                'count' => POSTenant::where('subscription_id', $plan->id)->count(),
            ];
        }

        // Recent platform audit logs
        $recentActivities = Audit::query()
            ->with('user')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($audit) {
                return [
                    'name'        => optional($audit->user)->name ?? 'System',
                    'description' => $this->formatAuditMessage($audit),
                    'time'        => $audit->created_at ? $audit->created_at->diffForHumans() : 'Recently',
                    'event'       => $audit->event,
                    'ip'          => $audit->ip_address,
                ];
            });

        return view('pages.sa.dashboard.index', compact(
            'metrics',
            'pendingVerifications',
            'recentTenants',
            'planBreakdown',
            'recentActivities',
            'tenantsDueSoon'
        ));
    }

    public function data()
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin), 403);

        $today = now()->toDateString();

        return response()->json([
            'totalTenants'              => POSTenant::count(),
            'activeTenants'             => POSTenant::where('status', 'active')->count(),
            'paidSubscriptions'         => POSTenant::where('payment_status', 'paid')->count(),
            'pendingVerificationsCount' => POSTenant::where('payment_status', 'pending_verification')->count(),
            'trialTenants'              => POSTenant::where('payment_status', 'trial')->count(),
            'totalSalesVolume'          => number_format((float) POSSale::sum('total_amount'), 2),
            'todaySalesVolume'          => number_format((float) POSSale::whereDate('created_at', $today)->sum('total_amount'), 2),
            'totalProducts'             => POSProducts::count(),
            'totalCustomers'            => POSCustomers::count(),
            'totalUsers'                => User::count(),
            'onlineUsers'               => User::whereNotNull('last_activity_at')->where('last_activity_at', '>=', now()->subMinutes(15))->count(),
        ]);
    }

    protected function formatAuditMessage($audit)
    {
        $user = optional($audit->user)->name ?? 'System';

        $model = Str::of(class_basename($audit->auditable_type ?? 'Record'))
            ->snake()
            ->replace('_', ' ')
            ->singular()
            ->lower();

        $article = in_array(substr($model, 0, 1), ['a', 'e', 'i', 'o', 'u']) ? 'an' : 'a';

        return match ($audit->event) {
            'created'  => "{$user} created {$article} {$model}",
            'updated'  => "{$user} updated {$article} {$model}",
            'deleted'  => "{$user} deleted {$article} {$model}",
            'restored' => "{$user} restored {$article} {$model}",
            default    => "{$user} performed an action on {$article} {$model}",
        };
    }
}
