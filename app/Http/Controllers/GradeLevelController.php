<?php

namespace App\Http\Controllers;

use App\Models\GradeLevel;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class GradeLevelController extends Controller
{
    use TCommonFunctions;
    public function index()
    {
        return view('pages.grade-levels.index');
    }

    public function ajaxData(Request $request)
    {
        $query = GradeLevel::query()
            ->latest();

        return DataTables::of($query)

            ->addColumn('grade_level', function ($gradeLevel) {

                return e($gradeLevel->GradeLevel);
            })

            ->addColumn('education_level', function ($gradeLevel) {

                return '
                    <span class="badge bg-info">
                        ' . e($gradeLevel->EducationLevel) . '
                    </span>
                ';
            })

            ->addColumn('has_semester', function ($gradeLevel) {

                if ($gradeLevel->HasSemester) {

                    return '
                        <span class="badge bg-success">
                            YES
                        </span>
                    ';
                }

                return '
                    <span class="badge bg-secondary">
                        NO
                    </span>
                ';
            })

            ->addColumn('status', function ($gradeLevel) {

                if ($gradeLevel->IsActive) {

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

            ->addColumn('created_at', function ($gradeLevel) {

                return $gradeLevel->created_at
                    ? $gradeLevel->created_at->format('M d, Y h:i A')
                    : '';
            })

            ->addColumn('actions', function ($gradeLevel) {

                $editUrl = route(
                    'grade-levels.edit',
                    encrypt($gradeLevel->id)
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
                'education_level',
                'has_semester',
                'status'
            ])

            ->make(true);
    }

    public function create()
    {
        return view('pages.grade-levels.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'GradeLevel' => [
                'required',
                'string',
                'max:100',
                'unique:grade_levels,GradeLevel'
            ],

            'EducationLevel' => [
                'required',
                Rule::in(['JHS','SHS'])
            ],
        ]);

        $i = new GradeLevel();

        $i->GradeLevel = $validated['GradeLevel'];

        $i->EducationLevel =
            $validated['EducationLevel'];

        $i->HasSemester =
            $validated['EducationLevel'] === 'SHS';

        $i->IsActive =
            $request->boolean('IsActive');

        $this->setCommonFields($i);

        $i->save();

        return redirect()
            ->route('grade-levels.index')
            ->with(
                'success',
                'Grade level created successfully.'
            );
    }

    public function show(GradeLevel $gradeLevel)
    {
        return redirect()
            ->route('grade-levels.index');
    }

    public function edit($id)
    {
        $gradeLevel = GradeLevel::findOrFail(
            decrypt($id)
        );

        return view(
            'pages.grade-levels.form',
            compact('gradeLevel')
        );
    }

    public function update(
        Request $request,
                $id
    ) {

        $gradeLevel = GradeLevel::findOrFail(
            decrypt($id)
        );

        $validated = $request->validate([
            'GradeLevel' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'grade-levels',
                    'GradeLevel'
                )->ignore($gradeLevel->id)
            ],

            'EducationLevel' => [
                'required',
                Rule::in(['JHS','SHS'])
            ],
        ]);

        $gradeLevel->GradeLevel =
            $validated['GradeLevel'];

        $gradeLevel->EducationLevel =
            $validated['EducationLevel'];

        $gradeLevel->HasSemester =
            $validated['EducationLevel'] === 'SHS';

        $gradeLevel->IsActive =
            $request->boolean('IsActive');

        $this->setCommonFields(
            $gradeLevel,
            false
        );

        $gradeLevel->save();

        return redirect()
            ->route('grade-levels.index')
            ->with(
                'success',
                'Grade level updated successfully.'
            );
    }

    public function destroy($id)
    {
        $gradeLevel = GradeLevel::findOrFail(
            decrypt($id)
        );

        $gradeLevel->delete();

        return redirect()
            ->route('grade-levels.index')
            ->with(
                'success',
                'Grade level deleted successfully.'
            );
    }
}
