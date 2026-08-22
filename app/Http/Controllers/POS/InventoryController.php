<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\Stocks;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $movements = Stocks::where('tenant_id', $tenantId)
            ->with(['product', 'creator'])
            ->latest()
            ->get();

        return view('pages.tenants.inventory.movements.index', compact('movements'));
    }
}
