<?php

namespace App\Http\Controllers;

use App\Models\Strands;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class StrandsController extends Controller
{
    use TCommonFunctions;
    public function index()
    {
        return view('pages.strands.index');
    }

    public function ajaxData(Request $request)
    {
        $query = Strands::query()
            ->latest();

        return DataTables::of($query)
            ->addColumn('strand_code', function ($strand) {
                return e($strand->StrandCode);
            })
            ->addColumn('strand_name', function ($strand) {
                return e($strand->StrandName);
            })
            ->addColumn('description', function ($strand) {
                return $strand->Description
                    ? e($strand->Description)
                    : '-';
            })
            ->addColumn('status', function ($strand) {
                if ($strand->IsActive) {
                    return '
                        <span class="badge bg-success">
                            Active
                        </span>
                    ';
                }

                return '
                    <span class="badge bg-secondary">
                        Inactive
                    </span>
                ';
            })
            ->addColumn('created_at', function ($strand) {
                return $strand->created_at
                    ? $strand->created_at->format('M d, Y h:i A')
                    : '';
            })
            ->addColumn('actions', function ($strand) {
                $editUrl = route(
                    'strands.edit',
                    encrypt($strand->id)
                );

                return '

                    <div class="dropdown">
                        <button
                            class="btn btn-light btn-sm"
                            data-bs-toggle="dropdown"
                        >
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a
                                    href="' . $editUrl . '"
                                    class="dropdown-item"
                                >
                                    <i class="bi bi-pencil-square me-2"></i>
                                    Edit
                                </a>
                            </li>
                        </ul>
                    </div>

                ';
            })
            ->rawColumns([
                'actions',
                'status'
            ])
            ->make(true);
    }

    public function create()
    {
        return view('pages.strands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'StrandCode' => [
                'required',
                'string',
                'max:20',
                'unique:strands,StrandCode'
            ],

            'StrandName' => [
                'required',
                'string',
                'max:255',
                'unique:strands,StrandName'
            ],
        ]);

        $i = new Strands();

        $i->StrandCode = $validated['StrandCode'];
        $i->StrandName = $validated['StrandName'];

        $this->setCommonFields($i);

        $i->save();

        return redirect()
            ->route('strands.index')
            ->with(
                'success',
                'Strand created successfully.'
            );
    }

    public function show(Strands $strand)
    {
        return redirect()
            ->route('strands.index');
    }

    public function edit($id)
    {
        $strand = Strands::findOrFail(
            decrypt($id)
        );

        return view(
            'pages.strands.create',
            compact('strand')
        );
    }

    public function update(
        Request $request,
                $id
    ) {

        $strand = Strands::findOrFail(
            decrypt($id)
        );

        $validated = $request->validate([
            'StrandCode' => [
                'required',
                'string',
                'max:20',

                Rule::unique(
                    'strands',
                    'StrandCode'
                )->ignore($strand->id)
            ],

            'StrandName' => [
                'required',
                'string',
                'max:255',

                Rule::unique(
                    'strands',
                    'StrandName'
                )->ignore($strand->id)
            ],
        ]);

        $strand->StrandCode =
            $validated['StrandCode'];

        $strand->StrandName =
            $validated['StrandName'];

        $this->setCommonFields(
            $strand,
            false
        );

        $strand->save();

        return redirect()
            ->route('strands.index')
            ->with(
                'success',
                'Strand updated successfully.'
            );
    }

    public function destroy($id)
    {
        $strand = Strands::findOrFail(
            decrypt($id)
        );

        $strand->delete();

        return redirect()
            ->route('strands.index')
            ->with(
                'success',
                'Strand deleted successfully.'
            );
    }
}
