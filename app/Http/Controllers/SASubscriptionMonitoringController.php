<?php

namespace App\Http\Controllers;

use App\Models\POS\POSCustomers;
use App\Models\POS\POSProducts;
use App\Models\POS\POSSale;
use App\Models\POS\POSTenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SASubscriptionMonitoringController extends Controller
{
    /**
     * Display Subscription Expiration & Due Date Monitoring Console
     */
    public function index()
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin), 403);

        $today = now()->startOfDay();
        $threeDays = now()->addDays(3)->endOfDay();
        $sevenDays = now()->addDays(7)->endOfDay();

        $metrics = [
            'total_tenants'     => POSTenant::count(),
            'due_in_3_days'     => POSTenant::whereNotNull('subscription_end')
                ->where('subscription_end', '>=', $today)
                ->where('subscription_end', '<=', $threeDays)
                ->count(),
            'due_in_7_days'     => POSTenant::whereNotNull('subscription_end')
                ->where('subscription_end', '>=', $today)
                ->where('subscription_end', '<=', $sevenDays)
                ->count(),
            'overdue_expired'   => POSTenant::whereNotNull('subscription_end')
                ->where('subscription_end', '<', $today)
                ->count(),
            'pending_verifs'    => POSTenant::where('payment_status', 'pending_verification')->count(),
            'trial_expiring'    => POSTenant::where('payment_status', 'trial')
                ->where(function ($q) use ($today, $sevenDays) {
                    $q->whereBetween('subscription_end', [$today, $sevenDays])
                      ->orWhereBetween('trial_ends_at', [$today, $sevenDays]);
                })
                ->count(),
            'healthy_active'    => POSTenant::whereNotNull('subscription_end')
                ->where('subscription_end', '>', $sevenDays)
                ->count(),
        ];

        return view('pages.sa.subscriptions.monitoring', compact('metrics'));
    }

    /**
     * AJAX DataTables Provider for Due Date Monitoring
     */
    public function ajaxData(Request $request)
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin), 403);

        $today = now()->startOfDay();
        $query = POSTenant::with(['subscription', 'branches'])->select('pos_tenants.*');

        // Filter based on due urgency
        $filter = $request->input('urgency', 'all');

        if ($filter === 'critical_3_days') {
            $query->whereNotNull('subscription_end')
                ->where('subscription_end', '>=', $today)
                ->where('subscription_end', '<=', now()->addDays(3)->endOfDay());
        } elseif ($filter === 'due_7_days') {
            $query->whereNotNull('subscription_end')
                ->where('subscription_end', '>=', $today)
                ->where('subscription_end', '<=', now()->addDays(7)->endOfDay());
        } elseif ($filter === 'overdue') {
            $query->whereNotNull('subscription_end')
                ->where('subscription_end', '<', $today);
        } elseif ($filter === 'trial') {
            $query->where('payment_status', 'trial');
        } elseif ($filter === 'pending_verif') {
            $query->where('payment_status', 'pending_verification');
        } elseif ($filter === 'healthy') {
            $query->whereNotNull('subscription_end')
                ->where('subscription_end', '>', now()->addDays(7)->endOfDay());
        }

        return DataTables::of($query)
            ->addColumn('store_info', function ($tenant) {
                $code = e($tenant->business_code ?? 'STORE');
                $name = e($tenant->business_name);
                $logo = $tenant->logo ? asset('storage/' . $tenant->logo) : null;

                $logoHtml = $logo
                    ? '<img src="' . $logo . '" class="rounded-3 border" style="width:40px;height:40px;object-fit:contain;">'
                    : '<div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold shadow-xs" style="width:40px;height:40px;background:linear-gradient(135deg, #059669, #047857);font-size:1.1rem;"><i class="bi bi-shop"></i></div>';

                return '
                    <div class="d-flex align-items-center gap-2.5">
                        ' . $logoHtml . '
                        <div class="min-w-0">
                            <div class="fw-bold text-dark text-truncate" style="font-size:0.88rem;">' . $name . '</div>
                            <span class="badge bg-light text-muted border font-mono extra-small" style="font-size:0.7rem;">' . $code . '</span>
                        </div>
                    </div>
                ';
            })
            ->addColumn('owner_info', function ($tenant) {
                $owner = e($tenant->owner_name ?? 'Store Owner');
                $phone = e($tenant->phone ?? 'No phone');
                $email = e($tenant->email ?? '');

                return '
                    <div>
                        <div class="fw-semibold text-dark text-truncate" style="font-size:0.84rem;">' . $owner . '</div>
                        <div class="d-flex align-items-center gap-1.5 mt-0.5">
                            <a href="tel:' . $phone . '" class="badge bg-success-subtle text-success text-decoration-none border border-success-subtle extra-small" title="Call Owner">
                                <i class="bi bi-telephone-fill me-1"></i> ' . $phone . '
                            </a>
                            <a href="sms:' . $phone . '" class="badge bg-primary-subtle text-primary text-decoration-none border border-primary-subtle extra-small" title="Send SMS">
                                <i class="bi bi-chat-text-fill"></i>
                            </a>
                        </div>
                    </div>
                ';
            })
            ->addColumn('plan_info', function ($tenant) {
                $planName = $tenant->subscription?->name ?? 'Free Trial';
                $price = $tenant->subscription?->price ? '₱' . number_format($tenant->subscription->price, 2) . '/mo' : '₱0.00';
                $badgeClass = match ($tenant->payment_status) {
                    'paid' => 'bg-success-subtle text-success border-success-subtle',
                    'trial' => 'bg-info-subtle text-info-emphasis border-info-subtle',
                    'pending_verification' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                    default => 'bg-secondary-subtle text-secondary border',
                };

                return '
                    <div>
                        <div class="fw-bold text-dark font-mono" style="font-size:0.84rem;">' . e($planName) . '</div>
                        <div class="d-flex align-items-center gap-1.5 mt-0.5">
                            <span class="badge ' . $badgeClass . ' border extra-small font-mono">' . ucfirst($tenant->payment_status ?? 'Active') . '</span>
                            <span class="text-muted extra-small font-mono">' . $price . '</span>
                        </div>
                    </div>
                ';
            })
            ->addColumn('due_status', function ($tenant) {
                $status = $tenant->due_status;
                $endFormatted = $tenant->subscription_end ? $tenant->subscription_end->format('M d, Y') : 'No Date';

                return '
                    <div>
                        <span class="badge ' . $status['badge_class'] . ' border px-2.5 py-1 extra-small fw-bold">
                            ' . e($status['label']) . '
                        </span>
                        <div class="text-muted extra-small font-mono mt-1" style="font-size:0.75rem;">
                            Due: <span class="fw-semibold text-dark">' . $endFormatted . '</span>
                        </div>
                    </div>
                ';
            })
            ->addColumn('usage_stats', function ($tenant) {
                $prodCount = $tenant->products()->count();
                $maxProd = $tenant->subscription?->max_products ?? 100;
                $custCount = POSCustomers::where('tenant_id', $tenant->id)->count();

                $prodPct = $maxProd > 0 ? min(100, round(($prodCount / $maxProd) * 100)) : 0;

                return '
                    <div style="min-width: 140px;">
                        <div class="d-flex justify-content-between extra-small font-mono text-muted mb-1">
                            <span>SKUs: <strong class="text-dark">' . $prodCount . '</strong>' . ($maxProd ? '/' . $maxProd : '') . '</span>
                            <span>' . $prodPct . '%</span>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar ' . ($prodPct >= 90 ? 'bg-danger' : ($prodPct >= 75 ? 'bg-warning' : 'bg-success')) . '" style="width: ' . $prodPct . '%;"></div>
                        </div>
                        <div class="extra-small text-muted font-mono mt-1" style="font-size:0.72rem;">
                            <i class="bi bi-people me-1"></i> ' . $custCount . ' Suki Customers
                        </div>
                    </div>
                ';
            })
            ->addColumn('actions', function ($tenant) {
                $encId = encrypt($tenant->id);
                $phone = $tenant->phone ?? '';
                $name = $tenant->business_name;
                $owner = $tenant->owner_name ?? 'Owner';
                $plan = $tenant->subscription?->name ?? 'LikhaPOS';
                $days = $tenant->days_until_due ?? 0;
                $dueFormatted = $tenant->subscription_end ? $tenant->subscription_end->format('M d, Y') : 'now';

                return '
                    <div class="dropdown text-end">
                        <button class="btn btn-light border btn-sm rounded-pill px-3 py-1 shadow-xs font-mono extra-small fw-bold" type="button" data-bs-toggle="dropdown">
                            Manage <i class="bi bi-chevron-down ms-1"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 py-2" style="min-width: 220px; z-index: 1060;">
                            <li>
                                <button type="button" class="dropdown-item py-1.5 px-3 extra-small d-flex align-items-center gap-2 text-warning-emphasis btn-send-reminder"
                                    data-id="' . $encId . '"
                                    data-name="' . e($name) . '"
                                    data-owner="' . e($owner) . '"
                                    data-phone="' . e($phone) . '"
                                    data-plan="' . e($plan) . '"
                                    data-days="' . $days . '"
                                    data-due="' . $dueFormatted . '">
                                    <i class="bi bi-bell-fill text-warning"></i>
                                    <span>Send Renewal Reminder</span>
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item py-1.5 px-3 extra-small d-flex align-items-center gap-2 text-primary btn-extend-due"
                                    data-id="' . $encId . '"
                                    data-name="' . e($name) . '"
                                    data-current-end="' . ($tenant->subscription_end ? $tenant->subscription_end->toDateString() : '') . '">
                                    <i class="bi bi-calendar-plus-fill text-primary"></i>
                                    <span>Grant Grace / Extend Due</span>
                                </button>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a href="' . route('sa.tenants.show', $encId) . '" class="dropdown-item py-1.5 px-3 extra-small d-flex align-items-center gap-2">
                                    <i class="bi bi-buildings"></i>
                                    <span>View Tenant Record</span>
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="' . route('sa.tenants.close-context') . '" class="d-none" id="ctxForm' . $tenant->id . '">
                                    ' . csrf_field() . '
                                </form>
                                <a href="tel:' . $phone . '" class="dropdown-item py-1.5 px-3 extra-small d-flex align-items-center gap-2 text-success">
                                    <i class="bi bi-telephone-outbound-fill"></i>
                                    <span>Direct Call: ' . $phone . '</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['store_info', 'owner_info', 'plan_info', 'due_status', 'usage_stats', 'actions'])
            ->make(true);
    }

    /**
     * Grant Grace Period or Extend Tenant Subscription Due Date
     */
    public function extendDue(Request $request, $id)
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin), 403);

        $tenantId = decrypt($id);
        $tenant = POSTenant::findOrFail($tenantId);

        $validated = $request->validate([
            'extension_type' => ['required', 'in:days,custom_date'],
            'days'           => ['nullable', 'integer', 'min:1', 'max:365'],
            'custom_date'    => ['nullable', 'date', 'after:today'],
            'remarks'        => ['nullable', 'string', 'max:500'],
        ]);

        $baseDate = $tenant->subscription_end && $tenant->subscription_end->isFuture()
            ? $tenant->subscription_end
            : now();

        if ($validated['extension_type'] === 'days') {
            $newEnd = $baseDate->copy()->addDays((int) $validated['days']);
        } else {
            $newEnd = Carbon::parse($validated['custom_date']);
        }

        $tenant->update([
            'subscription_end' => $newEnd->toDateString(),
            'status'           => 'active',
            'payment_status'   => $tenant->payment_status === 'pending_verification' ? 'pending_verification' : 'paid',
        ]);

        return response()->json([
            'success'  => true,
            'message'  => "Subscription for '{$tenant->business_name}' successfully extended to " . $newEnd->format('M d, Y') . '.',
            'new_date' => $newEnd->format('M d, Y'),
        ]);
    }
}
