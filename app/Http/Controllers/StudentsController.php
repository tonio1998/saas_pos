<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\Students;
use App\Models\User;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentsController extends Controller
{
    use TCommonFunctions;

    public function students_search(Request $request)
    {
        $search = $request->search;
        $employees = Students::query()
            ->when($search,function($q) use ($search){
                $q->where('FirstName', 'like', "%{$search}%")
                    ->orWhere('LastName', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();
        return $employees->map(function($employee){
            return [
                'id'=>$employee->id,
                'text'=>$employee->FirstName.' '.$employee->LastName
            ];
        });
    }
    public function index()
    {
        return view('pages.students.index');
    }

    public function edit($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $student = Students::findOrFail($id);
        return view('pages.students.create', compact('student'));
    }

    public function update(Request $request, $id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        DB::beginTransaction();

        try {

            $student = Students::findOrFail($id);

            $data = $request->validate(
                [
                    'LRN' => ['required','string','max:12'],
                    'FirstName' => ['required','string','max:255'],
                    'MiddleName' => ['nullable','string','max:255'],
                    'LastName' => ['required','string','max:255'],
                    'Suffix' => ['nullable','string','max:255'],
                    'PhoneNumber' => ['required','regex:/^\+639\d{9}$/'],
                    'GuardianID' => ['nullable','integer'],
                    'UserID' => ['nullable','integer'],
                    'filepath' => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
                    'Section' => ['nullable','string','max:255'],
                    'YearLevel' => ['required','string','max:255'],
                    'Strand' => ['required','string','max:255']
                ]
            );

            if ($request->hasFile('filepath')) {
                $path = $request->file('filepath')->store('students','public');
                $data['filepath'] = $path;
            }

            $student->update($data);

            if (!empty($data['UserID'])) {

                $user = User::find($data['UserID']);

                if ($user) {

                    $roleName = 'students';

                    if (!$user->hasRole($roleName)) {
                        $user->assignRole($roleName);
                    }

                    if ($student->UserID != $user->id) {
                        $student->UserID = $user->id;
                        $student->save();
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('students.index')
                ->with('success','Student updated successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function create()
    {
        return view('pages.students.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'LRN' => ['required','string','max:12'],
                'FirstName' => ['required','string','max:255'],
                'MiddleName' => ['nullable','string','max:255'],
                'LastName' => ['required','string','max:255'],
                'Suffix' => ['nullable','string','max:255'],
                'PhoneNumber' => ['required','regex:/^\+639\d{9}$/'],
                'GuardianID' => ['nullable','integer'],
                'UserID' => ['nullable','integer'],
                'filepath' => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
                'Section' => ['nullable','string','max:255'],
                'YearLevel' => ['required','string','max:255'],
                'Strand' => ['required','string','max:255']
            ]
        );

        DB::beginTransaction();

//        dd($data);

        try {

            if ($request->hasFile('filepath')) {
                $path = $request->file('filepath')->store('students','public');
                $data['filepath'] = $path;
            }

            $student = new Students();
            $student->fill($data);
            $this->setCommonFields($student);
            $student->save();

            if (!empty($data['UserID'])) {
                $user = User::find($data['UserID']);
                if ($user) {
                    $roleName = 'students';
                    if (!$user->hasRole($roleName)) {
                        $user->assignRole($roleName);
                    }
                    $student->UserID = $user->id;
                    $student->save();
                }
            }

            DB::commit();

            return redirect()
                ->route('students.index')
                ->with('success','Student created successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function ajaxData(Request $request)
    {
        $query = Students::with([
            'createdBy',
            'guardian',
            'studentUser'
        ]);

        return datatables()
            ->eloquent($query)

            ->addColumn('actions', function ($student) {

                $editUrl = route(
                    'students.edit',
                    encrypt($student->id)
                );

                $showIdUrl = route(
                    'users.printID',
                    encrypt($student->UserID ?? 0)
                );

                $changePhotoUrl = route(
                    'users.change-photo',
                    [encrypt($student->UserID ?? 0), 'q=students']
                );

                $passwordUrl = route(
                    'users.password',
                    ['students', $student->id, $student->UserID ?? 0]
                );

                $passwordType = $student->studentUser
                    ? 'regenerate'
                    : 'generate';

                $passwordLabel = $student->studentUser
                    ? 'Update Password'
                    : 'Generate Password';

                $modalId = 'studentActionModal' . $student->id;

                $button = '
                    <button
                        class="btn btn-soft-primary btn-sm"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#' . $modalId . '"
                    >
                        <i class="bi bi-gear"></i>
                        Actions
                    </button>
                ';

                            $modal = '
                    <div
                        class="modal fade"
                        id="' . $modalId . '"
                        tabindex="-1"
                        aria-hidden="true"
                    >
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content border-0 shadow">

                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Student Actions
                                    </h5>

                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                        aria-label="Close"
                                    ></button>
                                </div>

                                <div class="modal-body p-2">

                                    <div class="d-grid gap-2">

                                        <a
                                            href="' . $editUrl . '"
                                            class="btn btn-light text-start"
                                        >
                                            <i class="bi bi-pencil me-2 text-primary"></i>
                                            Edit Student
                                        </a>

                                        <a
                                            href="' . $showIdUrl . '"
                                            class="btn btn-light text-start"
                                        >
                                            <i class="bi bi-eye me-2 text-success"></i>
                                            Show ID
                                        </a>

                                        <a
                                            href="' . $changePhotoUrl . '"
                                            class="btn btn-light text-start"
                                        >
                                            <i class="bi bi-photo me-2 text-warning"></i>
                                            Change Photo
                                        </a>

                                        <button
                                            type="button"
                                            class="btn btn-light text-start btn-password"
                                            data-url="' . $passwordUrl . '"
                                            data-type="' . $passwordType . '"
                                        >
                                            <i class="bi bi-key me-2 text-danger"></i>
                                            ' . $passwordLabel . '
                                        </button>

                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                ';

                            return '
                    <div class="text-center">
                        ' . $button . '
                        ' . $modal . '
                    </div>
                ';
            })

            ->addColumn('name', function ($student) {

                $name = '
                <div class="fw-semibold">
                    ' . e($student->FirstName . ' ' . $student->LastName) . '
                </div>
            ';

                $guardian = '';

                if ($student->guardian) {

                    $guardian = '
                    <div class="text-muted small">
                        Parent:
                        ' . e(
                            $student->guardian->FirstName . ' ' .
                            $student->guardian->LastName
                        ) . '
                    </div>
                ';
                }

                return $name . $guardian;
            })

            ->addColumn('image', function ($student) {

                $src = $student->studentUser?->filepath
                    ? asset('storage/' . $student->studentUser->filepath)
                    : asset('images/avatar.png');

                return '
                <img
                    src="' . $src . '"
                    onerror="this.src=\'' . asset('images/avatar.png') . '\'"
                    style="
                        width:40px;
                        height:40px;
                        border-radius:100px;
                        object-fit:cover;
                    "
                >
            ';
            })

            ->addColumn('lrn', function ($student) {
                return e($student->LRN);
            })

            ->addColumn('phone_number', function ($student) {
                return e($student->PhoneNumber);
            })
            ->addColumn('year', function ($student) {
                return e($student->YearLevel);
            })

            ->editColumn('created_at', function ($student) {
                return optional($student->created_at)
                    ?->format('M d, Y h:i A');
            })

            ->addColumn('createdBy', function ($student) {
                return e($student->createdBy?->name ?? '');
            })

            ->rawColumns([
                'actions',
                'image',
                'name'
            ])

            ->make(true);
    }
}
