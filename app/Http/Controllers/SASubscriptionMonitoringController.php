<?php

namespace App\Http\Controllers;

use App\Mail\SubscriptionRenewalReminderMail;
use App\Models\POS\POSCustomers;
use App\Models\POS\POSProducts;
use App\Models\POS\POSSale;
use App\Models\POS\POSTenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            'reminded_today'    => POSTenant::whereNotNull('last_reminder_sent_at')
                ->whereDate('last_reminder_sent_at', now()->toDateString())
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
        } elseif ($filter === 'not_reminded') {
            $query->whereNotNull('subscription_end')
                ->where('subscription_end', '<=', now()->addDays(7)->endOfDay())
                ->where(function ($q) {
                    $q->whereNull('last_reminder_sent_at')
                      ->orWhereDate('last_reminder_sent_at', '<', now()->toDateString());
                });
        }

        return DataTables::of($query)
            ->addColumn('checkbox', function ($tenant) {
                return '<input type="checkbox" class="form-check-input tenant-checkbox" value="' . $tenant->id . '" data-id="' . encrypt($tenant->id) . '">';
            })
            ->addColumn('store_info', function ($tenant) {
                $code = e($tenant->business_code ?? 'STORE');
                $name = e($tenant->business_name);
                $logo = $tenant->logo ? asset('storage/' . $tenant->logo) : null;
                $statusPill = $tenant->status === 'active'
                    ? '<span class="badge bg-success-subtle text-success extra-small py-0.5 px-1.5 fw-semibold border border-success-subtle">Active</span>'
                    : '<span class="badge bg-danger-subtle text-danger extra-small py-0.5 px-1.5 fw-semibold border border-danger-subtle">' . ucfirst($tenant->status) . '</span>';

                $logoHtml = $logo
                    ? '<img src="' . $logo . '" class="rounded-3 border flex-shrink-0" style="width:38px;height:38px;object-fit:contain;">'
                    : '<div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold shadow-xs flex-shrink-0" style="width:38px;height:38px;background:linear-gradient(135deg, #059669, #047857);font-size:1.05rem;"><i class="bi bi-shop"></i></div>';

                return '
                    <div class="d-flex align-items-center gap-2.5">
                        ' . $logoHtml . '
                        <div class="min-w-0">
                            <div class="fw-bold text-dark text-truncate" style="font-size:0.875rem;">' . $name . '</div>
                            <div class="d-flex align-items-center gap-1.5 mt-0.5">
                                <span class="badge bg-light text-muted border font-mono extra-small" style="font-size:0.68rem;">' . $code . '</span>
                                ' . $statusPill . '
                            </div>
                        </div>
                    </div>
                ';
            })
            ->addColumn('owner_info', function ($tenant) {
                $owner = e($tenant->owner_name ?? 'Store Owner');
                $phone = e($tenant->phone ?? 'No phone');
                $email = e($tenant->email ?? '');

                $emailHtml = $email
                    ? '<div class="extra-small text-muted font-mono text-truncate" style="max-width:180px;" title="' . $email . '"><i class="bi bi-envelope me-1"></i>' . $email . '</div>'
                    : '<div class="extra-small text-danger fst-italic">No email registered</div>';

                return '
                    <div>
                        <div class="fw-semibold text-dark text-truncate" style="font-size:0.84rem;">' . $owner . '</div>
                        <div class="d-flex align-items-center gap-1.5 mt-0.5">
                            <a href="tel:' . $phone . '" class="badge bg-success-subtle text-success text-decoration-none border border-success-subtle extra-small" title="Call Owner">
                                <i class="bi bi-telephone-fill me-1"></i> ' . $phone . '
                            </a>
                        </div>
                        <div class="mt-0.5">' . $emailHtml . '</div>
                    </div>
                ';
            })
            ->addColumn('plan_info', function ($tenant) {
                $planName = $tenant->subscription?->name ?? 'Suki Growth';
                $price = $tenant->subscription?->price ? '₱' . number_format($tenant->subscription->price, 2) . '/mo' : '₱300.00/mo';
                $badgeClass = match ($tenant->payment_status) {
                    'paid' => 'bg-success-subtle text-success border-success-subtle',
                    'trial' => 'bg-info-subtle text-info-emphasis border-info-subtle',
                    'pending_verification' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                    default => 'bg-secondary-subtle text-secondary border',
                };

                return '
                    <div>
                        <div class="fw-bold text-dark font-mono" style="font-size:0.84rem;">' . e($planName) . '</div>
                        <div class="extra-small text-muted font-mono">' . $price . '</div>
                        <div class="mt-1">
                            <span class="badge ' . $badgeClass . ' border extra-small font-mono">' . ucfirst($tenant->payment_status ?? 'Active') . '</span>
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
                            Due: <strong class="text-dark">' . $endFormatted . '</strong>
                        </div>
                    </div>
                ';
            })
            ->addColumn('activity_health', function ($tenant) {
                $sevenDaysSales = (float) POSSale::where('tenant_id', $tenant->id)
                    ->where('created_at', '>=', now()->subDays(7))
                    ->sum('total_amount');

                $lastSale = POSSale::where('tenant_id', $tenant->id)->latest('created_at')->first();
                $lastSaleTime = $lastSale && $lastSale->created_at ? $lastSale->created_at->diffForHumans() : 'No sales';

                $healthBadge = '';
                if ($sevenDaysSales > 1000) {
                    $healthBadge = '<span class="badge bg-success-subtle text-success extra-small py-0.5 px-1.5"><i class="bi bi-activity me-1"></i>High Velocity</span>';
                } elseif ($sevenDaysSales > 0) {
                    $healthBadge = '<span class="badge bg-warning-subtle text-warning-emphasis extra-small py-0.5 px-1.5"><i class="bi bi-graph-up me-1"></i>Active</span>';
                } else {
                    $healthBadge = '<span class="badge bg-secondary-subtle text-secondary extra-small py-0.5 px-1.5"><i class="bi bi-pause-fill me-1"></i>Dormant</span>';
                }

                return '
                    <div style="min-width: 130px;">
                        <div class="d-flex align-items-center justify-content-between mb-0.5">
                            <span class="extra-small text-muted">7-Day Sales:</span>
                            <strong class="text-dark font-mono small">₱' . number_format($sevenDaysSales, 0) . '</strong>
                        </div>
                        <div class="extra-small text-muted font-mono mb-1">
                            Last: <span class="text-dark fw-semibold">' . $lastSaleTime . '</span>
                        </div>
                        <div>' . $healthBadge . '</div>
                    </div>
                ';
            })
            ->addColumn('reminder_sentinel', function ($tenant) {
                $remindedAt = $tenant->last_reminder_sent_at;
                $count = (int) ($tenant->reminder_count ?? 0);
                $encId = encrypt($tenant->id);
                $hasEmail = !empty($tenant->email);

                $reminderInfo = '';
                if ($remindedAt) {
                    $isToday = $remindedAt->isToday();
                    $badgeStyle = $isToday ? 'bg-success-subtle text-success' : 'bg-info-subtle text-info-emphasis';
                    $reminderInfo = '
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge ' . $badgeStyle . ' extra-small py-0.5 px-1.5 fw-bold">
                                <i class="bi bi-check2-all me-1"></i>' . $remindedAt->diffForHumans() . '
                            </span>
                            <span class="extra-small text-muted font-mono">(' . $count . 'x)</span>
                        </div>
                    ';
                } else {
                    $reminderInfo = '<span class="badge bg-secondary-subtle text-secondary extra-small py-0.5 px-1.5">Never Reminded</span>';
                }

                $emailBtn = $hasEmail
                    ? '<button type="button" class="btn btn-outline-danger btn-sm rounded-pill py-0.5 px-2 extra-small fw-bold btn-quick-email mt-1" 
                               data-id="' . $encId . '" data-name="' . e($tenant->business_name) . '" data-email="' . e($tenant->email) . '" data-plan="' . e($tenant->subscription?->name ?? 'Plan') . '">
                           <i class="bi bi-envelope-paper-fill me-1"></i> Send Email
                       </button>'
                    : '<span class="extra-small text-muted fst-italic mt-1 d-block">No email address</span>';

                return '
                    <div style="min-width: 140px;">
                        ' . $reminderInfo . '
                        ' . $emailBtn . '
                    </div>
                ';
            })
            ->addColumn('actions', function ($tenant) {
                $encId = encrypt($tenant->id);
                $phone = $tenant->phone ?? '';
                $email = $tenant->email ?? '';
                $name = $tenant->business_name;
                $owner = $tenant->owner_name ?? 'Owner';
                $plan = $tenant->subscription?->name ?? 'LikhaPOS';
                $days = $tenant->days_until_due ?? 0;
                $dueFormatted = $tenant->subscription_end ? $tenant->subscription_end->format('M d, Y') : 'now';
                $statusAction = $tenant->status === 'active' ? 'suspend' : 'activate';
                $statusLabel = $tenant->status === 'active' ? 'Suspend Store Account' : 'Reactivate Store Account';
                $statusIcon = $tenant->status === 'active' ? 'bi-lock-fill text-danger' : 'bi-unlock-fill text-success';

                return '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 extra-small dropdown-toggle shadow-xs fw-bold" type="button" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 py-1 font-sans" style="font-size: 0.8rem; min-width: 210px;">
                            <li>
                                <button type="button" class="dropdown-item py-1.5 px-3 extra-small d-flex align-items-center gap-2 text-danger fw-semibold btn-quick-email"
                                    data-id="' . $encId . '"
                                    data-name="' . e($name) . '"
                                    data-email="' . e($email) . '"
                                    data-owner="' . e($owner) . '"
                                    data-plan="' . e($plan) . '"
                                    data-days="' . $days . '"
                                    data-due="' . $dueFormatted . '">
                                    <i class="bi bi-envelope-paper-heart-fill text-danger"></i>
                                    <span>Send Email Renewal Notice</span>
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item py-1.5 px-3 extra-small d-flex align-items-center gap-2 text-warning fw-semibold btn-send-reminder"
                                    data-id="' . $encId . '"
                                    data-name="' . e($name) . '"
                                    data-owner="' . e($owner) . '"
                                    data-phone="' . e($phone) . '"
                                    data-plan="' . e($plan) . '"
                                    data-days="' . $days . '"
                                    data-due="' . $dueFormatted . '">
                                    <i class="bi bi-chat-dots-fill text-warning"></i>
                                    <span>Send SMS / WhatsApp Notice</span>
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item py-1.5 px-3 extra-small d-flex align-items-center gap-2 text-primary fw-semibold btn-extend-due"
                                    data-id="' . $encId . '"
                                    data-name="' . e($name) . '"
                                    data-current-end="' . ($tenant->subscription_end ? $tenant->subscription_end->toDateString() : '') . '">
                                    <i class="bi bi-calendar-plus-fill text-primary"></i>
                                    <span>Grant Grace / Extend Due</span>
                                </button>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <button type="button" class="dropdown-item py-1.5 px-3 extra-small d-flex align-items-center gap-2 btn-toggle-status"
                                    data-id="' . $encId . '"
                                    data-action="' . $statusAction . '"
                                    data-name="' . e($name) . '">
                                    <i class="bi ' . $statusIcon . '"></i>
                                    <span>' . $statusLabel . '</span>
                                </button>
                            </li>
                            <li>
                                <a href="' . route('sa.tenants.show', $encId) . '" class="dropdown-item py-1.5 px-3 extra-small d-flex align-items-center gap-2">
                                    <i class="bi bi-box-arrow-in-right text-success"></i>
                                    <span>Open Store Context</span>
                                </a>
                            </li>
                            <li>
                                <a href="tel:' . $phone . '" class="dropdown-item py-1.5 px-3 extra-small d-flex align-items-center gap-2 text-success">
                                    <i class="bi bi-telephone-outbound-fill"></i>
                                    <span>Direct Call (' . $phone . ')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['checkbox', 'store_info', 'owner_info', 'plan_info', 'due_status', 'activity_health', 'reminder_sentinel', 'actions'])
            ->make(true);
    }

    /**
     * Dispatch Single Email Renewal Reminder
     */
    public function sendEmailReminder(Request $request, $id)
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin), 403);

        $tenantId = decrypt($id);
        $tenant = POSTenant::with(['subscription'])->findOrFail($tenantId);

        $validated = $request->validate([
            'email'       => ['nullable', 'email'],
            'custom_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $recipientEmail = $validated['email'] ?? $tenant->email;

        if (empty($recipientEmail)) {
            $ownerUser = $tenant->creator ?? \App\Models\User::where('tenant_id', $tenant->id)->first();
            $recipientEmail = $ownerUser?->email;
        }

        if (empty($recipientEmail)) {
            return response()->json([
                'success' => false,
                'message' => "Store '{$tenant->business_name}' does not have a registered email address. Please specify one.",
            ], 422);
        }

        try {
            Mail::to($recipientEmail)->queue(new SubscriptionRenewalReminderMail($tenant, $validated['custom_note'] ?? null));

            $tenant->update([
                'last_reminder_sent_at' => now(),
                'reminder_count'        => (int) ($tenant->reminder_count ?? 0) + 1,
                'last_reminder_channel' => 'email',
                'last_reminder_notes'   => $validated['custom_note'] ?? 'Queued email reminder dispatched by SuperAdmin.',
            ]);

            return response()->json([
                'success' => true,
                'message' => "Renewal reminder queued for delivery to {$recipientEmail} for '{$tenant->business_name}'. Delivery runs asynchronously in the background so you can browse freely!",
                'sent_at' => now()->diffForHumans(),
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed sending renewal reminder email to {$recipientEmail}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Failed to deliver email: " . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Dispatch Bulk Email Renewal Reminders
     */
    public function sendBulkEmailReminders(Request $request)
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin), 403);

        $validated = $request->validate([
            'tenant_ids' => ['nullable', 'array'],
            'target'     => ['nullable', 'string', 'in:selected,all_critical,all_due'],
        ]);

        $target = $validated['target'] ?? 'selected';
        $query = POSTenant::with(['subscription']);

        if ($target === 'all_critical') {
            $query->whereNotNull('subscription_end')
                ->where('subscription_end', '<=', now()->addDays(3)->endOfDay())
                ->where('status', '!=', 'inactive');
        } elseif ($target === 'all_due') {
            $query->whereNotNull('subscription_end')
                ->where('subscription_end', '<=', now()->addDays(7)->endOfDay())
                ->where('status', '!=', 'inactive');
        } else {
            $ids = $validated['tenant_ids'] ?? [];
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select at least one store from the list.',
                ], 422);
            }
            $query->whereIn('id', $ids);
        }

        $tenants = $query->get();
        $sentCount = 0;
        $failedCount = 0;

        foreach ($tenants as $tenant) {
            $email = $tenant->email;
            if (empty($email)) {
                $ownerUser = $tenant->creator ?? \App\Models\User::where('tenant_id', $tenant->id)->first();
                $email = $ownerUser?->email;
            }

            if (empty($email)) {
                $failedCount++;
                continue;
            }

            try {
                Mail::to($email)->queue(new SubscriptionRenewalReminderMail($tenant));

                $tenant->update([
                    'last_reminder_sent_at' => now(),
                    'reminder_count'        => (int) ($tenant->reminder_count ?? 0) + 1,
                    'last_reminder_channel' => 'email',
                    'last_reminder_notes'   => 'Bulk queued automated email reminder dispatched.',
                ]);
                $sentCount++;
            } catch (\Throwable $e) {
                Log::error("Bulk reminder error for tenant {$tenant->id}: " . $e->getMessage());
                $failedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Bulk dispatch queued! {$sentCount} store renewal email(s) scheduled in background." . ($failedCount > 0 ? " ({$failedCount} skipped due to missing email)." : ""),
            'sent'    => $sentCount,
            'failed'  => $failedCount,
        ]);
    }

    /**
     * Trigger Automated Renewal Engine on Demand
     */
    public function runAutomatedEngine(Request $request)
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin), 403);

        try {
            Artisan::call('subscriptions:send-reminders');
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'Automated Subscription Renewal Sentinel scan completed successfully!',
                'output'  => $output,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error executing automated sentinel: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Suspend or Reactivate Store Account
     */
    public function toggleStatus(Request $request, $id)
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin), 403);

        $tenantId = decrypt($id);
        $tenant = POSTenant::findOrFail($tenantId);

        $validated = $request->validate([
            'action' => ['required', 'in:suspend,activate'],
        ]);

        $newStatus = $validated['action'] === 'suspend' ? 'locked' : 'active';
        $tenant->update(['status' => $newStatus]);
        \Illuminate\Support\Facades\Cache::forget('tenant_settings_' . $tenant->id);

        $actionWord = $newStatus === 'active' ? 'reactivated' : 'locked / suspended';

        return response()->json([
            'success' => true,
            'message' => "Store '{$tenant->business_name}' is now {$actionWord}.",
            'status'  => $newStatus,
        ]);
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

    /**
     * Export Due Monitoring Watchlist to CSV
     */
    public function exportCsv(Request $request)
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin), 403);

        $today = now()->startOfDay();
        $query = POSTenant::with(['subscription'])
            ->whereNotNull('subscription_end')
            ->orderBy('subscription_end', 'asc');

        $filter = $request->input('urgency', 'all');
        if ($filter === 'critical_3_days') {
            $query->whereBetween('subscription_end', [$today, now()->addDays(3)->endOfDay()]);
        } elseif ($filter === 'due_7_days') {
            $query->whereBetween('subscription_end', [$today, now()->addDays(7)->endOfDay()]);
        } elseif ($filter === 'overdue') {
            $query->where('subscription_end', '<', $today);
        }

        $tenants = $query->get();
        $fileName = 'LikhaPOS_Subscription_Due_Sentinel_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $columns = [
            'Store Code',
            'Store Name',
            'Owner Name',
            'Contact Phone',
            'Email Address',
            'Current Plan',
            'Monthly Price',
            'Expiration Date',
            'Days Remaining',
            'Account Status',
            'Payment Status',
            '7-Day Sales (PHP)',
            'Last Reminder Sent',
            'Reminder Count',
        ];

        $callback = function () use ($tenants, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tenants as $t) {
                $sales7d = (float) POSSale::where('tenant_id', $t->id)->where('created_at', '>=', now()->subDays(7))->sum('total_amount');
                fputcsv($file, [
                    $t->business_code,
                    $t->business_name,
                    $t->owner_name,
                    $t->phone ?? 'N/A',
                    $t->email ?? 'N/A',
                    $t->subscription?->name ?? 'Standard',
                    number_format($t->subscription?->price ?? 300, 2),
                    $t->subscription_end ? $t->subscription_end->format('Y-m-d') : 'N/A',
                    $t->days_until_due ?? 'N/A',
                    ucfirst($t->status),
                    ucfirst($t->payment_status),
                    number_format($sales7d, 2),
                    $t->last_reminder_sent_at ? $t->last_reminder_sent_at->format('Y-m-d H:i') : 'Never',
                    $t->reminder_count ?? 0,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
