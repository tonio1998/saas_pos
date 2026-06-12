<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\InventoryMovement;
use App\Models\POS\POSCategories;
use App\Models\POS\POSCustomers;
use App\Models\POS\POSPayment;
use App\Models\POS\POSProducts;
use App\Models\POS\POSSale;
use App\Models\POS\POSSaleItem;
use App\Models\POS\POSSales;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SalesController extends Controller
{
    use TCommonFunctions;
    public function products()
    {
        return POSProducts::select(
            'id',
            'name',
            'barcode',
            'selling_price',
            'stock_on_hand',
            'category_id',
            'image',
            'updated_at'
        )
            ->where('tenant_id', auth()->user()->tenant_id)
            ->get();
    }

    public function details(POSSale $sale): \Illuminate\Http\JsonResponse
    {
        abort_if(
            $sale->tenant_id !== auth()->user()->tenant_id,
            403
        );

        $sale->load([
            'customer',
            'cashier',
            'payments',
            'items.product',
        ]);

        $profit = $sale->items->sum(function ($item) {

            $cost =
                $item->product?->cost_price ?? 0;

            return (
                ($item->unit_price - $cost)
                * $item->qty
            );
        });

        return response()->json([

            'id' => $sale->id,

            'invoice_no' => $sale->invoice_no,

            'sale_date' => $sale->sale_date
                ? format_date($sale->sale_date)
                : '-',

            'customer' => $sale->customer?->name
                ?? 'Walk-in Customer',

            'cashier' => $sale->cashier?->name
                ?? 'System',

            'status' => ucfirst(
                $sale->status
            ),

            'subtotal' => '₱' . number_format(
                    $sale->subtotal ?? 0,
                    2
                ),

            'discount' => '₱' . number_format(
                    $sale->discount_amount ?? 0,
                    2
                ),

            'total' => '₱' . number_format(
                    $sale->total_amount ?? 0,
                    2
                ),

            'tendered' => '₱' . number_format(
                    $sale->tendered_amount ?? 0,
                    2
                ),

            'change' => '₱' . number_format(
                    $sale->change_amount ?? 0,
                    2
                ),

            'profit' => '₱' . number_format(
                    $profit,
                    2
                ),

            'notes' => $sale->notes ?: '-',

            'payments' => $sale->payments->map(function ($payment) {

                return [

                    'method' => ucfirst(
                        str_replace(
                            '_',
                            ' ',
                            $payment->payment_method
                        )
                    ),

                    'reference' => $payment->reference_number
                        ?: '-',

                    'amount' => '₱' . number_format(
                            $payment->amount ?? 0,
                            2
                        ),

                    'tendered_amount' => '₱' . number_format(
                            $payment->tendered_amount ?? 0,
                            2
                        ),

                    'change_amount' => '₱' . number_format(
                            $payment->change_amount ?? 0,
                            2
                        ),

                    'payment_date' => $payment->payment_date
                        ? format_date(
                            $payment->payment_date
                        )
                        : '-',

                    'notes' => $payment->notes ?: '-',
                ];
            })->values(),

            'items' => $sale->items->map(function ($item) {

                $cost =
                    $item->product?->cost_price ?? 0;

                $profit =
                    (
                        ($item->unit_price - $cost)
                        * $item->qty
                    );

                return [

                    'barcode' => $item->barcode
                        ?: '-',

                    'product' => $item->product_name
                        ?: ($item->product?->name ?? '-'),

                    'quantity' => $item->qty,

                    'price' => '₱' . number_format(
                            $item->unit_price ?? 0,
                            2
                        ),

                    'total' => '₱' . number_format(
                            $item->line_total ?? 0,
                            2
                        ),

                    'profit' => '₱' . number_format(
                            $profit,
                            2
                        ),
                ];
            })->values(),
        ]);
    }

    public function ajaxData(Request $request)
    {
        $query = POSSale::with([
            'customer',
            'cashier',
            'payments',
            'items',
            'items.product',
        ])
            ->where(
                'tenant_id',
                auth()->user()->tenant_id
            )
            ->latest();

        return datatables()
            ->eloquent($query)

            ->addColumn('actions', function ($sale) {

                return '
        <div class="btn-group">

            <button
                type="button"
                class="btn btn-soft-primary btn-sm btn-view-sale"
                data-id="' . $sale->id . '"
            >
                <i class="bi bi-eye"></i>
            </button>

            <button
                type="button"
                class="btn btn-soft-success btn-sm btn-print-sale"
                data-id="' . $sale->id . '"
            >
                <i class="bi bi-printer"></i>
            </button>

        </div>
    ';
            })
            ->addColumn('invoice_number', function ($sale) {

                return '
                <span class="fw-semibold">
                    ' . ($sale->invoice_no ?? '-') . '
                </span>
            ';
            })

            ->addColumn('sale_date', function ($sale) {

                return $sale->sale_date
                    ? format_date($sale->sale_date)
                    : 'N/A';
            })

            ->addColumn('customer', function ($sale) {

                return $sale->customer
                    ? '<span class="fw-semibold">' .
                    e($sale->customer->name) .
                    '</span>'
                    : '<span class="badge bg-light text-dark">
                    Walk-in
                </span>';
            })

            ->addColumn('total_items', function ($sale) {

                return '
                <span class="badge bg-info">
                    ' . $sale->items->sum('qty') . '
                </span>
            ';
            })

            ->addColumn('subtotal', function ($sale) {

                return '₱' . number_format(
                        $sale->subtotal ?? 0,
                        2
                    );
            })

            ->addColumn('discount', function ($sale) {

                return '₱' . number_format(
                        $sale->discount_amount ?? 0,
                        2
                    );
            })

            ->addColumn('total', function ($sale) {

                return '
                <span class="fw-bold text-success">
                    ₱' . number_format(
                        $sale->total_amount ?? 0,
                        2
                    ) . '
                </span>
            ';
            })

            ->addColumn('profit', function ($sale) {

                $profit = $sale->items->sum(function ($item) {

                    $cost =
                        $item->product->cost_price ?? 0;

                    return (
                        ($item->selling_price - $cost)
                        * $item->qty
                    );
                });

                return '
                <span class="fw-bold text-primary">
                    ₱' . number_format(
                        $profit,
                        2
                    ) . '
                </span>
            ';
            })

            ->addColumn('payment_method', function ($sale) {

                return match ($sale->payment_method) {

                    'cash' => '
                    <span class="badge bg-success">
                        Cash
                    </span>
                ',

                    'gcash' => '
                    <span class="badge bg-primary">
                        GCash
                    </span>
                ',

                    'bank_transfer' => '
                    <span class="badge bg-info">
                        Bank
                    </span>
                ',

                    default => '
                    <span class="badge bg-secondary">
                        Unknown
                    </span>
                '
                };
            })

            ->addColumn('tendered', function ($sale) {

                return '₱' . number_format(
                        $sale->tendered_amount ?? 0,
                        2
                    );
            })

            ->addColumn('change_amount', function ($sale) {

                return '₱' . number_format(
                        $sale->change_amount ?? 0,
                        2
                    );
            })

            ->addColumn('status', function ($sale) {

                return match ($sale->sale_status) {

                    'completed' => '
                    <span class="badge bg-success">
                        Completed
                    </span>
                ',

                    'voided' => '
                    <span class="badge bg-danger">
                        Voided
                    </span>
                ',

                    'refunded' => '
                    <span class="badge bg-warning text-dark">
                        Refunded
                    </span>
                ',

                    default => '
                    <span class="badge bg-secondary">
                        Pending
                    </span>
                '
                };
            })

            ->addColumn('cashier', function ($sale) {

                return $sale->cashier
                    ? '<span class="fw-semibold">' .
                    e($sale->cashier->name) .
                    '</span>'
                    : '<span class="badge bg-light text-dark">
                    System
                </span>';
            })

            ->editColumn('created_at', function ($sale) {

                return $sale->created_at
                    ? $sale->created_at->format(
                        'M d, Y h:i A'
                    )
                    : 'N/A';
            })

            ->filterColumn(
                'invoice_number',
                function ($query, $keyword) {

                    $query->where(
                        'invoice_no',
                        'like',
                        "%{$keyword}%"
                    );
                }
            )

            ->rawColumns([
                'actions',
                'invoice_number',
                'customer',
                'total_items',
                'total',
                'profit',
                'payment_method',
                'status',
                'cashier',
            ])

            ->make(true);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([

            'customer_id' => [
                'nullable',
                'integer'
            ],

            'subtotal' => [
                'required',
                'numeric',
                'min:0'
            ],

            'discount' => [
                'required',
                'numeric',
                'min:0'
            ],

            'total' => [
                'required',
                'numeric',
                'min:0'
            ],

            'change' => [
                'required',
                'numeric',
                'min:0'
            ],

            'discount_type' => [
                'nullable',
                'string'
            ],

            'discount_mode' => [
                'nullable',
                'string'
            ],

            'discount_value' => [
                'nullable',
                'numeric'
            ],

            'discount_holder' => [
                'nullable',
                'string',
                'max:255'
            ],

            'discount_id_no' => [
                'nullable',
                'string',
                'max:255'
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000'
            ],

            'payments' => [
                'required',
                'array',
                'min:1'
            ],

            'payments.*.method' => [
                'required',
                'string',
                'in:cash,gcash,bank_transfer'
            ],

            'payments.*.amount' => [
                'required',
                'numeric',
                'min:0.01'
            ],

            'payments.*.reference_number' => [
                'nullable',
                'string',
                'max:255'
            ],

            'items' => [
                'required',
                'array',
                'min:1'
            ],

            'items.*.product_id' => [
                'required',
                'integer'
            ],

            'items.*.qty' => [
                'required',
                'numeric',
                'min:0.01'
            ],

            'items.*.price' => [
                'required',
                'numeric',
                'min:0'
            ],

        ]);

        $totalPaid =
            collect(
                $data['payments']
            )->sum(
                'amount'
            );

        if (
            $totalPaid <
            $data['total']
        ) {

            throw ValidationException::withMessages([

                'payments' => [

                    'Insufficient payment.'

                ]

            ]);

        }

        $change =
            max(
                0,
                $totalPaid -
                $data['total']
            );

        $sale = null;

        DB::transaction(function () use (
            $data,
            $totalPaid,
            $change,
            &$sale
        ) {

            $taxRate = 12;

            $vatableSales =
                $data['total'] /
                (
                    1 +
                    (
                        $taxRate / 100
                    )
                );

            $taxAmount =
                $data['total'] -
                $vatableSales;

            $sale =
                new POSSale();

            $sale->tenant_id =
                auth()->user()->tenant_id;

            $sale->invoice_no =
                generateSaleInvoiceNo();

            $sale->cashier_id =
                auth()->id();

            $sale->customer_id =
                $data['customer_id'];

            $sale->payment_method =
                count(
                    $data['payments']
                ) === 1
                    ? $data['payments'][0]['method']
                    : 'split';

            $sale->subtotal =
                $data['subtotal'];

            $sale->discount_amount =
                $data['discount'];

            $sale->discount_type =
                $data['discount_type']
                ?? null;

            $sale->discount_holder =
                $data['discount_holder']
                ?? null;

            $sale->discount_id_no =
                $data['discount_id_no']
                ?? null;

            $sale->tax_amount =
                round(
                    $taxAmount,
                    2
                );

            $sale->total_amount =
                $data['total'];

            $sale->sale_status =
                'completed';

            $sale->sale_date =
                now();

            $sale->reference_number =
                collect(
                    $data['payments']
                )
                    ->pluck(
                        'reference_number'
                    )
                    ->filter()
                    ->implode(
                        ', '
                    ) ?: null;

            $sale->notes =
                $data['notes']
                ?? null;

            $this->setCommonFields(
                $sale
            );

            $sale->save();

            foreach (
                $data['items']
                as $item
            ) {

                $product =
                    POSProducts::findOrFail(
                        $item['product_id']
                    );

                if (
                    $product->stock_on_hand <
                    $item['qty']
                ) {

                    throw ValidationException::withMessages([

                        'stock' => [

                            "{$product->name} has insufficient stock."

                        ]

                    ]);

                }

                $newSaleItem =
                    new POSSaleItem();

                $newSaleItem->sale_id =
                    $sale->id;

                $newSaleItem->product_id =
                    $product->id;

                $newSaleItem->barcode =
                    $product->barcode;

                $newSaleItem->sku =
                    $product->sku;

                $newSaleItem->product_name =
                    $product->name;

                $newSaleItem->qty =
                    $item['qty'];

                $newSaleItem->unit_price =
                    $item['price'];

                $newSaleItem->discount_amount =
                    0;

                $newSaleItem->tax_amount =
                    0;

                $newSaleItem->line_total =
                    $item['qty'] *
                    $item['price'];

                $this->setCommonFields(
                    $newSaleItem
                );

                $newSaleItem->save();

                $newInv =
                    new InventoryMovement();

                $newInv->tenant_id =
                    auth()->user()->tenant_id;

                $newInv->product_id =
                    $product->id;

                $newInv->movement_type =
                    'sale';

                $newInv->reference_type =
                    'sale';

                $newInv->reference_id =
                    $sale->id;

                $newInv->qty =
                    -1 *
                    $item['qty'];

                $this->setCommonFields(
                    $newInv
                );

                $newInv->save();

                $product->decrement(
                    'stock_on_hand',
                    $item['qty']
                );

            }

            foreach (
                $data['payments']
                as $index => $payment
            ) {

                $newPayment =
                    new POSPayment();

                $newPayment->sale_id =
                    $sale->id;

                $newPayment->payment_method =
                    $payment['method'];

                $newPayment->amount =
                    $payment['amount'];

                $newPayment->tendered_amount =
                    $payment['amount'];

                $newPayment->change_amount =
                    $index === 0
                        ? $change
                        : 0;

                $newPayment->reference_number =
                    $payment['reference_number']
                    ?? null;

                $newPayment->notes =
                    $data['notes']
                    ?? null;

                $newPayment->payment_date =
                    now();

                $this->setCommonFields(
                    $newPayment
                );

                $newPayment->save();

            }

        });

        return response()->json([

            'success' => true,

            'sale_id' =>
                $sale->id,

            'invoice_no' =>
                $sale->invoice_no,

            'payment_method' =>
                $sale->payment_method,

            'reference_number' =>
                $sale->reference_number,

            'payments' =>
                POSPayment::where(
                    'sale_id',
                    $sale->id
                )->get(),

            'message' =>
                'Sale completed successfully.',

        ]);

    }

    public function index()
    {
        return view('pages.tenants.terminal.index');
    }

    public function create()
    {
        $products = POSProducts::query()
            ->with('createdBy')
            ->orderByDesc('stock_on_hand')
            ->orderBy('name')
//            ->limit(12)
            ->get();

        $categories = POSCategories::query()
            ->get();

        return view('pages.tenants.terminal.create', [
            'products' => $products,
            'categories' => $categories
        ]);
    }
}
