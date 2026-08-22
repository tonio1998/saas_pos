<?php

namespace App\Http\Controllers\POS;

use App\Helpers\StatusHelper;
use App\Http\Controllers\Controller;
use App\Models\POS\POSCashDrawer;
use App\Models\POS\POSCashShift;
use App\Models\POS\POSCashTransaction;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesCashTransactionController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        return view('pages.pos.cash-transactions.index');
    }

    public function ajaxData(Request $request)
    {
        $transactions = POSCashTransaction::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->with(['shift', 'drawer', 'cashier'])
            ->orderBy('created_at', 'desc');

        return datatables()
            ->eloquent($transactions)
            ->addColumn('actions', function ($txn) {
                $id = encryptId($txn->id);

                $btn = '
                    <a
                        href="' . route('cashiering.cash-transactions.show', $id) . '"
                        class="btn btn-light text-start"
                    >
                        <i class="bi bi-eye me-2 text-primary"></i>
                        View Details
                    </a>
                ';

                return '
                    <button
                        type="button"
                        class="btn btn-soft-primary btn-sm btn-actions"
                        data-bs-toggle="modal"
                        data-bs-target="#actionModal"
                        data-title="Transaction Actions"
                        data-template="actions-' . $txn->id . '"
                    >
                        <i class="bi bi-gear"></i>
                        Actions
                    </button>
                    <template id="actions-' . $txn->id . '">
                        <div class="d-grid gap-2">
                            ' . $btn . '
                        </div>
                    </template>
                ';
            })
            ->addColumn('shift_code', function ($txn) {
                return $txn->shift?->shift_code ?? '-';
            })
            ->addColumn('drawer_name', function ($txn) {
                return $txn->drawer?->drawer_name ?? '-';
            })
            ->addColumn('cashier_name', function ($txn) {
                return $txn->cashier?->name ?? '-';
            })
            ->addColumn('transaction_type_badge', function ($txn) {
                return StatusHelper::badge($txn->transaction_type ?? '-');
            })
            ->addColumn('amount_formatted', function ($txn) {
                return '&#8369;' . number_format($txn->amount, 2);
            })
            ->addColumn('created_at_fmt', function ($txn) {
                return $txn->created_at
                    ? StatusHelper::formatDateTime($txn->created_at)
                    : '-';
            })
            ->addColumn('createdBy', function ($txn) {
                return $txn->creator?->name ?? '-';
            })
            ->rawColumns([
                'actions',
                'shift_code',
                'drawer_name',
                'cashier_name',
                'transaction_type_badge',
                'amount_formatted',
                'created_at_fmt',
                'createdBy',
            ])
            ->make(true);
    }

    public function create()
    {
        $drawers = POSCashDrawer::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('drawer_name')
            ->get();

        $shifts = POSCashShift::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'open')
            ->orderBy('opened_at', 'desc')
            ->get();

        return view('pages.pos.cash-transactions.create', compact('drawers', 'shifts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'shift_id'         => ['required'],
            'drawer_id'        => ['required', 'integer'],
            'transaction_type' => ['required', 'string'],
            'amount'           => ['required', 'numeric', 'min:0.01'],
            'reference_no'     => ['nullable', 'string', 'max:255'],
            'remarks'          => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data) {
            $txn = new POSCashTransaction();
            $txn->tenant_id        = auth()->user()->tenant_id;
            $txn->shift_id         = decryptId($data['shift_id']);
            $txn->drawer_id        = $data['drawer_id'];
            $txn->cashier_id       = auth()->id();
            $txn->transaction_type = $data['transaction_type'];
            $txn->amount           = $data['amount'];
            $txn->reference_no     = $data['reference_no'] ?? null;
            $txn->remarks          = $data['remarks'] ?? null;
            $this->setCommonFields($txn);
            $txn->save();
        });

        return redirect()
            ->route('cashiering.cash-transactions.index')
            ->with('success', 'Cash transaction recorded successfully.');
    }

    public function show(string $id)
    {
        $transaction = POSCashTransaction::where(
            'tenant_id',
            auth()->user()->tenant_id
        )->with(['shift', 'drawer', 'cashier'])->findOrFail(decryptId($id));

        return view('pages.pos.cash-transactions.show', compact('transaction'));
    }

    public function edit(string $id)
    {
        abort(404);
    }

    public function update(Request $request, string $id)
    {
        abort(404);
    }

    public function destroy(string $id)
    {
        $txn = POSCashTransaction::where(
            'tenant_id',
            auth()->user()->tenant_id
        )->findOrFail(decryptId($id));

        $txn->updated_by = auth()->id();
        $txn->save();
        $txn->delete();

        return redirect()
            ->route('cashiering.cash-transactions.index')
            ->with('success', 'Cash transaction deleted.');
    }
}
