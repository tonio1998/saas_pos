<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SchoolController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        abort_unless(
            auth()->check() &&
            auth()->user()->hasRole('SA'),
            403
        );

        return view('pages.schools.index');
    }

    public function show($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $previousSchoolId = session('school_id');
        if ($previousSchoolId) {
            Cache::forget(
                'school_settings_' . $previousSchoolId,
            );
        }

        Cache::forget(
            'school_settings_' . $previousSchoolId,
        );

        $school = School::findOrFail($id);

        session([
            'school_id' => $school->id,
            'school_name' => $school->SchoolName,
        ]);

        return redirect()
            ->route('dashboard.index')
            ->with(
                'success',
                'School context activated successfully.'
            );
    }

    public function closeContext()
    {
        $schoolId = session('school_id');

        if ($schoolId) {

            Cache::forget(
                'school_settings_' . $schoolId,
            );
        }

        session()->forget([

            'school_id',
            'school_name',

        ]);

        return redirect()
            ->route('schools.index')
            ->with(
                'success',
                'School context closed successfully.'
            );
    }

    public function create()
    {
        return view('pages.schools.create');
    }

    public function edit($id)
    {
        try {

            $id = decrypt($id);

        } catch (\Exception $e) {

            abort(404);

        }

        $school = School::findOrFail($id);

        return view(
            'pages.schools.create',
            compact('school')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate(

            [

                'SystemTitle' => [
                    'required',
                    'string',
                    'max:150'
                ],

                'SchoolName' => [
                    'required',
                    'string',
                    'max:255'
                ],

                'SchoolCode' => [
                    'nullable',
                    'string',
                    'max:100',
                    'unique:school,SchoolCode'
                ],

                'alias_name' => [
                    'nullable',
                    'string',
                    'max:50'
                ],

                'EducationLevel' => [
                    'nullable',
                    'in:JHS,SHS,INTEGRATED'
                ],

                'Region' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'Division' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'Address' => [
                    'nullable',
                    'string'
                ],

                'ContactNumber' => [
                    'nullable',
                    'regex:/^\+639\d{9}$/'
                ],

                'EmailAddress' => [
                    'nullable',
                    'max:150'
                ],

                'ThemeColor' => [
                    'nullable',
                    'string',
                    'max:20'
                ],

                'Logo' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5000'
                ],

                'OfficialTimeIn' => [
                    'nullable'
                ],

                'OfficialTimeOut' => [
                    'nullable'
                ],

                'LateGraceMinutes' => [
                    'nullable',
                    'integer',
                    'min:0'
                ],

                'sms_provider' => [
                    'nullable',
                    'string',
                    'max:20'
                ],

                'status' => [
                    'required',
                    'in:active,inactive,locked'
                ],

            ],

            [

                'SystemTitle.required' => 'System title is required.',

                'SchoolName.required' => 'School name is required.',

                'SchoolCode.unique' => 'School code already exists.',

                'code.required' => 'Tenant code is required.',

                'code.unique' => 'Tenant code already exists.',

                'ContactNumber.regex' => 'Contact number is invalid.',

                'EmailAddress.email' => 'Email address is invalid.',

                'Logo.image' => 'Logo must be an image.',

                'Logo.mimes' => 'Logo must be JPG, JPEG, PNG, or WEBP.',

                'Logo.max' => 'Logo must not exceed 5MB.',

            ]

        );

        DB::beginTransaction();

        try {

            if ($request->hasFile('Logo')) {

                $path = $request->file('Logo')
                    ->store('schools', 'public');

                $data['Logo'] = $path;
            }

            $data['SchoolCode'] = strtoupper(
                trim($data['SchoolCode'] ?? '')
            );

            $school = new School();

            $school->fill($data);

            $this->setCommonFields($school);

            $school->save();

            DB::commit();

            return redirect()
                ->route('schools.index')
                ->with([
                    'success' => 'School created successfully.'
                ]);

        } catch (\Exception $e) {

            DB::rollBack();

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

        $school = School::findOrFail($id);

        $data = $request->validate(

            [

                'SystemTitle' => [
                    'required',
                    'string',
                    'max:150'
                ],

                'SchoolName' => [
                    'required',
                    'string',
                    'max:255'
                ],

                'SchoolCode' => [
                    'nullable',
                    'string',
                    'max:100',
                    'unique:school,SchoolCode,' . $school->id
                ],

                'alias_name' => [
                    'nullable',
                    'string',
                    'max:50'
                ],

                'EducationLevel' => [
                    'nullable',
                    'in:JHS,SHS,INTEGRATED'
                ],

                'Region' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'Division' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'Address' => [
                    'nullable',
                    'string'
                ],

                'ContactNumber' => [
                    'nullable',
                    'regex:/^\+639\d{9}$/'
                ],

                'EmailAddress' => [
                    'nullable',
                    'max:150'
                ],

                'ThemeColor' => [
                    'nullable',
                    'string',
                    'max:20'
                ],

                'Logo' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5000'
                ],

                'OfficialTimeIn' => [
                    'nullable'
                ],

                'OfficialTimeOut' => [
                    'nullable'
                ],

                'LateGraceMinutes' => [
                    'nullable',
                    'integer',
                    'min:0'
                ],

                'sms_provider' => [
                    'nullable',
                    'string',
                    'max:20'
                ],

                'status' => [
                    'required',
                    'in:active,inactive,locked'
                ],

            ]

        );

        DB::beginTransaction();

        try {

            if ($request->hasFile('Logo')) {

                if (
                    $school->Logo &&
                    Storage::disk('public')->exists($school->Logo)
                ) {

                    Storage::disk('public')
                        ->delete($school->Logo);

                }

                $path = $request->file('Logo')
                    ->store('schools', 'public');

                $data['Logo'] = $path;
            }

            $data['SchoolCode'] = strtoupper(
                trim($data['SchoolCode'] ?? '')
            );

            $school->update($data);

            Cache::forget(
                'school_settings_' . $school->id
            );

            DB::commit();

            return redirect()
                ->route('schools.index')
                ->with([
                    'success' => 'School updated successfully.'
                ]);

        } catch (\Exception $e) {

            DB::rollBack();
//            dd($e);

            return back()
                ->withErrors([
                    'general' => $e->getMessage()
                ])
                ->withInput();

        }
    }

    public function ajaxData(Request $request)
    {
        $query = School::query()
            ->withCount('users');

        return datatables()
            ->eloquent($query)

            ->addColumn('actions', function ($school) {

                $editUrl = route(
                    'schools.edit',
                    encrypt($school->id)
                );

                $manageUrl = route(
                    'schools.show',
                    [encrypt($school->id)]
                );

                $modalId = 'schoolActionModal' . $school->id;

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
                                    School Actions
                                </h5>

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                ></button>

                            </div>

                            <div class="modal-body p-2">

                                <div class="d-grid gap-2">

                                    <a
                                        href="' . $editUrl . '"
                                        class="btn btn-light text-start"
                                    >
                                        <i class="bi bi-pencil me-2 text-primary"></i>
                                        Edit School
                                    </a>

                                    <a
                                        href="' . $manageUrl . '"
                                        class="btn btn-light text-start"
                                    >
                                        <i class="bi bi-eye me-2 text-success"></i>
                                        Manage School
                                    </a>

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
            ->addColumn('logo', function ($school) {
                $src = $school->Logo
                    ? asset('storage/' . $school->Logo)
                    : asset('images/default-school.png');

                return '
                <img
                    src="' . $src . '"
                    onerror="this.src=\'' . asset('images/default-school.png') . '\';"
                    style="
                        width:48px;
                        height:48px;
                        border-radius:14px;
                        object-fit:cover;
                        border:1px solid #e5e7eb;
                    "
                >
            ';
            })

            ->addColumn('school', function ($school) {
                return '
                <div class="fw-semibold text-uppercase">
                    ' . e($school->SchoolName) . '
                </div>

                <div class="small text-muted">
                    ' . e($school->EmailAddress) . '
                </div>
            ';
            })

            ->addColumn('code', function ($school) {
                return '
                <div class="fw-medium">
                    ' . e($school->SchoolCode ?? '-') . '
                </div>

                <div class="small text-muted">
                    ' . e($school->code ?? '-') . '
                </div>
            ';
            })

            ->addColumn('education_level', function ($school) {

                return '
                <span class="badge bg-primary-subtle text-primary">
                    ' . e($school->EducationLevel) . '
                </span>
            ';
            })

            ->addColumn('theme', function ($school) {

                return '
                <div class="d-flex align-items-center gap-2">

                    <div
                        style="
                            width:18px;
                            height:18px;
                            border-radius:100px;
                            background:' . $school->ThemeColor . ';
                            border:1px solid #ddd;
                        "
                    ></div>

                    <span>
                        ' . e($school->ThemeColor) . '
                    </span>

                </div>
            ';
            })

            ->addColumn('sms', function ($school) {

                return '
                <div class="small">

                    <div>
                        Sent:
                        <strong>
                            ' . number_format($school->total_sent ?? 0) . '
                        </strong>
                    </div>

                    <div>
                        Failed:
                        <strong class="text-danger">
                            ' . number_format($school->sms_failed_count ?? 0) . '
                        </strong>
                    </div>

                </div>
            ';
            })

            ->addColumn('status', function ($school) {

                $status = strtolower($school->status);

                return match ($status) {

                    'active' => '
                    <span class="badge bg-success-subtle text-success">
                        Active
                    </span>
                ',

                    'inactive' => '
                    <span class="badge bg-secondary-subtle text-secondary">
                        Inactive
                    </span>
                ',

                    'locked' => '
                    <span class="badge bg-danger-subtle text-danger">
                        Locked
                    </span>
                ',

                    default => '
                    <span class="badge bg-warning-subtle text-warning">
                        Unknown
                    </span>
                ',
                };
            })

            ->editColumn('users_count', function ($school) {

                return '
                <span class="fw-semibold">
                    ' . number_format($school->users_count) . '
                </span>
            ';
            })

            ->editColumn('created_at', function ($school) {

                return optional($school->created_at)
                    ?->format('M d, Y h:i A');
            })

            ->rawColumns([
                'actions',
                'logo',
                'school',
                'code',
                'education_level',
                'theme',
                'sms',
                'users_count',
                'status',
            ])

            ->make(true);
    }
}
