<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSCategories;
use App\Models\POS\POSProducts;
use App\Models\POS\POSProductVariant;
use App\Models\POS\POSPromotion;
use App\Models\POS\POSPromotionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PromotionController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $categories = POSCategories::where('tenant_id', $tenantId)->orderBy('name')->get();
        $products = POSProducts::where('tenant_id', $tenantId)->orderBy('name')->get();
        $promoTypes = POSPromotion::promoTypes();

        $activePromosCount = POSPromotion::where('tenant_id', $tenantId)->active()->count();
        $totalPromosCount = POSPromotion::where('tenant_id', $tenantId)->count();
        $bulkPromosCount = POSPromotion::where('tenant_id', $tenantId)->where('promo_type', 'bulk_tier')->count();

        return view('pages.tenants.promotions.index', compact(
            'categories',
            'products',
            'promoTypes',
            'activePromosCount',
            'totalPromosCount',
            'bulkPromosCount'
        ));
    }

    public function ajaxData(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $query = POSPromotion::where('tenant_id', $tenantId)
            ->with(['product', 'category', 'variant', 'items'])
            ->withCount('items')
            ->latest('id');

        if ($request->filled('promo_type')) {
            $query->where('promo_type', $request->promo_type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        return DataTables::of($query)
            ->addColumn('actions', function ($row) {
                $itemsBtn = '';
                if ($row->applies_to !== 'all') {
                    $itemsBtn = '
                        <a href="' . route('promotions.items.index', $row->id) . '" class="btn btn-sm btn-primary text-white fw-bold rounded-pill px-2.5 py-1 shadow-xs d-inline-flex align-items-center gap-1" title="Manage Items Included in Promo">
                            <i class="bi bi-boxes"></i>
                            <span>Items (' . $row->items_count . ')</span>
                        </a>
                    ';
                }

                return '
                    <div class="d-flex align-items-center gap-1.5">
                        ' . $itemsBtn . '
                        <button type="button" class="btn btn-sm btn-light border btn-edit-promo" data-id="' . $row->id . '" title="Edit Promo Settings">
                            <i class="bi bi-pencil text-primary"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-light border btn-delete-promo" data-id="' . $row->id . '" data-title="' . e($row->title) . '" title="Delete Promo">
                            <i class="bi bi-trash text-danger"></i>
                        </button>
                    </div>
                ';
            })
            ->addColumn('promo_badge', function ($row) {
                $code = $row->promo_code ? '<span class="badge bg-dark font-mono mt-1"><i class="bi bi-ticket-perforated me-1"></i>' . e($row->promo_code) . '</span>' : '<span class="badge bg-light text-muted border font-mono mt-1">Automatic</span>';
                return '
                    <div>
                        <div class="fw-bold text-dark">' . e($row->title) . '</div>
                        ' . $code . '
                    </div>
                ';
            })
            ->addColumn('type_badge', function ($row) {
                $badges = [
                    'percentage'   => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill"><i class="bi bi-percent me-1"></i>Percentage</span>',
                    'fixed_amount' => '<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill"><i class="bi bi-tag-fill me-1"></i>Fixed Discount</span>',
                    'bulk_tier'    => '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 rounded-pill"><i class="bi bi-boxes me-1"></i>Wholesale / Bulk</span>',
                    'buy_x_get_y'  => '<span class="badge bg-purple-subtle text-purple border border-purple-subtle px-2.5 py-1 rounded-pill" style="background:#ede9fe; color:#6d28d9;"><i class="bi bi-gift-fill me-1"></i>Buy X Get Y</span>',
                ];
                return $badges[$row->promo_type] ?? '<span class="badge bg-secondary">' . e($row->promo_type) . '</span>';
            })
            ->addColumn('discount_rule', function ($row) {

                // ── Profitability note helper ──
                // For percentage: warn if discount >= 50% (likely danger zone)
                // For bulk_tier: load avg cost of targeted products and compare
                $profitNote = '';

                if ($row->promo_type === 'percentage') {
                    $disc = (float) $row->discount_value;
                    if ($disc >= 70) {
                        $profitNote = '<div class="mt-1 d-flex align-items-center gap-1" style="font-size:.68rem;">'
                            . '<span class="badge px-1.5 py-0.5 rounded-1 fw-bold" style="background:#fee2e2;color:#dc2626;">'
                            . '<i class="bi bi-exclamation-triangle-fill me-1"></i>HIGH RISK — likely a loss if margin &lt; ' . $disc . '%</span></div>';
                    } elseif ($disc >= 40) {
                        $profitNote = '<div class="mt-1 d-flex align-items-center gap-1" style="font-size:.68rem;">'
                            . '<span class="badge px-1.5 py-0.5 rounded-1 fw-bold" style="background:#fef9c3;color:#92400e;">'
                            . '<i class="bi bi-exclamation-circle-fill me-1"></i>CAUTION — profitable only if margin &gt; ' . $disc . '%</span></div>';
                    } elseif ($disc >= 10) {
                        $profitNote = '<div class="mt-1 d-flex align-items-center gap-1" style="font-size:.68rem;">'
                            . '<span class="badge px-1.5 py-0.5 rounded-1 fw-bold" style="background:#dcfce7;color:#166534;">'
                            . '<i class="bi bi-check-circle-fill me-1"></i>LOW RISK — safe if margin &gt; ' . $disc . '%</span></div>';
                    }
                    return '<span class="fw-black text-primary font-mono fs-6">' . $disc . '% OFF</span>'
                        . ($row->min_spend > 0 ? '<div class="extra-small text-muted">Min: ₱' . number_format($row->min_spend, 2) . '</div>' : '')
                        . $profitNote;

                } elseif ($row->promo_type === 'fixed_amount') {
                    $note = '<div class="mt-1" style="font-size:.68rem;">'
                        . '<span class="badge px-1.5 py-0.5 rounded-1" style="background:#eff6ff;color:#1d4ed8;">'
                        . '<i class="bi bi-info-circle me-1"></i>Check item cost &gt; sale price to avoid loss</span></div>';
                    return '<span class="fw-black text-success font-mono fs-6">₱' . number_format($row->discount_value, 2) . ' OFF</span>'
                        . ($row->min_spend > 0 ? '<div class="extra-small text-muted">Min: ₱' . number_format($row->min_spend, 2) . '</div>' : '')
                        . $note;

                } elseif ($row->promo_type === 'bulk_tier') {
                    // Load avg cost of first targeted product
                    $avgCost = null;
                    if ($row->target_id) {
                        $prod = \App\Models\POS\POSProducts::find($row->target_id);
                        $avgCost = $prod ? (float)($prod->cost_price ?? 0) : null;
                    } elseif ($row->items_count > 0) {
                        $avgCost = \App\Models\POS\POSPromotionItem::where('promotion_id', $row->id)
                            ->with('product')
                            ->limit(5)
                            ->get()
                            ->filter(fn($i) => $i->product)
                            ->avg(fn($i) => (float)($i->product->cost_price ?? 0));
                    }
                    $promoPrice = (float) $row->discount_value;
                    if ($avgCost !== null && $avgCost > 0) {
                        if ($promoPrice <= $avgCost) {
                            $profitNote = '<div class="mt-1" style="font-size:.68rem;"><span class="badge px-1.5 py-0.5 rounded-1 fw-bold" style="background:#fee2e2;color:#dc2626;">'
                                . '<i class="bi bi-x-circle-fill me-1"></i>LOSS — price ₱' . number_format($promoPrice,2) . ' ≤ cost ₱' . number_format($avgCost,2) . '</span></div>';
                        } else {
                            $margin = round((($promoPrice - $avgCost) / $promoPrice) * 100, 1);
                            $bg = $margin >= 20 ? '#dcfce7' : '#fef9c3';
                            $clr = $margin >= 20 ? '#166534' : '#92400e';
                            $icon = $margin >= 20 ? 'check-circle-fill' : 'exclamation-circle-fill';
                            $profitNote = '<div class="mt-1" style="font-size:.68rem;"><span class="badge px-1.5 py-0.5 rounded-1 fw-bold" style="background:' . $bg . ';color:' . $clr . ';">' .
                                '<i class="bi bi-' . $icon . ' me-1"></i>' . $margin . '% margin — cost ₱' . number_format($avgCost,2) . '</span></div>';
                        }
                    } else {
                        $profitNote = '<div class="mt-1" style="font-size:.68rem;"><span class="badge px-1.5 py-0.5 rounded-1" style="background:#f1f5f9;color:#64748b;">'
                            . '<i class="bi bi-question-circle me-1"></i>Set product cost price for profit check</span></div>';
                    }
                    return '<span class="fw-black text-warning-emphasis font-mono fs-6">₱' . number_format($row->discount_value, 2) . ' / pc</span>'
                        . '<div class="extra-small text-muted">For ' . $row->min_quantity . '+ pcs</div>'
                        . $profitNote;

                } elseif ($row->promo_type === 'buy_x_get_y') {
                    $note = '<div class="mt-1" style="font-size:.68rem;"><span class="badge px-1.5 py-0.5 rounded-1" style="background:#fef9c3;color:#92400e;">'
                        . '<i class="bi bi-gift me-1"></i>You absorb cost of free items — check margins</span></div>';
                    return '<span class="fw-black text-purple font-mono" style="color:#6d28d9;">Buy ' . $row->min_quantity . ' Get ' . $row->get_quantity . ' FREE</span>' . $note;
                }
                return '—';
            })
            ->addColumn('applies_to_badge', function ($row) {
                if ($row->applies_to === 'all') {
                    return '<span class="badge bg-secondary-subtle text-secondary border px-2 py-1"><i class="bi bi-cart4 me-1"></i>Entire Cart</span>';
                } elseif ($row->applies_to === 'category') {
                    if ($row->target_ids && is_array($row->target_ids) && count($row->target_ids) > 1) {
                        return '<span class="badge bg-info-subtle text-info-emphasis border px-2 py-1"><i class="bi bi-tags-fill me-1"></i>' . count($row->target_ids) . ' Categories</span>';
                    }
                    return '<span class="badge bg-info-subtle text-info-emphasis border px-2 py-1"><i class="bi bi-tag me-1"></i>' . e($row->category?->name ?? 'Category') . '</span>';
                } elseif ($row->applies_to === 'product') {
                    if ($row->target_ids && is_array($row->target_ids) && count($row->target_ids) > 1) {
                        return '<span class="badge bg-dark-subtle text-dark border px-2 py-1"><i class="bi bi-boxes me-1"></i>' . count($row->target_ids) . ' Products</span>';
                    }
                    return '<span class="badge bg-dark-subtle text-dark border px-2 py-1"><i class="bi bi-box me-1"></i>' . e($row->product?->name ?? 'Product') . '</span>';
                }
                return '<span class="badge bg-light text-dark border">' . e($row->applies_to) . '</span>';
            })
            ->addColumn('duration', function ($row) {
                if (!$row->start_date && !$row->end_date) {
                    return '<span class="badge bg-light text-success border"><i class="bi bi-infinity me-1"></i>Always Active</span>';
                }
                $start = $row->start_date ? $row->start_date->format('M d, Y') : 'Start';
                $end = $row->end_date ? $row->end_date->format('M d, Y') : 'Ongoing';
                return '<span class="small font-mono text-muted">' . $start . ' - ' . $end . '</span>';
            })
            ->addColumn('status_toggle', function ($row) {
                $checked = $row->is_active ? 'checked' : '';
                return '
                    <div class="form-check form-switch d-inline-block">
                        <input class="form-check-input btn-toggle-status" type="checkbox" role="switch" data-id="' . $row->id . '" ' . $checked . '>
                    </div>
                ';
            })
            ->rawColumns(['actions', 'promo_badge', 'type_badge', 'discount_rule', 'applies_to_badge', 'duration', 'status_toggle'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:190',
            'promo_code'     => 'nullable|string|max:50',
            'promo_type'     => 'required|in:percentage,fixed_amount,bulk_tier,buy_x_get_y',
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
        ]);

        $tenantId = auth()->user()->tenant_id;
        $validated['tenant_id'] = $tenantId;
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;
        $validated['created_by'] = auth()->id();
        if (!empty($validated['promo_code'])) {
            $validated['promo_code'] = strtoupper(trim($validated['promo_code']));
        }

        if (in_array($validated['promo_type'], ['percentage', 'fixed_amount'])) {
            $validated['min_quantity'] = 1;
            $validated['get_quantity'] = 0;
        }

        if ($request->filled('target_ids') && is_array($request->target_ids)) {
            $validated['target_ids'] = array_values(array_filter(array_map('intval', $request->target_ids)));
            if (empty($validated['target_id']) && count($validated['target_ids']) > 0) {
                $validated['target_id'] = $validated['target_ids'][0];
            }
        }

        $promo = POSPromotion::create($validated);

        $redirectUrl = ($promo->applies_to !== 'all')
            ? route('promotions.items.index', $promo->id)
            : null;

        return response()->json([
            'success'      => true,
            'message'      => 'Promotion created successfully!',
            'data'         => $promo,
            'redirect_url' => $redirectUrl,
        ]);
    }

    public function show($id)
    {
        $promo = POSPromotion::where('tenant_id', auth()->user()->tenant_id)->withCount('items')->findOrFail($id);
        return response()->json($promo);
    }

    public function update(Request $request, $id)
    {
        $tenantId = auth()->user()->tenant_id;
        $promo = POSPromotion::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'title'          => 'required|string|max:190',
            'promo_code'     => 'nullable|string|max:50',
            'promo_type'     => 'required|in:percentage,fixed_amount,bulk_tier,buy_x_get_y',
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
        ]);

        if (in_array($validated['promo_type'], ['percentage', 'fixed_amount'])) {
            $validated['min_quantity'] = 1;
            $validated['get_quantity'] = 0;
        }

        $wasAll = ($promo->applies_to === 'all');
        $promo->update($validated);

        if ($promo->applies_to === 'all' && !$wasAll) {
            POSPromotionItem::where('tenant_id', $tenantId)->where('promotion_id', $promo->id)->delete();
        }

        $redirectUrl = ($promo->applies_to !== 'all')
            ? route('promotions.items.index', $promo->id)
            : null;

        return response()->json([
            'success'      => true,
            'message'      => 'Promotion updated successfully!',
            'data'         => $promo,
            'redirect_url' => $redirectUrl,
        ]);
    }

    public function toggleActive($id)
    {
        $promo = POSPromotion::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);
        $promo->is_active = !$promo->is_active;
        $promo->save();

        return response()->json([
            'success'   => true,
            'is_active' => $promo->is_active,
            'message'   => $promo->is_active ? 'Promotion activated' : 'Promotion deactivated',
        ]);
    }

    public function destroy($id)
    {
        $promo = POSPromotion::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);
        $promo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Promotion removed successfully!',
        ]);
    }

    /**
     * Real-time Profit Preview for Promo Modal
     * Returns cost stats for a set of product/variant IDs so the frontend
     * can predict profit/loss before saving the promotion.
     */
    public function profitPreview(Request $request)
    {
        $tenantId  = auth()->user()->tenant_id;
        $promoType = $request->get('promo_type', 'percentage');
        $discValue = (float) $request->get('discount_value', 0);
        $minQty    = (int)   $request->get('min_quantity', 1);
        $getQty    = (int)   $request->get('get_quantity', 0);
        $appliesTo = $request->get('applies_to', 'all');
        $productIds= $request->get('product_ids', []);

        // ── Helper: effective sale price under this promo ─────────────────────
        $effectivePrice = function (float $base) use ($promoType, $discValue, $minQty, $getQty): float {
            switch ($promoType) {
                case 'percentage':
                    return max(0.0, $base * (1 - $discValue / 100));
                case 'fixed_amount':
                    return max(0.0, $base - $discValue);
                case 'bulk_tier':
                    return $discValue > 0 ? (float)$discValue : $base;
                case 'buy_x_get_y':
                    $t = $minQty + $getQty;
                    return $t > 0 ? ($base * $minQty / $t) : $base;
                default:
                    return $base;
            }
        };

        // ── Helper: build one sample row ──────────────────────────────────────
        $makeRow = function (string $label, float $cost, float $retailP, float $wsP) use ($effectivePrice): array {
            // Retail
            $rSale   = $effectivePrice($retailP);
            $rProfit = $rSale - $cost;
            $rMargin = $rSale > 0 ? round(($rProfit / $rSale) * 100, 1) : 0.0;

            // Wholesale
            $hasWs   = $wsP > 0;
            $wSale   = $hasWs ? $effectivePrice($wsP)    : null;
            $wProfit = $hasWs ? ($wSale - $cost)          : null;
            $wMargin = ($hasWs && $wSale > 0)
                        ? round(($wProfit / $wSale) * 100, 1)
                        : null;

            return [
                'name'             => $label,
                'cost'             => $cost,
                'retail_orig'      => $retailP,
                'retail_sale'      => round($rSale, 2),
                'retail_profit'    => round($rProfit, 2),
                'retail_margin'    => $rMargin,
                'retail_ok'        => $rProfit >= 0,
                'has_wholesale'    => $hasWs,
                'wholesale_orig'   => $wsP,
                'wholesale_sale'   => $wSale   !== null ? round($wSale, 2)   : null,
                'wholesale_profit' => $wProfit !== null ? round($wProfit, 2) : null,
                'wholesale_margin' => $wMargin,
                'wholesale_ok'     => $wProfit !== null ? ($wProfit >= 0)    : null,
            ];
        };
        if ($appliesTo === 'all' || empty($productIds)) {
            $products = \App\Models\POS\POSProducts::where('tenant_id', $tenantId)
                ->whereNotNull('cost_price')->where('cost_price', '>', 0)
                ->where('selling_price', '>', 0)
                ->with(['variants' => function ($q) {
                    $q->where('selling_price', '>', 0)->orWhere('cost_price', '>', 0);
                }])
                ->limit(150)->get();
        } else {
            $products = \App\Models\POS\POSProducts::where('tenant_id', $tenantId)
                ->whereIn('id', $productIds)
                ->with(['variants' => function ($q) {
                    $q->where('selling_price', '>', 0)->orWhere('cost_price', '>', 0);
                }])
                ->get();
        }

        if ($products->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No products with cost prices found. Please set cost prices on your products.',
                'samples' => [],
            ]);
        }

        // ── Build unified samples list (parents + variants) ───────────────────
        $rows = collect();

        foreach ($products as $p) {
            $pCost   = (float) ($p->cost_price ?? 0);
            $pRetail = (float) ($p->selling_price ?? 0);
            $pWs     = (float) ($p->wholesale_price ?? 0);

            $hasVariants = $p->variants->isNotEmpty();

            if (!$hasVariants) {
                if ($pRetail > 0) {
                    $rows->push($makeRow($p->name, $pCost, $pRetail, $pWs));
                }
            } else {
                foreach ($p->variants as $v) {
                    $vCost   = (float) ($v->cost_price ?? 0) ?: $pCost;
                    $vRetail = (float) ($v->selling_price ?: $p->selling_price ?? 0);
                    $vWs     = (float) ($v->wholesale_price ?: $pWs ?? 0);
                    $label   = $p->name . ' — ' . $v->variant_name;

                    if ($vRetail > 0 && $vCost > 0) {
                        $rows->push($makeRow($label, $vCost, $vRetail, $vWs));
                    }
                }
            }
        }

        if ($rows->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No priceable items found. Make sure products/variants have selling price and cost price set.',
                'samples' => [],
            ]);
        }

        // ── Aggregate ─────────────────────────────────────────────────────────
        $total        = $rows->count();
        $retailLoss   = $rows->where('retail_ok', false)->count();
        $wsRows       = $rows->where('has_wholesale', true);
        $wsLoss       = $wsRows->where('wholesale_ok', false)->count();

        return response()->json([
            'success'              => true,
            'total_evaluated'      => $total,
            'retail_loss_count'    => $retailLoss,
            'retail_profit_count'  => $rows->where('retail_ok', true)->count(),
            'avg_retail_margin'    => round($rows->avg('retail_margin'), 1),
            'avg_retail_profit'    => round($rows->avg('retail_profit'), 2),
            'wholesale_item_count' => $wsRows->count(),
            'wholesale_loss_count' => $wsLoss,
            'avg_ws_margin'        => $wsRows->count() > 0 ? round($wsRows->avg('wholesale_margin'), 1) : null,
            'avg_ws_profit'        => $wsRows->count() > 0 ? round($wsRows->avg('wholesale_profit'), 2) : null,
            'samples'              => $rows->sortBy('retail_margin')->take(12)->values(),
        ]);
    }


    /**
     * Evaluate Promo Code or Auto-Discounts from POS Terminal
     */
    public function validatePromo(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $code = strtoupper(trim($request->get('code', '')));
        $subtotal = (float)$request->get('subtotal', 0);
        $items = $request->get('items', []);

        if (!$code) {
            return response()->json(['success' => false, 'message' => 'Please enter a promo code.'], 422);
        }

        $promo = POSPromotion::where('tenant_id', $tenantId)
            ->where('promo_code', $code)
            ->active()
            ->first();

        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired promo code.'], 404);
        }

        if ($promo->min_spend > 0 && $subtotal < $promo->min_spend) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum spend of ₱' . number_format($promo->min_spend, 2) . ' required for this promo.'
            ], 422);
        }

        // Calculate discount amount
        $discount = 0;
        if ($promo->promo_type === 'percentage') {
            $discount = ($subtotal * ($promo->discount_value / 100));
        } elseif ($promo->promo_type === 'fixed_amount') {
            $discount = min($promo->discount_value, $subtotal);
        }

        return response()->json([
            'success'         => true,
            'message'         => 'Promo applied: ' . $promo->title,
            'promo_id'        => $promo->id,
            'promo_code'      => $promo->promo_code,
            'promo_title'     => $promo->title,
            'promo_type'      => $promo->promo_type,
            'discount_value'  => (float)$promo->discount_value,
            'discount_amount' => round($discount, 2),
        ]);
    }

    /**
     * Get all active automatic promotions (bulk tiers, auto percent) for POS Terminal cart
     */
    public function getActivePromos()
    {
        $tenantId = auth()->user()->tenant_id;
        $promos = POSPromotion::where('tenant_id', $tenantId)
            ->active()
            ->whereNull('promo_code')
            ->with(['items'])
            ->get();

        return response()->json([
            'success' => true,
            'promotions' => $promos,
        ]);
    }

    /**
     * Dedicated Page: Manage Items Included in Promotion
     */
    public function manageItems($id)
    {
        $tenantId = auth()->user()->tenant_id;
        $promo = POSPromotion::where('tenant_id', $tenantId)->withCount('items')->findOrFail($id);
        $categories = POSCategories::where('tenant_id', $tenantId)->orderBy('name')->get();

        return view('pages.tenants.promotions.manage_items', compact('promo', 'categories'));
    }

    /**
     * DataTables for Active Items in Promotion
     */
    public function itemsData($id)
    {
        $tenantId = auth()->user()->tenant_id;
        $promo = POSPromotion::where('tenant_id', $tenantId)->findOrFail($id);

        $query = POSPromotionItem::query()
            ->select('pos_promotion_items.*')
            ->where('pos_promotion_items.tenant_id', $tenantId)
            ->where('pos_promotion_items.promotion_id', $id)
            ->with(['product.category', 'product.unit', 'category', 'variant.product'])
            ->latest('pos_promotion_items.id');

        return DataTables::of($query)
            ->filter(function ($query) {
                if (request()->has('search') && !empty(request('search')['value'])) {
                    $search = request('search')['value'];
                    $query->where(function ($q) use ($search) {
                        $q->whereHas('product', function ($pq) use ($search) {
                            $pq->where('name', 'like', "%{$search}%")
                               ->orWhere('barcode', 'like', "%{$search}%")
                               ->orWhere('sku', 'like', "%{$search}%");
                        })->orWhereHas('category', function ($cq) use ($search) {
                            $cq->where('name', 'like', "%{$search}%");
                        });
                    });
                }
            })
            ->addColumn('item_name', function ($row) {
                if ($row->item_type === 'category') {
                    return '
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-info-subtle text-info-emphasis p-2 rounded-circle"><i class="bi bi-tag-fill"></i></span>
                            <div>
                                <span class="fw-bold text-dark">' . e($row->category?->name ?? 'Category') . '</span>
                                <div class="extra-small text-muted">All products in this category</div>
                            </div>
                        </div>
                    ';
                } elseif ($row->item_type === 'variant') {
                    $prodName = $row->variant?->product?->name ?? 'Product';
                    $varName = $row->variant?->variant_name ?? 'Variant';
                    return '
                        <div>
                            <span class="fw-bold text-dark">' . e($prodName) . ' (' . e($varName) . ')</span>
                            <div class="extra-small text-muted font-mono">Barcode: ' . e($row->variant?->barcode ?: '—') . '</div>
                        </div>
                    ';
                } else {
                    return '
                        <div>
                            <span class="fw-bold text-dark">' . e($row->product?->name ?? 'Product') . '</span>
                            <div class="extra-small text-muted font-mono">Barcode: ' . e($row->product?->barcode ?: '—') . ' | SKU: ' . e($row->product?->sku ?: '—') . '</div>
                        </div>
                    ';
                }
            })
            ->addColumn('category_badge', function ($row) {
                if ($row->item_type === 'category') {
                    return '<span class="badge bg-info-subtle text-info-emphasis border px-2 py-1"><i class="bi bi-tags me-1"></i>Entire Category</span>';
                }
                $catName = $row->product?->category?->name ?? ($row->variant?->product?->category?->name ?? 'Uncategorized');
                return '<span class="badge bg-light text-dark border">' . e($catName) . '</span>';
            })
            ->addColumn('regular_price', function ($row) {
                if ($row->item_type === 'category') {
                    return '<span class="text-muted extra-small">Category items</span>';
                }
                $price = (float)($row->variant ? ($row->variant->selling_price ?: $row->variant->price) : ($row->product?->selling_price ?: $row->product?->price ?? 0));
                return '<span class="font-mono fw-bold text-dark fs-6">₱' . number_format($price, 2) . '</span>';
            })
            ->addColumn('sale_price', function ($row) use ($promo) {
                if ($row->item_type === 'category') {
                    if ($promo->promo_type === 'percentage') {
                        return '<span class="badge bg-primary-subtle text-primary font-mono fw-bold px-2 py-1">' . (float)$promo->discount_value . '% OFF</span>'
                            . '<div class="mt-1 extra-small text-muted"><i class="bi bi-info-circle me-1"></i>Profit depends on each product\'s cost</div>';
                    }
                    return '<span class="badge bg-success-subtle text-success font-mono fw-bold px-2 py-1">₱' . number_format($promo->discount_value, 2) . ' OFF</span>'
                        . '<div class="mt-1 extra-small text-muted"><i class="bi bi-info-circle me-1"></i>Verify item cost vs discounted price</div>';
                }

                // Resolve cost price from product or variant
                $costPrice  = (float) ($row->variant?->product?->cost_price
                    ?? $row->variant?->cost_price
                    ?? $row->product?->cost_price
                    ?? 0);
                $origPrice  = (float) ($row->variant
                    ? ($row->variant->selling_price ?: $row->variant->price)
                    : ($row->product?->selling_price ?: $row->product?->price ?? 0));

                // Resolve effective sale price per promo type
                if ($promo->promo_type === 'percentage') {
                    $discountAmt = $origPrice * ($promo->discount_value / 100);
                    $salePrice   = max(0, $origPrice - $discountAmt);
                } elseif ($promo->promo_type === 'fixed_amount') {
                    $salePrice   = max(0, $origPrice - $promo->discount_value);
                } elseif ($promo->promo_type === 'bulk_tier') {
                    $salePrice   = (float) $promo->discount_value;
                } elseif ($promo->promo_type === 'buy_x_get_y') {
                    // Free items reduce effective revenue; show cost of free unit as warning
                    $freeRatio   = $promo->get_quantity / max(1, $promo->min_quantity + $promo->get_quantity);
                    $salePrice   = $origPrice * (1 - $freeRatio);
                } else {
                    $salePrice   = $origPrice;
                }

                // Compute profitability
                $grossProfit  = $salePrice - $costPrice;
                $marginPct    = $salePrice > 0 ? round(($grossProfit / $salePrice) * 100, 1) : 0;
                $hasCost      = $costPrice > 0;

                // Build profit badge
                if (!$hasCost) {
                    $profitBadge = '<span class="badge px-1.5 py-0.5 rounded-1 mt-1 d-inline-block" style="font-size:.65rem;background:#f1f5f9;color:#64748b;">'
                        . '<i class="bi bi-question-circle me-1"></i>No cost price set</span>';
                } elseif ($grossProfit < 0) {
                    $profitBadge = '<span class="badge px-1.5 py-0.5 rounded-1 mt-1 d-inline-block fw-bold" style="font-size:.65rem;background:#fee2e2;color:#dc2626;">'
                        . '<i class="bi bi-x-circle-fill me-1"></i>LOSS ₱' . number_format(abs($grossProfit), 2) . ' · Cost ₱' . number_format($costPrice, 2) . '</span>';
                } elseif ($marginPct < 10) {
                    $profitBadge = '<span class="badge px-1.5 py-0.5 rounded-1 mt-1 d-inline-block fw-bold" style="font-size:.65rem;background:#fef9c3;color:#92400e;">'
                        . '<i class="bi bi-exclamation-circle-fill me-1"></i>' . $marginPct . '% margin · Cost ₱' . number_format($costPrice, 2) . '</span>';
                } elseif ($marginPct < 20) {
                    $profitBadge = '<span class="badge px-1.5 py-0.5 rounded-1 mt-1 d-inline-block fw-bold" style="font-size:.65rem;background:#dbeafe;color:#1d4ed8;">'
                        . '<i class="bi bi-check-circle me-1"></i>' . $marginPct . '% margin · Cost ₱' . number_format($costPrice, 2) . '</span>';
                } else {
                    $profitBadge = '<span class="badge px-1.5 py-0.5 rounded-1 mt-1 d-inline-block fw-bold" style="font-size:.65rem;background:#dcfce7;color:#166534;">'
                        . '<i class="bi bi-check-circle-fill me-1"></i>' . $marginPct . '% margin · Cost ₱' . number_format($costPrice, 2) . '</span>';
                }

                // Build price display per type
                if ($promo->promo_type === 'percentage') {
                    $priceHtml = '<div class="d-flex flex-column">'
                        . '<span class="fw-black text-success font-mono fs-6">₱' . number_format($salePrice, 2) . '</span>'
                        . '<span class="extra-small text-muted text-decoration-line-through">₱' . number_format($origPrice, 2) . ' (-' . (float)$promo->discount_value . '%)</span>'
                        . $profitBadge . '</div>';
                } elseif ($promo->promo_type === 'fixed_amount') {
                    $priceHtml = '<div class="d-flex flex-column">'
                        . '<span class="fw-black text-success font-mono fs-6">₱' . number_format($salePrice, 2) . '</span>'
                        . '<span class="extra-small text-muted text-decoration-line-through">₱' . number_format($origPrice, 2) . ' (-₱' . number_format($promo->discount_value, 2) . ')</span>'
                        . $profitBadge . '</div>';
                } elseif ($promo->promo_type === 'bulk_tier') {
                    $priceHtml = '<div class="d-flex flex-column">'
                        . '<span class="fw-black text-warning-emphasis font-mono fs-6">₱' . number_format($salePrice, 2) . ' / pc</span>'
                        . '<span class="extra-small text-muted">For ' . $promo->min_quantity . '+ pcs</span>'
                        . $profitBadge . '</div>';
                } elseif ($promo->promo_type === 'buy_x_get_y') {
                    $priceHtml = '<div class="d-flex flex-column">'
                        . '<span class="fw-bold font-mono" style="color:#6d28d9;">Buy ' . $promo->min_quantity . ' Get ' . $promo->get_quantity . ' Free</span>'
                        . '<span class="extra-small text-muted">Eff. revenue ≈ ₱' . number_format($salePrice, 2) . '/unit</span>'
                        . $profitBadge . '</div>';
                } else {
                    $priceHtml = '—';
                }

                return $priceHtml;
            })

            ->addColumn('actions', function ($row) {
                return '
                    <button type="button" class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-2.5 py-1 btn-remove-item" data-id="' . $row->id . '" title="Remove Item from Promo">
                        <i class="bi bi-trash3 me-1"></i> Remove
                    </button>
                ';
            })
            ->rawColumns(['item_name', 'category_badge', 'regular_price', 'sale_price', 'actions'])
            ->make(true);
    }

    /**
     * Catalog of Products to Add to Promo
     */
    public function catalogData(Request $request, $id)
    {
        $tenantId = auth()->user()->tenant_id;
        $existingItems = POSPromotionItem::where('tenant_id', $tenantId)
            ->where('promotion_id', $id)
            ->get();

        $existingProductIds = $existingItems->where('item_type', 'product')->pluck('item_id')->toArray();
        $existingVariantIds = $existingItems->where('item_type', 'variant')->pluck('item_id')->toArray();

        $query = POSProducts::where('tenant_id', $tenantId)
            ->with(['category', 'unit', 'variants.unit'])
            ->orderBy('name');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('barcode', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%")
                  ->orWhereHas('variants', function($vq) use ($s) {
                      $vq->where('variant_name', 'like', "%{$s}%")
                         ->orWhere('barcode', 'like', "%{$s}%")
                         ->orWhere('sku', 'like', "%{$s}%");
                  });
            });
        }

        $products = $query->limit(100)->get()->map(function($p) use ($existingProductIds, $existingVariantIds) {
            $sellingPrice = (float)($p->selling_price ?: $p->price ?? 0);
            return [
                'id' => $p->id,
                'name' => $p->name,
                'barcode' => $p->barcode,
                'sku' => $p->sku,
                'selling_price' => $sellingPrice,
                'category_name' => $p->category?->name ?? 'Uncategorized',
                'unit_name' => $p->unit?->name ?? 'pcs',
                'is_added' => in_array($p->id, $existingProductIds),
                'variants' => $p->variants->map(function($v) use ($existingVariantIds) {
                    return [
                        'id' => $v->id,
                        'variant_name' => $v->variant_name,
                        'barcode' => $v->barcode,
                        'sku' => $v->sku,
                        'selling_price' => (float)($v->selling_price ?: $v->price ?? 0),
                        'unit_name' => $v->unit?->name ?? 'pcs',
                        'is_added' => in_array($v->id, $existingVariantIds),
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * Batch Add Multiple Products, Variants, or Categories to Promotion
     */
    public function addBatchItems(Request $request, $id)
    {
        $tenantId = auth()->user()->tenant_id;
        $promo = POSPromotion::where('tenant_id', $tenantId)->findOrFail($id);

        $addedCount = 0;

        // Mode 1: Array of items objects [{id: 1, type: 'product'}, {id: 2, type: 'variant'}]
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $it) {
                $itemId = (int)($it['id'] ?? 0);
                $itemType = $it['type'] ?? 'product';

                if ($itemId > 0 && in_array($itemType, ['product', 'variant', 'category'])) {
                    $exists = POSPromotionItem::where('tenant_id', $tenantId)
                        ->where('promotion_id', $promo->id)
                        ->where('item_type', $itemType)
                        ->where('item_id', $itemId)
                        ->exists();

                    if (!$exists) {
                        POSPromotionItem::create([
                            'tenant_id'    => $tenantId,
                            'promotion_id' => $promo->id,
                            'item_type'    => $itemType,
                            'item_id'      => $itemId,
                        ]);
                        $addedCount++;
                    }
                }
            }
        } elseif ($request->has('item_ids') && is_array($request->item_ids)) {
            // Mode 2: Array of IDs with single item_type
            $itemType = $request->get('item_type', 'product');
            $itemIds = array_unique(array_filter(array_map('intval', $request->item_ids)));

            foreach ($itemIds as $itemId) {
                $exists = POSPromotionItem::where('tenant_id', $tenantId)
                    ->where('promotion_id', $promo->id)
                    ->where('item_type', $itemType)
                    ->where('item_id', $itemId)
                    ->exists();

                if (!$exists) {
                    POSPromotionItem::create([
                        'tenant_id'    => $tenantId,
                        'promotion_id' => $promo->id,
                        'item_type'    => $itemType,
                        'item_id'      => $itemId,
                    ]);
                    $addedCount++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Added {$addedCount} item(s) to \"{$promo->title}\"!",
            'added_count' => $addedCount,
        ]);
    }

    /**
     * Rapid Barcode Scanner Item Add (Supports both Parent Products & Variants)
     */
    public function addByBarcode(Request $request, $id)
    {
        $tenantId = auth()->user()->tenant_id;
        $promo = POSPromotion::where('tenant_id', $tenantId)->findOrFail($id);
        $barcode = trim($request->get('barcode', ''));

        if (!$barcode) {
            return response()->json(['success' => false, 'message' => 'Please provide a barcode or SKU.'], 422);
        }

        // 1. Check in variants first
        $variant = POSProductVariant::where('tenant_id', $tenantId)
            ->where(function($q) use ($barcode) {
                $q->where('barcode', $barcode)->orWhere('sku', $barcode);
            })
            ->with('product')
            ->first();

        if ($variant && $variant->product) {
            $exists = POSPromotionItem::where('tenant_id', $tenantId)
                ->where('promotion_id', $promo->id)
                ->where('item_type', 'variant')
                ->where('item_id', $variant->id)
                ->exists();

            if ($exists) {
                return response()->json(['success' => false, 'message' => "{$variant->product->name} ({$variant->variant_name}) is already in this promotion."], 422);
            }

            POSPromotionItem::create([
                'tenant_id'    => $tenantId,
                'promotion_id' => $promo->id,
                'item_type'    => 'variant',
                'item_id'      => $variant->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Added {$variant->product->name} ({$variant->variant_name}) to promotion!",
            ]);
        }

        // 2. Check parent product
        $product = POSProducts::where('tenant_id', $tenantId)
            ->where(function($q) use ($barcode) {
                $q->where('barcode', $barcode)->orWhere('sku', $barcode);
            })
            ->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'No product or variant found matching barcode: ' . $barcode], 404);
        }

        $exists = POSPromotionItem::where('tenant_id', $tenantId)
            ->where('promotion_id', $promo->id)
            ->where('item_type', 'product')
            ->where('item_id', $product->id)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => "{$product->name} is already in this promotion."], 422);
        }

        POSPromotionItem::create([
            'tenant_id'    => $tenantId,
            'promotion_id' => $promo->id,
            'item_type'    => 'product',
            'item_id'      => $product->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Added {$product->name} to promotion!",
        ]);
    }

    /**
     * Remove Item from Promotion
     */
    public function removeItem($itemId)
    {
        $tenantId = auth()->user()->tenant_id;
        $item = POSPromotionItem::where('tenant_id', $tenantId)->findOrFail($itemId);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from promotion.',
        ]);
    }

    /**
     * Remove All Items from Promotion
     */
    public function removeAllItems($id)
    {
        $tenantId = auth()->user()->tenant_id;
        POSPromotionItem::where('tenant_id', $tenantId)->where('promotion_id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'All items removed from promotion.',
        ]);
    }
}
