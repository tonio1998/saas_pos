<?php

namespace App\Http\Controllers\POS;

use App\Helpers\StatusHelper;
use App\Http\Controllers\Controller;
use App\Models\POS\POSCashCount;
use App\Models\POS\POSCashDrawer;
use App\Models\POS\POSCashMovement;
use App\Models\POS\POSCashShift;
use App\Models\POS\POSPayment;
use App\Models\POS\POSSale;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class SalesCashShiftController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        return view('pages.pos.cash-shifts.index');
    }

    public function cashCount()
    {
        return view('pages.pos.cash-shifts.cash-count.index');
    }

    public function closeStore(Request $request)
    {
        $data = $request->validate([
            'shift_id' => ['required'],
            'bills' => ['required', 'array'],
            'coins' => ['required', 'array'],
            'centavos' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string']
        ]);

        DB::transaction(function () use ($data) {
            $shift = POSCashShift::findOrFail(
                decryptId($data['shift_id'])
            );

            abort_if($shift->status === 'closed', 404);

            POSCashCount::where('shift_id', $shift->id)->delete();

            $actualCash = 0;

            foreach ($data['bills'] as $denomination => $qty) {

                $qty = (int) $qty;

                if ($qty <= 0) {
                    continue;
                }

                $amount = $denomination * $qty;

                $ccc = new POSCashCount();
                $ccc->tenant_id = auth()->user()->tenant_id;
                $ccc->shift_id = $shift->id;
                $ccc->denomination = $denomination;
                $ccc->quantity = $qty;
                $ccc->amount = $amount;
                $this->setCommonFields($ccc);
                $ccc->save();

                $actualCash += $amount;
            }

            foreach ($data['coins'] as $denomination => $qty) {

                $qty = (int) $qty;

                if ($qty <= 0) {
                    continue;
                }

                $amount = $denomination * $qty;

                $cc = new POSCashCount();
                $cc->tenant_id = auth()->user()->tenant_id;
                $cc->shift_id = $shift->id;
                $cc->denomination = $denomination;
                $cc->quantity = $qty;
                $cc->amount = $amount;
                $this->setCommonFields($cc);
                $cc->save();

                $actualCash += $amount;
            }

            $centavos = (float) ($data['centavos'] ?? 0);

            if ($centavos > 0) {
                $cashcount = new POSCashCount();
                $cashcount->tenant_id = auth()->user()->tenant_id;
                $cashcount->shift_id = $shift->id;
                $cashcount->denomination = 0;
                $cashcount->quantity = 1;
                $cashcount->amount = $centavos;
                $this->setCommonFields($cashcount);
                $cashcount->save();

                $actualCash += $centavos;
            }

            $payments = POSPayment::query()
                ->where('shift_id', $shift->id)
                ->where('is_void', 0);

            $cashSales = (clone $payments)
                ->where('payment_method', 'cash')
                ->sum('amount');

            $cashIn = POSCashMovement::cashIn()
                ->forShift($shift->id)
                ->sum('amount');

            $cashOut = POSCashMovement::cashOut()
                ->forShift($shift->id)
                ->sum('amount');

            $expectedCash = $shift->opening_cash + $cashSales + ($cashIn - $cashOut);
            $variance = $actualCash - $expectedCash;

            $shift->cash_sales = $cashSales;
            $shift->cash_in = $cashIn;
            $shift->cash_out = $cashOut;

            $shift->expected_cash = $expectedCash;
            $shift->actual_cash = $actualCash;

            $shift->over_amount = 0;
            $shift->short_amount = 0;

            if ($variance > 0) {
                $shift->over_amount = $variance;
            } elseif ($variance < 0) {
                $shift->short_amount = abs($variance);
            }

            $shift->remarks = $data['remarks'] ?? null;
            $shift->closed_at = now();
            $shift->status = 'closed';
            $shift->save();
        });

        return redirect()
            ->route('cashiering.cash-shifts.index')
            ->with(
                'success',
                'Cash count submitted successfully.'
            );
    }

    protected function ensureShiftOpen(POSCashShift $shift)
    {
        if ($shift->closed_at || $shift->status !== 'open') {

            return view(
                'pages.pos.cash-shifts.closed',
                compact('shift')
            );

        }

        return null;
    }

    public function close(string $id)
    {
        $shift = $this->findShift(decryptId($id));

        if ($response = $this->ensureShiftOpen($shift)) {
            return $response;
        }

        $payments = POSPayment::query()
            ->where('shift_id', $shift->id)
            ->where('is_void', 0);

        $cashSales = (clone $payments)
            ->where('payment_method', 'cash')
            ->sum('amount');

        $gcashSales = (clone $payments)
            ->where('payment_method', 'gcash')
            ->sum('amount');

        $bankTransferSales = (clone $payments)
            ->where('payment_method', 'bank_transfer')
            ->sum('amount');

        $totalSales = (clone $payments)
            ->sum('amount');

        $cashIn = POSCashMovement::query()
            ->where('cash_shift_id', $shift->id)
            ->where('type', 'IN')
            ->sum('amount');

        $cashOut = POSCashMovement::query()
            ->where('cash_shift_id', $shift->id)
            ->where('type', 'OUT')
            ->sum('amount');

        $expectedCash = $shift->opening_cash + $cashSales + ($cashIn - $cashOut);

        return view(
            'pages.pos.cash-shifts.close',
            compact(
                'shift',
                'cashSales',
                'gcashSales',
                'bankTransferSales',
                'totalSales',
                'cashIn',
                'cashOut',
                'expectedCash'
            )
        );
    }

    public function create(Request $request, string $drawerId)
    {
        $drawerId = decryptId($drawerId);
        $drawer = $this->findDrawer($drawerId);

        if ($drawer->activeShift()->exists()) {
            return redirect()
                ->route('cashiering.cash-shifts.index')
                ->with('error', 'This cash drawer already has an active shift.');
        }

        $terminal = null;
        if ($request->filled('terminal')) {
            try {
                $terminal = \App\Models\POS\POSTerminal::find(decryptId($request->query('terminal')));
            } catch (\Throwable $e) {}
        }

        return view('pages.pos.cash-shifts.create', compact('drawer', 'terminal'));
    }

    public function ajaxData(Request $request)
    {
        $shifts = POSCashShift::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('opened_at', 'desc');

        return datatables()
            ->eloquent($shifts)
        ->addColumn('actions', function ($shift) {
                $id = encryptId($shift->id);

                $btn = '';

                $btn .= '
                    <a
                        href="' . route('cashiering.cash-shifts.show', $id) . '"
                        class="btn btn-light text-start"
                    >
                        <i class="bi bi-eye me-2 text-primary"></i>
                        View Details
                    </a>
                ';

                if($shift->status !== 'closed'){
                    $btn .= '
                    <a
                        href="' . route('cashiering.cash-shifts.edit', $id) . '"
                        class="btn btn-light text-start"
                    >
                        <i class="bi bi-pencil-square me-2 text-warning"></i>
                        Edit Shift
                    </a>
                ';
                }

                if (!$shift->closed_at) {
                    $btn .= '
                        <a
                            href="' . route('cashiering.cash-shifts.close', $id) . '"
                            class="btn btn-light text-start"
                        >
                            <i class="bi bi-x-circle me-2 text-danger"></i>
                            Close Shift
                        </a>
                    ';
                }

                return '
                    <button
                        type="button"
                        class="btn btn-soft-primary btn-sm btn-actions"
                        data-bs-toggle="modal"
                        data-bs-target="#actionModal"
                        data-title="Cash Shift Actions"
                        data-template="actions-' . $shift->id . '"
                    >
                        <i class="bi bi-gear"></i>
                        Actions
                    </button>

                    <template id="actions-' . $shift->id . '">
                        <div class="d-grid gap-2">
                            ' . $btn . '
                        </div>
                    </template>
                ';
            })
            ->addColumn('shift_code', function ($shift) {
                return $shift->shift_code;
            })
            ->addColumn('drawer', function ($shift) {
                return $shift->drawer?->drawer_name ?? '-';
            })
            ->addColumn('cashier', function ($shift) {
                return $shift->cashier?->name ?? '-';
            })
            ->addColumn('opening_cash', function ($shift) {
                return number_format($shift->opening_cash, 2);
            })
            ->addColumn('opened_at', function ($shift) {
                return $shift->opened_at ? StatusHelper::formatDateTime($shift->opened_at) : '-';
            })
            ->addColumn('closed_at', function ($shift) {
                return $shift->closed_at ? StatusHelper::formatDateTime($shift->closed_at) : '-';
            })
            ->addColumn('status', function ($shift) {
                return StatusHelper::badge($shift->status);
            })
            ->addColumn('remarks', function ($shift) {
                return $shift->remarks ?? '-';
            })
            ->addColumn('createdBy', function ($shift) {
                return $shift->creator?->name ?? '-';
            })
            ->addColumn('updatedBy', function ($shift) {
                return $shift->updater?->name ?? '-';
            })
            ->editColumn('created_at', function ($shift) {
                return $shift->created_at ? StatusHelper::formatDateTime($shift->created_at) : '-';
            })
            ->editColumn('updated_at', function ($shift) {
                return $shift->updated_at ? StatusHelper::formatDateTime($shift->updated_at) : '-';
            })
            ->rawColumns([
                'actions',
                'shift_code',
                'drawer',
                'cashier',
                'opening_cash',
                'opened_at',
                'closed_at',
                'status',
                'remarks',
                'createdBy',
                'updatedBy'
            ])
            ->make(true);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'drawer_id' => ['required', 'integer'],
            'opening_cash' => ['required', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string'],
            'terminal_id' => ['nullable']
        ]);

        if (
            POSCashShift::where('tenant_id', auth()->user()->tenant_id)
                ->where('drawer_id', $data['drawer_id'])
                ->where('status', 'open')
                ->exists()
        ) {
            return back()
                ->withInput()
                ->with('error', 'This cash drawer already has an active shift.');
        }

        $shift = new POSCashShift();
        $shift->tenant_id = auth()->user()->tenant_id;
        $shift->drawer_id = $data['drawer_id'];
        $shift->cashier_id = auth()->id();
        $shift->opening_cash = $data['opening_cash'];
        $shift->opened_at = now();
        $shift->remarks = $data['remarks'] ?? null;
        $shift->shift_code = 'SFT-' . strtoupper(\Illuminate\Support\Str::random(6));
        $this->setCommonFields($shift);
        $shift->status = 'open';
        $shift->save();

        // Resolve active POS device/terminal
        $terminal = null;
        if (!empty($data['terminal_id'])) {
            try {
                $terminal = \App\Models\POS\POSTerminal::where('tenant_id', auth()->user()->tenant_id)
                    ->find(decryptId($data['terminal_id']));
            } catch (\Throwable $e) {}
        }

        if (!$terminal) {
            $terminal = \App\Models\POS\POSTerminal::where('tenant_id', auth()->user()->tenant_id)
                ->where('drawer_id', $data['drawer_id'])
                ->where('status', 'active')
                ->first();
        }

        if ($terminal) {
            $sale = new POSSale();
            $sale->tenant_id = auth()->user()->tenant_id;
            $sale->terminal_id = $terminal->id;
            $sale->drawer_id = $terminal->drawer_id;
            $sale->cash_shift_id = $shift->id;
            $this->setCommonFields($sale);
            $sale->save();

            session()->put([
                'terminal_id'   => $terminal->id,
                'drawer_id'     => $terminal->drawer_id,
                'cash_shift_id' => $shift->id,
                'sale_id'       => $sale->id,
            ]);

            return redirect()->route(
                'sales.new',
                [
                    encryptId($sale->id)
                ]
            )->with('success', 'Cash shift opened successfully! Ready for transactions.');
        }

        return redirect()
            ->route('terminal.index')
            ->with('success', 'Cash shift opened successfully. Please select your device.');
    }

    public function show(string $id)
    {
        $shift = $this->findShift(decryptId($id));

        $payments = POSPayment::query()
            ->where('shift_id', $shift->id)
            ->where('is_void', 0);

        $cashSales = (clone $payments)
            ->where('payment_method', 'cash')
            ->sum('amount');

        $gcashSales = (clone $payments)
            ->where('payment_method', 'gcash')
            ->sum('amount');

        $bankTransferSales = (clone $payments)
            ->where('payment_method', 'bank_transfer')
            ->sum('amount');

        $totalSales = (clone $payments)
            ->sum('amount');

        $cashIn = POSCashMovement::query()
            ->where('cash_shift_id', $shift->id)
            ->where('type', 'IN')
            ->sum('amount');

        $cashOut = POSCashMovement::query()
            ->where('cash_shift_id', $shift->id)
            ->where('type', 'OUT')
            ->sum('amount');

        $expectedCash = $shift->opening_cash + $cashSales + ($cashIn - $cashOut);

        $cashCounts = POSCashCount::where('shift_id', $shift->id)
            ->orderBy('denomination', 'desc')
            ->get();

        return view(
            'pages.pos.cash-shifts.show',
            compact(
                'shift',
                'cashSales',
                'gcashSales',
                'bankTransferSales',
                'totalSales',
                'cashIn',
                'cashOut',
                'expectedCash',
                'cashCounts'
            )
        );
    }

    public function shifts(string $id)
    {
        $decryptedId = decryptId($id);

        $shift = POSCashShift::where('tenant_id', auth()->user()->tenant_id)->find($decryptedId);
        if ($shift) {
            return $this->show($id);
        }

        $drawer = POSCashDrawer::where('tenant_id', auth()->user()->tenant_id)->find($decryptedId);
        if ($drawer) {
            $latestShift = POSCashShift::where('tenant_id', auth()->user()->tenant_id)
                ->where('drawer_id', $drawer->id)
                ->latest('id')
                ->first();

            if ($latestShift) {
                return $this->show(encryptId($latestShift->id));
            }

            return redirect()->route('cashiering.cash-shifts.create', encryptId($drawer->id))
                ->with('info', 'No active cash shift found for this drawer. Please open a new shift.');
        }

        return redirect()->route('cashiering.cash-shifts.index')
            ->with('error', 'Cash shift or drawer record not found.');
    }

    public function approve(Request $request, string $id)
    {
        $shift = POSCashShift::where('tenant_id', auth()->user()->tenant_id)->findOrFail(decryptId($id));

        $note = $request->input('remarks') ?: 'Shift verified and approved without discrepancies.';
        $supervisorTag = "\n[APPROVED BY SUPERVISOR (" . auth()->user()->name . ") ON " . now()->format('M d, Y h:i A') . "]: " . $note;

        $shift->remarks = trim(($shift->remarks ?? '') . ' ' . $supervisorTag);
        $shift->updated_by = auth()->id();
        $shift->save();

        return back()->with('success', 'Shift #' . $shift->shift_code . ' approved successfully by supervisor!');
    }

    public function verifyAction(Request $request, string $id)
    {
        $shift = POSCashShift::where('tenant_id', auth()->user()->tenant_id)->findOrFail(decryptId($id));

        $action = strtoupper($request->input('action_type', 'REVIEW'));
        $note = $request->input('remarks') ?: 'Supervisor review performed.';
        $supervisorTag = "\n[" . $action . " BY SUPERVISOR (" . auth()->user()->name . ") ON " . now()->format('M d, Y h:i A') . "]: " . $note;

        $shift->remarks = trim(($shift->remarks ?? '') . ' ' . $supervisorTag);
        $shift->updated_by = auth()->id();
        $shift->save();

        return back()->with('success', 'Supervisor verification (' . $action . ') recorded for Shift #' . $shift->shift_code . '!');
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
        $shift = $this->findShift($id);

        if ($shift->status === 'open') {
            return back()
                ->with('error', 'Close the shift before deleting it.');
        }

        if ($shift->transactions()->exists()) {
            return back()
                ->with('error', 'Cash shift cannot be deleted because it has existing transactions.');
        }

        $shift->updated_by = auth()->id();
        $shift->save();
        $shift->delete();

        return redirect()
            ->route('cashiering.cash-shifts.index')
            ->with('success', 'Cash shift deleted successfully.');
    }

    private function findShift(string $id): POSCashShift
    {
        return POSCashShift::where(
            'tenant_id',
            auth()->user()->tenant_id
        )->findOrFail($id);
    }

    private function findDrawer(string $id): POSCashDrawer
    {
        return POSCashDrawer::where(
            'tenant_id',
            auth()->user()->tenant_id
        )->findOrFail($id);
    }
}
