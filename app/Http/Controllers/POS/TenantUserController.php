<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Tenant\TenantSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TenantUserController extends Controller
{
    protected TenantSubscriptionService $subscriptionService;

    public function __construct(TenantSubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Display Tenant User Accounts & Subscription Limit Dashboard
     */
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $usage = $this->subscriptionService->getUsageSummary($tenantId);

        $roles = Role::whereIn('name', ['tenant', 'admin', 'cashier', 'manager'])->get();

        return view('pages.tenants.users.index', compact('usage', 'roles'));
    }

    /**
     * AJAX DataTables JSON Endpoint for Tenant Users
     */
    public function ajaxData(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $query = User::with('roles')
            ->where('tenant_id', $tenantId)
            ->latest();

        return datatables()
            ->eloquent($query)

            ->addColumn('actions', function ($user) {
                $encId = encrypt($user->id);
                $isSelf = auth()->id() === $user->id;
                $roleName = $user->roles->first()?->name ?? 'cashier';

                return '
                <div class="dropdown">
                    <button class="btn btn-light border btn-sm rounded-2 extra-small font-mono fw-bold px-2.5 shadow-xs" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-popper-config=\'{"strategy":"fixed"}\'>
                        Actions <i class="bi bi-chevron-down ms-1"></i>
                    </button>
                    <ul class="dropdown-menu shadow-lg border-0 font-mono small rounded-3 p-1.5" style="z-index:1080; min-width: 190px;">
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-primary fw-semibold py-1.5 rounded-2 btn-edit-user" data-id="' . $encId . '" data-name="' . e($user->name) . '" data-username="' . e($user->username) . '" data-email="' . e($user->email) . '" data-role="' . e($roleName) . '" data-status="' . e($user->status ?? 'active') . '">
                                <i class="bi bi-pencil"></i> Edit Account
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-warning-emphasis fw-semibold py-1.5 rounded-2 btn-reset-password" data-id="' . $encId . '" data-name="' . e($user->name) . '" data-username="' . e($user->username) . '">
                                <i class="bi bi-key-fill"></i> Reset Password
                            </button>
                        </li>
                        ' . (!$isSelf ? '
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-danger fw-semibold py-1.5 rounded-2 btn-delete-user" data-id="' . $encId . '" data-name="' . e($user->name) . '">
                                <i class="bi bi-trash"></i> Delete Account
                            </button>
                        </li>
                        ' : '') . '
                    </ul>
                </div>
                ';
            })

            ->addColumn('user_info', function ($user) {
                $initial = strtoupper(substr($user->name ?: 'U', 0, 1));
                return '
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary border border-primary-subtle fw-black d-flex align-items-center justify-content-center flex-shrink-0" style="width:34px;height:34px;font-size:0.85rem;">
                            ' . $initial . '
                        </div>
                        <div class="min-w-0">
                            <div class="fw-bold text-dark lh-sm text-truncate" style="font-size:0.875rem;">' . e($user->name) . '</div>
                            <small class="text-muted font-mono extra-small d-block">ID: #' . $user->id . '</small>
                        </div>
                    </div>
                ';
            })

            ->addColumn('username', function ($user) {
                return '<span class="badge font-mono extra-small px-2.5 py-1" style="background:#f1f5f9;color:#334155;border:1px solid #cbd5e1;"><i class="bi bi-person-fill text-primary me-1"></i>' . e($user->username ?: $user->email) . '</span>';
            })

            ->addColumn('role_badge', function ($user) {
                $role = $user->roles->first()?->name ?? 'cashier';
                return match ($role) {
                    'tenant', 'owner' => '<span class="badge extra-small font-mono fw-bold" style="background:#f3e8ff;color:#7e22ce;border:1px solid #d8b4fe;"><i class="bi bi-award-fill me-1"></i>Store Owner</span>',
                    'admin' => '<span class="badge extra-small font-mono fw-bold" style="background:#e0f2fe;color:#0369a1;border:1px solid #7dd3fc;"><i class="bi bi-shield-lock-fill me-1"></i>Store Admin</span>',
                    'manager' => '<span class="badge extra-small font-mono fw-bold" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;"><i class="bi bi-person-badge-fill me-1"></i>Manager</span>',
                    default => '<span class="badge extra-small font-mono fw-bold" style="background:#dcfce7;color:#166534;border:1px solid #86efac;"><i class="bi bi-person-check-fill me-1"></i>Cashier Staff</span>',
                };
            })

            ->addColumn('status', function ($user) {
                return ($user->status ?? 'active') === 'active'
                    ? '<span class="badge extra-small fw-bold" style="background:#dcfce7;color:#166534;border:1px solid #86efac;"><i class="bi bi-check-circle-fill me-1"></i>Active</span>'
                    : '<span class="badge extra-small fw-bold" style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;"><i class="bi bi-x-circle-fill me-1"></i>Inactive</span>';
            })

            ->addColumn('createdAt', function ($user) {
                return '
                    <div>
                        <div class="fw-bold extra-small font-mono text-dark">' . ($user->created_at ? $user->created_at->format('M d, Y') : 'N/A') . '</div>
                        <small class="extra-small font-mono text-muted"><i class="bi bi-clock me-1"></i>' . ($user->created_at ? $user->created_at->format('h:i A') : '') . '</small>
                    </div>
                ';
            })

            ->rawColumns([
                'actions',
                'user_info',
                'username',
                'role_badge',
                'status',
                'createdAt'
            ])

            ->make(true);
    }

    /**
     * Store New User Account with Subscription Limits Validation & Store Prefix
     */
    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $tenant = \App\Models\POS\POSTenant::find($tenantId);

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100'],
            'email'    => ['nullable', 'string', 'email', 'max:255'],
            'role'     => ['required', 'string', 'in:admin,cashier,manager,tenant'],
            'password' => ['required', 'string', 'min:6'],
            'status'   => ['nullable', 'string', 'in:active,inactive'],
        ]);

        // Clean & construct store-prefixed username
        $storeCode = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $tenant?->business_code ?? 'store'));
        $rawUsername = strtolower(trim($validated['username']));
        
        // Prepend store code if not already prefixed
        if (!str_starts_with($rawUsername, $storeCode . '_') && !str_starts_with($rawUsername, 'store_')) {
            $finalUsername = $storeCode . '_' . $rawUsername;
        } else {
            $finalUsername = $rawUsername;
        }

        // Check username uniqueness
        if (User::where('username', $finalUsername)->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Username '{$finalUsername}' is already taken. Please choose another username.",
            ], 422);
        }

        // ENFORCE SUBSCRIPTION ACCOUNT LIMIT CHECK BEFORE CREATION
        $check = $this->subscriptionService->canCreateUser($tenantId, $validated['role']);
        if (!$check['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $check['message'],
                'limit_reached' => true,
            ], 422);
        }

        $user = new User();
        $user->tenant_id = $tenantId;
        $user->name      = trim($validated['name']);
        $user->username  = $finalUsername;
        $user->email     = !empty($validated['email']) ? strtolower(trim($validated['email'])) : $finalUsername . '@store.local';
        $user->password  = Hash::make($validated['password']);
        $user->status    = $validated['status'] ?? 'active';
        $user->verified  = 1;
        $user->save();

        $roleObj = Role::firstOrCreate(['name' => $validated['role'], 'guard_name' => 'web']);
        $user->assignRole($roleObj);

        // Clear subscription cache
        $this->subscriptionService->clearTenantCache($tenantId);

        return response()->json([
            'success' => true,
            'message' => "User account for {$user->name} created with Username: {$user->username}",
            'user'    => $user,
        ]);
    }

    /**
     * Update User Details & Role
     */
    public function update(Request $request, string $id)
    {
        $tenantId = auth()->user()->tenant_id;
        $realId = is_numeric($id) ? (int)$id : decrypt($id);

        $user = User::where('tenant_id', $tenantId)->findOrFail($realId);

        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role'   => ['required', 'string', 'in:admin,cashier,manager,tenant'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ]);

        $user->name   = trim($validated['name']);
        $user->email  = strtolower(trim($validated['email']));
        $user->status = $validated['status'] ?? 'active';
        $user->save();

        $roleObj = Role::firstOrCreate(['name' => $validated['role'], 'guard_name' => 'web']);
        $user->syncRoles([$roleObj]);

        $this->subscriptionService->clearTenantCache($tenantId);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "User account for {$user->name} updated successfully!",
            ]);
        }

        return redirect()->route('users.index')->with('success', "User account updated successfully!");
    }

    /**
     * Store Owner / Admin Reset Password Handler
     */
    public function resetPassword(Request $request, string $id)
    {
        $tenantId = auth()->user()->tenant_id;
        $realId = is_numeric($id) ? (int)$id : decrypt($id);

        $user = User::where('tenant_id', $tenantId)->findOrFail($realId);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Password for {$user->name} has been reset successfully!",
            ]);
        }

        return redirect()->route('users.index')->with('success', "Password for {$user->name} has been reset successfully!");
    }

    /**
     * Delete User Account
     */
    public function destroy(string $id)
    {
        $tenantId = auth()->user()->tenant_id;
        $realId = is_numeric($id) ? (int)$id : decrypt($id);

        if (auth()->id() === $realId) {
            return response()->json(['success' => false, 'message' => 'You cannot delete your own logged-in account.'], 422);
        }

        $user = User::where('tenant_id', $tenantId)->findOrFail($realId);
        $user->delete();

        $this->subscriptionService->clearTenantCache($tenantId);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User account deleted successfully.',
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User account deleted successfully.');
    }
}
