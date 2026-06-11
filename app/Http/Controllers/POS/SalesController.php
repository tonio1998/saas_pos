<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSCategories;
use App\Models\POS\POSProducts;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function products()
    {
        return POSProducts::select(
            'id',
            'name',
            'barcode',
            'selling_price',
            'stock_on_hand',
            'category_id',
            'image'
        )
            ->where('tenant_id', auth()->user()->tenant_id)
            ->get();
    }

    public function index()
    {
        return view('pages.store.terminal.index');
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

        return view('pages.tenants.terminal.index', [
            'products' => $products,
            'categories' => $categories
        ]);
    }
}
