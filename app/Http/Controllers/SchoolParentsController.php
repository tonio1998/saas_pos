<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use App\Models\User;
use App\Services\User\UserAccountService;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SchoolParentsController extends Controller
{
    use TCommonFunctions;
    protected UserAccountService $userAccountService;

    public function __construct(
        UserAccountService $userAccountService
    ) {

        $this->userAccountService = $userAccountService;
    }
    public function index()
    {
        return view('pages.store.parents.index');
    }

    public function edit($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $parent = Parents::findOrFail($id);

        return view('pages.store.parents.create', compact('parent'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(

            [
                'FirstName' => ['required','string','max:255'],
                'MiddleName' => ['nullable','string','max:255'],
                'LastName' => ['required','string','max:255'],
                'Suffix' => ['nullable','string','max:255'],
                'PhoneNumber' => ['required','regex:/^\+639\d{9}$/'],
                'Address' => ['required','string','max:255'],
                'UserID' => ['nullable','integer']
            ],

            [
                'FirstName.required' => 'First Name is required.',
                'LastName.required' => 'Last Name is required.',
                'PhoneNumber.required' => 'Phone Number is required.',
                'PhoneNumber.regex' => 'Phone Number is not valid.',
                'Address.required' => 'Address is required.',
            ]

        );

        DB::beginTransaction();

        try {

            $parent = new Parents();

            $parent->FirstName = $data['FirstName'];
            $parent->MiddleName = $data['MiddleName'] ?? null;
            $parent->LastName = $data['LastName'];
            $parent->Suffix = $data['Suffix'] ?? null;
            $parent->PhoneNumber = $data['PhoneNumber'];
            $parent->Address = $data['Address'];

            $this->setCommonFields($parent);

            $parent->save();

            $schoolSettings = cache('school_settings_' . session('school_id'));

            $schoolCode = $schoolSettings?->id;

            $schoolEmail = $schoolSettings?->EmailAddress
                ?? env('SCHOOL_EMAIL', '@school.local');

            $generatedEmail = $this->userAccountService
                ->generateUsername(
                    $request->FirstName,
                    $request->LastName,
                    $schoolEmail
                );

            $generatedPassword = strtoupper(Str::random(6));

            $user = new User();
            $user->conn_id = $parent->id;
            $user->school_id = $schoolCode;
            $user->name = strtoupper(trim(
                $request->FirstName . ' ' . $request->LastName
            ));
            $user->email = $generatedEmail;
            $user->password = Hash::make($generatedPassword);
            $user->qr_code = generateQrCode();

            $this->setCommonFields($user);

            $user->save();

            $user->assignRole('parents');
            $parent->UserID = $user->id;
            $parent->save();

            DB::commit();

            return redirect()
                ->route('parents.index')
                ->with('success','Parent created successfully');

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

        $data = $request->validate(

            [
                'FirstName' => ['required','string','max:255'],
                'MiddleName' => ['nullable','string','max:255'],
                'LastName' => ['required','string','max:255'],
                'Suffix' => ['nullable','string','max:255'],
                'PhoneNumber' => ['required','regex:/^\+639\d{9}$/'],
                'Address' => ['required','string','max:255'],
                'UserID' => ['nullable','integer']
            ],

            [
                'FirstName.required' => 'First Name is required.',
                'LastName.required' => 'Last Name is required.',
                'PhoneNumber.required' => 'Phone Number is required.',
                'PhoneNumber.regex' => 'Phone Number is not valid.',
                'Address.required' => 'Address is required.',
            ]

        );

        DB::beginTransaction();

        try {

            $parent = Parents::findOrFail($id);

            $parent->update($data);

            if (!empty($data['UserID'])) {

                $user = User::find($data['UserID']);

                if ($user) {

                    $roleName = 'parents';

                    if (!$user->hasRole($roleName)) {

                        $user->assignRole($roleName);

                    }

                    if ($parent->UserID != $user->id) {

                        $parent->UserID = $user->id;

                        $parent->save();

                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('parents.index')
                ->with('success','Parent updated successfully');

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
        return view('pages.store.parents.create');
    }

    public function ajaxData(Request $request)
    {
        $query = Parents::with(['createdBy', 'parentUser']);

        return datatables()
            ->eloquent($query)
            ->addColumn('actions', function ($parent) {

                $editUrl = route(
                    'parents.edit',
                    encrypt($parent->id)
                );

                $passwordUrl = route(
                    'users.password',
                    [
                        'parents',
                        $parent->id,
                        $parent->UserID ?? 0
                    ]
                );

                $passwordType = $parent->parentUser
                    ? 'regenerate'
                    : 'generate';

                $passwordLabel = $parent->parentUser
                    ? 'Update Password'
                    : 'Generate Password';

                $modalId = 'parentActionModal' . $parent->id;

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
                            Edit Parent
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
                                        Parent Actions
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
            ->addColumn('name', function ($parent) {
                $a = "<div class='fw-bold'>" . $parent->FirstName . ' ' . $parent->LastName . "</div>";
                return $a;
            })
            ->addColumn('phone_number', function ($parent) {
                return $parent->PhoneNumber;
            })
            ->addColumn('address', function ($parent) {
                return $parent->Address;
            })
            ->addColumn('students', function ($parent) {
                $a = '';
                foreach ($parent->students as $student) {
                    $a .= "<div class='text-muted'>" . $student->FirstName . ' ' . $student->LastName . "</div>";
                }
                return $a;
            })
            ->editColumn('created_at', function ($parent) {
                return $parent->created_at->format('M d, Y h:i A');
            })
            ->addColumn('createdBy', function ($parent) {
                return $parent->createdBy?->name;
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('FirstName', 'like', "%{$keyword}%")
                        ->orWhere('LastName', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['actions','students', 'name'])
            ->make(true);
    }
    public function parents_search(Request $request)
    {
        $search = $request->search;
        $parents = Parents::query()
            ->when($search,function($q) use ($search){
                $q->where('FirstName', 'like', "%{$search}%")
                    ->orWhere('LastName', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();
        return $parents->map(function($parent){
            return [
                'id'=>$parent->id,
                'text'=>$parent->FirstName.' '.$parent->LastName
            ];
        });
    }
}
