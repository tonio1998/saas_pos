<?php

namespace App\Http\Controllers\POS;
use App\Http\Controllers\Controller;
use App\Models\POS\POSCashDrawer;
use App\Models\POS\POSCashShift;
use App\Models\POS\POSSale;
use App\Models\POS\POSTerminal;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class POSTerminalController extends Controller
{
    use TCommonFunctions;
    public function index(Request $request)
    {
        $terminals = POSTerminal::query()
            ->with('drawer')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->whereIn('status', ['active', 'ACTIVE'])
            ->orderBy('terminal_name')
            ->get();

        if ($terminals->count() === 1 && !$request->boolean('change')) {
            return $this->processTerminalSelection($terminals->first());
        }

        return view(
            'pages.pos.terminal.select-terminal',
            compact('terminals')
        );
    }

    public function store(Request $request)
    {
        $deviceCheck = (new \App\Services\Tenant\TenantSubscriptionService())->canCreateDevice(auth()->user()->tenant_id);
        if (!$deviceCheck['allowed']) {
            return back()
                ->withInput()
                ->withErrors([
                    'terminal_name' => $deviceCheck['message']
                ]);
        }

        $data = $request->validate([
            'terminal_name' => [
                'required',
                'string',
                'max:100'
            ],
            'drawer_id' => [
                'nullable',
                Rule::exists('pos_cash_drawers', 'id')
                    ->where('tenant_id', auth()->user()->tenant_id)
            ],
            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                    'maintenance'
                ])
            ],
            'remarks' => [
                'nullable',
                'string'
            ]
        ]);

        if (!empty($data['drawer_id'])) {
            $drawerAssigned = POSTerminal::query()
                ->where('tenant_id', auth()->user()->tenant_id)
                ->where('drawer_id', $data['drawer_id'])
                ->exists();

            if ($drawerAssigned) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'drawer_id' => 'The selected cash drawer is already assigned to another POS device.'
                    ]);
            }
        }

        DB::transaction(function () use ($data) {
            $drawerId = $data['drawer_id'] ?? null;

            if (!$drawerId) {
                $drawer = new POSCashDrawer();
                $drawer->tenant_id = auth()->user()->tenant_id;
                $drawer->drawer_name = $data['terminal_name'] . ' Drawer';
                $drawer->drawer_code = 'DRW-' . strtoupper(\Illuminate\Support\Str::random(6));
                $drawer->status = 'active';
                $this->setCommonFields($drawer);
                $drawer->save();
                $drawerId = $drawer->id;
            }

            $terminal = new POSTerminal();
            $terminal->tenant_id = auth()->user()->tenant_id;
            $terminal->terminal_name = $data['terminal_name'];
            $terminal->terminal_code = 'DEV-' . strtoupper(\Illuminate\Support\Str::random(6));
            $terminal->drawer_id = $drawerId;
            $terminal->status = $data['status'];
            $terminal->remarks = $data['remarks'] ?? null;
            $this->setCommonFields($terminal);
            $terminal->save();
        });

        return redirect()
            ->route('terminal.index')
            ->with('success', 'POS Device has been registered successfully.');
    }

    public function create()
    {
        $deviceCheck = (new \App\Services\Tenant\TenantSubscriptionService())->canCreateDevice(auth()->user()->tenant_id);
        $drawers = POSCashDrawer::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('drawer_name')
            ->get();

        return view('pages.pos.terminal.create', compact('drawers', 'deviceCheck'));
    }

    public function select(Request $request)
    {
        $request->validate([
            'terminal_id' => ['required']
        ]);

        $terminal = POSTerminal::with('drawer')
            ->findOrFail(decryptId($request->terminal_id));

        return $this->processTerminalSelection($terminal);
    }

    protected function processTerminalSelection(POSTerminal $terminal)
    {
        $shifts = POSCashShift::query()
            ->with('cashier')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('drawer_id', $terminal->drawer_id)
            ->whereNull('closed_at')
            ->orderByDesc('opened_at')
            ->get();

        if ($shifts->isEmpty()) {

            return redirect()->route(
                'cashiering.cash-shifts.create',
                [
                    'terminal' => encryptId($terminal->id),
                    'drawer'   => encryptId($terminal->drawer_id)
                ]
            );

        }

        if ($shifts->count() == 1) {

            return $this->createSale(
                $terminal,
                $shifts->first()
            );

        }

        return view(
            'pages.tenants.terminal.select-shift',
            compact(
                'terminal',
                'shifts'
            )
        );
    }

    protected function createSale(POSTerminal $terminal, POSCashShift $shift) {
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
        );
    }
}
