<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSProducts;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index()
    {
        return view('pages.store.terminal.index');
    }

    public function create()
    {
        $products = POSProducts::query()
            ->with('createdBy')
            ->orderBy('name')
            ->get();

        return view('pages.tenants.terminal.index', [
            'products' => $products,
        ]);
    }
}
