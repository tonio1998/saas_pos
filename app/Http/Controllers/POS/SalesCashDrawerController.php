<?php

namespace App\Http\Controllers\POS;

use App\Helpers\StatusHelper;
use App\Http\Controllers\Controller;
use App\Models\POS\POSCashDrawer;
use App\Models\POS\POSCustomers;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SalesCashDrawerController extends Controller
{
    use TCommonFunctions;
    public function index()
    {
        return view('pages.pos.cash-drawers.index');
    }

    public function cashDrawers_search(Request $request)
    {
        $search = $request->search;
        $users = POSCashDrawer::query()
            ->when($search,function($q) use ($search){
                $q->where('drawer_name','like',"%{$search}%");
            })
            ->where('tenant_id',auth()->user()->tenant_id)
            ->limit(10)
            ->get();
        return $users->map(function($user){
            return [
                'id'=>$user->id,
                'text'=>$user->drawer_name . ' [' . $user->drawer_code . ']'
            ];
        });
    }

    public function ajaxData(Request $request)
    {
        $drawers = POSCashDrawer::query()->where('tenant_id', auth()->user()->tenant_id);
        return DataTables::eloquent($drawers)
            ->editColumn('status', function ($row) {
                return StatusHelper::badge($row->status);
            })
            ->editColumn('created_at', function ($row) {
                return StatusHelper::formatDateTime($row->created_at);
            })
            ->addColumn('createdBy', function ($row) {
                return $row->creator?->name ?? '-';
            })
            ->addColumn('actions', function ($drawer) {
                $id = encryptId($drawer->id);
                $btn = '';
                $btn .= '
                    <a
                        href="' . route('cashiering.cash-drawers.show', $id) . '"
                        class="btn btn-light text-start"
                    >
                        <i class="bi bi-eye me-2 text-primary"></i>
                        View Details
                    </a>
                ';

                $btn .= '
                    <a
                        href="' . route('cashiering.cash-drawers.edit', $id) . '"
                        class="btn btn-light text-start"
                    >
                        <i class="bi bi-pencil-square me-2 text-warning"></i>
                        Edit Drawer
                    </a>
                ';

//                if (!$drawer->activeShift) {
//                    $btn .= '
//                        <a
//                            href="' . route('cashiering.cash-shifts.create', [encryptId($drawer->id)]) . '"
//                            class="btn btn-light text-start"
//                        >
//                            <i class="bi bi-unlock me-2 text-success"></i>
//                            Open Shift
//                        </a>
//                    ';
//                }

                $btn .= '
                    <a
                        href="' . route('cashiering.cash-shifts.shifts', [encryptId($drawer->id)]) . '"
                        class="btn btn-light text-start"
                    >
                        <i class="bi bi-clock-history me-2 text-info"></i>
                        Shift History
                    </a>
                ';

                if (!$drawer->shifts()->exists()) {
                    $btn .= '
                        <button
                            type="button"
                            class="btn btn-light text-start btn-delete"
                            data-url="' . route('cashiering.cash-drawers.destroy', $id) . '"
                        >
                            <i class="bi bi-trash me-2 text-danger"></i>
                            Delete Drawer
                        </button>
                    ';
                }

                return '
                    <button
                        type="button"
                        class="btn btn-soft-primary btn-sm btn-actions"
                        data-bs-toggle="modal"
                        data-bs-target="#actionModal"
                        data-title="Cash Drawer Actions"
                        data-template="actions-' . $drawer->id . '"
                    >
                        <i class="bi bi-gear"></i>
                        Actions
                    </button>

                    <template id="actions-' . $drawer->id . '">
                        <div class="d-grid gap-2">
                            ' . $btn . '
                        </div>
                    </template>
                ';
            })
            ->rawColumns([
                'status',
                'actions'
            ])
            ->make(true);
    }

    public function create(Request $request)
    {
        return view('pages.pos.cash-drawers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'drawer_name' => [
                'required',
                'string',
                'max:100'
            ]
        ]);

        $drawer = new POSCashDrawer();
        $drawer->tenant_id = auth()->user()->tenant_id;
        $drawer->drawer_name = $data['drawer_name'];
        $this->setCommonFields($drawer);
        $drawer->save();

        return redirect()->route('cashiering.cash-drawers.index')
            ->with(
                'success',
                'Cash drawer created successfully.'
            );
    }

    public function show(string $id)
    {
        $drawer = POSCashDrawer::findOrFail(decryptId($id));
        return view('pages.pos.cash-drawers.show', compact('drawer'));
    }

    public function edit(string $id)
    {
        $drawer = POSCashDrawer::findOrFail(
            decryptId($id)
        );

        return view(
            'pages.pos.cash-drawers.edit',
            compact('drawer')
        );
    }

    public function update(Request $request, string $id)
    {
        try {
            $data = $request->validate([
                'drawer_name' => [
                    'required',
                    'string',
                    'max:100'
                ],
            ]);

            $drawer = POSCashDrawer::findOrFail(decrypt($id));
            $drawer->drawer_name = $data['drawer_name'];
            $drawer->remarks = $data['remarks'] ?? null;
            $drawer->save();

            return redirect()
                ->route('cashiering.cash-drawers.index')
                ->with('success', 'Cash drawer updated successfully.');
        } catch (\Exception $e) {
            dd($e);
            return redirect()
                ->route('cashiering.cash-drawers.index')
                ->with('error', 'Cash drawer update failed.');
        }
    }

    public function destroy(string $id)
    {
        $drawer = POSCashDrawer::findOrFail(
            decrypt($id)
        );

        if ($drawer->shifts()->exists()) {
            return redirect()->route('cashiering.cash-drawers.index')
                ->with(
                    'error',
                    'Cash drawer cannot be deleted because it has existing shift records.'
                );
        }

        $drawer->updated_by = auth()->id();
        $drawer->save();

        $drawer->delete();

        return redirect()->route('cash-drawers.cash-drawers.index')
            ->with(
                'success',
                'Cash drawer deleted successfully.'
            );
    }
}
