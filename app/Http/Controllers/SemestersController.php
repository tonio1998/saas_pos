<?php

namespace App\Http\Controllers;

use App\Models\Semesters;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SemestersController extends Controller
{
    use TCommonFunctions;
    public function index()
    {
        return view('pages.semesters.index');
    }

    public function data(Request $request)
    {
        $query = Semesters::query()
            ->latest();

        return DataTables::of($query)

            ->addColumn('semester_name', function ($semester) {

                return e($semester->SemesterName);
            })

            ->addColumn('semester_order', function ($semester) {

                return e($semester->SemesterOrder);
            })

            ->addColumn('status', function ($semester) {

                if ($semester->IsActive) {

                    return '
                        <span class="badge bg-success">
                            Active
                        </span>
                    ';
                }

                return '
                    <span class="badge bg-secondary">
                        Inactive
                    </span>
                ';
            })

            ->addColumn('created_at', function ($semester) {

                return $semester->created_at
                    ? $semester->created_at->format('M d, Y h:i A')
                    : '';
            })

            ->addColumn('actions', function ($semester) {

                $editUrl = route(
                    'semesters.edit',
                    $semester->id
                );

                return '

                    <div class="dropdown">

                        <button
                            class="btn btn-light btn-sm"
                            data-bs-toggle="dropdown"
                        >
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">

                            <li>

                                <a
                                    href="' . $editUrl . '"
                                    class="dropdown-item"
                                >
                                    <i class="bi bi-pencil-square me-2"></i>
                                    Edit
                                </a>

                            </li>

                        </ul>

                    </div>

                ';
            })

            ->rawColumns([
                'actions',
                'status'
            ])

            ->make(true);
    }

    public function create()
    {
        return view('pages.semesters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'SemesterName' => [
                'required',
                'string',
                'max:100',
                'unique:semesters,SemesterName'
            ],

            'SemesterOrder' => [
                'required',
                'integer',
                'min:1'
            ],
        ]);

        if ($request->boolean('IsActive')) {

            Semesters::query()
                ->update([
                    'IsActive' => 0
                ]);
        }

        $i = new Semesters();

        $i->SemesterName = $validated['SemesterName'];
        $i->SemesterOrder = $validated['SemesterOrder'];
        $i->IsActive = $request->boolean('IsActive');

        $this->setCommonFields($i);

        $i->save();

        return redirect()
            ->route('semesters.index')
            ->with(
                'success',
                'Semester created successfully.'
            );
    }

    public function show(Semesters $semesters)
    {
        return redirect()
            ->route('semesters.index');
    }

    public function edit(Request $request)
    {
        $id = $request->segment(3);
        $semester = Semesters::findOrFail($id);

        return view(
            'pages.semesters.create',
            compact('semester')
        );
    }

    public function update(
        Request $request,
        Semesters $semesters
    ) {

        $validated = $request->validate([
            'SemesterName' => [
                'required',
                'string',
                'max:100',
                Rule::unique('semesters')
                    ->where(function ($query) use ($request) {
                        return $query->where(
                            'SemesterName',
                            $request->SemesterName
                        );
                    })
                    ->ignore($semesters->SemesterID, 'SemesterID'),
            ],

            'SemesterOrder' => [
                'required',
                'integer',
                'min:1'
            ],
        ]);

        if ($request->boolean('IsActive')) {

            Semesters::query()
                ->where(
                    'SemesterID',
                    '!=',
                    $semesters->SemesterID
                )
                ->update([
                    'IsActive' => 0
                ]);
        }

        $semesters->SemesterName = $validated['SemesterName'];
        $semesters->SemesterOrder = $validated['SemesterOrder'];
        $semesters->IsActive = $request->boolean('IsActive');

        $this->setCommonFields($semesters, false);

        $semesters->save();

        return redirect()
            ->route('semesters.index')
            ->with(
                'success',
                'Semester updated successfully.'
            );
    }

    public function destroy(Semesters $semesters)
    {
        if ($semesters->IsActive) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Active semester cannot be deleted.'
                );
        }

        $semesters->delete();

        return redirect()
            ->route('semesters.index')
            ->with(
                'success',
                'Semester deleted successfully.'
            );
    }

    public function ajaxData(Request $request)
    {
        $query = Semesters::query()
            ->latest();

        return DataTables::of($query)
            ->addColumn('semester_name', function ($semester) {
                return generateSemesterName($semester->SemesterName);
            })
            ->addColumn('semester_order', function ($semester) {
                return e($semester->SemesterOrder);
            })
            ->addColumn('status', function ($semester) {
                if ($semester->IsActive) {
                    return '
                        <span class="badge bg-success">
                            Active
                        </span>
                    ';
                }
                return '
                    <span class="badge bg-secondary">
                        Inactive
                    </span>
                ';
            })
            ->addColumn('created_at', function ($semester) {
                return $semester->created_at
                    ? $semester->created_at->format('M d, Y h:i A')
                    : '';
            })
            ->addColumn('actions', function ($semester) {
                $editUrl = route('semesters.edit', $semester->id);

                return '
                    <div class="dropdown">
                        <button
                            class="btn btn-light btn-sm"
                            data-bs-toggle="dropdown"
                        >
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a
                                    href="' . $editUrl . '"
                                    class="dropdown-item"
                                >
                                    <i class="bi bi-pencil-square me-2"></i>
                                    Edit
                                </a>
                            </li>
                        </ul>
                    </div>
                ';
            })
            ->rawColumns([
                'actions',
                'status'
            ])
            ->make(true);
    }
}
