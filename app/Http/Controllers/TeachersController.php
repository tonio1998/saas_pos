<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use App\Models\Teachers;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;

class TeachersController extends Controller
{
    use TCommonFunctions;
    public function index()
    {
        return view('pages.teachers.index');
    }

    public function edit($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $teacher = Teachers::findOrFail($id);

        return view('pages.teachers.create', compact('teacher'));
    }

    public function update(Request $request, $id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $student = Teachers::findOrFail($id);

        $data = $request->validate([
            'FirstName' => ['required','string','max:255'],
            'LastName' => ['required','string','max:255'],
            'Suffix' => ['nullable','string','max:255'],
            'PhoneNumber' => ['required','regex:/^\+639\d{9}$/'],
            'Address' => ['required','string','max:255']
        ],[
            'FirstName.required' => 'First name is required.',
            'LastName.required' => 'Last name is required.',
            'PhoneNumber.regex' => 'Phone number must start with +639 and contain 13 characters.',
            'Address.required' => 'Address is required.'
        ]);

        $student->update($data);

        return redirect()
            ->route('teachers.index')
            ->with('success','Teacher updated successfully');
    }

    public function create()
    {
        return view('pages.teachers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'FirstName' => ['required','string','max:255'],
                'LastName' => ['required','string','max:255'],
                'Suffix' => ['nullable','string','max:255'],
                'PhoneNumber' => ['required','regex:/^\+639\d{9}$/'],
                'Address' => ['required','string','max:255']
            ],
            [
                'FirstName.required' => 'First name is required.',
                'LastName.required' => 'Last name is required.',
                'PhoneNumber.regex' => 'Phone number must start with +639 and contain 13 characters.',
                'Address.required' => 'Address is required.'
            ]
        );

        $new_parent = new Teachers();
        $new_parent->fill($data);
        $this->setCommonFields($new_parent);
        $new_parent->save();

        return redirect()
            ->route('teachers.index')
            ->with('success','Parent created successfully');
    }

    public function ajaxData(Request $request)
    {
        $query = Teachers::with(['createdBy', 'teacherUser']);

        return datatables()
            ->eloquent($query)
            ->addColumn('actions', function ($teacher) {
                $menu = '';
                if(auth()->user()->can('view users')) {
                    $menu .= '
                    <li>
                        <a href="'.route('teachers.edit',encrypt($teacher->id)).'" class="dropdown-item">
                            <i class="bi bi-pencil me-2"></i> Edit
                        </a>
                    </li>';
                }

                if(auth()->user()->can('view users')) {
                    $menu .= '
                        <li>
                            <a href="" class="dropdown-item">
                                <i class="bi bi-person-badge me-2"></i> Generate ID
                            </a>
                        </li>';
                }

                $menu .= '
                <li>
                <a href="javascript:void(0)"
                   class="dropdown-item btn-password"
                   data-url="'.route('users.password',['teachers',$teacher->id,$teacher->UserID ?? 0]).'"
                   data-type="'.($teacher->teacherUser ? 'regenerate' : 'generate').'">
                   <i class="bi bi-key me-2"></i>
                   '.($teacher->teacherUser ? 'Update Password' : 'Generate Password').'
                </a>
                </li>';

                if($menu == '') return '';

                return '
                    <div class="dropdown">
                        <button class="btn btn-soft-primary btn-sm p-2 px-3 dropdown-toggle" data-bs-toggle="dropdown">
                           Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            '.$menu.'
                        </ul>
                    </div>';

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
            ->addColumn('residents', function ($teacher) {
                $a = '';
                foreach ($teacher->students as $teacher) {
                    $a .= "<div class='text-muted'>" . $teacher->FirstName . ' ' . $teacher->LastName . "</div>";
                }
                return $a;
            })
            ->editColumn('created_at', function ($teacher) {
                return $teacher->created_at->format('M d, Y h:i A');
            })
            ->addColumn('createdBy', function ($teacher) {
                return $teacher->createdBy->name;
            })
            ->rawColumns(['actions','residents', 'name'])
            ->make(true);
    }
}
