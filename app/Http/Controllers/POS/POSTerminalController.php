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
        $tenantId = auth()->user()->tenant_id;

        // Check if the current cashier already has an active open shift anywhere
        $userActiveShift = POSCashShift::query()
            ->with(['drawer'])
            ->where('tenant_id', $tenantId)
            ->where('cashier_id', auth()->id())
            ->where('status', 'open')
            ->whereNull('closed_at')
            ->first();

        // If the user already has an active shift and hasn't explicitly requested to change/view all terminals,
        // automatically route them to their active terminal
        if ($userActiveShift && !$request->boolean('change')) {
            $userTerminal = POSTerminal::where('tenant_id', $tenantId)
                ->where('drawer_id', $userActiveShift->drawer_id)
                ->whereIn('status', ['active', 'ACTIVE'])
                ->first();

            if ($userTerminal) {
                return $this->createSale($userTerminal, $userActiveShift);
            }
        }

        $terminals = POSTerminal::query()
            ->with(['drawer', 'drawer.activeShift.cashier'])
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['active', 'ACTIVE'])
            ->orderBy('terminal_name')
            ->get();

        // If only 1 terminal registered and no active shift conflict
        if ($terminals->count() === 1 && !$request->boolean('change')) {
            return $this->processTerminalSelection($terminals->first());
        }

        return view(
            'pages.pos.terminal.select-terminal',
            compact('terminals', 'userActiveShift')
        );
    }

    public function select(Request $request)
    {
        $request->validate([
            'terminal_id' => ['required'],
        ]);

        try {
            $terminalId = decryptId($request->input('terminal_id'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Invalid POS device selection.');
        }

        $terminal = POSTerminal::where('tenant_id', auth()->user()->tenant_id)
            ->where('id', $terminalId)
            ->firstOrFail();

        return $this->processTerminalSelection($terminal);
    }

    public function create(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $deviceCheck = (new \App\Services\Tenant\TenantSubscriptionService())->canCreateDevice($tenantId);
        $drawers = POSCashDrawer::where('tenant_id', $tenantId)->where('status', 'active')->get();

        return view('pages.pos.terminal.create', compact('deviceCheck', 'drawers'));
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
            $terminal->terminal_code = 'TERM-' . strtoupper(\Illuminate\Support\Str::random(6));
            $terminal->drawer_id = $drawerId;
            $terminal->status = strtolower($data['status']);
            $terminal->remarks = $data['remarks'] ?? null;
            $this->setCommonFields($terminal);
            $terminal->save();
        });

        return redirect()->route('terminal.index')->with('success', 'POS Terminal registered successfully.');
    }

    protected function processTerminalSelection(POSTerminal $terminal)
    {
        $tenantId = auth()->user()->tenant_id;
        $drawerId = $terminal->drawer_id;

        // Auto-assign drawer if not present
        if (!$drawerId) {
            $drawer = POSCashDrawer::where('tenant_id', $tenantId)->first();
            if (!$drawer) {
                $drawer = new POSCashDrawer();
                $drawer->tenant_id = $tenantId;
                $drawer->drawer_name = ($terminal->terminal_name ?: 'Main Terminal') . ' Drawer';
                $drawer->drawer_code = 'DRW-' . strtoupper(\Illuminate\Support\Str::random(6));
                $drawer->status = 'active';
                $this->setCommonFields($drawer);
                $drawer->save();
            }
            $drawerId = $drawer->id;
            $terminal->drawer_id = $drawerId;
            $terminal->save();
        }

        // 1. RULE: Does current cashier have an active open shift on ANOTHER drawer?
        $userActiveShift = POSCashShift::query()
            ->with(['drawer'])
            ->where('tenant_id', $tenantId)
            ->where('cashier_id', auth()->id())
            ->where('status', 'open')
            ->whereNull('closed_at')
            ->first();

        if ($userActiveShift && $userActiveShift->drawer_id != $drawerId) {
            $activeDrawerName = $userActiveShift->drawer?->drawer_name ?? 'ibang terminal';
            return redirect()->route('terminal.index', ['change' => 1])
                ->with('error', "Mayroon ka pang aktibong shift sa [{$activeDrawerName}]. Bawal mag-transact sa higit sa isang terminal nang sabay. Paki-close muna ang iyong kasalukuyang shift bago lumipat.");
        }

        // 2. RULE: Does the target terminal's drawer currently have an active open shift?
        $drawerActiveShift = POSCashShift::query()
            ->with('cashier')
            ->where('tenant_id', $tenantId)
            ->where('drawer_id', $drawerId)
            ->where('status', 'open')
            ->whereNull('closed_at')
            ->orderByDesc('opened_at')
            ->first();

        if ($drawerActiveShift) {
            // If the active shift belongs to another cashier, BLOCK ACCESS
            if ($drawerActiveShift->cashier_id != auth()->id()) {
                $cashierName = $drawerActiveShift->cashier?->name ?? 'ibang cashier';
                $openedTime = $drawerActiveShift->opened_at ? \Carbon\Carbon::parse($drawerActiveShift->opened_at)->format('g:i A') : '';
                return redirect()->route('terminal.index', ['change' => 1])
                    ->with('error', "Ang {$terminal->terminal_name} ay kasalukuyang ginagamit ni Cashier {$cashierName} (Shift bukas mula {$openedTime}). Mangyaring pumili ng bakanteng terminal o ipa-close ang shift kay {$cashierName}.");
            }

            // If it's the cashier's own active shift, resume transaction
            return $this->createSale($terminal, $drawerActiveShift);
        }

        // 3. No active shift on this drawer: prompt cashier to open a new shift
        return redirect()->route(
            'cashiering.cash-shifts.create',
            [
                'drawer'   => encryptId($drawerId),
                'terminal' => encryptId($terminal->id),
            ]
        );
    }

    protected function createSale(POSTerminal $terminal, POSCashShift $shift)
    {
        // Check if there is an existing pending sale for this cashier & shift
        $pendingSale = POSSale::where('tenant_id', auth()->user()->tenant_id)
            ->where('terminal_id', $terminal->id)
            ->where('cash_shift_id', $shift->id)
            ->where('created_by', auth()->id())
            ->where('sale_status', 'pending')
            ->latest()
            ->first();

        if ($pendingSale) {
            $sale = $pendingSale;
        } else {
            $sale = new POSSale();
            $sale->tenant_id = auth()->user()->tenant_id;
            $sale->terminal_id = $terminal->id;
            $sale->drawer_id = $terminal->drawer_id;
            $sale->cash_shift_id = $shift->id;
            $sale->sale_status = 'pending';
            $this->setCommonFields($sale);
            $sale->save();
        }

        session()->put([
            'terminal_id'   => $terminal->id,
            'drawer_id'     => $terminal->drawer_id,
            'cash_shift_id' => $shift->id,
            'sale_id'       => $sale->id,
        ]);

        return redirect()->route('sales.create', [
            'sale' => encryptId($sale->id),
        ]);
    }
}
