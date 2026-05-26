<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Audit\AuditDescriptionService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AuditLogController extends Controller
{
    public function __construct(
        protected AuditLogService         $auditLogService,
        protected AuditDescriptionService $auditDescriptionService
    )
    {
    }

    public function index()
    {
        return view(
            'pages.sa.audit-logs.index',
            [
                'users' => User::query()
                    ->orderBy('name')
                    ->get([
                        'id',
                        'name'
                    ]),
            ]
        );
    }

    public function data(Request $request)
    {
        $logs = $this->auditLogService
            ->query($request);

        return DataTables::of($logs)
            ->addColumn('actions', function ($log) {

                return '
                    <button
                        type="button"
                        class="audit-action-btn view-log-btn"
                        data-id="' . encrypt($log->id) . '"
                    >
                        <i class="bi bi-eye"></i>
                    </button>
                ';
            })
            ->editColumn('user', function ($log) {

                return '
                    <div class="audit-user">
                        ' . e(
                        $log->user?->name ?? 'System'
                    ) . '
                    </div>
                ';
            })
            ->editColumn('event', function ($log) {

                $severity = $this->auditLogService
                    ->severity($log->event);

                return '
                    <span class="audit-event ' . e($severity) . '">
                        ' . e(
                        strtoupper($log->event)
                    ) . '
                    </span>
                ';
            })
            ->addColumn('description', function ($log) {

                return $this
                    ->auditDescriptionService
                    ->make($log)['html'];
            })
            ->addColumn('module', function ($log) {

                return '
                    <div class="audit-module">
                        ' . e(
                        class_basename(
                            $log->auditable_type
                        )
                    ) . '
                    </div>
                ';
            })
            ->editColumn('ip_address', function ($log) {

                return '
                    <div class="audit-ip">
                        ' . e(
                        $log->ip_address ?? 'N/A'
                    ) . '
                    </div>
                ';
            })
            ->editColumn('created_at', function ($log) {

                return '
                    <div class="audit-date">
                        ' . e(
                        $log->created_at
                            ?->format('M d, Y h:i A')
                    ) . '
                    </div>
                ';
            })
            ->filter(function ($query) use ($request) {

                if ($request->filled('user_id')) {

                    $query->where(
                        'user_id',
                        $request->user_id
                    );
                }

                if ($request->filled('event')) {

                    $query->where(
                        'event',
                        $request->event
                    );
                }

                if ($request->filled('from')) {

                    $query->whereDate(
                        'created_at',
                        '>=',
                        $request->from
                    );
                }

                if ($request->filled('to')) {

                    $query->whereDate(
                        'created_at',
                        '<=',
                        $request->to
                    );
                }
            })
            ->rawColumns([
                'actions',
                'user',
                'event',
                'description',
                'module',
                'ip_address',
                'created_at',
            ])
            ->make(true);
    }
}
