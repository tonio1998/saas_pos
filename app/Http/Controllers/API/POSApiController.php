<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\POS\POSCustomerLedger;
use App\Models\POS\POSCustomers;
use App\Models\POS\POSProducts;
use App\Models\POS\POSSale;
use App\Models\POS\POSSaleItem;
use App\Models\POS\POSPayment;
use App\Models\POS\InventoryMovement;
use App\Models\POS\POSTenant;
use App\Models\POS\POSCategories;
use App\Models\POS\POSUnits;
use App\Models\POS\POSProductVariant;


use App\Models\User;
use App\Services\SecurityService;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class POSApiController extends Controller
{
    public function login(Request $request)
    {
        $login = trim($request->input('login', $request->input('email', '')));
        $password = $request->input('password', '');

        if (empty($login) || empty($password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email/Username and Password are required.',
            ], 422);
        }

        $user = User::where('email', $login)->orWhere('username', $login)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $tenant = POSTenant::find($user->tenant_id);
        $plainToken = base64_encode($user->id . '|' . Str::random(32));

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token'   => $plainToken,
            'api_token' => $plainToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar ?? null,
                'role' => $user->getRoleNames()->first() ?? 'cashier',
                'tenant_id' => $user->tenant_id,
            ],
            'roles' => method_exists($user, 'getRoleNames') ? $user->getRoleNames() : ['cashier'],
            'permissions' => method_exists($user, 'getAllPermissions') ? $user->getAllPermissions()->pluck('name') : [],
            'tenant' => $tenant ? [
                'id' => $tenant->id,
                'business_name' => $tenant->business_name,
                'tin' => $tenant->tin,
                'branch_code' => $tenant->branch_code,
                'payment_status' => $tenant->payment_status,
                'is_active' => method_exists($tenant, 'isActive') ? $tenant->isActive() : true,
            ] : null,
        ]);
    }

    public function loginWithGoogle(Request $request)
    {
        $googleToken = $request->input('token'); // ID token from Google SDK
        $email = $request->input('email');
        $name = $request->input('name');
        $photo = $request->input('photo');

        // If Google ID token is provided (like in UISFIX), verify with Google OAuth2 endpoint
        if ($googleToken) {
            try {
                $http = \Illuminate\Support\Facades\Http::timeout(15);
                if (!app()->environment('production')) {
                    $http = $http->withoutVerifying();
                }
                $googleResponse = $http->get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $googleToken,
                ]);

                if ($googleResponse->ok()) {
                    $googleUser = $googleResponse->json();
                    $email = $googleUser['email'] ?? $email;
                    $name = $googleUser['name'] ?? $name ?? $email;
                    $photo = $googleUser['picture'] ?? $photo;
                }
            } catch (\Throwable $e) {
                // Fallback to provided email and name if offline/local development
            }
        }

        if (empty($email)) {
            return response()->json([
                'success' => false,
                'message' => 'Valid Google email is required.',
            ], 422);
        }

        $email = strtolower(trim($email));
        $user = User::where('email', $email)->first();

        if (!$user) {
            $defaultTenant = POSTenant::first();
            $tenantId = $defaultTenant ? $defaultTenant->id : 1;

            $user = User::create([
                'name'      => $name ?? explode('@', $email)[0],
                'email'     => $email,
                'username'  => explode('@', $email)[0] . rand(100, 999),
                'password'  => Hash::make(Str::random(16)),
                'tenant_id' => $tenantId,
                'verified'  => 1,
                'avatar'    => $photo,
            ]);

            try {
                $role = Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
                $user->assignRole($role);
            } catch (\Throwable $e) {
                // role assignment fallback
            }
        } else {
            if ($photo && empty($user->avatar)) {
                $user->avatar = $photo;
                $user->save();
            }
        }

        $tenant = POSTenant::find($user->tenant_id);
        $plainToken = base64_encode($user->id . '|' . Str::random(32));

        return response()->json([
            'success' => true,
            'message' => 'Google authentication successful',
            'token'   => $plainToken,
            'api_token' => $plainToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar ?? $photo,
                'role' => $user->getRoleNames()->first() ?? 'cashier',
                'tenant_id' => $user->tenant_id,
            ],
            'roles' => method_exists($user, 'getRoleNames') ? $user->getRoleNames() : ['cashier'],
            'permissions' => method_exists($user, 'getAllPermissions') ? $user->getAllPermissions()->pluck('name') : [],
            'tenant' => $tenant ? [
                'id' => $tenant->id,
                'business_name' => $tenant->business_name,
                'tin' => $tenant->tin,
                'branch_code' => $tenant->branch_code,
                'payment_status' => $tenant->payment_status,
                'is_active' => method_exists($tenant, 'isActive') ? $tenant->isActive() : true,
            ] : null,
        ]);
    }



    protected function getAuthenticatedUser(Request $request): ?User
    {
        $bearer = $request->bearerToken();
        if ($bearer) {
            $decoded = base64_decode($bearer);
            if ($decoded && str_contains($decoded, '|')) {
                $userId = explode('|', $decoded)[0];
                return User::find($userId);
            }
        }
        return $request->user();
    }

    public function products(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = $user?->tenant_id ?? $request->input('tenant_id');

        $query = POSProducts::query();
        if ($tenantId) {
            $query->where(function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId)->orWhere('tenant_id', 0);
            });
        }

        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->search);
            $stripped = ltrim($search, '0');
            $query->where(function ($q) use ($search, $stripped) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");

                if (!empty($stripped)) {
                    $q->orWhere('barcode', 'like', "%{$stripped}%");
                }

                $q->orWhereHas('variants', function ($vq) use ($search, $stripped) {
                    $vq->where('name', 'like', "%{$search}%")
                       ->orWhere('barcode', 'like', "%{$search}%")
                       ->orWhere('sku', 'like', "%{$search}%");
                    if (!empty($stripped)) {
                        $vq->orWhere('barcode', 'like', "%{$stripped}%");
                    }
                });
            });
        }

        $products = $query->with(['category', 'unit', 'variants'])->orderBy('name')->get();

        // Masterlist barcode fallback across tenants if search returned 0 items
        if ($request->has('search') && !empty($request->search) && $products->isEmpty()) {
            $search = trim($request->search);
            $stripped = ltrim($search, '0');
            $products = POSProducts::query()
                ->where(function ($q) use ($search, $stripped) {
                    $q->where('barcode', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                    if (!empty($stripped)) {
                        $q->orWhere('barcode', 'like', "%{$stripped}%");
                    }
                })
                ->with(['category', 'unit', 'variants'])
                ->get();
        }

        $products->transform(function ($p) {
            if ($p->image) {
                if (str_starts_with($p->image, 'http')) {
                    $p->image_url = $p->image;
                } else {
                    $p->image_url = asset('storage/' . ltrim($p->image, '/'));
                }
            } else {
                $p->image_url = null;
            }
            return $p;
        });

        return response()->json([
            'success' => true,
            'count' => $products->count(),
            'data' => $products,
        ]);
    }

    public function storeProduct(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = (int) ($user?->tenant_id ?? $request->input('tenant_id', 1));
        $cashierId = (int) ($user?->id ?? $request->input('cashier_id', 1));


        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'barcode'         => 'nullable|string|max:100',
            'selling_price'   => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'cost_price'      => 'nullable|numeric|min:0',
            'stock_on_hand'   => 'nullable|numeric|min:0',
            'category_id'     => 'nullable|integer',
            'unit_id'         => 'nullable|integer',
            'reorder_level'   => 'nullable|numeric|min:0',
            'image_base64'    => 'nullable|string',
        ]);

        $barcode = !empty($validated['barcode']) ? trim($validated['barcode']) : null;
        $sku = 'SKU-' . strtoupper(Str::random(8));

        // 1. Strict Duplicate Barcode check in current store
        if (!empty($barcode)) {
            $existingBarcodeProduct = POSProducts::where('tenant_id', $tenantId)
                ->where(function ($q) use ($barcode) {
                    $stripped = ltrim($barcode, '0');
                    $q->where('barcode', $barcode);
                    if (!empty($stripped)) {
                        $q->orWhere('barcode', $stripped);
                    }
                })
                ->where('status', '!=', 'deleted')
                ->first();

            if ($existingBarcodeProduct) {
                return response()->json([
                    'success' => false,
                    'is_duplicate' => true,
                    'message' => "Barcode \"{$barcode}\" is already registered to \"{$existingBarcodeProduct->name}\" in this store (Stock: {$existingBarcodeProduct->stock_on_hand}).",
                    'existing_product' => $existingBarcodeProduct->load(['category', 'unit', 'variants']),
                ], 422);
            }
        }

        // 2. Strict Duplicate Product Name check in current store
        $existingNameProduct = POSProducts::where('tenant_id', $tenantId)
            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($validated['name']))])
            ->where('status', '!=', 'deleted')
            ->first();

        if ($existingNameProduct) {
            return response()->json([
                'success' => false,
                'is_duplicate' => true,
                'message' => "A product named \"{$existingNameProduct->name}\" already exists in this store (Barcode: {$existingNameProduct->barcode}).",
                'existing_product' => $existingNameProduct->load(['category', 'unit', 'variants']),
            ], 422);
        }

        $categoryId = $validated['category_id'] ?? POSCategories::where(function($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId)->orWhere('tenant_id', 0);
        })->value('id') ?? 1;

        $unitId = $validated['unit_id'] ?? POSUnits::where(function($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId)->orWhere('tenant_id', 0);
        })->value('id') ?? 2;

        $product = new POSProducts();
        $product->tenant_id = $tenantId;
        $product->name = $validated['name'];
        $product->barcode = $barcode;

        $product->sku = $sku;
        $product->selling_price = $validated['selling_price'];
        $product->wholesale_price = $validated['wholesale_price'] ?? $validated['selling_price'];
        $product->cost_price = $validated['cost_price'] ?? round($validated['selling_price'] * 0.8, 2);
        $product->stock_on_hand = $validated['stock_on_hand'] ?? 50;
        $product->category_id = $categoryId;
        $product->unit_id = $unitId;
        $product->reorder_level = $validated['reorder_level'] ?? 5;
        $product->allow_decimal_qty = 0;
        $product->created_by = $cashierId;
        $product->updated_by = $cashierId;
        $product->status = 'active';

        // Image Base64 Upload
        if (!empty($validated['image_base64'])) {
            $imgData = $validated['image_base64'];
            $ext = 'jpg';
            if (preg_match('/^data:image\/(\w+);base64,/', $imgData, $type)) {
                $imgData = substr($imgData, strpos($imgData, ',') + 1);
                $ext = strtolower($type[1]) === 'jpeg' ? 'jpg' : strtolower($type[1]);
            }
            $decodedImg = base64_decode($imgData);
            if ($decodedImg !== false) {
                $filename = 'products/' . Str::random(40) . '.' . $ext;
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $decodedImg);
                $product->image = $filename;
            }
        }

        $product->save();

        $product->load(['category', 'unit', 'variants']);
        if ($product->image) {
            $product->image_url = asset('storage/' . ltrim($product->image, '/'));
        }


        return response()->json([
            'success' => true,
            'message' => 'Product registered successfully.',
            'product' => $product,
        ]);
    }

    public function stockIn(Request $request, $id)
    {
        $user = $this->getAuthenticatedUser($request);
        $cashierId = (int) ($user?->id ?? $request->input('cashier_id', 1));

        $validated = $request->validate([
            'qty'   => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        $product = POSProducts::findOrFail($id);
        $product->stock_on_hand = (float)($product->stock_on_hand ?? 0) + (float)$validated['qty'];
        $product->save();

        // Record Inventory Movement
        $inv = new InventoryMovement();
        $inv->tenant_id = $product->tenant_id;
        $inv->product_id = $product->id;
        $inv->movement_type = 'stock_in';
        $inv->reference_type = 'quick_restock';
        $inv->reference_id = $product->id;
        $inv->qty = $validated['qty'];
        $inv->created_by = $cashierId;
        $inv->updated_by = $cashierId;
        $inv->status = 'active';
        $inv->save();

        $product->load(['category', 'unit', 'variants']);

        return response()->json([
            'success' => true,
            'message' => "Successfully added +{$validated['qty']} stock to {$product->name}.",
            'product' => $product,
        ]);
    }


    public function lookupGlobalProduct(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        $currentTenantId = (int) ($user?->tenant_id ?? $request->input('tenant_id', 1));

        $barcode = trim($request->input('barcode', ''));
        $name = trim($request->input('name', ''));

        if (empty($barcode) && empty($name)) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $stripped = ltrim($barcode, '0');

        $query = POSProducts::query()
            ->with(['category', 'unit']);

        if (!empty($barcode)) {
            $query->where(function ($q) use ($barcode, $stripped) {
                $q->where('barcode', $barcode)
                  ->orWhere('barcode', 'like', "%{$barcode}%")
                  ->orWhere('sku', $barcode);
                if (!empty($stripped)) {
                    $q->orWhere('barcode', 'like', "%{$stripped}%");
                }
            });
        } elseif (!empty($name)) {
            $query->where('name', 'like', "%{$name}%");
        }

        $matches = $query->take(6)->get();

        return response()->json([
            'success' => true,
            'count' => $matches->count(),
            'data' => $matches->map(function ($p) use ($currentTenantId) {
                $tenant = POSTenant::find($p->tenant_id);
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'barcode' => $p->barcode,
                    'sku' => $p->sku,
                    'selling_price' => (float)$p->selling_price,
                    'wholesale_price' => (float)$p->wholesale_price,
                    'cost_price' => (float)$p->cost_price,
                    'stock_on_hand' => (float)$p->stock_on_hand,
                    'category_id' => $p->category_id,
                    'category_name' => $p->category?->name,
                    'unit_id' => $p->unit_id,
                    'unit_name' => $p->unit?->name,
                    'store_name' => $tenant?->business_name ?? 'SaaS Network Store',
                    'tenant_id' => $p->tenant_id,
                    'is_current_store' => ((int)$p->tenant_id === (int)$currentTenantId),
                ];
            }),
        ]);
    }



    public function categories(Request $request)
    {
        $tenantId = $request->user()?->tenant_id ?? $request->input('tenant_id', 1);
        $categories = POSCategories::where(function($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId)->orWhere('tenant_id', 0);
        })->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function units(Request $request)
    {
        $tenantId = $request->user()?->tenant_id ?? $request->input('tenant_id', 1);
        $units = POSUnits::where(function($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId)->orWhere('tenant_id', 0);
        })->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $units,
        ]);
    }

    public function customers(Request $request)

    {
        $tenantId = $request->user()?->tenant_id ?? $request->input('tenant_id');
        $query = POSCustomers::query()->with('credit');

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($request->has('customer_type') && !empty($request->customer_type) && $request->customer_type !== 'all') {
            $query->where('customer_type', $request->customer_type);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('CustomerName', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%")
                  ->orWhere('customer_code', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('CustomerName')->get()->map(function ($cust) {
            $runningBalance = $cust->credit ? (float)$cust->credit->running_balance : (float)($cust->current_balance ?? 0);
            return [
                'id' => $cust->id,
                'tenant_id' => $cust->tenant_id,
                'customer_code' => $cust->customer_code,
                'CustomerName' => $cust->CustomerName,
                'name' => $cust->CustomerName,
                'company_name' => $cust->company_name,
                'email' => $cust->email,
                'mobile_number' => $cust->mobile_number,
                'phone' => $cust->mobile_number,
                'CustomerAddress' => $cust->CustomerAddress,
                'address' => $cust->CustomerAddress,
                'customer_type' => $cust->customer_type ?? 'regular',
                'discount_percent' => (float)($cust->discount_percent ?? 0),
                'credit_limit' => (float)($cust->credit_limit ?? 5000),
                'running_balance' => $runningBalance,
                'TotalPoints' => (int)($cust->TotalPoints ?? 0),
                'remarks' => $cust->remarks,
                'created_at' => $cust->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $customers->count(),
            'data' => $customers,
        ]);
    }

    public function showCustomer(Request $request, $id)
    {
        $tenantId = $request->user()?->tenant_id ?? $request->input('tenant_id');
        $query = POSCustomers::query()->with(['credit', 'ledger' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(20);
        }]);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $customer = $query->findOrFail($id);
        $runningBalance = $customer->credit ? (float)$customer->credit->running_balance : (float)($customer->current_balance ?? 0);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $customer->id,
                'tenant_id' => $customer->tenant_id,
                'customer_code' => $customer->customer_code,
                'CustomerName' => $customer->CustomerName,
                'name' => $customer->CustomerName,
                'company_name' => $customer->company_name,
                'email' => $customer->email,
                'mobile_number' => $customer->mobile_number,
                'phone' => $customer->mobile_number,
                'CustomerAddress' => $customer->CustomerAddress,
                'address' => $customer->CustomerAddress,
                'customer_type' => $customer->customer_type ?? 'regular',
                'discount_percent' => (float)($customer->discount_percent ?? 0),
                'credit_limit' => (float)($customer->credit_limit ?? 5000),
                'running_balance' => $runningBalance,
                'TotalPoints' => (int)($customer->TotalPoints ?? 0),
                'remarks' => $customer->remarks,
                'ledger' => $customer->ledger,
            ]
        ]);
    }

    public function storeCustomer(Request $request)
    {
        $tenantId = $request->user()?->tenant_id ?? $request->input('tenant_id', 1);

        $validated = $request->validate([
            'CustomerName'     => 'required_without:name|string|max:255',
            'name'             => 'nullable|string|max:255',
            'company_name'     => 'nullable|string|max:255',
            'email'            => 'nullable|email|max:255',
            'mobile_number'    => 'nullable|string|max:50',
            'phone'            => 'nullable|string|max:50',
            'CustomerAddress'  => 'nullable|string|max:255',
            'address'          => 'nullable|string|max:255',
            'customer_type'    => 'nullable|string|in:walkin,regular,business,senior,pwd,credit',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'credit_limit'     => 'nullable|numeric|min:0',
            'remarks'          => 'nullable|string',
        ]);

        $name = $validated['CustomerName'] ?? $validated['name'];
        $phone = $validated['mobile_number'] ?? $validated['phone'] ?? null;
        $address = $validated['CustomerAddress'] ?? $validated['address'] ?? null;
        $customerType = $validated['customer_type'] ?? 'regular';
        $creditLimit = (float)($validated['credit_limit'] ?? 5000);
        $discountPercent = (float)($validated['discount_percent'] ?? 0);

        $customer = new POSCustomers();
        $customer->tenant_id = $tenantId;
        $customer->CustomerName = $name;
        $customer->company_name = $validated['company_name'] ?? null;
        $customer->email = $validated['email'] ?? null;
        $customer->mobile_number = $phone;
        $customer->CustomerAddress = $address;
        $customer->customer_type = $customerType;
        $customer->discount_percent = $discountPercent;
        $customer->credit_limit = $creditLimit;
        $customer->current_balance = 0;
        $customer->remarks = $validated['remarks'] ?? null;
        $customer->status = 'active';
        $customer->created_by = $request->user()?->id ?? 1;
        $customer->updated_by = $request->user()?->id ?? 1;
        $customer->save();

        $customer->customer_code = getCustomerCode($customer->id);
        $customer->save();

        POSCustomerLedger::create([
            'tenant_id' => $tenantId,
            'customer_id' => $customer->id,
            'transaction_type' => 'CUSTOMER_REGISTRATION',
            'debit' => 0,
            'credit' => 0,
            'running_balance' => 0,
            'remarks' => 'Initial CRM Registration',
            'created_by' => $request->user()?->id ?? 1,
            'updated_by' => $request->user()?->id ?? 1,
            'status' => 'active',
            'archived' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Customer registered successfully.',
            'data' => $customer,
        ]);
    }

    public function updateCustomer(Request $request, $id)
    {
        $customer = POSCustomers::findOrFail($id);

        $validated = $request->validate([
            'CustomerName'     => 'nullable|string|max:255',
            'name'             => 'nullable|string|max:255',
            'company_name'     => 'nullable|string|max:255',
            'email'            => 'nullable|email|max:255',
            'mobile_number'    => 'nullable|string|max:50',
            'phone'            => 'nullable|string|max:50',
            'CustomerAddress'  => 'nullable|string|max:255',
            'address'          => 'nullable|string|max:255',
            'customer_type'    => 'nullable|string',
            'discount_percent' => 'nullable|numeric|min:0',
            'credit_limit'     => 'nullable|numeric|min:0',
            'remarks'          => 'nullable|string',
        ]);

        if (isset($validated['CustomerName']) || isset($validated['name'])) {
            $customer->CustomerName = $validated['CustomerName'] ?? $validated['name'];
        }
        if (isset($validated['mobile_number']) || isset($validated['phone'])) {
            $customer->mobile_number = $validated['mobile_number'] ?? $validated['phone'];
        }
        if (isset($validated['CustomerAddress']) || isset($validated['address'])) {
            $customer->CustomerAddress = $validated['CustomerAddress'] ?? $validated['address'];
        }
        if (isset($validated['company_name'])) $customer->company_name = $validated['company_name'];
        if (isset($validated['email'])) $customer->email = $validated['email'];
        if (isset($validated['customer_type'])) $customer->customer_type = $validated['customer_type'];
        if (isset($validated['discount_percent'])) $customer->discount_percent = $validated['discount_percent'];
        if (isset($validated['credit_limit'])) $customer->credit_limit = $validated['credit_limit'];
        if (isset($validated['remarks'])) $customer->remarks = $validated['remarks'];

        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Customer profile updated successfully.',
            'data' => $customer,
        ]);
    }

    public function updateCreditLimit(Request $request, $id)
    {
        $validated = $request->validate([
            'credit_limit' => 'required|numeric|min:0',
        ]);

        $customer = POSCustomers::findOrFail($id);
        $customer->credit_limit = $validated['credit_limit'];
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Customer credit limit updated.',
            'credit_limit' => $customer->credit_limit,
        ]);
    }

    public function customerLedger(Request $request, $id)
    {
        $ledger = POSCustomerLedger::with(['sale', 'createdBy'])
            ->where('customer_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $ledger,
        ]);
    }

    public function customerSales(Request $request, $id)
    {
        $sales = POSSale::with(['items', 'payments'])
            ->where('customer_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sales,
        ]);
    }

    public function payUtangCollection(Request $request)
    {
        $validated = $request->validate([
            'customer_id'    => 'required|integer',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string',
            'reference_no'   => 'nullable|string|max:50',
            'remarks'        => 'nullable|string|max:255',
        ]);

        $tenantId = $request->user()?->tenant_id ?? $request->input('tenant_id', 1);
        $customerId = $validated['customer_id'];
        $customer = POSCustomers::findOrFail($customerId);

        $lastBalance = POSCustomerLedger::where('customer_id', $customerId)
            ->latest('id')
            ->value('running_balance') ?? (float)($customer->current_balance ?? 0);

        if ($validated['amount'] > $lastBalance) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount (₱' . number_format($validated['amount'], 2) . ') exceeds outstanding utang balance (₱' . number_format($lastBalance, 2) . ').',
            ], 422);
        }

        $runningBalance = max(0, $lastBalance - $validated['amount']);

        DB::transaction(function () use ($validated, $tenantId, $customerId, $customer, $runningBalance, $request) {
            $payment = new POSPayment();
            $payment->customer_id = $customerId;
            $payment->tenant_id = $tenantId;
            $payment->payment_date = now();
            $payment->payment_method = $validated['payment_method'] ?? 'cash';
            $payment->amount = $validated['amount'];
            $payment->tendered_amount = $validated['amount'];
            $payment->change_amount = 0;
            $payment->reference_number = $validated['reference_no'] ?? ('COL-' . strtoupper(Str::random(6)));
            $payment->notes = $validated['remarks'] ?? 'Suki Utang Collection Payment';
            $payment->save();

            $ledger = new POSCustomerLedger();
            $ledger->tenant_id = $tenantId;
            $ledger->customer_id = $customerId;
            $ledger->payment_id = $payment->id;
            $ledger->reference_no = $payment->reference_number;
            $ledger->transaction_type = 'PAYMENT';
            $ledger->debit = 0;
            $ledger->credit = $validated['amount'];
            $ledger->running_balance = $runningBalance;
            $ledger->remarks = $validated['remarks'] ?? 'Suki Debt Settlement Payment';
            $ledger->created_by = $request->user()?->id ?? 1;
            $ledger->updated_by = $request->user()?->id ?? 1;
            $ledger->status = 'active';
            $ledger->save();

            $customer->current_balance = $runningBalance;
            $customer->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Collection payment of ₱' . number_format($validated['amount'], 2) . ' recorded successfully.',
            'running_balance' => $runningBalance,
        ]);
    }

    public function createSale(Request $request)
    {
        $tenantId = (int) ($request->user()?->tenant_id ?? $request->input('tenant_id', 1));
        $cashierId = (int) ($request->user()?->id ?? $request->input('cashier_id', 1));

        $validated = $request->validate([
            'tenant_id'       => 'nullable|integer',
            'cashier_id'      => 'nullable|integer',
            'customer_id'     => 'nullable|integer',
            'subtotal'        => 'required|numeric|min:0',
            'discount'        => 'required|numeric|min:0',
            'total'           => 'required|numeric|min:0',
            'discount_type'   => 'nullable|string',
            'discount_holder' => 'nullable|string',
            'discount_id_no'  => 'nullable|string',
            'payments'        => 'required|array|min:1',
            'payments.*.method' => 'required|string',
            'payments.*.amount' => 'required|numeric|min:0.01',
            'payments.*.reference_no' => 'nullable|string',
            'payments.*.reference_number' => 'nullable|string',
            'items'           => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.variant_id' => 'nullable|integer',
            'items.*.qty'     => 'required|numeric|min:0.01',
            'items.*.price'   => 'required|numeric|min:0',
        ]);

        $totalPaid = collect($validated['payments'])->sum('amount');
        $isChargeToCredit = collect($validated['payments'])->contains(function ($p) {
            return in_array(strtolower($p['method']), ['credit', 'utang', 'charge', 'charge_to_account', 'account']);
        });

        // Credit limit check
        if ($isChargeToCredit) {
            if (empty($validated['customer_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'A customer must be selected to charge sale to Utang / Credit account.',
                ], 422);
            }

            $customer = POSCustomers::findOrFail($validated['customer_id']);
            $currentDebt = POSCustomerLedger::where('customer_id', $customer->id)->latest('id')->value('running_balance') ?? (float)($customer->current_balance ?? 0);
            $creditLimit = (float)($customer->credit_limit ?? 5000);

            if (($currentDebt + $validated['total']) > $creditLimit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sale amount exceeds Suki credit limit. Current utang: ₱' . number_format($currentDebt, 2) . ', Credit Limit: ₱' . number_format($creditLimit, 2),
                ], 422);
            }
        } elseif ($totalPaid < $validated['total']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient payment tendered.',
            ], 422);
        }

        $change = max(0, $totalPaid - $validated['total']);
        $sale = null;

        try {
            DB::transaction(function () use ($validated, $tenantId, $cashierId, $totalPaid, $change, $isChargeToCredit, &$sale, $request) {
                $discountType = $validated['discount_type'] ?? null;
                $isScPwd = in_array(strtolower($discountType ?? ''), ['sc_pwd', 'senior', 'pwd']) || !empty($validated['discount_id_no']);

                $subtotal = $validated['subtotal'];
                $total = $validated['total'];
                $taxAmount = ($isScPwd || $discountType === 'zero_rated') ? 0 : round($total - ($total / 1.12), 2);

                $refNumbers = collect($validated['payments'])
                    ->map(fn($p) => $p['reference_no'] ?? ($p['reference_number'] ?? null))
                    ->filter()
                    ->implode(', ');

                $sale = new POSSale();
                $sale->tenant_id = $tenantId;
                $sale->invoice_no = generateSaleInvoiceNo();
                $sale->cashier_id = $cashierId;
                $sale->customer_id = $validated['customer_id'] ?? null;
                $sale->payment_method = count($validated['payments']) === 1 ? $validated['payments'][0]['method'] : 'split';
                $sale->subtotal = $subtotal;
                $sale->discount_amount = $validated['discount'] ?? 0;
                $sale->discount_type = $discountType;
                $sale->discount_holder = $validated['discount_holder'] ?? null;
                $sale->discount_id_no = $validated['discount_id_no'] ?? null;
                $sale->tax_amount = $taxAmount;
                $sale->total_amount = $total;
                $sale->reference_number = $refNumbers ?: null;
                $sale->notes = $request->input('notes', 'Mobile POS Sale');
                $sale->sale_status = 'completed';
                $sale->sale_date = now();
                $sale->created_by = $cashierId;
                $sale->updated_by = $cashierId;
                $sale->status = 'active';
                $sale->save();

                foreach ($validated['items'] as $item) {
                    $product = POSProducts::findOrFail($item['product_id']);
                    $newSaleItem = new POSSaleItem();
                    $newSaleItem->sale_id = $sale->id;
                    $newSaleItem->product_id = $product->id;
                    $newSaleItem->barcode = $product->barcode;
                    $newSaleItem->sku = $product->sku;
                    $newSaleItem->product_name = $product->name;
                    $newSaleItem->qty = $item['qty'];
                    $newSaleItem->unit_price = $item['price'];
                    $newSaleItem->discount_amount = 0;
                    $newSaleItem->tax_amount = 0;
                    $newSaleItem->line_total = $item['qty'] * $item['price'];
                    $newSaleItem->created_by = $cashierId;
                    $newSaleItem->updated_by = $cashierId;
                    $newSaleItem->status = 'active';
                    $newSaleItem->save();

                    // Record Inventory Movement
                    $newInv = new InventoryMovement();
                    $newInv->tenant_id = $tenantId;
                    $newInv->product_id = $product->id;
                    $newInv->movement_type = 'sale';
                    $newInv->reference_type = 'sale';
                    $newInv->reference_id = $sale->id;
                    $newInv->qty = -1 * $item['qty'];
                    $newInv->created_by = $cashierId;
                    $newInv->updated_by = $cashierId;
                    $newInv->status = 'active';
                    $newInv->save();

                    // Safe stock update
                    $curStock = (float)($product->stock_on_hand ?? 0);
                    $product->stock_on_hand = max(0, $curStock - (float)$item['qty']);
                    $product->save();

                    // Variant stock update if applicable
                    if (!empty($item['variant_id'])) {
                        $variant = \App\Models\POS\POSProductVariant::find($item['variant_id']);
                        if ($variant) {
                            $vStock = (float)($variant->stock_on_hand ?? 0);
                            $variant->stock_on_hand = max(0, $vStock - (float)$item['qty']);
                            $variant->save();
                        }
                    }
                }

                foreach ($validated['payments'] as $index => $p) {
                    $refNo = $p['reference_no'] ?? ($p['reference_number'] ?? null);

                    $newPayment = new POSPayment();
                    $newPayment->sale_id = $sale->id;
                    $newPayment->customer_id = $validated['customer_id'] ?? null;
                    $newPayment->tenant_id = $tenantId;
                    $newPayment->payment_method = $p['method'];
                    $newPayment->amount = $p['amount'];
                    $newPayment->tendered_amount = $p['amount'];
                    $newPayment->change_amount = $index === 0 ? $change : 0;
                    $newPayment->reference_number = $refNo;
                    $newPayment->payment_date = now();
                    $newPayment->created_by = $cashierId;
                    $newPayment->updated_by = $cashierId;
                    $newPayment->status = 'active';
                    $newPayment->save();
                }

                // Customer loyalty points & credit handling
                if (!empty($validated['customer_id'])) {
                    $customerId = $validated['customer_id'];
                    $customer = POSCustomers::find($customerId);

                    if ($customer) {
                        // Suki loyalty points (+1 point per ₱100)
                        $earnedPoints = max(1, (int) floor($total / 100));
                        $customer->increment('TotalPoints', $earnedPoints);

                        // If charged to credit / utang, record to customer ledger
                        if ($isChargeToCredit) {
                            $lastBalance = POSCustomerLedger::where('customer_id', $customerId)->latest('id')->value('running_balance') ?? (float)($customer->current_balance ?? 0);
                            $newBalance = $lastBalance + $total;

                            $ledger = new POSCustomerLedger();
                            $ledger->tenant_id = $tenantId;
                            $ledger->customer_id = $customerId;
                            $ledger->sale_id = $sale->id;
                            $ledger->reference_no = $sale->invoice_no;
                            $ledger->transaction_type = 'SALE_CHARGE_CREDIT';
                            $ledger->debit = $total;
                            $ledger->credit = 0;
                            $ledger->running_balance = $newBalance;
                            $ledger->remarks = 'POS Charge to Utang Account #' . $sale->invoice_no;
                            $ledger->created_by = $cashierId;
                            $ledger->updated_by = $cashierId;
                            $ledger->status = 'active';
                            $ledger->save();

                            $customer->current_balance = $newBalance;
                            $customer->save();
                        }
                    }
                }
            });

            $sale->load(['items', 'customer', 'payments']);

            return response()->json([
                'success' => true,
                'message' => 'Sale transaction completed successfully.',
                'sale' => $sale,
            ]);
        } catch (\Throwable $e) {
            \Log::error('POSApiController createSale error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Transaction failed: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function saleDetails($id)
    {
        $sale = POSSale::with(['items.product', 'customer', 'cashier', 'payments'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $sale,
        ]);
    }

    public function salesList(Request $request)

    {
        $tenantId = $request->user()?->tenant_id ?? $request->input('tenant_id');

        $query = POSSale::query();
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('sale_code', 'like', "%{$search}%");
            });
        }

        $sales = $query->with(['items', 'customer', 'payments'])
            ->orderBy('id', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $sales->items(),
            'total' => $sales->total(),
            'current_page' => $sales->currentPage(),
            'last_page' => $sales->lastPage(),
        ]);
    }
}

