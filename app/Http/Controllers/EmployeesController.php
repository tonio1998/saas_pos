<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use App\Models\Employees;
use App\Models\User;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeesController extends Controller
{
    use TCommonFunctions;

    public function employees_search(Request $request)
    {
        $search = $request->search;
        $employees = Employees::query()
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
        return view('pages.employees.index');
    }

    public function edit($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $teacher = Employees::findOrFail($id);

        return view('pages.employees.create', compact('teacher'));
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

            $teacher = Employees::findOrFail($id);

            $data = $request->validate([
                'FirstName' => ['required','string','max:255'],
                'LastName' => ['required','string','max:255'],
                'Suffix' => ['nullable','string','max:255'],
                'PhoneNumber' => ['required','regex:/^\+639\d{9}$/'],
                'Address' => ['required','string','max:255'],
                'UserID' => ['nullable','integer']
            ]);

            $teacher->update($data);

            if (!empty($data['UserID'])) {

                $user = User::find($data['UserID']);

                if ($user) {

                    $roleName = 'employees';

                    if (!$user->hasRole($roleName)) {
                        $user->assignRole($roleName);
                    }

                    if ($teacher->UserID != $user->id) {
                        $teacher->UserID = $user->id;
                        $teacher->save();
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('employees.index')
                ->with('success','Teacher updated successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function create()
    {
        return view('pages.employees.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $data = $request->validate(
                [
                    'FirstName' => ['required','string','max:255'],
                    'MiddleName' => ['nullable','string','max:255'],
                    'LastName' => ['required','string','max:255'],
                    'Suffix' => ['nullable','string','max:255'],
                    'PhoneNumber' => ['required','regex:/^\+639\d{9}$/'],
                    'Address' => ['required','string','max:255'],
                    'UserID' => ['nullable','integer']
                ]
            );

            $parent = new Employees();
            $parent->fill($data);
            $this->setCommonFields($parent);
            $parent->save();

            if (!empty($data['UserID'])) {

                $user = User::find($data['UserID']);

                if ($user) {

                    $roleName = 'employees';

                    if (!$user->hasRole($roleName)) {
                        $user->assignRole($roleName);
                    }

                    $parent->UserID = $user->id;
                    $parent->save();
                }
            }

            DB::commit();

            return redirect()
                ->route('employees.index')
                ->with('success','Teacher created successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function ajaxData(Request $request)
    {
        $query = Employees::with(['createdBy', 'teacherUser']);

        return datatables()
            ->eloquent($query)
            ->addColumn('actions', function ($teacher) {

                $editUrl = route(
                    'employees.edit',
                    encrypt($teacher->id)
                );

                $passwordUrl = route(
                    'users.password',
                    [
                        'employees',
                        $teacher->id,
                        $teacher->UserID ?? 0
                    ]
                );

                $passwordType = $teacher->teacherUser
                    ? 'regenerate'
                    : 'generate';

                $passwordLabel = $teacher->teacherUser
                    ? 'Update Password'
                    : 'Generate Password';

                $modalId = 'teacherActionModal' . $teacher->id;

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

                            $actions = '';

                            if(auth()->user()->can('view users')) {

                                $actions .= '
                        <a
                            href="' . $editUrl . '"
                            class="btn btn-light text-start"
                        >
                            <i class="bi bi-pencil me-2 text-primary"></i>
                            Edit Employee
                        </a>
                    ';
                            }

                            $actions .= '
                    <button
                        type="button"
                        class="btn btn-light text-start btn-password"
                        data-url="' . $passwordUrl . '"
                        data-type="' . $passwordType . '"
                    >
                        <i class="bi bi-key me-2 text-danger"></i>
                        ' . $passwordLabel . '
                    </button>
                ';

                            if($actions == '') {
                                return '';
                            }

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
                                        Employee Actions
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
                                        ' . $actions . '
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
            ->addColumn('name', function ($teacher) {
                $a = "<div class='fw-bold'>" . $teacher->FirstName . ' ' . $teacher->LastName . "</div>";
                return $a;
            })
            ->addColumn('phone_number', function ($teacher) {
                return $teacher->PhoneNumber;
            })
            ->addColumn('address', function ($teacher) {
                return $teacher->Address;
            })
            ->editColumn('created_at', function ($teacher) {
                return $teacher->created_at->format('M d, Y h:i A');
            })
            ->addColumn('createdBy', function ($teacher) {
                return $teacher->createdBy?->name;
            })
            ->rawColumns(['actions','students', 'name'])
            ->make(true);
    }
}
