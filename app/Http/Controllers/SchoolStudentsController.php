<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\QrCodes;
use App\Models\Students;
use App\Models\User;
use App\Services\User\UserAccountService;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SchoolStudentsController extends Controller
{
    use TCommonFunctions;
    protected UserAccountService $userAccountService;

    public function __construct(
        UserAccountService $userAccountService
    ) {

        $this->userAccountService = $userAccountService;
    }

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
//        dd(session()->all());
        return view('pages.schools.students.index');
    }

    public function edit($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $student = Students::findOrFail($id);
        return view('pages.schools.students.create', compact('student'));
    }

    private function generateQrCode()
    {
        $prefix = cache('school_settings')?->SchoolCode;
        $schoolSettings = cache('school_settings');
        $schoolCode = $schoolSettings?->id;

        $lastRow = QrCodes::where('prefix', $prefix)
            ->orderByDesc('last_number')
            ->first();

        $newNumber = ($lastRow?->last_number ?? 0) + 1;

        $qrCodeRow = new QrCodes();
        $qrCodeRow->school_id = $schoolCode;
        $qrCodeRow->prefix = $prefix;
        $qrCodeRow->last_number = $newNumber;
        $qrCodeRow->created_by = 0;
        $qrCodeRow->updated_by = 0;
        $qrCodeRow->created_at = now();
        $qrCodeRow->updated_at = now();
        $qrCodeRow->status = 'active';
        $qrCodeRow->archived = 0;
        $qrCodeRow->save();

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $data = $request->validate(

            [
                'LRN' => ['required', 'string', 'max:12', 'min:12'],
                'FirstName' => ['required', 'string', 'max:255'],
                'MiddleName' => ['nullable', 'string', 'max:255'],
                'LastName' => ['required', 'string', 'max:255'],
                'Suffix' => ['nullable', 'string', 'max:255'],
                'PhoneNumber' => ['nullable', 'regex:/^\+639\d{9}$/'],
                'GuardianID' => ['nullable', 'integer'],
                'UserID' => ['nullable', 'integer'],
                'filepath' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
                'Section' => ['nullable', 'string', 'max:255'],
                'YearLevel' => ['required', 'string', 'max:255'],
                'Strand' => ['required', 'string', 'max:255']
            ],

            [
                'LRN.required' => 'LRN is required.',
                'LRN.min' => 'Learner Reference Number should be 12 characters.',
                'FirstName.required' => 'First Name is required.',
                'LastName.required' => 'Last Name is required.',
                'PhoneNumber.required' => 'Phone Number is required.',
                'PhoneNumber.regex' => 'Phone Number is not valid.',
                'YearLevel.required' => 'Year Level is required.',
                'Strand.required' => 'Strand is required.',
                'filepath.image' => 'File must be an image.',
                'filepath.mimes' => 'Image must be JPG, JPEG, or PNG.',
                'filepath.max' => 'Image must not exceed 2MB.',
            ]

        );

        DB::beginTransaction();

        try {

            if ($request->hasFile('filepath')) {

                $path = $request->file('filepath')
                    ->store('students', 'public');

                $data['filepath'] = $path;
            }

            $student = new Students();
            $student->fill($data);

            $this->setCommonFields($student);

            $student->save();

            $schoolSettings = cache('school_settings');

            $schoolEmail = $schoolSettings?->EmailAddress
                ?? env('SCHOOL_EMAIL');

            $schoolCode = $schoolSettings?->id;

            $generatedEmail = $this->userAccountService
                ->generateUsername(
                    $request->FirstName,
                    $request->LastName,
                    $schoolEmail
                );

            $generatedPassword = strtoupper(Str::random(6));

            $user = new User();
            $user->conn_id = $student->id;
            $user->school_id = $schoolCode;
            $user->name = trim(
                $request->FirstName . ' ' . $request->LastName
            );
            $user->email = $generatedEmail;
            $user->password = Hash::make($generatedPassword);
            $user->qr_code = generateQrCode();

            $this->setCommonFields($user);

            $user->save();

            $user->assignRole('students');

            $student->UserID = $user->id;
            $student->save();

            DB::commit();

            return redirect()
                ->route('students.index')
                ->with([
                    'success' => 'Student created successfully.',
                    'generated_username' => $user->email,
                    'generated_password' => $generatedPassword
                ]);

        } catch (\Exception $e) {

            DB::rollBack();
            dd($e->getMessage());
            return back()
                ->withErrors([
                    'general' => $e->getMessage()
                ])
                ->withInput();

        }
    }

    public function update(Request $request, $id)
    {
        try {

            $id = decrypt($id);

        } catch (\Exception $e) {

            abort(404);

        }

        $data = $request->validate(

            [
                'LRN' => ['required','string','max:12', 'min:12'],
                'FirstName' => ['required','string','max:255'],
                'MiddleName' => ['nullable','string','max:255'],
                'LastName' => ['required','string','max:255'],
                'Suffix' => ['nullable','string','max:255'],
                'PhoneNumber' => ['nullable','regex:/^\+639\d{9}$/'],
                'GuardianID' => ['nullable','integer'],
                'UserID' => ['nullable','integer'],
                'filepath' => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
                'Section' => ['nullable','string','max:255'],
                'YearLevel' => ['required','string','max:255'],
                'Strand' => ['required','string','max:255']
            ],

            [
                'LRN.required' => 'LRN is required.',
                'FirstName.required' => 'First Name is required.',
                'LastName.required' => 'Last Name is required.',
                'PhoneNumber.required' => 'Phone Number is required.',
                'PhoneNumber.regex' => 'Phone Number is not valid.',
                'YearLevel.required' => 'Year Level is required.',
                'Strand.required' => 'Strand is required.',
                'filepath.image' => 'File must be an image.',
                'filepath.mimes' => 'Image must be JPG, JPEG, or PNG.',
                'filepath.max' => 'Image must not exceed 2MB.',
            ]

        );

        DB::beginTransaction();

        try {

            $student = Students::findOrFail($id);

            if ($request->hasFile('filepath')) {

                $path = $request->file('filepath')
                    ->store('students','public');

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

            return back()
                ->withErrors([
                    'general' => $e->getMessage()
                ])
                ->withInput();

        }
    }
    public function create()
    {
        return view('pages.schools.students.create');
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
            ->addColumn('photo', function ($student) {
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
            ->filterColumn('name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('FirstName', 'like', "%{$keyword}%")
                        ->orWhere('LastName', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns([
                'actions',
                'image',
                'name',
                'photo'
            ])
            ->make(true);
    }
}
