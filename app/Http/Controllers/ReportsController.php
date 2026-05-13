<?php

namespace App\Http\Controllers;

use App\Models\ScanLogs;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $baseQuery = ScanLogs::query();

        $totalEntriesToday = (clone $baseQuery)
            ->whereDate('created_at', $today)
            ->where('mode', 'IN')
            ->count();

        $totalExitsToday = (clone $baseQuery)
            ->whereDate('created_at', $today)
            ->where('mode', 'OUT')
            ->count();

        $currentlyInside = max(
            0,
            $totalEntriesToday - $totalExitsToday
        );

        $failedAttempts = (clone $baseQuery)
            ->whereDate('created_at', $today)
            ->whereIn('status', [
                'DENIED',
                'FAILED',
                'BLOCKED'
            ])
            ->count();

        $gates = ScanLogs::query()
            ->select('gate_name')
            ->whereNotNull('gate_name')
            ->distinct()
            ->orderBy('gate_name')
            ->pluck('gate_name');

        return view('pages.reports.index', [
            'totalEntriesToday' => $totalEntriesToday,
            'totalExitsToday' => $totalExitsToday,
            'currentlyInside' => $currentlyInside,
            'failedAttempts' => $failedAttempts,
            'gates' => $gates
        ]);
    }

    public function gateLogsData(Request $request)
    {
        $query = ScanLogs::query()
            ->with([
                'user.roles',
                'user'
            ]);

        if ($request->filled('date_from')) {

            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );

        }

        if ($request->filled('date_to')) {

            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );

        }

        if ($request->filled('gate')) {

            $query->where(
                'gate_name',
                $request->gate
            );

        }

        if ($request->filled('mode')) {

            $query->where(
                'mode',
                strtoupper($request->mode)
            );

        }

        if ($request->filled('status')) {

            $query->where(
                'status',
                strtoupper($request->status)
            );

        }

        $search =
            trim(
                (string) (
                    $request->custom_search ?? ''
                )
            );

        if ($search !== '') {

            $query->where(function ($q) use ($search) {

                $q->where(
                    'VerificationCode',
                    'LIKE',
                    "%{$search}%"
                )

                    ->orWhere(
                        'device',
                        'LIKE',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'gate_name',
                        'LIKE',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'mode',
                        'LIKE',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'status',
                        'LIKE',
                        "%{$search}%"
                    )

                    ->orWhereHas('user', function ($userQuery) use ($search) {

                        $userQuery

                            ->where(
                                'name',
                                'LIKE',
                                "%{$search}%"
                            )

                            ->orWhere(
                                'id',
                                'LIKE',
                                "%{$search}%"
                            )

                            ->orWhere(
                                'department',
                                'LIKE',
                                "%{$search}%"
                            );

                    });

            });

        }

        return datatables()

            ->eloquent(
                $query->latest('created_at')
            )

            ->addColumn('actions', function ($log) {

                if (!$log->user) {

                    return '
                    <button
                        class="
                            btn
                            btn-sm
                            btn-light
                            border
                            rounded-3
                        "
                        disabled
                    >
                        <i class="bi bi-slash-circle"></i>
                    </button>
                ';

                }

                return '
                <a
                    href="' . route(
                        'users.edit',
                        encrypt($log->user->id)
                    ) . '"

                    class="
                        btn
                        btn-sm
                        btn-light
                        border
                        rounded-3
                    "
                >
                    <i class="bi bi-eye"></i>
                </a>
            ';
            })

            ->addColumn('user', function ($log) {

                if (!$log->user) {

                    return '
                    <div class="fw-semibold text-danger">
                        Unknown User
                    </div>
                ';

                }

                $role =
                    optional(
                        $log->user->roles->first()
                    )->name ?? 'User';

                $initial =
                    strtoupper(
                        substr(
                            $log->user->name,
                            0,
                            1
                        )
                    );

                return '
                <div
                    class="
                        d-flex
                        align-items-center
                        gap-2
                    "
                >

                    <div
                        class="
                            rounded-circle
                            bg-primary-subtle
                            text-primary

                            d-flex
                            align-items-center
                            justify-content-center

                            fw-bold
                        "

                        style="
                            width:40px;
                            height:40px;
                            font-size:14px;
                            flex-shrink:0;
                        "
                    >
                        ' . e($initial) . '
                    </div>

                    <div>

                        <div class="fw-semibold">
                            ' . e($log->user->name) . '
                        </div>

                        <div class="text-muted small">

                            ' . e($role) . '

                            ·

                            ID:
                            ' . e($log->user->id) . '

                        </div>

                    </div>

                </div>
            ';
            })

            ->addColumn('department', function ($log) {

                return '
                <span class="fw-medium">
                    ' . e(
                        $log->user->department ?? '-'
                    ) . '
                </span>
            ';
            })

            ->addColumn('gate', function ($log) {

                return '
                <span
                    class="
                        badge
                        rounded-pill
                        bg-light
                        text-dark
                        border
                        px-3
                        py-2
                    "
                >
                    ' . e(
                        $log->gate_name ?? 'Unknown'
                    ) . '
                </span>
            ';
            })

            ->addColumn('mode', function ($log) {

                $mode =
                    strtoupper(
                        $log->mode ?? '-'
                    );

                $class = match ($mode) {

                    'IN' => 'success',
                    'OUT' => 'danger',

                    default => 'secondary'

                };

                $icon = match ($mode) {

                    'IN' => 'box-arrow-in-right',
                    'OUT' => 'box-arrow-right',

                    default => 'question-circle'

                };

                return '
                <span
                    class="
                        badge
                        bg-' . $class . '
                        rounded-pill
                        px-3
                        py-2
                    "
                >

                    <i
                        class="
                            bi bi-' . $icon . '
                            me-1
                        "
                    ></i>

                    ' . e($mode) . '

                </span>
            ';
            })

            ->addColumn('status', function ($log) {

                $status =
                    strtoupper(
                        $log->status ?? '-'
                    );

                $class = match ($status) {

                    'ALLOWED' => 'success',
                    'LATE' => 'warning',
                    'DENIED' => 'danger',

                    default => 'secondary'

                };

                return '
                <span
                    class="
                        badge
                        bg-' . $class . '
                        rounded-pill
                        px-3
                        py-2
                    "
                >
                    ' . e($status) . '
                </span>
            ';
            })

            ->addColumn('device', function ($log) {

                return '
                <div
                    class="
                        d-flex
                        align-items-center
                        gap-2
                    "
                >

                    <i
                        class="
                            bi bi-cpu
                            text-muted
                        "
                    ></i>

                    <span>
                        ' . e(
                        $log->device ?? '-'
                    ) . '
                    </span>

                </div>
            ';
            })

            ->addColumn('time', function ($log) {

                return '
                <div class="d-flex flex-column">

                    <span class="fw-semibold">
                        ' . optional(
                        $log->created_at
                    )->format('h:i A') . '
                    </span>

                    <small class="text-muted">
                        ' . optional(
                        $log->created_at
                    )->format('M d, Y') . '
                    </small>

                </div>
            ';
            })

            ->rawColumns([
                'actions',
                'user',
                'department',
                'gate',
                'mode',
                'status',
                'device',
                'time'
            ])

            ->make(true);
    }
}
