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
        return view('pages.store.products.price-history.index',);
    }

    public function ajaxData(Request $request)
    {
        $query = ProductPriceHistory::with([
            'product',
            'variant',
            'createdBy',
        ])
            ->latest('created_at');

        return datatables()
            ->eloquent($query)

            ->addColumn('actions', function ($priceHistory) {

                $viewUrl = route(
                    'products.price-history.show',
                    encrypt($priceHistory->id)
                );

                return '
                <a
                    href="' . $viewUrl . '"
                    class="btn btn-soft-primary btn-sm"
                >
                    <i class="bi bi-eye"></i>
                    View
                </a>
            ';
            })

            ->addColumn('product', function ($priceHistory) {

                if (!$priceHistory->product) {
                    return '
                    <span class="badge bg-danger">
                        Deleted Product
                    </span>
                ';
                }

                $variantBadge = '';
                if ($priceHistory->variant) {
                    $variantBadge = '<span class="badge bg-purple-subtle text-purple border extra-small mt-0.5" style="background:#f3e8ff;color:#7e22ce;border-color:#e9d5ff;font-size:0.7rem;"><i class="bi bi-tag-fill me-1"></i>' . e($priceHistory->variant->variant_name) . '</span>';
                }

                return '
                <div>
                    <div class="fw-bold">
                        ' . e($priceHistory->product->name) . '
                    </div>
                    ' . $variantBadge . '
                    <small class="text-muted d-block">
                        SKU: ' . e(
                        $priceHistory->variant?->sku ?: ($priceHistory->product->sku ?? 'N/A')
                    ) . '
                    </small>
                </div>
            ';
            })

            ->addColumn('price_changes', function ($priceHistory) {

                $costDiff =
                    (float) $priceHistory->new_cost_price -
                    (float) $priceHistory->cost_price;

                $sellingDiff =
                    (float) $priceHistory->new_selling_price -
                    (float) $priceHistory->selling_price;

                $wholesaleDiff =
                    (float) $priceHistory->new_wholesale_price -
                    (float) $priceHistory->wholesale_price;

                $oldProfit =
                    (float) $priceHistory->selling_price -
                    (float) $priceHistory->cost_price;

                $newProfit =
                    (float) $priceHistory->new_selling_price -
                    (float) $priceHistory->new_cost_price;

                $profitDiff =
                    $newProfit - $oldProfit;

                return '
                <div class="d-flex flex-column gap-1">

                    <div>
                        <span class="badge bg-light text-dark">
                            Cost
                        </span>

                        ₱' . number_format(
                        $priceHistory->cost_price,
                        2
                    ) . '

                        <i class="bi bi-arrow-right"></i>

                        ₱' . number_format(
                        $priceHistory->new_cost_price,
                        2
                    ) . '

                        <span class="badge bg-' .
                    ($costDiff >= 0
                        ? 'success'
                        : 'danger') .
                    '">
                            ' .
                    ($costDiff > 0 ? '+' : '') .
                    number_format(
                        $costDiff,
                        2
                    ) .
                    '
                        </span>
                    </div>

                    <div>
                        <span class="badge bg-light text-dark">
                            Selling
                        </span>

                        ₱' . number_format(
                        $priceHistory->selling_price,
                        2
                    ) . '

                        <i class="bi bi-arrow-right"></i>

                        ₱' . number_format(
                        $priceHistory->new_selling_price,
                        2
                    ) . '

                        <span class="badge bg-' .
                    ($sellingDiff >= 0
                        ? 'success'
                        : 'danger') .
                    '">
                            ' .
                    ($sellingDiff > 0 ? '+' : '') .
                    number_format(
                        $sellingDiff,
                        2
                    ) .
                    '
                        </span>
                    </div>

                    <div>
                        <span class="badge bg-light text-dark">
                            Wholesale
                        </span>

                        ₱' . number_format(
                        $priceHistory->wholesale_price,
                        2
                    ) . '

                        <i class="bi bi-arrow-right"></i>

                        ₱' . number_format(
                        $priceHistory->new_wholesale_price,
                        2
                    ) . '

                        <span class="badge bg-' .
                    ($wholesaleDiff >= 0
                        ? 'success'
                        : 'danger') .
                    '">
                            ' .
                    ($wholesaleDiff > 0 ? '+' : '') .
                    number_format(
                        $wholesaleDiff,
                        2
                    ) .
                    '
                        </span>
                    </div>

                    <div class="mt-1 pt-1 border-top">

                        <span class="badge bg-primary">
                            Profit Impact
                        </span>

                        ₱' . number_format(
                        $oldProfit,
                        2
                    ) . '

                        <i class="bi bi-arrow-right"></i>

                        ₱' . number_format(
                        $newProfit,
                        2
                    ) . '

                        <span class="badge bg-' .
                    ($profitDiff >= 0
                        ? 'success'
                        : 'danger') .
                    '">
                            ' .
                    ($profitDiff > 0 ? '+' : '') .
                    number_format(
                        $profitDiff,
                        2
                    ) .
                    '
                        </span>

                    </div>

                </div>
            ';
            })

            ->addColumn('remarks', function ($priceHistory) {

                return '
                <div>
                    <div class="fw-semibold">
                        ' . e(
                        $priceHistory->remarks ??
                        'No Remarks'
                    ) . '
                    </div>

                    <small class="text-muted">
                        Effective:
                        ' . optional(
                        $priceHistory->effective_date
                    )?->format(
                        'M d, Y h:i A'
                    ) . '
                    </small>
                </div>
            ';
            })

            ->addColumn('status', function ($priceHistory) {

                return match ($priceHistory->status) {
                    'active' =>
                    '<span class="badge bg-success">Active</span>',
                    'inactive' =>
                    '<span class="badge bg-secondary">Inactive</span>',
                    'locked' =>
                    '<span class="badge bg-danger">Locked</span>',
                    'unlocked' =>
                    '<span class="badge bg-info">Unlocked</span>',
                    default =>
                    '<span class="badge bg-warning">Unknown</span>',
                };
            })

            ->addColumn('created_at', function ($priceHistory) {

                return '
                <div>
                    <div class="fw-semibold">
                        ' . $priceHistory->created_at?->format(
                        'M d, Y'
                    ) . '
                    </div>

                    <small class="text-muted">
                        ' . $priceHistory->created_at?->format(
                        'h:i A'
                    ) . '
                    </small>
                </div>
            ';
            })

            ->addColumn('createdBy', function ($priceHistory) {

                return $priceHistory->createdBy
                    ? '
                    <span class="fw-semibold">
                        ' . e(
                        $priceHistory->createdBy->name
                    ) . '
                    </span>
                '
                    : '
                    <span class="badge bg-light text-muted">
                        System
                    </span>
                ';
            })

            ->filterColumn('product', function (
                $query,
                $keyword
            ) {
                $query->whereHas(
                    'product',
                    function ($q) use ($keyword) {
                        $q->where(
                            'name',
                            'like',
                            "%{$keyword}%"
                        )
                            ->orWhere(
                                'sku',
                                'like',
                                "%{$keyword}%"
                            );
                    }
                );
            })

            ->filterColumn('remarks', function (
                $query,
                $keyword
            ) {
                $query->where(
                    'remarks',
                    'like',
                    "%{$keyword}%"
                );
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
        $priceHistory = ProductPriceHistory::with('product')
            ->where(
                'tenant_id',
                auth()->user()->tenant_id
            )
            ->findOrFail($id);

        return view(
            'pos.price-history.show',
            compact('priceHistory')
        );
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
