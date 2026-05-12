<?php

namespace App\Http\Controllers;

use App\Models\Strand;
use App\Models\Strands;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Enrollment;
use App\Models\GradeLevel;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class EnrollmentController extends Controller
{
    use TCommonFunctions;
    public function index()
    {
        return view('pages.enrollments.index');
    }

    public function ajaxData(Request $request)
    {
        $Semester = session('Semester');
        $AYFrom = session('AYFrom');
        $AYTo = session('AYTo');

        $query = Enrollment::query()

            ->with([
                'student',
                'gradeLevel',
                'strand',
                'section'
            ])
            ->where('Semester', $Semester)
            ->where('AYFrom', $AYFrom)
            ->where('AYTo', $AYTo)
            ->latest();

        return DataTables::of($query)

            ->addColumn('student', function ($row) {

                return $row->student?->FullName;
            })

            ->addColumn('lrn', function ($row) {

                return $row->student?->LRN;
            })

            ->addColumn('grade_level', function ($row) {

                return $row->gradeLevel?->GradeLevel;
            })

            ->addColumn('strand', function ($row) {

                return $row->strand?->StrandCode
                    ?? '-';
            })

            ->addColumn('section', function ($row) {

                return $row->section?->SectionName;
            })

            ->addColumn('semester', function ($row) {

                return match($row->Semester){
                    1 => '1st Semester',
                    2 => '2nd Semester',
                    3 => 'Summer',
                    default => '-'
                };
            })

            ->addColumn('academic_year', function ($row) {

                return
                    $row->AYFrom .
                    '-' .
                    $row->AYTo;
            })

            ->addColumn('status', function ($row) {

                return '
                    <span class="badge bg-success">
                        ' . e($row->EnrollmentStatus) . '
                    </span>
                ';
            })

            ->addColumn('actions', function ($row) {

                return '

                    <a
                        href="' . route(
                        'enrollments.edit',
                        encrypt($row->id)
                    ) . '"

                        class="btn btn-sm btn-light"
                    >
                        <i class="bi bi-pencil"></i>
                    </a>

                ';
            })

            ->rawColumns([
                'status',
                'actions'
            ])

            ->make(true);
    }

    public function create()
    {
        $gradeLevels = GradeLevel::query()->get();
        $strands = Strands::query()->get();
        $sections = Classes::query()->get();

        return view(
            'pages.enrollments.form',
            compact(
                'gradeLevels',
                'strands',
                'sections'
            )
        );
    }

    public function store(Request $request)
    {
        $Semester = session('Semester');
        $AYFrom = session('AYFrom');
        $AYTo = session('AYTo');
        $validated = $request->validate([

            'StudentID' => [
                'required',
                'exists:students,id'
            ],

            'SectionID' => [
                'required',
                'exists:classes,id'
            ],

            'EnrollmentStatus' => [
                'required',

                Rule::in([
                    'ENROLLED',
                    'PENDING',
                    'DROPPED',
                    'TRANSFERRED',
                    'COMPLETED'
                ])
            ],

            'EnrollmentDate' => [
                'nullable',
                'date'
            ],

            'Remarks' => [
                'nullable',
                'string'
            ],

        ]);

        $section = Classes::query()

            ->with([
                'gradeLevel',
                'strand'
            ])

            ->findOrFail(
                $validated['SectionID']
            );

        $exists = Enrollment::query()
            ->where('StudentID',$validated['StudentID'])
            ->where('Semester', $Semester)
            ->where('AYFrom', $AYFrom)
            ->where('AYTo', $AYTo)
            ->exists();

        if($exists){

            return back()

                ->withInput()

                ->withErrors([
                    'StudentID' =>
                        'Student already enrolled in this school year.'
                ]);
        }

        $i = new Enrollment();

        $i->StudentID =
            $validated['StudentID'];

        $i->SectionID =
            $section->id;

        $i->Semester =
            $section->Semester;

        $i->AYFrom =
            $section->AYFrom;

        $i->AYTo =
            $section->AYTo;

        $i->GradeLevelID =
            $section->GradeLevelID;

        $i->StrandID =
            $section->StrandID;

        $i->EnrollmentStatus =
            $validated['EnrollmentStatus'];

        $i->EnrollmentDate =
            $validated['EnrollmentDate']
            ?? now();

        $i->Remarks =
            $validated['Remarks'];

        $this->setCommonFields($i);

        $i->save();

        return redirect()

            ->route('enrollments.index')

            ->with(
                'success',
                'Enrollment created successfully.'
            );
    }

    public function update(
        Request $request,
        Enrollment $enrollment
    ) {

        $validated = $request->validate([

            'StudentID' => [
                'required',
                'exists:students,id'
            ],

            'SectionID' => [
                'required',
                'exists:classes,id'
            ],

            'EnrollmentStatus' => [
                'required',

                Rule::in([
                    'ENROLLED',
                    'PENDING',
                    'DROPPED',
                    'TRANSFERRED',
                    'COMPLETED'
                ])
            ],

            'EnrollmentDate' => [
                'nullable',
                'date'
            ],

            'Remarks' => [
                'nullable',
                'string'
            ],

        ]);

        $section = Classes::query()

            ->with([
                'gradeLevel',
                'strand'
            ])

            ->findOrFail(
                $validated['SectionID']
            );

        $exists = Enrollment::query()

            ->where(
                'StudentID',
                $validated['StudentID']
            )

            ->where(
                'SectionID',
                $section->id
            )

            ->where(
                'id',
                '!=',
                $enrollment->id
            )

            ->exists();

        if($exists){

            return back()

                ->withInput()

                ->withErrors([
                    'StudentID' =>
                        'Student already enrolled in this section.'
                ]);
        }

        $enrollment->StudentID =
            $validated['StudentID'];

        $enrollment->SectionID =
            $section->id;

        $enrollment->Semester =
            $section->Semester;

        $enrollment->AYFrom =
            $section->AYFrom;

        $enrollment->AYTo =
            $section->AYTo;

        $enrollment->GradeLevelID =
            $section->GradeLevelID;

        $enrollment->StrandID =
            $section->StrandID;

        $enrollment->EnrollmentStatus =
            $validated['EnrollmentStatus'];

        $enrollment->EnrollmentDate =
            $validated['EnrollmentDate']
            ?? now();

        $enrollment->Remarks =
            $validated['Remarks'];

        $this->updateCommonFields(
            $enrollment
        );

        $enrollment->save();

        return redirect()

            ->route('enrollments.index')

            ->with(
                'success',
                'Enrollment updated successfully.'
            );
    }

    public function edit($id)
    {
        $enrollment = Enrollment::findOrFail(
            decrypt($id)
        );

        $gradeLevels = GradeLevel::query()
            ->where('IsActive',1)
            ->get();

        $strands = Strands::query()->get();

        $sections = Classes::query()
            ->where('IsActive',1)
            ->get();

        return view(
            'pages.enrollments.form',
            compact(
                'enrollment',
                'gradeLevels',
                'strands',
                'sections'
            )
        );
    }
}
