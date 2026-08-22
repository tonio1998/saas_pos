<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSSale;
use App\Models\POS\POSSaleItem;
use App\Models\POS\POSProducts;
use App\Models\POS\InventoryMovement;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReturnController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        return view('pages.pos.returns.index');
    }

    public function create()
    {
        return view('pages.pos.returns.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => 'required|integer',
            'product_id' => 'required|integer',
            'qty' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            $product = POSProducts::findOrFail($validated['product_id']);
            $product->increment('stock_on_hand', $validated['qty']);

            $inv = new InventoryMovement();
            $inv->tenant_id = auth()->user()->tenant_id;
            $inv->product_id = $product->id;
            $inv->movement_type = 'return';
            $inv->reference_type = 'sale';
            $inv->reference_id = $validated['sale_id'];
            $inv->qty = $validated['qty'];
            $this->setCommonFields($inv);
            $inv->save();
        });

        return redirect()->route('returns.index')->with('success', 'Return processed and inventory adjusted.');
    }

    public function show($id)
    {
        $sale = POSSale::with(['items.product', 'customer'])->find($id);
        return view('pages.pos.returns.show', compact('sale'));
    }

    public function edit($id)
    {
        return redirect()->route('returns.index');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('returns.index');
    }

    public function destroy($id)
    {
        return redirect()->route('returns.index');
    }

    public function ajaxData()
    {
        $movements = InventoryMovement::where('tenant_id', auth()->user()->tenant_id)
            ->where('movement_type', 'return')
            ->with('product')
            ->latest();

        return DataTables::of($movements)
            ->addColumn('product_name', fn($row) => $row->product?->name ?? 'N/A')
            ->editColumn('created_at', fn($row) => $row->created_at ? $row->created_at->format('M d, Y h:i A') : 'N/A')
            ->make(true);
    }
}
