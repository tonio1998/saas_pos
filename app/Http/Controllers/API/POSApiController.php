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
use App\Models\POS\POSCashShift;
use App\Models\POS\POSCashDrawer;
use App\Models\POS\POSPromotion;
use App\Models\POS\POSPromotionItem;
use App\Models\POS\POSCashTransaction;
use Carbon\Carbon;


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
                'business_code' => $tenant->business_code,
                'owner_name' => $tenant->owner_name,
                'email' => $tenant->email,
                'phone' => $tenant->phone,
                'address' => $tenant->address,
                'logo' => $tenant->logo,
                'tin' => $tenant->tin,
                'branch_code' => $tenant->branch_code,
                'bir_acc_no' => $tenant->bir_acc_no,
                'bir_acc_date' => $tenant->bir_acc_date,
                'bir_min' => $tenant->bir_min,
                'bir_sn' => $tenant->bir_sn,
                'header_text' => $tenant->header_text,
                'footer_text' => $tenant->footer_text,
                'currency_symbol' => $tenant->currency_symbol ?? '₱',
                'payment_status' => $tenant->payment_status,
                'is_active' => method_exists($tenant, 'isActive') ? $tenant->isActive() : true,
                'theme_settings' => $tenant->theme_settings,
                'crm_settings' => $tenant->crm_settings,
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
                'business_code' => $tenant->business_code,
                'owner_name' => $tenant->owner_name,
                'email' => $tenant->email,
                'phone' => $tenant->phone,
                'address' => $tenant->address,
                'logo' => $tenant->logo,
                'tin' => $tenant->tin,
                'branch_code' => $tenant->branch_code,
                'bir_acc_no' => $tenant->bir_acc_no,
                'bir_acc_date' => $tenant->bir_acc_date,
                'bir_min' => $tenant->bir_min,
                'bir_sn' => $tenant->bir_sn,
                'header_text' => $tenant->header_text,
                'footer_text' => $tenant->footer_text,
                'currency_symbol' => $tenant->currency_symbol ?? '₱',
                'payment_status' => $tenant->payment_status,
                'is_active' => method_exists($tenant, 'isActive') ? $tenant->isActive() : true,
                'theme_settings' => $tenant->theme_settings,
                'crm_settings' => $tenant->crm_settings,
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

    protected function getProductImageUrl(?string $image, Request $request): ?string
    {
        if (empty($image)) {
            return null;
        }
        if (str_starts_with($image, 'http')) {
            if (str_contains($image, 'pos.dev.com') || str_contains($image, 'localhost')) {
                $parsed = parse_url($image, PHP_URL_PATH);
                return $request->schemeAndHttpHost() . $parsed;
            }
            return $image;
        }
        return $request->schemeAndHttpHost() . '/storage/' . ltrim($image, '/');
    }

    public function lookupGlobalProduct(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        $currentTenantId = (int) ($user?->tenant_id ?? $request->input('tenant_id', 1));
        $barcode = trim($request->input('barcode', ''));
        $name = trim($request->input('name', ''));

        if (empty($barcode) && empty($name)) {
            return response()->json(['success' => true, 'count' => 0, 'data' => []]);
        }

        $query = POSProducts::query()->where('status', '!=', 'deleted');

        if (!empty($barcode)) {
            $stripped = ltrim($barcode, '0');
            $query->where(function ($q) use ($barcode, $stripped) {
                $q->where('barcode', $barcode);
                if (!empty($stripped)) {
                    $q->orWhere('barcode', $stripped);
                }
                $q->orWhereHas('variants', function ($vq) use ($barcode, $stripped) {
                    $vq->where('barcode', $barcode);
                    if (!empty($stripped)) {
                        $vq->orWhere('barcode', $stripped);
                    }
                });
            });
        } elseif (!empty($name)) {
            $query->where('name', 'like', "%{$name}%");
        }

        $products = $query->with(['category', 'unit', 'variants', 'tenant'])->get();

        $data = $products->map(function ($p) use ($currentTenantId, $request) {
            $imageUrl = $this->getProductImageUrl($p->image, $request);

            return [
                'id'               => $p->id,
                'name'             => $p->name,
                'barcode'          => $p->barcode,
                'sku'              => $p->sku,
                'description'      => $p->description,
                'selling_price'    => (float) $p->selling_price,
                'wholesale_price'  => (float) ($p->wholesale_price ?? $p->selling_price),
                'cost_price'       => (float) ($p->cost_price ?? 0),
                'stock_on_hand'    => (float) ($p->stock_on_hand ?? 0),
                'reorder_level'    => (float) ($p->reorder_level ?? 5),
                'category_id'      => $p->category_id,
                'category_name'    => $p->category?->name,
                'unit_id'          => $p->unit_id,
                'unit_name'        => $p->unit?->name,
                'image_url'        => $imageUrl,
                'tenant_id'        => $p->tenant_id,
                'store_name'       => $p->tenant?->business_name ?? 'LikhaPOS Store #' . $p->tenant_id,
                'is_current_store' => ((int)$p->tenant_id === (int)$currentTenantId),
                'variants'         => $p->variants->map(function($v) {
                    return [
                        'id'              => $v->id,
                        'name'            => $v->variant_name,   // DB column is variant_name
                        'variant_name'    => $v->variant_name,
                        'barcode'         => $v->barcode,
                        'sku'             => $v->sku,
                        'cost_price'      => (float) $v->cost_price,
                        'selling_price'   => (float) $v->selling_price,
                        'wholesale_price' => (float) $v->wholesale_price,
                        'stock_on_hand'   => (float) $v->stock_on_hand,
                        'qty_per_pack'    => (float) ($v->qty_per_pack ?? 1),
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'count'   => $data->count(),
            'data'    => $data->values()->all(),
        ]);
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

        $products->transform(function ($p) use ($request) {
            $p->image_url = $this->getProductImageUrl($p->image, $request);

            // Explicitly remap variants so name = variant_name regardless of accessor state
            if ($p->relationLoaded('variants')) {
                $p->setRelation('variants', $p->variants->map(function ($v) {
                    $vName = $v->variant_name ?? $v->getAttributes()['name'] ?? '';
                    return [
                        'id'              => $v->id,
                        'name'            => $vName,
                        'variant_name'    => $vName,
                        'barcode'         => $v->barcode,
                        'sku'             => $v->sku,
                        'cost_price'      => (float) $v->cost_price,
                        'selling_price'   => (float) $v->selling_price,
                        'wholesale_price' => (float) ($v->wholesale_price ?? 0),
                        'stock_on_hand'   => (float) $v->stock_on_hand,
                        'qty_per_pack'    => (float) ($v->qty_per_pack ?? 1),
                    ];
                })->values());
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
                $existingBarcodeProduct->load(['category', 'unit', 'variants']);
                $existingBarcodeProduct->image_url = $this->getProductImageUrl($existingBarcodeProduct->image, $request);
                return response()->json([
                    'success' => false,
                    'is_duplicate' => true,
                    'message' => "Barcode \"{$barcode}\" is already registered to \"{$existingBarcodeProduct->name}\" in this store (Stock: {$existingBarcodeProduct->stock_on_hand}).",
                    'existing_product' => $existingBarcodeProduct,
                ], 422);
            }
        }

        // 2. Strict Duplicate Product Name check in current store
        $existingNameProduct = POSProducts::where('tenant_id', $tenantId)
            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($validated['name']))])
            ->where('status', '!=', 'deleted')
            ->first();

        if ($existingNameProduct) {
            $existingNameProduct->load(['category', 'unit', 'variants']);
            $existingNameProduct->image_url = $this->getProductImageUrl($existingNameProduct->image, $request);
            return response()->json([
                'success' => false,
                'is_duplicate' => true,
                'message' => "A product named \"{$existingNameProduct->name}\" already exists in this store (Barcode: {$existingNameProduct->barcode}).",
                'existing_product' => $existingNameProduct,
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

        // Image handling: Base64 Upload or Inherited Network Image
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
        } elseif ($request->filled('image_url')) {
            $rawImg = $request->input('image_url');
            if (str_contains($rawImg, '/storage/')) {
                $parts = explode('/storage/', $rawImg);
                $product->image = end($parts);
            }
        }

        $product->save();

        // 3. Save Variants if provided
        if ($request->has('variants') && is_array($request->input('variants'))) {
            foreach ($request->input('variants') as $v) {
                if (empty($v['name']) && empty($v['variant_name'])) continue;
                $vName = $v['name'] ?? $v['variant_name'];
                
                POSProductVariant::create([
                    'tenant_id'       => $tenantId,
                    'product_id'      => $product->id,
                    'variant_name'    => $vName,
                    'barcode'         => !empty($v['barcode']) ? trim($v['barcode']) : null,
                    'sku'             => 'SKU-' . strtoupper(Str::random(8)),
                    'qty_per_pack'    => (float) ($v['qty_per_pack'] ?? 1),
                    'cost_price'      => (float) ($v['cost_price'] ?? $product->cost_price),
                    'selling_price'   => (float) ($v['selling_price'] ?? $product->selling_price),
                    'wholesale_price' => (float) ($v['wholesale_price'] ?? $product->wholesale_price),
                    'stock_on_hand'   => (float) ($v['stock_on_hand'] ?? 0),
                    'status'          => 'active',
                    'created_by'      => $cashierId,
                    'updated_by'      => $cashierId,
                ]);
            }
        }

        $product->load(['category', 'unit', 'variants']);
        $product->image_url = $this->getProductImageUrl($product->image, $request);

        return response()->json([
            'success' => true,
            'message' => 'Product registered successfully.',
            'product' => $product,
        ]);
    }

    public function updateProduct(Request $request, $id)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = (int) ($user?->tenant_id ?? $request->input('tenant_id', 1));
        $cashierId = (int) ($user?->id ?? $request->input('cashier_id', 1));

        $product = POSProducts::where('tenant_id', $tenantId)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'name'            => 'sometimes|required|string|max:255',
            'barcode'         => 'nullable|string|max:100',
            'selling_price'   => 'sometimes|required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'cost_price'      => 'nullable|numeric|min:0',
            'stock_on_hand'   => 'nullable|numeric|min:0',
            'category_id'     => 'nullable|integer',
            'unit_id'         => 'nullable|integer',
            'reorder_level'   => 'nullable|numeric|min:0',
            'description'     => 'nullable|string',
            'image_base64'    => 'nullable|string',
        ]);

        if (array_key_exists('name', $validated)) {
            $product->name = $validated['name'];
        }
        if (array_key_exists('barcode', $validated)) {
            $product->barcode = !empty($validated['barcode']) ? trim($validated['barcode']) : null;
        }
        if (array_key_exists('selling_price', $validated)) {
            $product->selling_price = $validated['selling_price'];
        }
        if (array_key_exists('wholesale_price', $validated)) {
            $product->wholesale_price = $validated['wholesale_price'];
        }
        if (array_key_exists('cost_price', $validated)) {
            $product->cost_price = $validated['cost_price'];
        }
        if (array_key_exists('stock_on_hand', $validated)) {
            $product->stock_on_hand = $validated['stock_on_hand'];
        }
        if (array_key_exists('category_id', $validated) && !empty($validated['category_id'])) {
            $product->category_id = $validated['category_id'];
        }
        if (array_key_exists('unit_id', $validated) && !empty($validated['unit_id'])) {
            $product->unit_id = $validated['unit_id'];
        }
        if (array_key_exists('reorder_level', $validated)) {
            $product->reorder_level = $validated['reorder_level'];
        }
        if (array_key_exists('description', $validated)) {
            $product->description = $validated['description'];
        }

        $product->updated_by = $cashierId;

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

        // Sync / Update Variants
        if ($request->has('variants') && is_array($request->input('variants'))) {
            foreach ($request->input('variants') as $v) {
                if (empty($v['name']) && empty($v['variant_name'])) continue;
                $vName = $v['name'] ?? $v['variant_name'];

                if (!empty($v['id'])) {
                    $existingVariant = POSProductVariant::where('tenant_id', $tenantId)
                        ->where('product_id', $product->id)
                        ->where('id', $v['id'])
                        ->first();

                    if ($existingVariant) {
                        $existingVariant->update([
                            'variant_name'    => $vName,
                            'barcode'         => !empty($v['barcode']) ? trim($v['barcode']) : null,
                            'qty_per_pack'    => (float) ($v['qty_per_pack'] ?? $existingVariant->qty_per_pack),
                            'cost_price'      => (float) ($v['cost_price'] ?? $existingVariant->cost_price),
                            'selling_price'   => (float) ($v['selling_price'] ?? $existingVariant->selling_price),
                            'wholesale_price' => (float) ($v['wholesale_price'] ?? $existingVariant->wholesale_price),
                            'stock_on_hand'   => (float) ($v['stock_on_hand'] ?? $existingVariant->stock_on_hand),
                            'updated_by'      => $cashierId,
                        ]);
                        continue;
                    }
                }

                POSProductVariant::create([
                    'tenant_id'       => $tenantId,
                    'product_id'      => $product->id,
                    'variant_name'    => $vName,
                    'barcode'         => !empty($v['barcode']) ? trim($v['barcode']) : null,
                    'sku'             => 'SKU-' . strtoupper(Str::random(8)),
                    'qty_per_pack'    => (float) ($v['qty_per_pack'] ?? 1),
                    'cost_price'      => (float) ($v['cost_price'] ?? $product->cost_price),
                    'selling_price'   => (float) ($v['selling_price'] ?? $product->selling_price),
                    'wholesale_price' => (float) ($v['wholesale_price'] ?? $product->wholesale_price),
                    'stock_on_hand'   => (float) ($v['stock_on_hand'] ?? 0),
                    'status'          => 'active',
                    'created_by'      => $cashierId,
                    'updated_by'      => $cashierId,
                ]);
            }
        }

        $product->load(['category', 'unit', 'variants']);
        $product->image_url = $this->getProductImageUrl($product->image, $request);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'product' => $product,
        ]);
    }

    public function stockIn(Request $request, $id)
    {
        $user = $this->getAuthenticatedUser($request);
        $cashierId = (int) ($user?->id ?? $request->input('cashier_id', 1));

        $validated = $request->validate([
            'qty'           => 'required|numeric|not_in:0',
            'notes'         => 'nullable|string',
            'variant_id'    => 'nullable|integer',
            'movement_type' => 'nullable|string|in:purchase,adjustment,return,transfer',
        ]);

        $product = POSProducts::findOrFail($id);
        $variantId = $validated['variant_id'] ?? null;
        $targetName = $product->name;
        $qtyChange = (float) $validated['qty'];
        $movType = $validated['movement_type'] ?? ($qtyChange > 0 ? 'purchase' : 'adjustment');

        if ($variantId) {
            $variant = POSProductVariant::where('product_id', $id)->findOrFail($variantId);
            $variant->stock_on_hand = max(0, (float)($variant->stock_on_hand ?? 0) + $qtyChange);
            $variant->save();
            $targetName = "{$product->name} ({$variant->name})";
        } else {
            $product->stock_on_hand = max(0, (float)($product->stock_on_hand ?? 0) + $qtyChange);
            $product->save();
        }

        $formattedQty = $qtyChange > 0 ? "+{$qtyChange}" : "{$qtyChange}";
        $defaultRemark = $movType === 'purchase'
            ? "Restocked {$formattedQty} to {$targetName}"
            : "Stock adjustment ({$formattedQty}) for {$targetName}";

        // Record Inventory Movement (movement_type enum: 'purchase', 'sale', 'adjustment', 'return', 'transfer')
        $inv = new InventoryMovement();
        $inv->tenant_id = $product->tenant_id;
        $inv->product_id = $product->id;
        $inv->variant_id = $variantId;
        $inv->movement_type = $movType;
        $inv->reference_type = $movType === 'purchase' ? 'quick_restock' : 'stock_adjustment';
        $inv->reference_id = $variantId ?? $product->id;
        $inv->qty = $qtyChange;
        $inv->remarks = $validated['notes'] ?? $defaultRemark;
        $inv->created_by = $cashierId;
        $inv->updated_by = $cashierId;
        $inv->status = 'active';
        $inv->save();

        $product->load(['category', 'unit', 'variants']);
        $product->image_url = $this->getProductImageUrl($product->image, $request);

        $actionWord = $qtyChange > 0 ? 'added' : 'adjusted';
        return response()->json([
            'success' => true,
            'message' => "Successfully {$actionWord} {$formattedQty} stock for {$targetName}.",
            'product' => $product,
        ]);
    }

    public function showProduct(Request $request, $id)
    {
        $product = POSProducts::with(['category', 'unit', 'variants'])->find($id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        $product->image_url = $this->getProductImageUrl($product->image, $request);

        return response()->json([
            'success' => true,
            'data' => $product,
            'product' => $product,
        ]);
    }

    public function productPerformance(Request $request, $id)
    {
        $product = POSProducts::with(['category', 'unit', 'variants'])->find($id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        $product->image_url = $this->getProductImageUrl($product->image, $request);

        $stock = (float)($product->stock_on_hand ?? 0);
        $cost = (float)($product->cost_price ?? 0);
        $selling = (float)($product->selling_price ?? 0);
        $wholesale = (float)($product->wholesale_price ?? 0);

        // Fetch sales data
        $saleItems = POSSaleItem::with(['sale.customer'])
            ->where('product_id', $id)
            ->latest('id')
            ->get();

        $totalUnitsSold = (float)$saleItems->sum('qty');
        $totalRevenue = (float)$saleItems->sum('line_total');
        $distinctOrdersCount = $saleItems->pluck('sale_id')->unique()->count();

        // Profit calculations
        $profitPerUnit = max(0, $selling - $cost);
        $marginPercent = $selling > 0 ? round(($profitPerUnit / $selling) * 100, 1) : 0;
        $estLifetimeProfit = $profitPerUnit * $totalUnitsSold;

        // Stock valuation
        $stockValueCost = $stock * $cost;
        $stockValueRetail = $stock * $selling;

        // Per-variant performance breakdown
        $variantStats = $product->variants->map(function ($v) use ($saleItems) {
            $vSales = $saleItems->where('variant_id', $v->id);
            $vSoldQty = (float)$vSales->sum('qty');
            $vRevenue = (float)$vSales->sum('line_total');
            $vCost = (float)($v->cost_price ?? 0);
            $vSelling = (float)($v->selling_price ?? 0);
            $vStock = (float)($v->stock_on_hand ?? 0);
            $vProfitPerUnit = max(0, $vSelling - $vCost);
            $vMargin = $vSelling > 0 ? round(($vProfitPerUnit / $vSelling) * 100, 1) : 0;

            return [
                'id' => $v->id,
                'name' => $v->name ?? $v->variant_name ?? 'Pack Variant',
                'sku' => $v->sku,
                'barcode' => $v->barcode,
                'stock_on_hand' => $vStock,
                'qty_per_pack' => (float)($v->qty_per_pack ?? 1),
                'cost_price' => $vCost,
                'selling_price' => $vSelling,
                'wholesale_price' => (float)($v->wholesale_price ?? 0),
                'total_units_sold' => $vSoldQty,
                'total_revenue' => $vRevenue,
                'profit_margin_percent' => $vMargin,
                'stock_value_retail' => $vStock * $vSelling,
                'is_low_stock' => $vStock <= 5,
                'is_out_of_stock' => $vStock <= 0,
            ];
        })->values();

        // Recent sales history formatted for CRM / Ledger (with variant_name)
        $recentSales = $saleItems->take(40)->map(function ($item) use ($product) {
            $sale = $item->sale;
            $customerName = $sale?->customer?->CustomerName ?? $sale?->customer?->name ?? 'Walk-in Customer';
            $variantName = null;
            if ($item->variant_id) {
                $matchedVar = $product->variants->firstWhere('id', $item->variant_id);
                $variantName = $matchedVar?->name ?? $matchedVar?->variant_name ?? 'Pack Variant';
            }

            return [
                'id' => $item->id,
                'sale_id' => $item->sale_id,
                'variant_id' => $item->variant_id,
                'variant_name' => $variantName,
                'invoice_no' => $sale?->invoice_no ?? ('#' . $item->sale_id),
                'customer_name' => $customerName,
                'qty' => (float)$item->qty,
                'unit_price' => (float)$item->unit_price,
                'discount' => (float)($item->discount_amount ?? 0),
                'line_total' => (float)$item->line_total,
                'payment_status' => $sale?->payment_status ?? 'paid',
                'created_at' => $item->created_at ? $item->created_at->format('M d, Y h:i A') : '',
                'created_at_raw' => $item->created_at ? $item->created_at->toIso8601String() : '',
            ];
        })->values();

        // Recent stock movements
        $movements = InventoryMovement::where('product_id', $id)
            ->latest('id')
            ->take(25)
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'variant_id' => $m->variant_id,
                    'movement_type' => $m->movement_type ?? 'stock_in',
                    'qty' => (float)$m->qty,
                    'reference_type' => $m->reference_type,
                    'remarks' => $m->remarks ?? ($m->movement_type === 'stock_in' ? 'Stock Added' : 'Stock Movement'),
                    'created_at' => $m->created_at ? $m->created_at->format('M d, Y h:i A') : '',
                ];
            });

        return response()->json([
            'success' => true,
            'product' => $product,
            'stats' => [
                'stock_on_hand' => $stock,
                'total_units_sold' => $totalUnitsSold,
                'total_revenue' => $totalRevenue,
                'total_orders' => $distinctOrdersCount,
                'cost_price' => $cost,
                'selling_price' => $selling,
                'wholesale_price' => $wholesale,
                'profit_per_unit' => $profitPerUnit,
                'profit_margin_percent' => $marginPercent,
                'est_lifetime_profit' => $estLifetimeProfit,
                'stock_value_cost' => $stockValueCost,
                'stock_value_retail' => $stockValueRetail,
                'is_low_stock' => $stock <= 5,
                'is_out_of_stock' => $stock <= 0,
            ],
            'variant_stats' => $variantStats,
            'recent_sales' => $recentSales,
            'stock_movements' => $movements,
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
            $runningBalance = $cust->credit ? (float)$cust->credit->running_balance : 0;
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
        $runningBalance = $customer->credit ? (float)$customer->credit->running_balance : 0;

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

        $lastBalance = (float)(POSCustomerLedger::where('customer_id', $customerId)
            ->latest('id')
            ->value('running_balance') ?? 0);

        if ($validated['amount'] > $lastBalance) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount (₱' . number_format($validated['amount'], 2) . ') exceeds outstanding utang balance (₱' . number_format($lastBalance, 2) . ').',
            ], 422);
        }

        $runningBalance = max(0, $lastBalance - $validated['amount']);

        DB::transaction(function () use ($validated, $tenantId, $customerId, $customer, $runningBalance, $request) {
            $paymentMethod = $validated['payment_method'] ?? 'cash';
            $refNo = $validated['reference_no'] ?? ('COL-' . strtoupper(Str::random(6)));
            $remarks = $validated['remarks'] ?? ('Utang collection payment via ' . strtoupper($paymentMethod));

            $payment = new POSPayment();
            $payment->customer_id = $customerId;
            $payment->tenant_id = $tenantId;
            $payment->payment_date = now();
            $payment->payment_method = $paymentMethod;
            $payment->amount = $validated['amount'];
            $payment->tendered_amount = $validated['amount'];
            $payment->change_amount = 0;
            $payment->reference_number = $refNo;
            $payment->notes = $remarks;
            $payment->save();

            $ledger = new POSCustomerLedger();
            $ledger->tenant_id = $tenantId;
            $ledger->customer_id = $customerId;
            $ledger->payment_id = $payment->id;
            $ledger->reference_no = $refNo;
            $ledger->transaction_type = 'PAYMENT';
            $ledger->debit = 0;
            $ledger->credit = $validated['amount'];
            $ledger->running_balance = $runningBalance;
            $ledger->remarks = $remarks;
            $ledger->created_by = $request->user()?->id ?? 1;
            $ledger->updated_by = $request->user()?->id ?? 1;
            $ledger->status = 'active';
            $ledger->save();

            if (strtolower($paymentMethod) === 'cash') {
                $cash = new POSCashTransaction();
                $cash->tenant_id = $tenantId;
                $cash->transaction_code = 'IN-' . strtoupper(Str::random(6));
                $cash->cashier_id = $request->user()?->id ?? 1;
                $cash->transaction_type = 'cash_in';
                $cash->category = 'UTANG_COLLECTION';
                $cash->amount = $validated['amount'];
                $cash->reference_no = $refNo;
                $cash->remarks = 'Utang collection payment from ' . $customer->CustomerName . ' [Completed]';
                $cash->status = 'active';
                $cash->archived = 0;
                $cash->created_by = $request->user()?->id ?? 1;
                $cash->updated_by = $request->user()?->id ?? 1;
                $cash->created_at = now();
                $cash->save();
            }
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
            'items.*.product_id' => 'nullable',
            'items.*.variant_id' => 'nullable|integer',
            'items.*.qty'     => 'required|numeric|min:0.01',
            'items.*.price'   => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric',
            'items.*.promo_id' => 'nullable|integer',
            'items.*.promo_name' => 'nullable|string',
            'items.*.line_total' => 'nullable|numeric',
            'items.*.name'    => 'nullable|string',
            'items.*.is_custom' => 'nullable|boolean',
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
            $currentDebt = (float)(POSCustomerLedger::where('customer_id', $customer->id)->latest('id')->value('running_balance') ?? 0);
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
                    $isCustom = !empty($item['is_custom']) || empty($item['product_id']) || !is_numeric($item['product_id']);

                    if ($isCustom) {
                        $newSaleItem = new POSSaleItem();
                        $newSaleItem->sale_id = $sale->id;
                        $newSaleItem->product_id = null;
                        $newSaleItem->variant_id = null;
                        $newSaleItem->barcode = null;
                        $newSaleItem->sku = null;
                        $newSaleItem->product_name = $item['name'] ?? $item['product_name'] ?? 'Custom Service / Fee';
                        $newSaleItem->qty = $item['qty'];
                        $newSaleItem->unit_price = $item['price'];
                        $newSaleItem->discount_amount = isset($item['discount_amount']) ? (float)$item['discount_amount'] : 0;
                        $newSaleItem->promo_id = null;
                        $newSaleItem->tax_amount = 0;
                        $newSaleItem->line_total = isset($item['line_total']) ? (float)$item['line_total'] : max(0, ($item['qty'] * $item['price']) - $newSaleItem->discount_amount);
                        $newSaleItem->created_by = $cashierId;
                        $newSaleItem->updated_by = $cashierId;
                        $newSaleItem->status = 'active';
                        $newSaleItem->save();
                        continue;
                    }

                    $product = POSProducts::findOrFail($item['product_id']);
                    $variantId = !empty($item['variant_id']) ? $item['variant_id'] : null;
                    $variant = $variantId ? \App\Models\POS\POSProductVariant::find($variantId) : null;

                    $newSaleItem = new POSSaleItem();
                    $newSaleItem->sale_id = $sale->id;
                    $newSaleItem->product_id = $product->id;
                    $newSaleItem->variant_id = $variant?->id;
                    $newSaleItem->barcode = $variant?->barcode ?: $product->barcode;
                    $newSaleItem->sku = $variant?->sku ?: $product->sku;
                    $newSaleItem->product_name = $variant ? "{$product->name} ({$variant->variant_name})" : $product->name;
                    $newSaleItem->qty = $item['qty'];
                    $newSaleItem->unit_price = $item['price'];
                    $newSaleItem->discount_amount = isset($item['discount_amount']) ? (float)$item['discount_amount'] : 0;
                    $newSaleItem->promo_id = !empty($item['promo_id']) ? (int)$item['promo_id'] : null;
                    $newSaleItem->tax_amount = 0;
                    $newSaleItem->line_total = isset($item['line_total']) ? (float)$item['line_total'] : max(0, ($item['qty'] * $item['price']) - $newSaleItem->discount_amount);
                    $newSaleItem->created_by = $cashierId;
                    $newSaleItem->updated_by = $cashierId;
                    $newSaleItem->status = 'active';
                    $newSaleItem->save();

                    // Record Inventory Movement
                    $newInv = new InventoryMovement();
                    $newInv->tenant_id = $tenantId;
                    $newInv->product_id = $product->id;
                    $newInv->variant_id = $variant?->id;
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
                    if ($variant) {
                        $vStock = (float)($variant->stock_on_hand ?? 0);
                        $variant->stock_on_hand = max(0, $vStock - (float)$item['qty']);
                        $variant->save();
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
                            $lastBalance = (float)(POSCustomerLedger::where('customer_id', $customerId)->latest('id')->value('running_balance') ?? 0);
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
                        }
                    }
                }
            });

            $sale->load(['items', 'customer', 'payments', 'tenant']);

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
        $sale = POSSale::with(['items.product', 'customer', 'cashier', 'payments', 'tenant'])->findOrFail($id);

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

    public function dashboard(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = $user?->tenant_id ?? $request->input('tenant_id');

        if (!$tenantId) {
            return response()->json(['success' => false, 'message' => 'Tenant ID required.'], 400);
        }

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisMonthStart = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // 1. Sales & Growth
        $todaySales = (float) POSSale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', $today)
            ->whereIn('sale_status', ['completed', 'refund'])
            ->sum('total_amount');

        $yesterdaySales = (float) POSSale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', $yesterday)
            ->whereIn('sale_status', ['completed', 'refund'])
            ->sum('total_amount');

        $salesGrowth = $yesterdaySales > 0 
            ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1) 
            : ($todaySales > 0 ? 100 : 0);

        $todayOrdersCount = POSSale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', $today)
            ->where('sale_status', 'completed')
            ->count();

        $monthSales = (float) POSSale::where('tenant_id', $tenantId)
            ->where('sale_date', '>=', $thisMonthStart)
            ->whereIn('sale_status', ['completed', 'refund'])
            ->sum('total_amount');

        $lastMonthSales = (float) POSSale::where('tenant_id', $tenantId)
            ->whereBetween('sale_date', [$lastMonthStart, $lastMonthEnd])
            ->whereIn('sale_status', ['completed', 'refund'])
            ->sum('total_amount');

        $monthGrowth = $lastMonthSales > 0
            ? round((($monthSales - $lastMonthSales) / $lastMonthSales) * 100, 1)
            : ($monthSales > 0 ? 100 : 0);

        // 2. Gross Profit Calculation (Today) with Variant-aware costing
        $todaySaleIds = POSSale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', $today)
            ->whereIn('sale_status', ['completed', 'refund'])
            ->pluck('id');

        $todayCostOfGoods = (float) POSSaleItem::whereIn('sale_id', $todaySaleIds)
            ->leftJoin('pos_products', 'pos_sale_items.product_id', '=', 'pos_products.id')
            ->leftJoin('pos_product_variants', 'pos_sale_items.variant_id', '=', 'pos_product_variants.id')
            ->selectRaw('SUM(pos_sale_items.qty * COALESCE(NULLIF(pos_product_variants.cost_price, 0), pos_products.cost_price, 0)) as total_cost')
            ->value('total_cost') ?? 0;

        $todayGrossProfit = max(0, $todaySales - $todayCostOfGoods);
        $todayProfitMargin = $todaySales > 0 ? round(($todayGrossProfit / $todaySales) * 100, 1) : 0;

        // 3. Drawer Balance & Shift
        $activeShift = POSCashShift::with(['cashier', 'drawer'])
            ->where('tenant_id', $tenantId)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        $drawerStartingCash = $activeShift ? (float)$activeShift->opening_cash : 2000.00;
        $shiftCashSales = $activeShift
            ? (float) POSPayment::where('shift_id', $activeShift->id)->where('payment_method', 'cash')->sum('amount')
            : (float) POSPayment::where('tenant_id', $tenantId)->whereDate('payment_date', $today)->where('payment_method', 'cash')->sum('amount');

        $currentDrawerBalance = $drawerStartingCash + $shiftCashSales;

        // 4. Receivables (Utang)
        $totalDebits = (float) POSCustomerLedger::where('tenant_id', $tenantId)->sum('debit');
        $totalCredits = (float) POSCustomerLedger::where('tenant_id', $tenantId)->sum('credit');
        $totalUtangReceivables = max(0, $totalDebits - $totalCredits);

        $customersWithUtangCount = POSCustomerLedger::where('tenant_id', $tenantId)
            ->select('customer_id')
            ->groupBy('customer_id')
            ->havingRaw('(SUM(debit) - SUM(credit)) > 0')
            ->get()
            ->count();

        // 5. Stock Health & Valuations
        $standaloneProductsCount = POSProducts::where('tenant_id', $tenantId)->where('status', '!=', 'deleted')->doesntHave('variants')->count();
        $variantsCount = POSProductVariant::whereHas('product', function ($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId)->where('status', '!=', 'deleted');
        })->where(function ($q) {
            $q->whereNull('status')->orWhere('status', 'active');
        })->count();
        $totalProducts = $standaloneProductsCount + $variantsCount;

        $standaloneInventoryCost = (float) POSProducts::where('tenant_id', $tenantId)
            ->where('status', '!=', 'deleted')
            ->doesntHave('variants')
            ->selectRaw('SUM(COALESCE(stock_on_hand, 0) * COALESCE(cost_price, 0)) as total_val')
            ->value('total_val') ?? 0;

        $variantInventoryCost = (float) POSProductVariant::join('pos_products', 'pos_product_variants.product_id', '=', 'pos_products.id')
            ->where('pos_products.tenant_id', $tenantId)
            ->where('pos_products.status', '!=', 'deleted')
            ->where(function ($q) {
                $q->whereNull('pos_product_variants.status')->orWhere('pos_product_variants.status', 'active');
            })
            ->selectRaw('SUM(COALESCE(pos_product_variants.stock_on_hand, 0) * COALESCE(NULLIF(pos_product_variants.cost_price, 0), pos_products.cost_price, 0)) as total_val')
            ->value('total_val') ?? 0;

        $totalInventoryCost = $standaloneInventoryCost + $variantInventoryCost;

        $lowStockProductsCount = POSProducts::where('tenant_id', $tenantId)
            ->where('status', '!=', 'deleted')
            ->doesntHave('variants')
            ->where('stock_on_hand', '>', 0)
            ->whereColumn('stock_on_hand', '<=', 'reorder_level')
            ->count();

        $outOfStockProductsCount = POSProducts::where('tenant_id', $tenantId)
            ->where('status', '!=', 'deleted')
            ->doesntHave('variants')
            ->where('stock_on_hand', '<=', 0)
            ->count();

        $lowStockVariantsCount = POSProductVariant::whereHas('product', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId)->where('status', '!=', 'deleted');
            })
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 'active');
            })
            ->where('stock_on_hand', '>', 0)
            ->whereColumn('stock_on_hand', '<=', 'reorder_level')
            ->count();

        $outOfStockVariantsCount = POSProductVariant::whereHas('product', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId)->where('status', '!=', 'deleted');
            })
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 'active');
            })
            ->where('stock_on_hand', '<=', 0)
            ->count();

        $lowStockCount = $lowStockProductsCount + $lowStockVariantsCount;
        $outOfStockCount = $outOfStockProductsCount + $outOfStockVariantsCount;

        // 6. Recent Sales Feed
        $recentSales = POSSale::with('customer')
            ->where('tenant_id', $tenantId)
            ->orderByDesc('id')
            ->limit(6)
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'invoice_no' => $sale->invoice_no ?: ($sale->sale_code ?: ('#' . $sale->id)),
                    'customer_name' => $sale->customer?->CustomerName ?: ($sale->customer?->name ?? 'Walk-In Customer'),
                    'payment_method' => ucfirst(str_replace('_', ' ', $sale->payment_method ?: 'cash')),
                    'total_amount' => (float)$sale->total_amount,
                    'total_formatted' => '₱' . number_format($sale->total_amount, 2),
                    'time_formatted' => $sale->sale_date ? $sale->sale_date->format('M d, h:i A') : ($sale->created_at ? $sale->created_at->format('M d, h:i A') : '-'),
                ];
            });

        // 7. 7-Day Trend Chart
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $rawSales = POSSale::where('tenant_id', $tenantId)
            ->whereIn('sale_status', ['completed', 'refund'])
            ->whereBetween('sale_date', [$startDate, Carbon::now()->endOfDay()])
            ->selectRaw('DATE(sale_date) as date, SUM(total_amount) as total_revenue, COUNT(CASE WHEN sale_status = \'completed\' THEN 1 END) as total_orders')
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->get()
            ->keyBy('date');

        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $curr = Carbon::now()->subDays($i)->format('Y-m-d');
            $trend[] = [
                'date' => $curr,
                'day' => Carbon::parse($curr)->format('D'),
                'label' => Carbon::parse($curr)->format('M d'),
                'revenue' => (float)($rawSales[$curr]->total_revenue ?? 0),
                'orders' => (int)($rawSales[$curr]->total_orders ?? 0),
            ];
        }

        // 8. Payment Breakdown
        $paymentBreakdown = POSPayment::where('tenant_id', $tenantId)
            ->where('payment_date', '>=', Carbon::now()->subDays(30))
            ->select('payment_method', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        $paymentStats = [
            'cash' => (float)($paymentBreakdown['cash']->total_amount ?? 0),
            'gcash' => (float)($paymentBreakdown['gcash']->total_amount ?? 0),
            'maya' => (float)($paymentBreakdown['maya']->total_amount ?? 0),
            'credit' => (float)($paymentBreakdown['credit']->total_amount ?? 0),
            'card' => (float)($paymentBreakdown['card']->total_amount ?? 0),
        ];

        // 9. Stock Alerts
        $lowStockProductsList = POSProducts::with(['unit', 'category'])
            ->where('tenant_id', $tenantId)
            ->where('status', '!=', 'deleted')
            ->doesntHave('variants')
            ->where(function ($q) {
                $q->whereColumn('stock_on_hand', '<=', 'reorder_level')
                  ->orWhere('stock_on_hand', '<=', 0);
            })
            ->get()
            ->map(function ($prod) {
                return [
                    'id' => $prod->id,
                    'name' => $prod->name ?: 'Product',
                    'barcode' => $prod->barcode ?: 'No Barcode',
                    'category' => $prod->category?->name ?? 'General',
                    'stock' => (float)$prod->stock_on_hand,
                    'unit' => $prod->unit?->name ?? 'pcs',
                    'is_out_of_stock' => $prod->stock_on_hand <= 0,
                ];
            });

        $lowStockVariantsList = POSProductVariant::with(['product.category', 'unit'])
            ->whereHas('product', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId)->where('status', '!=', 'deleted');
            })
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 'active');
            })
            ->where(function ($q) {
                $q->whereColumn('stock_on_hand', '<=', 'reorder_level')
                  ->orWhere('stock_on_hand', '<=', 0);
            })
            ->get()
            ->map(function ($v) {
                $productName = $v->product?->name ?? 'Product';
                return [
                    'id' => $v->product_id,
                    'name' => $productName . ' (' . $v->variant_name . ')',
                    'barcode' => $v->barcode ?: ($v->product?->barcode ?: 'No Barcode'),
                    'category' => $v->product?->category?->name ?? 'General',
                    'stock' => (float)$v->stock_on_hand,
                    'unit' => $v->unit?->name ?: ($v->product?->unit?->name ?? 'pcs'),
                    'is_out_of_stock' => $v->stock_on_hand <= 0,
                ];
            });

        $mergedAlerts = $lowStockProductsList->concat($lowStockVariantsList)->sortBy('stock')->values()->take(6);

        // 10. Top Suki Leaderboard (by TotalPoints & CRM engagement)
        $topSuki = POSCustomers::where('tenant_id', $tenantId)
            ->orderByDesc('TotalPoints')
            ->limit(5)
            ->get()
            ->map(function ($c) use ($tenantId) {
                $bal = (float) POSCustomerLedger::where('tenant_id', $tenantId)
                    ->where('customer_id', $c->id)
                    ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as bal')
                    ->value('bal');

                return [
                    'id' => $c->id,
                    'name' => $c->CustomerName ?: ($c->name ?? 'Customer'),
                    'phone' => $c->mobile_number ?: ($c->phone ?? 'No Phone'),
                    'points' => (int)($c->TotalPoints ?? 0),
                    'loyalty_points' => (int)($c->TotalPoints ?? 0),
                    'balance' => max(0, $bal),
                    'total_spent_formatted' => number_format((int)($c->TotalPoints ?? 0)) . ' pts',
                ];
            });

        return response()->json([
            'success' => true,
            'kpis' => [
                'today_sales' => $todaySales,
                'today_sales_formatted' => '₱' . number_format($todaySales, 2),
                'yesterday_sales' => $yesterdaySales,
                'sales_growth' => $salesGrowth,
                'today_orders_count' => $todayOrdersCount,
                'month_sales' => $monthSales,
                'month_sales_formatted' => '₱' . number_format($monthSales, 2),
                'month_growth' => $monthGrowth,
                'today_gross_profit' => $todayGrossProfit,
                'today_gross_profit_formatted' => '₱' . number_format($todayGrossProfit, 2),
                'today_profit_margin' => $todayProfitMargin,
                'drawer_starting_cash' => $drawerStartingCash,
                'drawer_starting_cash_formatted' => '₱' . number_format($drawerStartingCash, 2),
                'current_drawer_balance' => $currentDrawerBalance,
                'current_drawer_balance_formatted' => '₱' . number_format($currentDrawerBalance, 2),
                'total_utang_receivables' => $totalUtangReceivables,
                'total_utang_receivables_formatted' => '₱' . number_format($totalUtangReceivables, 2),
                'customers_with_utang_count' => $customersWithUtangCount,
                'total_inventory_cost' => $totalInventoryCost,
                'total_inventory_cost_formatted' => '₱' . number_format($totalInventoryCost, 2),
                'total_products_count' => $totalProducts,
                'low_stock_total' => $lowStockCount + $outOfStockCount,
                'low_stock_count' => $lowStockCount,
                'out_of_stock_count' => $outOfStockCount,
            ],
            'active_shift' => $activeShift ? [
                'id' => $activeShift->id,
                'shift_code' => $activeShift->shift_code ?: ('SHIFT-' . $activeShift->id),
                'cashier_name' => $activeShift->cashier?->name ?? 'Cashier',
                'opened_at' => $activeShift->opened_at ? Carbon::parse($activeShift->opened_at)->format('h:i A') : '-',
            ] : null,
            'sales_trend' => $trend,
            'payment_stats' => $paymentStats,
            'recent_sales' => $recentSales,
            'inventory_alerts' => $mergedAlerts,
            'top_suki' => $topSuki,
        ]);
    }

    public function promotions(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = $user?->tenant_id ?? $request->input('tenant_id', 1);

        $promos = POSPromotion::with(['items.product', 'items.category', 'items.variant', 'product', 'category'])
            ->where('tenant_id', $tenantId)
            ->orderByDesc('id')
            ->get()
            ->map(function ($promo) {
                return [
                    'id'             => $promo->id,
                    'tenant_id'      => $promo->tenant_id,
                    'title'          => $promo->title,
                    'name'           => $promo->title,
                    'promo_code'     => $promo->promo_code,
                    'code'           => $promo->promo_code,
                    'promo_type'     => $promo->promo_type,
                    'discount_type'  => $promo->promo_type,
                    'discount_value' => (float)$promo->discount_value,
                    'min_spend'      => (float)($promo->min_spend ?? 0),
                    'min_quantity'   => (int)($promo->min_quantity ?? 0),
                    'get_quantity'   => (int)($promo->get_quantity ?? 0),
                    'applies_to'     => $promo->applies_to,
                    'target_id'      => $promo->target_id,
                    'target_ids'     => $promo->target_ids ?? ($promo->items ? $promo->items->pluck('item_id')->all() : []),
                    'start_date'     => $promo->start_date ? $promo->start_date->format('Y-m-d') : null,
                    'end_date'       => $promo->end_date ? $promo->end_date->format('Y-m-d') : null,
                    'is_active'      => (bool)$promo->is_active,
                    'description'    => $promo->description,
                    'items_count'    => $promo->items ? $promo->items->count() : 0,
                    'items'          => $promo->items,
                    'product'        => $promo->product,
                    'category'       => $promo->category,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $promos,
        ]);
    }

    public function storePromotion(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = $user?->tenant_id ?? $request->input('tenant_id', 1);

        $validated = $request->validate([
            'title'          => 'required|string|max:190',
            'promo_code'     => 'nullable|string|max:50',
            'promo_type'     => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'min_spend'      => 'nullable|numeric|min:0',
            'min_quantity'   => 'nullable|integer|min:1',
            'get_quantity'   => 'nullable|integer|min:0',
            'applies_to'     => 'required|in:all,category,product,variant',
            'target_id'      => 'nullable|integer',
            'target_ids'     => 'nullable|array',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'usage_limit'    => 'nullable|integer|min:1',
            'description'    => 'nullable|string|max:500',
            'is_active'      => 'nullable|boolean',
        ]);

        $validated['tenant_id'] = $tenantId;
        $validated['is_active'] = isset($validated['is_active']) ? (bool)$validated['is_active'] : true;
        $validated['created_by'] = $user?->id ?? 1;

        if (!empty($validated['promo_code'])) {
            $validated['promo_code'] = strtoupper(trim($validated['promo_code']));
        }

        $validated['min_quantity'] = 1;
        $validated['get_quantity'] = 0;

        if ($request->filled('target_ids') && is_array($request->target_ids)) {
            $validated['target_ids'] = array_values(array_filter(array_map('intval', $request->target_ids)));
            if (empty($validated['target_id']) && count($validated['target_ids']) > 0) {
                $validated['target_id'] = $validated['target_ids'][0];
            }
        }

        $promo = POSPromotion::create($validated);

        if ($promo->applies_to !== 'all' && !empty($validated['target_ids'])) {
            foreach ($validated['target_ids'] as $tid) {
                POSPromotionItem::create([
                    'tenant_id'    => $tenantId,
                    'promotion_id' => $promo->id,
                    'item_type'    => $promo->applies_to === 'category' ? 'category' : 'product',
                    'item_id'      => $tid,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Promotion campaign created successfully!',
            'data'    => $promo->load(['product', 'category', 'items']),
        ]);
    }

    public function updatePromotion(Request $request, $id)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = $user?->tenant_id ?? $request->input('tenant_id', 1);

        $promo = POSPromotion::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'title'          => 'required|string|max:190',
            'promo_code'     => 'nullable|string|max:50',
            'promo_type'     => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'min_spend'      => 'nullable|numeric|min:0',
            'min_quantity'   => 'nullable|integer|min:1',
            'get_quantity'   => 'nullable|integer|min:0',
            'applies_to'     => 'required|in:all,category,product,variant',
            'target_id'      => 'nullable|integer',
            'target_ids'     => 'nullable|array',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'usage_limit'    => 'nullable|integer|min:1',
            'description'    => 'nullable|string|max:500',
            'is_active'      => 'nullable|boolean',
        ]);

        if (!empty($validated['promo_code'])) {
            $validated['promo_code'] = strtoupper(trim($validated['promo_code']));
        }

        $validated['min_quantity'] = 1;
        $validated['get_quantity'] = 0;

        if ($request->filled('target_ids') && is_array($request->target_ids)) {
            $validated['target_ids'] = array_values(array_filter(array_map('intval', $request->target_ids)));
            if (empty($validated['target_id']) && count($validated['target_ids']) > 0) {
                $validated['target_id'] = $validated['target_ids'][0];
            }
        }

        $promo->update($validated);

        POSPromotionItem::where('tenant_id', $tenantId)->where('promotion_id', $promo->id)->delete();
        if ($promo->applies_to !== 'all' && !empty($validated['target_ids'])) {
            foreach ($validated['target_ids'] as $tid) {
                POSPromotionItem::create([
                    'tenant_id'    => $tenantId,
                    'promotion_id' => $promo->id,
                    'item_type'    => $promo->applies_to === 'category' ? 'category' : 'product',
                    'item_id'      => $tid,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Promotion updated successfully!',
            'data'    => $promo->fresh(['product', 'category', 'items']),
        ]);
    }

    public function togglePromotion(Request $request, $id)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = $user?->tenant_id ?? $request->input('tenant_id', 1);

        $promo = POSPromotion::where('tenant_id', $tenantId)->findOrFail($id);
        $promo->is_active = !$promo->is_active;
        $promo->save();

        return response()->json([
            'success'   => true,
            'is_active' => (bool)$promo->is_active,
            'message'   => $promo->is_active ? 'Promotion activated' : 'Promotion deactivated',
        ]);
    }

    public function destroyPromotion(Request $request, $id)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = $user?->tenant_id ?? $request->input('tenant_id', 1);

        $promo = POSPromotion::where('tenant_id', $tenantId)->findOrFail($id);
        $promo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Promotion removed successfully!',
        ]);
    }

    public function promoItems(Request $request, $id)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = $user?->tenant_id ?? $request->input('tenant_id', 1);

        $promo = POSPromotion::where('tenant_id', $tenantId)->findOrFail($id);

        $items = POSPromotionItem::where('tenant_id', $tenantId)
            ->where('promotion_id', $id)
            ->with(['product.category', 'category', 'variant.product'])
            ->latest('id')
            ->get();

        return response()->json([
            'success' => true,
            'promo'   => $promo,
            'data'    => $items,
        ]);
    }

    public function addPromoItems(Request $request, $id)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = $user?->tenant_id ?? $request->input('tenant_id', 1);

        $promo = POSPromotion::where('tenant_id', $tenantId)->findOrFail($id);

        $itemIds = $request->input('item_ids', []);
        if (!is_array($itemIds) && $request->filled('item_id')) {
            $itemIds = [(int)$request->input('item_id')];
        }

        $itemType = $request->input('item_type', $promo->applies_to === 'category' ? 'category' : 'product');

        $addedCount = 0;
        foreach ($itemIds as $itemId) {
            $itemId = (int)$itemId;
            $exists = POSPromotionItem::where('tenant_id', $tenantId)
                ->where('promotion_id', $id)
                ->where('item_type', $itemType)
                ->where('item_id', $itemId)
                ->exists();

            if (!$exists) {
                POSPromotionItem::create([
                    'tenant_id'    => $tenantId,
                    'promotion_id' => $id,
                    'item_type'    => $itemType,
                    'item_id'      => $itemId,
                ]);
                $addedCount++;
            }
        }

        $allTids = POSPromotionItem::where('tenant_id', $tenantId)
            ->where('promotion_id', $id)
            ->pluck('item_id')
            ->all();

        $promo->target_ids = $allTids;
        $promo->target_id = count($allTids) > 0 ? $allTids[0] : null;
        if ($promo->applies_to === 'all') {
            $promo->applies_to = $itemType;
        }
        $promo->save();

        return response()->json([
            'success'     => true,
            'message'     => $addedCount . ' item(s) added to promotion successfully!',
            'added_count' => $addedCount,
            'total_items' => count($allTids),
            'data'        => $promo->load(['items.product', 'items.category']),
        ]);
    }

    public function removePromoItem(Request $request, $id, $itemId)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = $user?->tenant_id ?? $request->input('tenant_id', 1);

        $promo = POSPromotion::where('tenant_id', $tenantId)->findOrFail($id);

        POSPromotionItem::where('tenant_id', $tenantId)
            ->where('promotion_id', $id)
            ->where(function ($q) use ($itemId) {
                $q->where('id', $itemId)->orWhere('item_id', $itemId);
            })
            ->delete();

        $allTids = POSPromotionItem::where('tenant_id', $tenantId)
            ->where('promotion_id', $id)
            ->pluck('item_id')
            ->all();

        $promo->target_ids = $allTids;
        $promo->target_id = count($allTids) > 0 ? $allTids[0] : null;
        $promo->save();

        return response()->json([
            'success'     => true,
            'message'     => 'Item removed from promotion.',
            'total_items' => count($allTids),
            'data'        => $promo->load(['items.product', 'items.category']),
        ]);
    }

    public function clearPromoItems(Request $request, $id)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = $user?->tenant_id ?? $request->input('tenant_id', 1);

        $promo = POSPromotion::where('tenant_id', $tenantId)->findOrFail($id);

        POSPromotionItem::where('tenant_id', $tenantId)
            ->where('promotion_id', $id)
            ->delete();

        $promo->target_ids = [];
        $promo->target_id = null;
        $promo->save();

        return response()->json([
            'success' => true,
            'message' => 'All items cleared from promotion.',
            'data'    => $promo,
        ]);
    }

    public function activeShift(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = $user?->tenant_id ?? $request->input('tenant_id');

        $shift = POSCashShift::with(['cashier', 'drawer', 'terminal'])
            ->where('tenant_id', $tenantId)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        if ($shift) {
            $shiftSales = (float) POSPayment::where('shift_id', $shift->id)->sum('amount');
            $shiftCashSales = (float) POSPayment::where('shift_id', $shift->id)->where('payment_method', 'cash')->sum('amount');
            $currentCashInDrawer = (float)$shift->opening_cash + $shiftCashSales;

            return response()->json([
                'success' => true,
                'has_active_shift' => true,
                'shift' => [
                    'id' => $shift->id,
                    'shift_code' => $shift->shift_code ?: ('SHIFT-' . $shift->id),
                    'cashier_name' => $shift->cashier?->name ?? ($user?->name ?? 'Cashier'),
                    'drawer_name' => $shift->drawer?->drawer_name ?? 'Main Drawer',
                    'opening_cash' => (float)$shift->opening_cash,
                    'opening_cash_formatted' => '₱' . number_format($shift->opening_cash, 2),
                    'shift_sales' => $shiftSales,
                    'shift_sales_formatted' => '₱' . number_format($shiftSales, 2),
                    'current_cash' => $currentCashInDrawer,
                    'current_cash_formatted' => '₱' . number_format($currentCashInDrawer, 2),
                    'opened_at' => $shift->opened_at ? Carbon::parse($shift->opened_at)->format('M d, Y h:i A') : '-',
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'has_active_shift' => false,
            'shift' => null,
        ]);
    }

    public function openShift(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = $user?->tenant_id ?? $request->input('tenant_id');

        $request->validate([
            'opening_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $existing = POSCashShift::where('tenant_id', $tenantId)->where('status', 'open')->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'There is already an active open shift (' . ($existing->shift_code ?: $existing->id) . '). Close it first.',
            ], 422);
        }

        $shift = new POSCashShift();
        $shift->tenant_id = $tenantId;
        $shift->cashier_id = $user?->id ?? auth()->id();
        $shift->opening_cash = $request->input('opening_cash', 2000);
        $shift->status = 'open';
        $shift->opened_at = now();
        $shift->notes = $request->input('notes');
        $shift->shift_code = 'SHIFT-' . date('YmdHis');
        $shift->save();

        return response()->json([
            'success' => true,
            'message' => 'Cash shift opened successfully.',
            'shift' => $shift,
        ]);
    }

    public function closeShift(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = $user?->tenant_id ?? $request->input('tenant_id');

        $shift = POSCashShift::where('tenant_id', $tenantId)->where('status', 'open')->latest('opened_at')->first();
        if (!$shift) {
            return response()->json(['success' => false, 'message' => 'No active shift found to close.'], 404);
        }

        $actualCash = (float) $request->input('actual_cash', 0);
        $shiftCashSales = (float) POSPayment::where('shift_id', $shift->id)->where('payment_method', 'cash')->sum('amount');
        $expectedCash = (float)$shift->opening_cash + $shiftCashSales;
        $shortageExcess = $actualCash - $expectedCash;

        $shift->closing_cash = $actualCash;
        $shift->expected_cash = $expectedCash;
        $shift->difference = $shortageExcess;
        $shift->status = 'closed';
        $shift->closed_at = now();
        $shift->closing_notes = $request->input('notes');
        $shift->save();

        return response()->json([
            'success' => true,
            'message' => 'Shift closed successfully.',
            'shift' => $shift,
        ]);
    }

    public function ordersSwitcher(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = $user?->tenant_id ?? $request->input('tenant_id');

        $sales = POSSale::with(['customer', 'items', 'payments', 'cashier'])
            ->where('tenant_id', $tenantId)
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->map(function ($sale) {
                $isCompleted = strtolower($sale->sale_status ?? '') === 'completed';
                $customerName = $sale->customer?->CustomerName ?: ($sale->customer?->name ?? 'Walk-in Customer');
                $itemsCount = $sale->items->count();
                $totalQty = (float) $sale->items->sum('qty');

                return [
                    'id' => $sale->id,
                    'sale_code' => $sale->sale_code ?: ('#' . $sale->id),
                    'invoice_no' => $sale->invoice_no ?: $sale->sale_code,
                    'customer_name' => $customerName,
                    'status' => $isCompleted ? 'completed' : 'pending',
                    'status_label' => $isCompleted ? 'Completed (Paid)' : 'Open / In-Progress',
                    'total_amount' => (float)$sale->total_amount,
                    'total_formatted' => '₱' . number_format($sale->total_amount, 2),
                    'items_count' => $itemsCount,
                    'total_qty' => $totalQty,
                    'payment_method' => ucfirst(str_replace('_', ' ', $sale->payment_method ?? 'cash')),
                    'time_formatted' => $sale->sale_date ? $sale->sale_date->format('h:i A') : ($sale->created_at ? $sale->created_at->format('h:i A') : '-'),
                    'date_formatted' => $sale->sale_date ? $sale->sale_date->format('M d, Y') : ($sale->created_at ? $sale->created_at->format('M d, Y') : '-'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $sales,
        ]);
    }

    public function getSettings(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = $user?->tenant_id ?? $request->input('tenant_id');

        $tenant = POSTenant::with('subscription')->find($tenantId);
        if (!$tenant) {
            $tenant = POSTenant::with('subscription')->first();
        }

        return response()->json([
            'success' => true,
            'tenant' => $tenant,
            'theme' => $tenant?->theme_settings ?? [
                'preset' => 'emerald',
                'primary_color' => '#059669',
                'topbar_color' => '#064E3B',
                'topbar_text_color' => '#FFFFFF',
                'sidebar_color' => '#0F172A',
                'sidebar_text_color' => '#CBD5E1',
                'sidebar_active_color' => '#059669',
                'sidebar_active_text_color' => '#FFFFFF',
                'accent_color' => '#10B981',
                'dark_mode' => false,
            ],
            'crm' => $tenant?->crm_settings ?? [
                'points_per_peso' => 0.01,
                'default_credit_limit' => 5000,
                'sms_receipt_enabled' => true,
                'sms_utang_reminder_enabled' => true,
            ],
        ]);
    }

    public function updateSettings(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        $tenantId = $user?->tenant_id ?? $request->input('tenant_id');

        $tenant = POSTenant::where('id', $tenantId)->first();
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Tenant not found.'], 404);
        }

        if ($request->has('business_name')) $tenant->business_name = $request->input('business_name');
        if ($request->has('owner_name')) $tenant->owner_name = $request->input('owner_name');
        if ($request->has('phone')) $tenant->phone = $request->input('phone');
        if ($request->has('email')) $tenant->email = $request->input('email');
        if ($request->has('address')) $tenant->address = $request->input('address');
        if ($request->has('tin')) $tenant->tin = $request->input('tin');
        if ($request->has('header_text')) $tenant->header_text = $request->input('header_text');
        if ($request->has('footer_text')) $tenant->footer_text = $request->input('footer_text');

        if ($request->has('theme')) {
            $tenant->theme_settings = array_merge($tenant->theme_settings ?? [], $request->input('theme', []));
        }

        if ($request->has('crm')) {
            $tenant->crm_settings = array_merge($tenant->crm_settings ?? [], $request->input('crm', []));
        }

        $tenant->save();

        return response()->json([
            'success' => true,
            'message' => 'Store settings, theme, and CRM configurations saved successfully.',
            'tenant' => $tenant,
            'theme' => $tenant->theme_settings,
            'crm' => $tenant->crm_settings,
        ]);
    }
}




