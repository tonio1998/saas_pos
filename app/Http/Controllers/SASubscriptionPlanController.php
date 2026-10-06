<?php

namespace App\Http\Controllers;

use App\Models\POS\POSSubscription;
use App\Models\POS\POSTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SASubscriptionPlanController extends Controller
{

    /**
     * Display Subscription Plans and Promos Studio
     */
    public function index(Request $request)
    {
        $query = POSSubscription::query()
            ->withCount(['tenants as active_tenants_count' => function ($q) {
                $q->whereIn('status', ['active', 'locked', 'unlocked']);
            }])
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc');

        // Filter by tab
        $tab = $request->query('tab', 'all');
        if ($tab === 'promos') {
            $query->where('is_promo', 1);
        } elseif ($tab === 'standard') {
            $query->where('is_promo', 0)->where('status', 'active');
        } elseif ($tab === 'inactive') {
            $query->where('status', 'inactive');
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('promo_code', 'like', "%{$search}%")
                  ->orWhere('badge_text', 'like', "%{$search}%");
            });
        }

        $plans = $query->get();

        // Calculate KPI metrics
        $allPlans = POSSubscription::withCount(['tenants as active_tenants_count' => function ($q) {
            $q->whereIn('status', ['active', 'locked', 'unlocked']);
        }])->get();

        $totalActivePlans = $allPlans->where('status', 'active')->where('is_promo', 0)->count();
        $totalPromos = $allPlans->where('is_promo', 1)->count();
        $totalSubscribedStores = POSTenant::whereNotNull('subscription_id')->count();
        
        // Total provisioned terminals across all subscribed stores
        $totalTerminalsLimit = $allPlans->sum(function ($p) {
            return ($p->max_terminals ?? 1) * $p->active_tenants_count;
        });

        // Monthly projected subscription MRR
        $monthlyMRR = $allPlans->sum(function ($p) {
            $price = $p->effectivePrice();
            return $price * $p->active_tenants_count;
        });

        return view('pages.sa.subscriptions.plans', compact(
            'plans',
            'tab',
            'totalActivePlans',
            'totalPromos',
            'totalSubscribedStores',
            'totalTerminalsLimit',
            'monthlyMRR'
        ));
    }

    /**
     * Show dedicated page to create a new subscription plan or special promo
     */
    public function create()
    {
        return view('pages.sa.subscriptions.plans.create');
    }

    /**
     * Show dedicated page to edit an existing subscription plan or promo
     */
    public function edit($id)
    {
        $plan = POSSubscription::findOrFail($id);
        return view('pages.sa.subscriptions.plans.edit', compact('plan'));
    }

    /**
     * Store new subscription plan or special promo package
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => ['required', 'string', 'max:100'],
            'description'          => ['nullable', 'string', 'max:500'],
            'price'                => ['required', 'numeric', 'min:0'],
            'is_promo'             => ['nullable', 'boolean'],
            'promo_price'          => ['nullable', 'numeric', 'min:0'],
            'promo_code'           => ['nullable', 'string', 'max:50'],
            'badge_text'           => ['nullable', 'string', 'max:100'],
            'promo_expires_at'     => ['nullable', 'date'],
            'featured'             => ['nullable', 'boolean'],
            'billing_cycle'        => ['required', 'string', 'in:monthly,quarterly,semi_annual,yearly,custom'],
            'duration_days'        => ['required', 'integer', 'min:1', 'max:3650'],
            'max_terminals'        => ['required', 'integer', 'min:1', 'max:999'],
            'max_products'         => ['required', 'integer', 'min:1'],
            'max_customers'        => ['nullable', 'integer', 'min:0'],
            'max_cashier_accounts' => ['required', 'integer', 'min:1', 'max:999'],
            'max_admin_accounts'   => ['required', 'integer', 'min:1', 'max:99'],
            'max_users'            => ['required', 'integer', 'min:1', 'max:999'],
            'max_branches'         => ['required', 'integer', 'min:1', 'max:99'],
            'allow_multi_branch'   => ['nullable', 'boolean'],
            'allow_inventory'      => ['nullable', 'boolean'],
            'allow_reports'        => ['nullable', 'boolean'],
            'inclusions'           => ['nullable', 'array'],
            'inclusions.*'         => ['nullable', 'string'],
            'limitations'          => ['nullable', 'array'],
            'limitations.*'        => ['nullable', 'string'],
            'status'               => ['required', 'in:active,inactive'],
            'sort_order'           => ['nullable', 'integer', 'min:0'],
        ]);

        $inclusions = array_values(array_filter($request->input('inclusions', []), fn($val) => !empty(trim($val ?? ''))));
        $limitations = array_values(array_filter($request->input('limitations', []), fn($val) => !empty(trim($val ?? ''))));

        $plan = new POSSubscription();
        $plan->name = trim($validated['name']);
        $plan->description = trim($validated['description'] ?? '');
        $plan->price = (float) $validated['price'];
        $plan->is_promo = $request->boolean('is_promo');
        $plan->promo_price = $request->filled('promo_price') ? (float) $validated['promo_price'] : null;
        $plan->promo_code = $request->filled('promo_code') ? strtoupper(trim($validated['promo_code'])) : null;
        $plan->badge_text = $request->filled('badge_text') ? trim($validated['badge_text']) : null;
        $plan->promo_expires_at = $validated['promo_expires_at'] ?? null;
        $plan->featured = $request->boolean('featured');
        $plan->billing_cycle = $validated['billing_cycle'];
        $plan->duration_days = (int) $validated['duration_days'];
        $plan->max_terminals = (int) $validated['max_terminals'];
        $plan->max_products = (int) $validated['max_products'];
        $plan->max_customers = $request->filled('max_customers') ? (int) $validated['max_customers'] : null;
        $plan->max_cashier_accounts = (int) $validated['max_cashier_accounts'];
        $plan->max_admin_accounts = (int) $validated['max_admin_accounts'];
        $plan->max_users = (int) $validated['max_users'];
        $plan->max_branches = (int) $validated['max_branches'];
        $plan->allow_multi_branch = $request->boolean('allow_multi_branch', (int) $validated['max_branches'] > 1);
        $plan->allow_inventory = $request->boolean('allow_inventory', true);
        $plan->allow_reports = $request->boolean('allow_reports', true);
        $plan->inclusions = $inclusions;
        $plan->limitations = $limitations;
        $plan->status = $validated['status'];
        $plan->sort_order = (int) ($validated['sort_order'] ?? 0);
        $plan->created_by = auth()->id();
        $plan->updated_by = auth()->id();
        $plan->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Subscription plan '{$plan->name}' created successfully.",
                'plan'    => $plan,
            ]);
        }

        return redirect()->route('sa.subscriptions.plans')->with('success', "Subscription plan '{$plan->name}' created successfully.");
    }

    /**
     * Update existing plan or promo
     */
    public function update(Request $request, $id)
    {
        $plan = POSSubscription::findOrFail($id);

        $validated = $request->validate([
            'name'                 => ['required', 'string', 'max:100'],
            'description'          => ['nullable', 'string', 'max:500'],
            'price'                => ['required', 'numeric', 'min:0'],
            'is_promo'             => ['nullable', 'boolean'],
            'promo_price'          => ['nullable', 'numeric', 'min:0'],
            'promo_code'           => ['nullable', 'string', 'max:50'],
            'badge_text'           => ['nullable', 'string', 'max:100'],
            'promo_expires_at'     => ['nullable', 'date'],
            'featured'             => ['nullable', 'boolean'],
            'billing_cycle'        => ['required', 'string', 'in:monthly,quarterly,semi_annual,yearly,custom'],
            'duration_days'        => ['required', 'integer', 'min:1', 'max:3650'],
            'max_terminals'        => ['required', 'integer', 'min:1', 'max:999'],
            'max_products'         => ['required', 'integer', 'min:1'],
            'max_customers'        => ['nullable', 'integer', 'min:0'],
            'max_cashier_accounts' => ['required', 'integer', 'min:1', 'max:999'],
            'max_admin_accounts'   => ['required', 'integer', 'min:1', 'max:99'],
            'max_users'            => ['required', 'integer', 'min:1', 'max:999'],
            'max_branches'         => ['required', 'integer', 'min:1', 'max:99'],
            'allow_multi_branch'   => ['nullable', 'boolean'],
            'allow_inventory'      => ['nullable', 'boolean'],
            'allow_reports'        => ['nullable', 'boolean'],
            'inclusions'           => ['nullable', 'array'],
            'inclusions.*'         => ['nullable', 'string'],
            'limitations'          => ['nullable', 'array'],
            'limitations.*'        => ['nullable', 'string'],
            'status'               => ['required', 'in:active,inactive'],
            'sort_order'           => ['nullable', 'integer', 'min:0'],
        ]);

        $inclusions = array_values(array_filter($request->input('inclusions', []), fn($val) => !empty(trim($val ?? ''))));
        $limitations = array_values(array_filter($request->input('limitations', []), fn($val) => !empty(trim($val ?? ''))));

        $plan->name = trim($validated['name']);
        $plan->description = trim($validated['description'] ?? '');
        $plan->price = (float) $validated['price'];
        $plan->is_promo = $request->boolean('is_promo');
        $plan->promo_price = $request->filled('promo_price') ? (float) $validated['promo_price'] : null;
        $plan->promo_code = $request->filled('promo_code') ? strtoupper(trim($validated['promo_code'])) : null;
        $plan->badge_text = $request->filled('badge_text') ? trim($validated['badge_text']) : null;
        $plan->promo_expires_at = $validated['promo_expires_at'] ?? null;
        $plan->featured = $request->boolean('featured');
        $plan->billing_cycle = $validated['billing_cycle'];
        $plan->duration_days = (int) $validated['duration_days'];
        $plan->max_terminals = (int) $validated['max_terminals'];
        $plan->max_products = (int) $validated['max_products'];
        $plan->max_customers = $request->filled('max_customers') ? (int) $validated['max_customers'] : null;
        $plan->max_cashier_accounts = (int) $validated['max_cashier_accounts'];
        $plan->max_admin_accounts = (int) $validated['max_admin_accounts'];
        $plan->max_users = (int) $validated['max_users'];
        $plan->max_branches = (int) $validated['max_branches'];
        $plan->allow_multi_branch = $request->boolean('allow_multi_branch', (int) $validated['max_branches'] > 1);
        $plan->allow_inventory = $request->boolean('allow_inventory', true);
        $plan->allow_reports = $request->boolean('allow_reports', true);
        $plan->inclusions = $inclusions;
        $plan->limitations = $limitations;
        $plan->status = $validated['status'];
        $plan->sort_order = (int) ($validated['sort_order'] ?? 0);
        $plan->updated_by = auth()->id();
        $plan->save();

        // Clear cached limits for tenants on this plan
        POSTenant::where('subscription_id', $plan->id)->pluck('id')->each(function ($tId) {
            Cache::forget("tenant_{$tId}_sub_limits");
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Plan '{$plan->name}' updated successfully.",
                'plan'    => $plan,
            ]);
        }

        return redirect()->route('sa.subscriptions.plans')->with('success', "Plan '{$plan->name}' updated successfully.");
    }

    /**
     * Toggle Plan active / inactive status
     */
    public function toggleStatus(Request $request, $id)
    {
        $plan = POSSubscription::findOrFail($id);
        $newStatus = $plan->status === 'active' ? 'inactive' : 'active';
        $plan->update(['status' => $newStatus, 'updated_by' => auth()->id()]);

        return response()->json([
            'success' => true,
            'message' => "Plan '{$plan->name}' is now {$newStatus}.",
            'status'  => $newStatus,
        ]);
    }

    /**
     * Duplicate / Clone an existing plan or turn it into a promo
     */
    public function clone(Request $request, $id)
    {
        $source = POSSubscription::findOrFail($id);
        
        $clone = $source->replicate();
        $clone->name = $source->name . ' (Promo)';
        $clone->is_promo = true;
        $clone->badge_text = 'SPECIAL PROMO';
        $clone->promo_price = round($source->price * 0.8, 2); // 20% discount preset
        $clone->promo_code = 'PROMO' . strtoupper(substr(md5(uniqid()), 0, 5));
        $clone->sort_order = ($source->sort_order ?? 0) + 1;
        $clone->created_by = auth()->id();
        $clone->updated_by = auth()->id();
        $clone->created_at = now();
        $clone->updated_at = now();
        $clone->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Cloned '{$source->name}' into promo '{$clone->name}'.",
                'plan'    => $clone,
            ]);
        }

        return redirect()->route('sa.subscriptions.plans')->with('success', "Cloned '{$source->name}' into promo '{$clone->name}'.");
    }

    /**
     * Delete or archive a plan
     */
    public function destroy($id)
    {
        $plan = POSSubscription::findOrFail($id);
        $activeSubscribers = POSTenant::where('subscription_id', $plan->id)->count();

        if ($activeSubscribers > 0) {
            // Cannot hard delete active plans; archive instead
            $plan->update(['status' => 'inactive', 'archived' => 1]);
            return response()->json([
                'success' => true,
                'message' => "Plan '{$plan->name}' has {$activeSubscribers} active store(s). It has been archived and set to inactive.",
            ]);
        }

        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => "Plan '{$plan->name}' deleted successfully.",
        ]);
    }
}
