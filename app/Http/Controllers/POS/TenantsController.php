<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSTenant;
use App\Models\POS\POSSubscription;
use App\Models\User;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TenantsController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        abort_unless(
            auth()->check() &&
            auth()->user()->hasRole('SA'),
            403
        );

        return view('pages.tenants.index');
    }

    public function show($id)
    {
        try {

            $id = decrypt($id);

        } catch (\Exception $e) {

            abort(404);

        }

        $previousTenantId = session('tenant_id');

        if ($previousTenantId) {
            Cache::forget('tenant_settings_' . $previousTenantId);
        }

        $tenant = POSTenant::findOrFail($id);
        session([
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->business_name,
            'tenant_code' => $tenant->business_code,
        ]);

        return redirect()
            ->route('dashboard.index')
            ->with(
                'success',
                'Tenant context activated successfully.'
            );
    }

    public function closeContext()
    {
        $tenantId = session('tenant_id');

        if ($tenantId) {

            Cache::forget(
                'tenant_settings_' . $tenantId
            );
        }

        session()->forget([

            'tenant_id',

            'tenant_name',

            'tenant_code',

        ]);

        return redirect()
            ->route('sa.tenants.index')
            ->with(
                'success',
                'Tenant context closed successfully.'
            );
    }

    public function create()
    {
        $subscriptions = POSSubscription::query()
            ->orderBy('name')
            ->get();

        return view(
            'pages.tenants.create',
            compact('subscriptions')
        );
    }

    public function edit($id)
    {
        try {

            $id = decrypt($id);

        } catch (\Exception $e) {

            abort(404);

        }

        $tenant = POSTenant::findOrFail($id);

        $subscriptions = POSSubscription::query()
            ->orderBy('name')
            ->get();

        return view(
            'pages.tenants.create',
            compact(
                'tenant',
                'subscriptions'
            )
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([

            'subscription_id' => [
                'required',
                'exists:pos_subscriptions,id'
            ],

            'business_name' => [
                'required',
                'string',
                'max:255'
            ],

            'business_code' => [
                'required',
                'string',
                'max:100',
                'unique:pos_tenants,business_code'
            ],

            'owner_name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:users,email'
            ],

            'phone' => [
                'nullable',
                'string',
                'max:100'
            ],

            'address' => [
                'nullable',
                'string'
            ],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120'
            ],

            'subscription_start' => [
                'nullable',
                'date'
            ],

            'subscription_end' => [
                'nullable',
                'date',
                'after_or_equal:subscription_start'
            ],

            'trial_ends_at' => [
                'nullable',
                'date'
            ],

            'status' => [
                'required',
                'in:active,inactive,locked,unlocked'
            ],

        ]);

        DB::beginTransaction();

        try {

            $data['business_code'] = strtoupper(
                trim($data['business_code'])
            );

            if ($request->hasFile('logo')) {

                $data['logo'] = $request
                    ->file('logo')
                    ->store(
                        'pos/tenants',
                        'public'
                    );
            }

            $tenant = new POSTenant();

            $tenant->fill($data);
            $this->setCommonFields(
                $tenant,
                $data
            );
            $tenant->save();

            if($tenant){
                $newUser = new User();
                $newUser->name = $data['business_name'];
                $newUser->email = $data['email'];
                $newUser->password = Hash::make($data['email']);
                $newUser->tenant_id = $tenant->id;
                $this->setCommonFields($newUser);
                $newUser->save();

                $newUser->syncRoles('tenants');
                $newUser->syncPermissions('tenants');
            }

            DB::commit();

            return redirect()
                ->route('sa.tenants.index')
                ->with(
                    'success',
                    'Tenant created successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            if (
                isset($data['logo']) &&
                Storage::disk('public')->exists(
                    $data['logo']
                )
            ) {

                Storage::disk('public')
                    ->delete(
                        $data['logo']
                    );
            }

            report($e);

            return back()
                ->withErrors([
                    'general' => 'Unable to create tenant.'
                ])
                ->withInput();
        }
    }

    public function update(
        Request $request,
                $id
    ) {
        try {

            $id = decrypt($id);

        } catch (\Throwable $e) {

            abort(404);

        }

        $tenant = POSTenant::findOrFail($id);

        $data = $request->validate([

            'subscription_id' => [
                'required',
                'exists:pos_subscriptions,id'
            ],

            'business_name' => [
                'required',
                'string',
                'max:255'
            ],

            'business_code' => [
                'required',
                'string',
                'max:100',
                'unique:pos_tenants,business_code,' . $tenant->id
            ],

            'owner_name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'nullable',
                'email',
                'max:255'
            ],

            'phone' => [
                'nullable',
                'string',
                'max:100'
            ],

            'address' => [
                'nullable',
                'string'
            ],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120'
            ],

            'subscription_start' => [
                'nullable',
                'date'
            ],

            'subscription_end' => [
                'nullable',
                'date',
                'after_or_equal:subscription_start'
            ],

            'trial_ends_at' => [
                'nullable',
                'date'
            ],

            'status' => [
                'required',
                'in:active,inactive,locked,unlocked'
            ],

        ]);

        DB::beginTransaction();

        try {

            $data['business_code'] = strtoupper(
                trim($data['business_code'])
            );

            $oldLogo = $tenant->logo;

            if ($request->hasFile('logo')) {

                $data['logo'] = $request
                    ->file('logo')
                    ->store(
                        'pos/tenants',
                        'public'
                    );
            }

            $tenant->fill($data);

            $this->setCommonFields(
                $tenant,
                $data
            );

            $tenant->save();

            if (
                isset($data['logo']) &&
                $oldLogo &&
                Storage::disk('public')->exists(
                    $oldLogo
                )
            ) {

                Storage::disk('public')
                    ->delete(
                        $oldLogo
                    );
            }

            Cache::forget(
                'tenant_settings_' . $tenant->id
            );

            DB::commit();

            return redirect()
                ->route('sa.tenants.index')
                ->with(
                    'success',
                    'Tenant updated successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            if (
                isset($data['logo']) &&
                Storage::disk('public')->exists(
                    $data['logo']
                )
            ) {

                Storage::disk('public')
                    ->delete(
                        $data['logo']
                    );
            }

            report($e);

            return back()
                ->withErrors([
                    'general' => 'Unable to update tenant.'
                ])
                ->withInput();
        }
    }

    public function ajaxData(Request $request)
    {
        $query = POSTenant::query()
            ->with([
                'subscription',
                'creator',
                'products',
                'users',
            ]);

        return datatables()
            ->eloquent($query)
            ->addColumn('actions', function ($tenant) {

                $editUrl = route(
                    'sa.tenants.edit',
                    encrypt($tenant->id)
                );

                $manageUrl = route(
                    'sa.tenants.show',
                    encrypt($tenant->id)
                );

                $modalId = 'tenantActionModal' . $tenant->id;

                $button = '
                    <button
                        class="btn btn-soft-primary btn-sm"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#' . $modalId . '"
                    >
                        <i class="bi bi-gear"></i>
                        Actions
                    </button>
                ';

                $modal = '
                    <div
                        class="modal fade"
                        id="' . $modalId . '"
                        tabindex="-1"
                        aria-hidden="true"
                    >
                        <div class="modal-dialog modal-dialog-centered modal-sm">

                            <div class="modal-content border-0 shadow">

                                <div class="modal-header">

                                    <h5 class="modal-title">
                                        Tenant Actions
                                    </h5>

                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                    ></button>

                                </div>

                                <div class="modal-body p-2">

                                    <div class="d-grid gap-2">

                                        <a
                                            href="' . $editUrl . '"
                                            class="btn btn-light text-start"
                                        >
                                            <i class="bi bi-pencil me-2 text-primary"></i>
                                            Edit Tenant
                                        </a>

                                        <a
                                            href="' . $manageUrl . '"
                                            class="btn btn-light text-start"
                                        >
                                            <i class="bi bi-building me-2 text-success"></i>
                                            Switch Tenant
                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>
                ';

                            return '
                    <div class="text-center">
                        ' . $button . '
                        ' . $modal . '
                    </div>
                ';
            })
            ->addColumn('products_count', function ($tenant) {
                return e($tenant->products->count());
            })
            ->addColumn('users_count', function ($tenant) {
                return e($tenant->users->count());
            })
            ->addColumn('logo', function ($tenant) {

                $src = $tenant->logo
                    ? asset('storage/' . $tenant->logo)
                    : asset('images/no-business.png');

                return '
                    <img
                        src="' . $src . '"
                        style="
                            width:48px;
                            height:48px;
                            border-radius:12px;
                            object-fit:cover;
                        "
                    >
                ';
            })

            ->addColumn('business', function ($tenant) {

                return '
                    <div class="fw-semibold">
                        ' . e($tenant->business_name) . '
                    </div>

                    <div class="small text-muted">
                        ' . e($tenant->email) . '
                    </div>
                ';
            })

            ->addColumn('owner', function ($tenant) {

                return '
                    <div class="fw-medium">
                        ' . e($tenant->owner_name) . '
                    </div>

                    <div class="small text-muted">
                        ' . e($tenant->phone) . '
                    </div>
                ';
            })

            ->addColumn('subscription', function ($tenant) {

                return $tenant->subscription?->name ?? '-';
            })

            ->addColumn('status', function ($tenant) {

                return match ($tenant->status) {

                    'active' => '<span class="badge bg-success">Active</span>',

                    'inactive' => '<span class="badge bg-secondary">Inactive</span>',

                    'locked' => '<span class="badge bg-danger">Locked</span>',

                    'unlocked' => '<span class="badge bg-info">Unlocked</span>',

                    default => '<span class="badge bg-warning">Unknown</span>',
                };
            })

            ->editColumn('created_at', function ($tenant) {

                return $tenant->created_at?->format(
                    'M d, Y h:i A'
                );
            })

            ->rawColumns([
                'actions',
                'logo',
                'business',
                'owner',
                'status',
            ])

            ->make(true);
    }
}
