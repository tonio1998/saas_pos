<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Strands;
use App\Models\Employees;
use App\Models\GradeLevel;
use App\Models\SchoolYear;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ClassesController extends Controller
{
    use TCommonFunctions;
    public function sections_search(Request $request)
    {
        $search = $request->search;
        $Semester = session('Semester');
        $AYFrom = session('AYFrom');
        $AYTo = session('AYTo');

        $semesterNames = [
            1 => '1st Semester',
            2 => '2nd Semester',
            3 => 'Summer',
        ];

        $sections = Classes::query()
            ->with([
                'gradeLevel',
                'strand',
                'adviser'
            ])
            ->when($search,function($q) use ($search){
                $q->where(
                    'SectionName',
                    'like',
                    "%{$search}%"
                );
            })
            ->where('Semester',$Semester)
            ->where('AYFrom',$AYFrom)
            ->where('AYTo',$AYTo)
            ->limit(60)
            ->get();

        return $sections->map(function($section) use ($semesterNames){
            return [
                'id' => $section->id,
                'text' => $section->ClassName . ' • ' . ($section->gradeLevel?->GradeLevel ?? '-') . ' • ' . ($section->strand?->StrandCode ?? 'N/A') . ' • SY ' . $section->AYFrom . '-' . $section->AYTo,
                'semester' => $semesterNames[$section->Semester] ?? '-',
                'academic_year' => $section->AYFrom . '-' . $section->AYTo,
                'grade_level' => $section->gradeLevel?->GradeLevel ?? '-',
                'strand' => $section->strand?->StrandCode ?? 'N/A',
                'adviser' => $section->adviser?->FullName ?? 'N/A'
            ];

        });
    }

    public function index()
    {
        return view('pages.classes.index');
    }

    public function ajaxData(Request $request)
    {
        $Semester = session('Semester');
        $AYFrom = session('AYFrom');
        $AYTo = session('AYTo');

        $query = Classes::query()
            ->with([
                'schoolYear',
                'gradeLevel',
                'strand',
                'adviser'
            ])
            ->where('Semester',$Semester)
            ->where('AYFrom',$AYFrom)
            ->where('AYTo',$AYTo)
            ->latest();

        return DataTables::of($query)
            ->addColumn('school_year', function ($section) {
                return e(
                    generateSemesterName($section?->Semester) . "\n" . $section?->AYFrom . '-' . $section?->AYTo
                );
            })
            ->addColumn('grade_level', function ($section) {
                return e(
                    $section->gradeLevel?->GradeLevel
                );
            })
            ->addColumn('strand', function ($section) {
                return $section->strand
                    ? e($section->strand->StrandCode)
                    : '-';
            })
            ->addColumn('section_name', function ($section) {
                return e($section->SectionName);
            })
            ->addColumn('adviser', function ($section) {
                return $section->adviser
                    ? e($section->adviser->FullName)
                    : '-';
            })
            ->addColumn('student_count', function ($section) {
                return 0;
            })
            ->addColumn('capacity', function ($section) {
                return e($section->Capacity);
            })
            ->addColumn('room', function ($section) {
                return $section->Room
                    ? e($section->Room)
                    : '-';
            })
            ->addColumn('status', function ($section) {

                if ($section->status === 'active') {

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

            ->addColumn('actions', function ($section) {

                $editUrl = route(
                    'classes.edit',
                    encrypt($section->id)
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
        $schoolYears = SchoolYear::query()
            ->where('status','=','active')
            ->get();

        $gradeLevels = GradeLevel::query()
            ->where('status','=','active')
            ->get();

        $strands = Strands::query()
            ->where('status','=','active')
            ->get();

        return view(
            'pages.classes.form',
            compact(
                'schoolYears',
                'gradeLevels',
                'strands'
            )
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'GradeLevelID' => [
                'required',
                'exists:grade_levels,id'
            ],
            'StrandID' => [
                'nullable',
                'exists:strands,id'
            ],
            'AdviserID' => [
                'nullable',
                'exists:employees,id'
            ],
            'SectionName' => [
                'required',
                'string',
                'max:100'
            ],
            'Capacity' => [
                'nullable',
                'integer',
                'min:1'
            ],
        ]);

        $Semester = session('Semester');
        $AYFrom = session('AYFrom');
        $AYTo = session('AYTo');

        $exists = Classes::query()
            ->where('Semester', $Semester)
            ->where('AYFrom', $AYFrom)
            ->where('AYTo', $AYTo)
            ->where('SectionName', $validated['SectionName'])
            ->where('GradeLevelID', $validated['GradeLevelID'])
            ->where('StrandID', $validated['StrandID'])
            ->exists();

        if ($exists) {

            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'SectionName' =>
                        'Classes already exists.'
                ]);
        }

        $i = new Classes();
        $i->Semester = $Semester;
        $i->AYFrom = $AYFrom;
        $i->AYTo = $AYTo;
        $i->GradeLevelID =
            $validated['GradeLevelID'];

        $i->StrandID =
            $validated['StrandID'] ?? null;

        $i->AdviserID =
            $validated['AdviserID'] ?? null;

        $i->SectionName =
            $validated['SectionName'];

        $i->Capacity =
            $validated['Capacity'] ?? 50;

        $this->setCommonFields($i);

        $i->save();

        return redirect()
            ->route('classes.index')
            ->with(
                'success',
                'Classes created successfully.'
            );
    }

    public function edit($id)
    {
        $section = Classes::findOrFail(
            decrypt($id)
        );

        $schoolYears = SchoolYear::query()
            ->where('status','=','active')
            ->get();

        $gradeLevels = GradeLevel::query()
            ->where('status','=','active')
            ->get();

        $strands = Strands::query()
            ->where('status','=','active')
            ->get();

        return view(
            'pages.classes.form',
            compact(
                'section',
                'schoolYears',
                'gradeLevels',
                'strands'
            )
        );
    }

    public function update(
        Request $request,
                $id
    ) {

        $section = Classes::findOrFail(
            decrypt($id)
        );

        $validated = $request->validate([
            'SchoolYearID' => [
                'required',
                'exists:school_years,id'
            ],

            'GradeLevelID' => [
                'required',
                'exists:grade_levels,id'
            ],

            'StrandID' => [
                'nullable',
                'exists:strands,id'
            ],

            'AdviserID' => [
                'nullable',
                'exists:employees,id'
            ],

            'SectionName' => [
                'required',
                'string',
                'max:100'
            ],

            'Capacity' => [
                'nullable',
                'integer',
                'min:1'
            ],

            'Room' => [
                'nullable',
                'string',
                'max:100'
            ],
        ]);

        $exists = Classes::query()

            ->where(
                'SchoolYearID',
                $validated['SchoolYearID']
            )

            ->where(
                'GradeLevelID',
                $validated['GradeLevelID']
            )

            ->where(
                'SectionName',
                $validated['SectionName']
            )

            ->where('id','!=',$section->id)

            ->exists();

        if ($exists) {

            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'SectionName' =>
                        'Classes already exists.'
                ]);
        }

        $section->SchoolYearID =
            $validated['SchoolYearID'];

        $section->GradeLevelID =
            $validated['GradeLevelID'];

        $section->StrandID =
            $validated['StrandID'] ?? null;

        $section->AdviserID =
            $validated['AdviserID'] ?? null;

        $section->SectionName =
            $validated['SectionName'];

        $section->Capacity =
            $validated['Capacity'] ?? 50;

        $section->Room =
            $validated['Room'] ?? null;

        $section->IsActive =
            $request->boolean('IsActive');

        $this->setCommonFields(
            $section,
            false
        );

        $section->save();

        return redirect()
            ->route('classes.index')
            ->with(
                'success',
                'Classes updated successfully.'
            );
    }

    public function destroy($id)
    {
        $section = Classes::findOrFail(
            decrypt($id)
        );

        $section->delete();

        return redirect()
            ->route('classes.index')
            ->with(
                'success',
                'Classes deleted successfully.'
            );
    }
}
