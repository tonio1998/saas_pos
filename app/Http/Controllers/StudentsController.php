<?php

namespace App\Http\Controllers;

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
                    'YearLevel' => ['required','string','max:255'],
                    'Section' => ['required','string','max:255'],
                    'GuardianID' => ['nullable','integer'],
                    'UserID' => ['nullable','integer'],
                    'filepath' => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
                    'Strand' => ['nullable','string','max:255']
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
                'LastName' => ['required','string','max:255'],
                'Suffix' => ['nullable','string','max:255'],
                'PhoneNumber' => ['required','regex:/^\+639\d{9}$/'],
                'YearLevel' => ['required','string','max:255'],
                'Section' => ['required','string','max:255'],
                'GuardianID' => ['nullable','integer'],
                'UserID' => ['nullable','integer'],
                'filepath' => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
                'Strand' => ['nullable','string','max:255']
            ]
        );

        DB::beginTransaction();

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
        $query = Students::with(['createdBy', 'guardian', 'studentUser']);

        return datatables()
            ->eloquent($query)
            ->addColumn('actions', function ($student) {

                $menu = [];

                $menu[] = '
                <li>
                    <a href="'.route('students.edit',encrypt($student->id)).'" class="dropdown-item">
                        <i class="bi bi-pencil me-2"></i> Edit
                    </a>
                </li>';

                $menu[] = '
                <li>
                    <a href="'.route('users.printID',encrypt($student->UserID ?? 0)).'" class="dropdown-item">
                        <i class="bi bi-eye me-2"></i> Show ID
                    </a>
                </li>';

//                route('users.change-photo',[encrypt($user->id), 'q=students'])

                $menu[] = '
                <li>
                    <a href="'.route('users.change-photo',[encrypt($student->UserID ?? 0), 'q=students']).'" class="dropdown-item">
                        <i class="bi bi-photo me-2"></i> Change Photo
                    </a>
                </li>';


                $menu[] = '
                <li>
                    <a href="javascript:void(0)"
                       class="dropdown-item btn-password"
                       data-url="'.route('users.password',['students',$student->id,$student->UserID ?? 0]).'"
                       data-type="'.($student->studentUser ? 'regenerate' : 'generate').'">
                       <i class="bi bi-key me-2"></i>
                       '.($student->studentUser ? 'Update Password' : 'Generate Password').'
                    </a>
                </li>';


                if(empty($menu)) return '';

                return '
                <div class="dropdown">
                    <button class="btn btn-soft-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        '.implode('', $menu).'
                    </ul>
                </div>';

            })
            ->addColumn('name', function ($student) {
                $a = "<div class='fw-bold'>".$student->FirstName . ' ' . $student->LastName."</div>";
                if(isset($student->guardian)){
                    $a .= "<div class='text-muted'>Parent: ".$student->guardian->FirstName.' '.$student->guardian->LastName."</div>";
                }
                return $a;
            })
            ->addColumn('image', function ($student) {
                $src = $student->studentUser ? asset('storage/'.$student->studentUser->filepath) : url('//images/logo.png');
//                return $src;
                return '<img
                src="'.$src.'"
                onerror="this.src=\''.url('/images/avatar.png').'\'"
                style="width:40px;height:40px;border-radius:100px;object-fit:cover;"
            >';
            })
            ->addColumn('lrn', function ($student) {
                return $student->LRN;
            })
            ->addColumn('phone_number', function ($student) {
                return $student->PhoneNumber;
            })
            ->addColumn('section', function ($student) {
                return $student->Section;
            })
            ->addColumn('year', function ($student) {
                return $student->YearLevel;
            })
            ->editColumn('created_at', function ($student) {
                return $student->created_at->format('M d, Y h:i A');
            })
            ->addColumn('createdBy', function ($student) {
                return $student->createdBy->name;
            })
            ->rawColumns(['actions','image', 'name'])
            ->make(true);
    }
}
