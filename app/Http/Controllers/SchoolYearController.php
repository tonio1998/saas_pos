<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use App\Models\SchoolYear;
use App\Models\School;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolYearController extends Controller
{
    use TCommonFunctions;
    public function index()
    {
        $schoolYears = SchoolYear::query()
            ->latest()
            ->get();

        return view(
            'pages.school_years.index',
            compact('schoolYears')
        );
    }

    public function create()
    {
        return redirect()
            ->route('school_years.index');
    }

    public function ajaxData(Request $request)
    {
        $query = SchoolYear::where('archived', 0);

        return datatables()
            ->eloquent($query)
            ->addColumn('actions', function ($parent) {
                $menu = '';
                if(auth()->user()->can('view users')) {
//                    $menu .= '
//                    <li>
//                        <a href="'.route('school_years.edit',encrypt($parent->id)).'" class="dropdown-item">
//                            <i class="bi bi-pencil me-2"></i> Edit
//                        </a>
//                    </li>';
                }

                return '
                    <div class="dropdown">
                        <button class="btn btn-soft-primary btn-sm p-2 px-3 dropdown-toggle" data-bs-toggle="dropdown">
                           Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            '.$menu.'
                        </ul>
                    </div>';

            })
            ->addColumn('SchoolYear', function ($parent) {
                $a = "<div class='fw-bold'>" . $parent->AYFrom . '-' . $parent->AYTo . "</div>";
                return $a;
            })
            ->addColumn('StartDate', function ($parent) {
                return date('F d, Y', strtotime($parent->StartDate));
            })
            ->addColumn('EndDate', function ($parent) {
                return date('F d, Y', strtotime($parent->EndDate));
            })
            ->addColumn('IsActive', function ($parent) {
                return $parent->IsActive == 1 ? 'Active' : 'Inactive';
            })
            ->editColumn('created_at', function ($parent) {
                return $parent->created_at->format('M d, Y h:i A');
            })
            ->rawColumns(['actions', 'name', 'SchoolYear'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'SchoolYear' => [
                'required',
                'string',
                'max:20',
            ],

            'StartDate' => [
                'required',
                'date'
            ],

            'EndDate' => [
                'required',
                'date',
                'after:StartDate'
            ],
        ]);

        if ($request->boolean('IsActive')) {

            SchoolYear::query()
                ->update([
                    'IsActive' => 0
                ]);
        }

        $i = new SchoolYear();
        [$ayFrom, $ayTo] = explode('-', $validated['SchoolYear']);
        $i->AYFrom = $ayFrom;
        $i->AYTo = $ayTo;
        $i->StartDate = $validated['StartDate'];
        $i->EndDate = $validated['EndDate'];
        $i->IsActive = $request->boolean('IsActive');

        $this->setCommonFields($i);

        $i->save();

        if ($i->IsActive) {

            $settings = School::query()->first();

            if ($settings) {

                $settings->CurrentSchoolYearID = $i->id;

                $settings->save();
            }
        }

        return redirect()
            ->route('school_years.index')
            ->with(
                'success',
                'School year created successfully.'
            );
    }

    public function show(SchoolYear $schoolYear)
    {
        return redirect()
            ->route('school_years.index');
    }

    public function edit(SchoolYear $schoolYear)
    {
        return redirect()
            ->route('school_years.index');
    }

    public function update(
        Request $request,
        SchoolYear $schoolYear
    ) {

        $validated = $request->validate([
            'SchoolYear' => [
                'required',
                'string',
                'max:20'
            ],

            'StartDate' => [
                'required',
                'date'
            ],

            'EndDate' => [
                'required',
                'date',
                'after:StartDate'
            ],
        ]);

        if ($request->boolean('IsActive')) {

            SchoolYear::query()
                ->where('id', '!=', $schoolYear->id)
                ->update([
                    'IsActive' => 0
                ]);
        }

        [$ayFrom, $ayTo] = explode('-', $validated['SchoolYear']);
        $schoolYear->AYFrom = $ayFrom;
        $schoolYear->AYTo = $ayTo;
        $schoolYear->StartDate = $validated['StartDate'];
        $schoolYear->EndDate = $validated['EndDate'];
        $schoolYear->IsActive = $request->boolean('IsActive');

        $this->setCommonFields($schoolYear, false);

        $schoolYear->save();

        if ($schoolYear->IsActive) {

            $settings = School::query()->first();

            if ($settings) {

                $settings->CurrentSchoolYearID = $schoolYear->id;

                $settings->save();
            }
        }

        return redirect()
            ->route('school_years.index')
            ->with(
                'success',
                'School year updated successfully.'
            );
    }

    public function destroy(SchoolYear $schoolYear)
    {
        if ($schoolYear->IsActive) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Active school year cannot be deleted.'
                );
        }

        $schoolYear->delete();

        return redirect()
            ->route('school_years.index')
            ->with(
                'success',
                'School year deleted successfully.'
            );
    }
}
