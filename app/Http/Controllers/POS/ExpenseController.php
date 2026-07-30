<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        return view('pages.tenants.expenses.index');
    }

    public function create()
    {
        return view('pages.tenants.expenses.create');
    }
}
