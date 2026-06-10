<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\NFCCodes;
use App\Models\Parents;
use App\Models\QrCodes;
use App\Models\SchoolUsers;
use App\Models\Students;
use App\Models\User;
use App\Services\User\UserAccountService;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class SchoolUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use TCommonFunctions;

    protected UserAccountService $userAccountService;

    public function __construct(
        UserAccountService $userAccountService
    ) {

        $this->userAccountService = $userAccountService;
    }
    public function index()
    {
        return view('pages.store.users.index');

    }

    public function users_search(Request $request)
    {
        $search = $request->search;
        $users = SchoolUsers::query()
            ->when($search,function($q) use ($search){
                $q->where('name','like',"%{$search}%");
            })
            ->limit(10)
            ->get();
        return $users->map(function($user){
            return [
                'id'=>$user->id,
                'text'=>$user->name
            ];
        });
    }

    public function editRoles(Request $request)
    {
        $user = SchoolUsers::findOrFail(decrypt($request->segment(2)));
        $roles = Role::query()
            ->where('name', '!=', 'SA')
            ->get();
//        dd($roles);
        return view('pages.store.users.roles',compact('user','roles'));
    }

    public function editPermissions(Request $request)
    {
        $user = SchoolUsers::findOrFail(decrypt($request->segment(2)));
        $permissions = Permission::all();
        return view('pages.store.users.permissions',compact('user','permissions'));
    }

    public function updateRoles(Request $request, SchoolUsers $user)
    {
        $roles = $request->roles ?? [];
        $user->syncRoles($roles);

        return redirect()->back()->with('success','Roles updated successfully.');
    }

    public function updatePermissions(Request $request, SchoolUsers $user)
    {
        $permissions = $request->permissions ?? [];
        $user->syncPermissions($permissions);

        return redirect()->back()->with('success','Permissions updated successfully.');
    }

    public function users_data(Request $request)
    {
        $users = SchoolUsers::query()
            ->with([
                'roles:id,name',
            ])
            ->withCount('logs')
            ->where('school_id', '!=', 0)
            ->select([
                'id',
                'school_id',
                'name',
                'email',
                'filepath',
                'nfc_code',
            ]);

        return DataTables::of($users)

            ->editColumn('filepath', function ($user) {

                $avatar = $user->filepath
                    ? asset('storage/' . $user->filepath)
                    : asset('images/avatar.png');

                $fallback = asset('images/avatar.png');

                return '
                <div class="d-flex align-items-center gap-3">

                    <img
                        src="' . $avatar . '"
                        onerror="this.onerror=null;this.src=\'' . $fallback . '\';"
                        class="rounded-circle border shadow-sm"
                        style="
                            width:48px;
                            height:48px;
                            object-fit:cover;
                        "
                    >

                    <div class="min-w-0">

                        <div class="fw-semibold text-dark text-truncate">
                            ' . e($user->name) . '
                        </div>

                        <div class="small text-muted text-truncate">
                            ' . e($user->email) . '
                        </div>

                    </div>

                </div>
            ';
            })
            ->addColumn('role', function ($user) {

                if ($user->roles->isEmpty()) {

                    return '
            <span class="badge bg-secondary-subtle text-secondary border">
                No Role
            </span>
        ';
                }

                return $user->roles
                    ->map(function ($role) {

                        return '
                <span class="badge bg-primary-subtle text-primary border me-1 mb-1">
                    ' . e(ucfirst($role->name)) . '
                </span>
            ';
                    })
                    ->implode('');
            })
            ->filterColumn('name', function ($query, $keyword) {

                $query->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('email', 'LIKE', "%{$keyword}%");
            })

            ->addColumn('logs', function ($user) {

                return '
                <div class="text-center">

                    <div class="fw-bold text-success fs-6">
                        ' . number_format($user->logs_count) . '
                    </div>

                    <div class="small text-muted">
                        Scan Logs
                    </div>

                </div>
            ';
            })

            ->editColumn('nfc_code', function ($user) {

                if (!$user->nfc_code) {

                    return '
                    <div class="text-center">
                        <span class="badge rounded-pill bg-secondary-subtle text-secondary border px-3 py-2">
                            <i class="bi bi-x-circle me-1"></i>
                            No NFC
                        </span>
                    </div>
                ';
                }

                return '
                <div class="text-center">
                    <span class="badge rounded-pill bg-success-subtle text-success border px-3 py-2">
                        <i class="bi bi-credit-card-2-front me-1"></i>
                        ' . e($user->nfc_code) . '
                    </span>
                </div>
            ';
            })

            ->addColumn('actions', function ($user) {

                $encryptedId = encrypt($user->id);

                $permissionsUrl = route(
                    'school-users.permissions',
                    $encryptedId
                );

                $rolesUrl = route(
                    'school-users.roles',
                    $encryptedId
                );

                $nfcUrl = route(
                    'school-users.nfc',
                    $encryptedId
                );

                $assignText = $user->nfc_code
                    ? 'Update NFC'
                    : 'Assign NFC';

                return '
                <div class="dropdown text-center">

                    <button
                        class="btn btn-light btn-sm"
                        type="button"
                        data-bs-toggle="dropdown"
                    >
                        <i class="bi bi-three-dots"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2">

                        <li>
                            <a
                                href="' . $permissionsUrl . '"
                                class="dropdown-item rounded-3 py-2"
                            >
                                <i class="bi bi-shield-lock me-2 text-primary"></i>
                                Permissions
                            </a>
                        </li>

                        <li>
                            <a
                                href="' . $rolesUrl . '"
                                class="dropdown-item rounded-3 py-2"
                            >
                                <i class="bi bi-person-badge me-2 text-warning"></i>
                                Roles
                            </a>
                        </li>

                        <li>
                            <a
                                href="' . $nfcUrl . '"
                                class="dropdown-item rounded-3 py-2"
                            >
                                <i class="bi bi-credit-card-2-front me-2 text-success"></i>
                                ' . $assignText . '
                            </a>
                        </li>

                    </ul>

                </div>
            ';
            })

            ->rawColumns([
                'filepath',
                'logs',
                'nfc_code',
                'actions',
                'role',
            ])

            ->make(true);
    }

    public function create()
    {

        return view('pages.store.users.create');

    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6']
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()
            ->route('users.index')
            ->with('success','User created successfully');

    }

    public function edit(User $user)
    {

        return view('pages.store.users.edit', compact('user'));

    }

    public function update(Request $request, User $user)
    {

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email,' . $user->id],
            'password' => ['nullable','string','min:6']
        ]);

        if(!empty($data['password'])){
            $data['password'] = Hash::make($data['password']);
        }else{
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('success','User updated successfully');

    }

    public function destroy(User $user)
    {

        $user->delete();

        return redirect()->route('users.index')->with('success','User deleted successfully');

    }

    private function generateQrCode()
    {
        $prefix = cache('school_settings')?->SchoolCode;

        $lastRow = QrCodes::where('prefix', $prefix)
            ->orderByDesc('last_number')
            ->first();

        $newNumber = ($lastRow?->last_number ?? 0) + 1;

        $qrCodeRow = new QrCodes();
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

    public function generatePassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $UserTypeID = $request->segment(4);
        $user_type = $request->segment(3);
        $UserID = $request->segment(5);

        $newPassword = strtoupper(Str::random(6));

        DB::beginTransaction();

        try {
            if($user_type === 'students'){
                $UserT = Students::findOrFail($UserTypeID);
            }elseif($user_type === 'employees'){
                $UserT = Employees::findOrFail($UserTypeID);
            }elseif($user_type === 'parents'){
                $UserT = Parents::findOrFail($UserTypeID);
            }else{
                return response()->json(['message'=>'Invalid user type'],400);
            }

            $user = User::find($UserID);

            if(!$user){
                $schoolSettings = cache('school_settings');
                $schoolCode = $schoolSettings?->id;
                $schoolEmail = $schoolSettings?->EmailAddress
                    ?? env('SCHOOL_EMAIL', '@school.local');
                $generatedPassword = strtoupper(Str::random(6));

                $generatedEmail = $this->userAccountService
                    ->generateUsername(
                        $UserT->FirstName,
                        $UserT->LastName,
                        $schoolEmail
                    );

                $user = new User();
                $user->conn_id = $UserT->id;
                $user->school_id = $schoolCode;
                $user->name = trim($UserT->FirstName . ' ' . $UserT->LastName);
                $user->email = $generatedEmail;
                $user->password = Hash::make($generatedPassword);
                $user->qr_code = generateQrCode();
                $this->setCommonFields($user);
                $user->save();

                $email = $user->email;

            }else{
                $user->conn_id = $UserTypeID;
                $user->name = $UserT->FirstName.' '.$UserT->LastName;
                $user->password = Hash::make($newPassword);
                if(empty($user->qr_code) || $user->qr_code == '0'){
                    $user->qr_code = $this->generateQrCode();
                }
                $user->save();

                $email = $user->email;
            }

            $user->syncRoles($user_type);

            $UserT->UserID = $user->id;
            $UserT->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Password generated successfully.',
                'password' => $newPassword,
                'username' => $email
            ]);

        }catch(\Exception $e){

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ],500);

        }
    }

    public function changePhoto(Request $request)
    {
        try {
            $id = decrypt($request->segment(2));

            if (!$id) {
                abort(404, 'Invalid user identifier');
            }

            $user = User::findOrFail($id);
            $user_type = $request->q ?? null;

            return view('pages.store.users.change-photo', compact('user', 'user_type'));

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            dd($e->getMessage());
//            return redirect()->back()->with('error', 'Invalid or tampered ID.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            dd($e->getMessage());
//            return redirect()->back()->with('error', 'User not found.');

        } catch (\Throwable $e) {
            report($e);

            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function printID(Request $request)
    {
        $id = decrypt($request->segment(3));
        $user = User::find($id);
        if(!$user) return redirect()->route('students.index')->with('error','Generate Password first');

        $student = Students::with(['guardian', 'user'])->where('id', $user->conn_id)->firstOrFail();
//        dd($student);
        $user_type = 'students';

        return view('pages.store.users.print', compact('student', 'user', 'user_type'))
            ->with('success', 'Student ID printed successfully');
    }

    public function nfc(Request $request)
    {
        $id = decrypt($request->segment(2));
        $user = User::find($id);
        if(!$user) return redirect()->route('students.index')->with('error','Generate Password first');
        return view('pages.store.users.nfc', compact('user'));
    }

    public function assignNfc(Request $request)
    {
        try {

            $validated = $request->validate([
                'userID' => 'required|exists:users,id',
                'nfc_uid' => 'required|string|max:255'
            ]);

            $user = User::find($validated['userID']);

            if (!$user) {
                return redirect()
                    ->route('users.index')
                    ->with('error', 'User not found.');
            }

            $nfcOwner = User::where('nfc_code', trim($validated['nfc_uid']))
                ->where('id', '!=', $user->id)
                ->first();

            if ($nfcOwner) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'This NFC card is already assigned to another user.');
            }

            if ($user->nfc_code && !$request->has('force_replace')) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('confirm_replace', [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'old_nfc' => $user->nfc_code,
                        'new_nfc' => trim($validated['nfc_uid'])
                    ]);
            }

            $oldNfc = $user->nfc_code;

            $user->update([
                'nfc_code' => trim($validated['nfc_uid'])
            ]);

            $settings = cache('school_settings');
            $schoolId = $settings?->id;

            $newNFC = new NFCCodes();
            $newNFC->UserID = $user->id;
            $newNFC->school_id = $schoolId;
            $newNFC->nf_codes = trim($validated['nfc_uid']);
            $this->setCommonFields($newNFC);
            $newNFC->save();

            $message = $oldNfc
                ? 'NFC card updated successfully.'
                : 'NFC card assigned successfully.';

            return redirect()
                ->route('school-users.index')
                ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Throwable $e) {
            report($e);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', config('app.debug')
                    ? $e->getMessage()
                    : 'Something went wrong while assigning the NFC card.');
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'cropped_photo' => ['required', 'string'],
            'user_id' => ['required', 'exists:users,id'],
            'user_type' => ['required', 'string', 'in:students,employees']
        ]);

        $user = User::findOrFail($request->user_id);

        $image = $request->cropped_photo;

        if (!preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
            return back()->withErrors(['cropped_photo' => 'Invalid image format']);
        }

        $image = substr($image, strpos($image, ',') + 1);
        $image = base64_decode($image);

        if ($image === false) {
            return back()->withErrors(['cropped_photo' => 'Base64 decode failed']);
        }

        $extension = strtolower($type[1]);
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return back()->withErrors(['cropped_photo' => 'Invalid image type']);
        }

        $fileName = 'users/' . $request->user_type.'/'. $request->user_id.'/'.Str::uuid() . '.' . $extension;

        Storage::disk('public')->put($fileName, $image);

        if ($user->filepath && Storage::disk('public')->exists($user->filepath)) {
            Storage::disk('public')->delete($user->filepath);
        }

        $user->update([
            'filepath' => $fileName,
        ]);

        return redirect()
            ->back()
            ->with('success', 'User photo updated successfully');
    }
}
