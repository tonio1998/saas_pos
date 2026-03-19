<?php

namespace App\Http\Controllers;

use App\Models\Residents;
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
        return view('pages.residents.index');
    }

    public function edit($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $student = Residents::findOrFail($id);
        return view('pages.residents.create', compact('student'));
    }

    public function update(Request $request, $id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $student = Residents::findOrFail($id);

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
                'filepath' => ['nullable','image','mimes:jpg,jpeg,png','max:2048']
            ],
            [
                'LRN.required' => 'Learner Reference Number (LRN) is required.',
                'LRN.max' => 'LRN must not exceed 12 characters.',
                'FirstName.required' => 'First name is required.',
                'LastName.required' => 'Last name is required.',
                'PhoneNumber.regex' => 'Phone number must start with +639 and contain 13 characters.',
                'YearLevel.required' => 'Year level is required.',
                'Section.required' => 'Section is required.',
                'filepath.image' => 'Uploaded file must be an image.',
                'filepath.max' => 'Image must not exceed 2MB.'
            ]
        );

        if ($request->hasFile('filepath')) {
            $path = $request->file('filepath')->store('residents','public');
            $data['filepath'] = $path;
        }

        $student->update($data);

        return redirect()
            ->route('residents.index')
            ->with('success','Student updated successfully');
    }

    public function create()
    {
        return view('pages.residents.create');
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
                'filepath' => ['nullable','image','mimes:jpg,jpeg,png','max:2048']
            ],
            [
                'LRN.required' => 'Learner Reference Number (LRN) is required.',
                'LRN.max' => 'LRN must not exceed 12 characters.',
                'FirstName.required' => 'First name is required.',
                'LastName.required' => 'Last name is required.',
                'PhoneNumber.regex' => 'Phone number must start with +639 and contain 13 characters.',
                'YearLevel.required' => 'Year level is required.',
                'Section.required' => 'Section is required.',
                'filepath.image' => 'Uploaded file must be an image.',
                'filepath.max' => 'Image must not exceed 2MB.'
            ]
        );

        if ($request->hasFile('filepath')) {
            $path = $request->file('filepath')->store('residents','public');
            $data['filepath'] = $path;
        }

        $student = new Residents();
        $student->fill($data);
        $this->setCommonFields($student);
        $student->save();

        return redirect()
            ->route('residents.index')
            ->with('success','Student created successfully');
    }
    public function ajaxData(Request $request)
    {
        $query = Residents::with(['createdBy', 'guardian', 'studentUser']);

        return datatables()
            ->eloquent($query)
            ->addColumn('actions', function ($student) {

                $menu = [];

                $menu[] = '
                <li>
                    <a href="'.route('residents.edit',encrypt($student->id)).'" class="dropdown-item">
                        <i class="bi bi-pencil me-2"></i> Edit
                    </a>
                </li>';


                $menu[] = '
                <li>
                    <a href="javascript:void(0)"
                       class="dropdown-item btn-password"
                       data-url="'.route('users.password',['residents',$student->id,$student->UserID ?? 0]).'"
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
                $src = $student->filepath ? url('/storage/'.$student->filepath) : url('/logo.png');

                return '<img
                src="'.$src.'"
                onerror="this.src=\''.url('/logo.png').'\'"
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
