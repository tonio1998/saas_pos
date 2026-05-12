<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AcademicContextController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'Semester' => [
                'required',
                'integer',
                'in:1,2,3'
            ],

            'AYFrom' => [
                'required',
                'integer',
                'digits:4'
            ],
        ]);

        $ayFrom =
            (int)$request->AYFrom;

        $ayTo =
            $ayFrom + 1;

        session([
            'Semester' =>
                (int)$request->Semester,

            'AYFrom' =>
                $ayFrom,

            'AYTo' =>
                $ayTo,
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Academic context updated successfully.'
            );
    }
}
