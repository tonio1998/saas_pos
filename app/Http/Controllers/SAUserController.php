<?php

namespace App\Http\Controllers;

use App\Models\POS\POSTenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class SAUserController extends Controller
{
    /**
     * Display SuperAdmin All Users Management Console
     */
    public function index()
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin), 403);

        $kpis = [
            'total_users'     => User::count(),
            'super_admins'    => User::whereHas('roles', fn($q) => $q->where('name', 'SA'))->orWhere('is_super_admin', true)->count(),
            'store_owners'    => User::whereHas('roles', fn($q) => $q->whereIn('name', ['tenant', 'admin']))->count(),
            'cashiers_staff'  => User::whereHas('roles', fn($q) => $q->whereIn('name', ['cashier', 'manager', 'employees']))->count(),
            'active_users'    => User::where('status', 'active')->orWhereNull('status')->count(),
            'google_users'    => User::whereNotNull('google_id')->count(),
        ];

        $tenants = POSTenant::select('id', 'business_name', 'business_code')->orderBy('business_name')->get();
        $roles   = Role::orderBy('name')->get();

        return view('pages.sa.users.index', compact('kpis', 'tenants', 'roles'));
    }

    /**
     * AJAX DataTables Provider for Platform-Wide Users
     */
    public function ajaxData(Request $request)
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin), 403);

        $query = User::with(['tenant', 'roles'])->select('users.*');

        // Filter by Tenant
        if ($request->filled('tenant_id')) {
            if ($request->tenant_id === 'root') {
                $query->whereNull('tenant_id');
            } else {
                $query->where('tenant_id', $request->tenant_id);
            }
        }

        // Filter by Role
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Filter by Status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where(function ($q) {
                    $q->where('status', 'active')->orWhereNull('status');
                });
            } else {
                $query->where('status', $request->status);
            }
        }

        return DataTables::of($query)
            ->addColumn('user_info', function ($user) {
                $initial = strtoupper(substr($user->name ?? 'U', 0, 1));
                $avatar = $user->avatar ?? ($user->filepath ? asset('storage/' . $user->filepath) : null);
                $isSuperAdmin = $user->hasRole('SA') || $user->is_super_admin;
                
                $avatarHtml = $avatar 
                    ? '<img src="' . e($avatar) . '" class="rounded-circle border" style="width:42px;height:42px;object-fit:cover;">'
                    : '<div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs" style="width:42px;height:42px;background:' . ($isSuperAdmin ? 'linear-gradient(135deg, #f59e0b, #d97706)' : 'linear-gradient(135deg, #059669, #047857)') . ';font-size:1.05rem;">' . $initial . '</div>';

                return '
                    <div class="d-flex align-items-center gap-2.5">
                        ' . $avatarHtml . '
                        <div class="min-w-0">
                            <div class="fw-bold text-dark text-truncate d-flex align-items-center gap-1.5" style="font-size:0.88rem;">
                                <span>' . e($user->name) . '</span>
                                ' . ($isSuperAdmin ? '<i class="bi bi-patch-check-fill text-warning" title="Super Administrator"></i>' : '') . '
                            </div>
                            <div class="text-muted extra-small font-mono text-truncate" style="font-size:0.75rem;">
                                @' . e($user->username ?? Str::slug($user->name, '')) . '
                            </div>
                        </div>
                    </div>
                ';
            })
            ->addColumn('email_info', function ($user) {
                $googleBadge = $user->google_id 
                    ? '<span class="badge bg-light text-primary border rounded-pill px-2 py-0.5 extra-small" title="Google Account Connected"><i class="bi bi-google me-1"></i> Google</span>'
                    : '<span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 extra-small"><i class="bi bi-envelope me-1"></i> Password</span>';

                return '
                    <div>
                        <div class="font-mono text-dark small text-truncate" style="font-size:0.82rem;">' . e($user->email) . '</div>
                        <div class="mt-1">' . $googleBadge . '</div>
                    </div>
                ';
            })
            ->addColumn('tenant_info', function ($user) {
                if ($user->tenant) {
                    return '
                        <div>
                            <div class="fw-semibold text-dark text-truncate" style="font-size:0.84rem;">
                                <i class="bi bi-shop text-success me-1"></i> ' . e($user->tenant->business_name) . '
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle extra-small font-mono mt-0.5" style="font-size:0.7rem;">
                                ' . e($user->tenant->business_code ?? 'STORE') . '
                            </span>
                        </div>
                    ';
                }

                return '
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">
                        <i class="bi bi-shield-shaded me-1"></i> Platform Root / SA
                    </span>
                ';
            })
            ->addColumn('role_info', function ($user) {
                $roleBadges = $user->roles->map(function ($role) {
                    $color = match ($role->name) {
                        'SA' => 'bg-warning text-dark border-warning',
                        'admin' => 'bg-primary text-white border-primary',
                        'tenant' => 'bg-success text-white border-success',
                        'manager' => 'bg-info text-dark border-info',
                        'cashier' => 'bg-secondary text-white border-secondary',
                        default => 'bg-light text-dark border',
                    };

                    return '<span class="badge ' . $color . ' px-2 py-1 extra-small fw-bold rounded-2">' . strtoupper($role->name) . '</span>';
                })->implode(' ');

                if ($user->roles->isEmpty()) {
                    return '<span class="badge bg-light text-muted border px-2 py-1 extra-small">No Role</span>';
                }

                return '<div class="d-flex flex-wrap gap-1">' . $roleBadges . '</div>';
            })
            ->addColumn('status_info', function ($user) {
                $status = $user->status ?? 'active';
                if ($status === 'active') {
                    return '<span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2.5 py-1 extra-small fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Active</span>';
                } elseif ($status === 'inactive') {
                    return '<span class="badge rounded-pill bg-secondary-subtle text-secondary border px-2.5 py-1 extra-small fw-bold"><i class="bi bi-dash-circle me-1"></i> Inactive</span>';
                }

                return '<span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 extra-small fw-bold"><i class="bi bi-x-circle-fill me-1"></i> ' . ucfirst($status) . '</span>';
            })
            ->addColumn('created_date', function ($user) {
                return '
                    <div class="extra-small text-muted font-mono" style="font-size:0.75rem;">
                        <div>' . $user->created_at->format('M d, Y') . '</div>
                        <div class="opacity-75">' . $user->created_at->format('h:i A') . '</div>
                    </div>
                ';
            })
            ->addColumn('actions', function ($user) {
                $isSelf = auth()->id() === $user->id;
                $encId = encrypt($user->id);
                $primaryRole = $user->roles->first()?->name ?? '';

                return '
                    <div class="dropdown text-end">
                        <button class="btn btn-light border btn-sm rounded-pill px-3 py-1 shadow-xs font-mono extra-small fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Actions <i class="bi bi-chevron-down ms-1"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 py-2" style="min-width: 200px; z-index: 1060;">
                            <li>
                                <button type="button" class="dropdown-item py-1.5 px-3 extra-small d-flex align-items-center gap-2 btn-edit-user"
                                    data-id="' . $encId . '"
                                    data-name="' . e($user->name) . '"
                                    data-username="' . e($user->username) . '"
                                    data-email="' . e($user->email) . '"
                                    data-tenant-id="' . ($user->tenant_id ?? '') . '"
                                    data-role="' . e($primaryRole) . '"
                                    data-status="' . e($user->status ?? 'active') . '">
                                    <i class="bi bi-pencil-square text-primary"></i>
                                    <span>Edit User Info</span>
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item py-1.5 px-3 extra-small d-flex align-items-center gap-2 btn-reset-password"
                                    data-id="' . $encId . '"
                                    data-name="' . e($user->name) . '"
                                    data-email="' . e($user->email) . '">
                                    <i class="bi bi-key-fill text-warning"></i>
                                    <span>Change Password</span>
                                </button>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                ' . ($isSelf ? '
                                    <span class="dropdown-item py-1.5 px-3 extra-small text-muted disabled">
                                        <i class="bi bi-slash-circle me-1"></i> Cannot delete self
                                    </span>
                                ' : '
                                    <button type="button" class="dropdown-item py-1.5 px-3 extra-small text-danger d-flex align-items-center gap-2 btn-delete-user"
                                        data-id="' . $encId . '"
                                        data-name="' . e($user->name) . '">
                                        <i class="bi bi-trash3-fill"></i>
                                        <span>Delete Account</span>
                                    </button>
                                ') . '
                            </li>
                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['user_info', 'email_info', 'tenant_info', 'role_info', 'status_info', 'created_date', 'actions'])
            ->make(true);
    }

    /**
     * Create a new platform user across any tenant or SuperAdmin root
     */
    public function store(Request $request)
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin), 403);

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'username'  => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'password'  => ['required', 'string', 'min:6'],
            'tenant_id' => ['nullable', 'exists:pos_tenants,id'],
            'role'      => ['required', 'string', 'exists:roles,name'],
            'status'    => ['required', 'in:active,inactive,suspended'],
        ]);

        $username = $validated['username'] ?: strtolower(trim(Str::slug($validated['name'], '')));
        // Ensure unique username
        if (User::where('username', $username)->exists()) {
            $username .= rand(100, 999);
        }

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'username'  => $username,
            'password'  => Hash::make($validated['password']),
            'tenant_id' => $validated['tenant_id'] ?: null,
            'status'    => $validated['status'],
        ]);

        $user->syncRoles([$validated['role']]);

        if ($validated['role'] === 'SA') {
            $user->update(['is_super_admin' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => "Platform user '{$user->name}' was successfully created.",
        ]);
    }

    /**
     * Update an existing platform user
     */
    public function update(Request $request, $id)
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin), 403);

        $userId = decrypt($id);
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'username'  => ['nullable', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'password'  => ['nullable', 'string', 'min:6'],
            'tenant_id' => ['nullable', 'exists:pos_tenants,id'],
            'role'      => ['required', 'string', 'exists:roles,name'],
            'status'    => ['required', 'in:active,inactive,suspended'],
        ]);

        $updateData = [
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'tenant_id' => $validated['tenant_id'] ?: null,
            'status'    => $validated['status'],
        ];

        if (!empty($validated['username'])) {
            $updateData['username'] = $validated['username'];
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->syncRoles([$validated['role']]);

        if ($validated['role'] === 'SA') {
            $user->update(['is_super_admin' => true]);
        } else {
            $user->update(['is_super_admin' => false]);
        }

        return response()->json([
            'success' => true,
            'message' => "User '{$user->name}' details were updated successfully.",
        ]);
    }

    /**
     * Change / Reset User Password
     */
    public function resetPassword(Request $request, $id)
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin), 403);

        $userId = decrypt($id);
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Password for '{$user->name}' was successfully reset.",
        ]);
    }

    /**
     * Delete user from system
     */
    public function destroy($id)
    {
        abort_unless(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin), 403);

        $userId = decrypt($id);
        if (auth()->id() === $userId) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own active administrator account.',
            ], 422);
        }

        $user = User::findOrFail($userId);
        $userName = $user->name;
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => "User account '{$userName}' was deleted successfully.",
        ]);
    }
}
