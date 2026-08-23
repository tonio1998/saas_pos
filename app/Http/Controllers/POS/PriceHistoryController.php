<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\ProductPriceHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PriceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $query = ProductPriceHistory::where('tenant_id', $tenantId);

        $selectedProduct = null;
        if ($request->filled('product_id')) {
            $prodId = $request->input('product_id');
            if (!is_numeric($prodId)) {
                try { $prodId = decrypt($prodId); } catch (\Throwable $e) { $prodId = null; }
            }
            if ($prodId) {
                $selectedProduct = \App\Models\POS\POSProducts::where('tenant_id', $tenantId)->find($prodId);
                if ($selectedProduct) {
                    $query->where('product_id', $selectedProduct->id);
                }
            }
        }

        $totalLogs    = (clone $query)->count();
        $variantLogs  = (clone $query)->whereNotNull('variant_id')->count();
        $baseLogs     = (clone $query)->whereNull('variant_id')->count();
        $priceUpLogs  = (clone $query)->whereRaw('COALESCE(new_selling_price, 0) > COALESCE(selling_price, 0)')->count();

        return view('pages.tenants.products.price-history.index', compact(
            'totalLogs',
            'variantLogs',
            'baseLogs',
            'priceUpLogs',
            'selectedProduct'
        ));
    }

    public function ajaxData(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $query = ProductPriceHistory::with([
            'product',
            'variant',
            'createdBy',
        ])
            ->where('tenant_id', $tenantId);

        if ($request->filled('product_id')) {
            $prodId = $request->input('product_id');
            if (!is_numeric($prodId)) {
                try { $prodId = decrypt($prodId); } catch (\Throwable $e) { $prodId = null; }
            }
            if ($prodId) {
                $query->where('product_id', $prodId);
            }
        }

        if ($request->filled('item_type')) {
            if ($request->item_type === 'variant') {
                $query->whereNotNull('variant_id');
            } elseif ($request->item_type === 'base') {
                $query->whereNull('variant_id');
            }
        }

        if ($request->filled('reason')) {
            $query->where('reason', 'like', "%{$request->reason}%");
        }

        $query->latest('created_at');

        return datatables()
            ->eloquent($query)

            ->addColumn('actions', function ($priceHistory) {
                return '
                <div class="dropdown">
                    <button class="btn btn-light border btn-sm rounded-2 extra-small font-mono fw-bold px-2.5 shadow-xs" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-popper-config=\'{"strategy":"fixed"}\'>
                        Actions <i class="bi bi-chevron-down ms-1"></i>
                    </button>
                    <ul class="dropdown-menu shadow-lg border-0 font-mono small rounded-3 p-1.5" style="z-index:1080; min-width: 190px;">
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-primary fw-semibold py-1.5 rounded-2 btn-view-price-history" data-id="' . $priceHistory->id . '">
                                <i class="bi bi-eye-fill"></i> View Price Audit
                            </button>
                        </li>
                        ' . ($priceHistory->product ? '
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 text-dark fw-semibold py-1.5 rounded-2" href="' . route('products.edit', encrypt($priceHistory->product->id)) . '">
                                <i class="bi bi-pencil"></i> Edit Product Master
                            </a>
                        </li>
                        ' : '') . '
                    </ul>
                </div>
                ';
            })

            ->addColumn('product', function ($priceHistory) {
                if (!$priceHistory->product) {
                    return '<span class="badge extra-small fw-bold" style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;">Deleted Product</span>';
                }

                $productName = e($priceHistory->product->name);
                $sku = e($priceHistory->variant?->sku ?: ($priceHistory->product->sku ?? 'N/A'));
                $barcode = e($priceHistory->variant?->barcode ?: ($priceHistory->product->barcode ?? ''));

                $typeBadge = $priceHistory->variant
                    ? '<span class="badge extra-small font-mono fw-bold" style="background:#f3e8ff;color:#6b21a8;border:1px solid #d8b4fe;font-size:0.7rem;"><i class="bi bi-tag-fill me-1"></i>Variant: ' . e($priceHistory->variant->variant_name) . '</span>'
                    : '<span class="badge extra-small font-mono fw-bold" style="background:#e0f2fe;color:#0369a1;border:1px solid #7dd3fc;font-size:0.7rem;"><i class="bi bi-box-seam me-1"></i>Base Product</span>';

                $codeStr = '';
                if ($barcode) {
                    $codeStr .= '<span class="font-mono extra-small" style="color:#64748b;"><i class="bi bi-barcode me-1"></i>' . $barcode . '</span>';
                }
                if ($sku && $sku !== 'N/A') {
                    $codeStr .= ($codeStr ? ' &bull; ' : '') . '<span class="font-mono extra-small" style="color:#64748b;">SKU: ' . $sku . '</span>';
                }

                return '
                    <div class="d-flex flex-column gap-1">
                        <div class="fw-bold fs-6 lh-sm" style="color:#0f172a;">' . $productName . '</div>
                        <div>' . $typeBadge . '</div>
                        ' . ($codeStr ? '<div>' . $codeStr . '</div>' : '') . '
                    </div>
                ';
            })

            ->addColumn('price_changes', function ($priceHistory) {
                $oldCost     = (float) $priceHistory->cost_price;
                $newCost     = (float) ($priceHistory->new_cost_price ?? $oldCost);
                $costDiff    = $newCost - $oldCost;

                $oldSelling  = (float) $priceHistory->selling_price;
                $newSelling  = (float) ($priceHistory->new_selling_price ?? $oldSelling);
                $sellingDiff = $newSelling - $oldSelling;

                $oldProfit = $oldSelling - $oldCost;
                $newProfit = $newSelling - $newCost;
                $profitDiff = $newProfit - $oldProfit;

                $oldMargin = $oldSelling > 0 ? round(($oldProfit / $oldSelling) * 100, 1) : 0;
                $newMargin = $newSelling > 0 ? round(($newProfit / $newSelling) * 100, 1) : 0;
                $marginDiff = $newMargin - $oldMargin;

                $sellingPill = '';
                if ($sellingDiff != 0 || $oldSelling > 0) {
                    $badgeStyle = $sellingDiff > 0
                        ? 'background:#dcfce7;color:#166534;border:1px solid #86efac;'
                        : ($sellingDiff < 0 ? 'background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;' : 'background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;');
                    $sellingPill = '
                        <div class="d-flex align-items-center gap-2 flex-wrap extra-small">
                            <span class="badge extra-small font-mono fw-bold" style="background:#f1f5f9;color:#334155;border:1px solid #cbd5e1;">Selling Price</span>
                            <span class="font-mono text-decoration-line-through" style="color:#94a3b8;">₱' . number_format($oldSelling, 2) . '</span>
                            <i class="bi bi-arrow-right" style="color:#94a3b8;"></i>
                            <span class="font-mono fw-black fs-6" style="color:#0f172a;">₱' . number_format($newSelling, 2) . '</span>
                            <span class="badge extra-small font-mono fw-bold" style="' . $badgeStyle . '">
                                ' . ($sellingDiff > 0 ? '+' : '') . '₱' . number_format($sellingDiff, 2) . '
                            </span>
                        </div>
                    ';
                }

                $costPill = '';
                if ($costDiff != 0 || $oldCost > 0) {
                    $badgeStyle = $costDiff > 0
                        ? 'background:#fef3c7;color:#92400e;border:1px solid #fcd34d;'
                        : ($costDiff < 0 ? 'background:#dcfce7;color:#166534;border:1px solid #86efac;' : 'background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;');
                    $costPill = '
                        <div class="d-flex align-items-center gap-2 flex-wrap extra-small">
                            <span class="badge extra-small font-mono fw-bold" style="background:#f1f5f9;color:#334155;border:1px solid #cbd5e1;">Cost Price</span>
                            <span class="font-mono text-decoration-line-through" style="color:#94a3b8;">₱' . number_format($oldCost, 2) . '</span>
                            <i class="bi bi-arrow-right" style="color:#94a3b8;"></i>
                            <span class="font-mono fw-bold" style="color:#334155;">₱' . number_format($newCost, 2) . '</span>
                            <span class="badge extra-small font-mono" style="' . $badgeStyle . '">
                                ' . ($costDiff > 0 ? '+' : '') . '₱' . number_format($costDiff, 2) . '
                            </span>
                        </div>
                    ';
                }

                $marginBadgeStyle = $marginDiff >= 0
                    ? 'background:#dcfce7;color:#166534;border:1px solid #86efac;'
                    : 'background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;';

                $marginPill = '
                    <div class="d-flex align-items-center gap-2 flex-wrap extra-small pt-1 border-top mt-1">
                        <span class="badge extra-small font-mono fw-bold" style="background:#e0f2fe;color:#0369a1;border:1px solid #7dd3fc;">Margin Shift</span>
                        <span class="font-mono" style="color:#64748b;">' . $oldMargin . '%</span>
                        <i class="bi bi-arrow-right" style="color:#94a3b8;"></i>
                        <span class="font-mono fw-bold" style="color:' . ($newMargin >= $oldMargin ? '#166534' : '#b91c1c') . ';">' . $newMargin . '%</span>
                        <span class="badge extra-small font-mono fw-bold" style="' . $marginBadgeStyle . '">
                            ' . ($marginDiff > 0 ? '+' : '') . $marginDiff . '%
                        </span>
                    </div>
                ';

                return '
                    <div class="d-flex flex-column gap-1 py-1">
                        ' . $sellingPill . '
                        ' . $costPill . '
                        ' . $marginPill . '
                    </div>
                ';
            })

            ->addColumn('remarks', function ($priceHistory) {
                $reason = e($priceHistory->reason ?: 'Price Update');
                $remarks = e($priceHistory->remarks ?: '');

                $reasonBadge = match (true) {
                    str_contains(strtolower($reason), 'initial') => '<span class="badge extra-small fw-bold" style="background:#e0f2fe;color:#0369a1;border:1px solid #7dd3fc;"><i class="bi bi-asterisk me-1"></i>Initial Setup</span>',
                    str_contains(strtolower($reason), 'stock')   => '<span class="badge extra-small fw-bold" style="background:#dcfce7;color:#166534;border:1px solid #86efac;"><i class="bi bi-box-arrow-in-down me-1"></i>Restock Cost</span>',
                    str_contains(strtolower($reason), 'bulk')    => '<span class="badge extra-small fw-bold" style="background:#fef3c7;color:#92400e;border:1px solid #fcd34d;"><i class="bi bi-sliders me-1"></i>Bulk Adjustment</span>',
                    default                                      => '<span class="badge extra-small fw-bold" style="background:#e0f2fe;color:#0369a1;border:1px solid #7dd3fc;"><i class="bi bi-pencil-square me-1"></i>' . $reason . '</span>',
                };

                return '
                    <div class="d-flex flex-column gap-1">
                        <div>' . $reasonBadge . '</div>
                        ' . ($remarks ? '<small class="font-mono extra-small d-block text-truncate" style="color:#64748b;max-width:240px;">' . $remarks . '</small>' : '') . '
                    </div>
                ';
            })

            ->addColumn('status', function ($priceHistory) {
                return '<span class="badge extra-small fw-bold" style="background:#dcfce7;color:#166534;border:1px solid #86efac;"><i class="bi bi-check-circle-fill me-1"></i>Active</span>';
            })

            ->addColumn('created_at', function ($priceHistory) {
                $dateStr = $priceHistory->effective_date ? $priceHistory->effective_date->format('M d, Y') : $priceHistory->created_at?->format('M d, Y');
                $timeStr = $priceHistory->effective_date ? $priceHistory->effective_date->format('h:i A') : $priceHistory->created_at?->format('h:i A');
                return '
                    <div>
                        <div class="fw-bold extra-small font-mono" style="color:#1e293b;">' . $dateStr . '</div>
                        <small class="extra-small font-mono" style="color:#64748b;"><i class="bi bi-clock me-1"></i>' . $timeStr . '</small>
                    </div>
                ';
            })

            ->addColumn('createdBy', function ($priceHistory) {
                $userName = $priceHistory->createdBy ? e($priceHistory->createdBy->name) : 'System';
                $initial = strtoupper(substr($userName, 0, 1));
                return '
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle fw-bold d-flex align-items-center justify-content-center extra-small" style="width:26px;height:26px;background:#e0f2fe;color:#0369a1;font-size:0.75rem;">
                            ' . $initial . '
                        </div>
                        <span class="fw-bold extra-small" style="color:#334155;">' . $userName . '</span>
                    </div>
                ';
            })

            ->rawColumns([
                'actions',
                'product',
                'price_changes',
                'remarks',
                'status',
                'created_at',
                'createdBy',
            ])

            ->make(true);
    }

    public function show(int $id)
    {
        $tenantId = auth()->user()->tenant_id;
        $priceHistory = ProductPriceHistory::with([
            'product',
            'variant',
            'createdBy',
        ])
            ->where('tenant_id', $tenantId)
            ->findOrFail($id);

        if (request()->expectsJson() || request()->ajax()) {
            $oldCost     = (float)$priceHistory->cost_price;
            $newCost     = (float)($priceHistory->new_cost_price ?? $oldCost);
            $oldSelling  = (float)$priceHistory->selling_price;
            $newSelling  = (float)($priceHistory->new_selling_price ?? $oldSelling);
            $oldWholesale= $priceHistory->wholesale_price !== null ? (float)$priceHistory->wholesale_price : null;
            $newWholesale= $priceHistory->new_wholesale_price !== null ? (float)$priceHistory->new_wholesale_price : null;

            $oldProfit  = $oldSelling - $oldCost;
            $newProfit  = $newSelling - $newCost;
            $oldMargin  = $oldSelling > 0 ? round(($oldProfit / $oldSelling) * 100, 1) : 0;
            $newMargin  = $newSelling > 0 ? round(($newProfit / $newSelling) * 100, 1) : 0;

            return response()->json([
                'success'         => true,
                'id'              => $priceHistory->id,
                'product_name'    => $priceHistory->product?->name ?? 'Deleted Product',
                'variant_name'    => $priceHistory->variant?->variant_name,
                'is_variant'      => (bool)$priceHistory->variant_id,
                'sku'             => $priceHistory->variant?->sku ?: ($priceHistory->product?->sku ?? 'N/A'),
                'barcode'         => $priceHistory->variant?->barcode ?: ($priceHistory->product?->barcode ?? 'N/A'),
                'reason'          => $priceHistory->reason ?: 'Price Update',
                'remarks'         => $priceHistory->remarks,
                'old_cost'        => number_format($oldCost, 2),
                'new_cost'        => number_format($newCost, 2),
                'cost_diff'       => number_format($newCost - $oldCost, 2),
                'old_selling'     => number_format($oldSelling, 2),
                'new_selling'     => number_format($newSelling, 2),
                'selling_diff'    => number_format($newSelling - $oldSelling, 2),
                'old_wholesale'   => $oldWholesale !== null ? number_format($oldWholesale, 2) : 'N/A',
                'new_wholesale'   => $newWholesale !== null ? number_format($newWholesale, 2) : 'N/A',
                'old_margin'      => $oldMargin,
                'new_margin'      => $newMargin,
                'margin_diff'     => round($newMargin - $oldMargin, 1),
                'effective_date'  => $priceHistory->effective_date ? $priceHistory->effective_date->format('M d, Y h:i A') : $priceHistory->created_at?->format('M d, Y h:i A'),
                'created_by'      => $priceHistory->createdBy?->name ?? 'System Admin',
                'created_at'      => $priceHistory->created_at?->format('M d, Y h:i A'),
            ]);
        }

        return view('pages.tenants.products.price-history.show', compact('priceHistory'));
    }

    public function destroy(int $id): JsonResponse
    {
        $priceHistory = ProductPriceHistory::where(
            'tenant_id',
            auth()->user()->tenant_id
        )->findOrFail($id);

        $priceHistory->archived = 1;
        $priceHistory->save();

        return response()->json([
            'success' => true,
            'message' => 'Price history archived successfully.',
        ]);
    }
}
