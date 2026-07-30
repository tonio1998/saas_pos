<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;

class TenantsContextController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        return view('pages.pos.tenants.context.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'FiscalYear' => [
                'required',
                'year'
            ],
        ]);

        session([
            'FiscalYear' => (int)$request->FiscalYear,
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'FiscalYear context updated successfully.'
            );
    }
}
