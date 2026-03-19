<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use App\Models\Residents;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;

class ParentsController extends Controller
{
    use TCommonFunctions;
    public function index()
    {
        return view('pages.parents.index');
    }

    public function edit($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $parent = Parents::findOrFail($id);

        return view('pages.parents.create', compact('parent'));
    }

    public function update(Request $request, $id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }

        $student = Parents::findOrFail($id);

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
            ->route('parents.index')
            ->with('success','Parent updated successfully');
    }

    public function create()
    {
        return view('pages.parents.create');
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

        $new_parent = new Parents();
        $new_parent->fill($data);
        $this->setCommonFields($new_parent);
        $new_parent->save();

        return redirect()
            ->route('parents.index')
            ->with('success','Parent created successfully');
    }

    public function ajaxData(Request $request)
    {
        $query = Parents::with(['createdBy', 'parentUser']);

        return datatables()
            ->eloquent($query)
            ->addColumn('actions', function ($parent) {
                $menu = '';
                if(auth()->user()->can('view users')) {
                    $menu .= '
                    <li>
                        <a href="'.route('parents.edit',encrypt($parent->id)).'" class="dropdown-item">
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
                   data-url="'.route('users.password',['parents',$parent->id,$parent->UserID ?? 0]).'"
                   data-type="'.($parent->parentUser ? 'regenerate' : 'generate').'">
                   <i class="bi bi-key me-2"></i>
                   '.($parent->parentUser ? 'Update Password' : 'Generate Password').'
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
            ->addColumn('residents', function ($parent) {
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
                return $parent->createdBy->name;
            })
            ->rawColumns(['actions','residents', 'name'])
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
