<?php

namespace App\Http\Controllers;

use App\Models\LoginActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LoginActivityController extends Controller
{
    public function index()
    {
        return view(
            'pages.sa.security.login-activities.index',
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
        $query = LoginActivity::query()
            ->with('user')
            ->latest();

        return DataTables::of($query)

            ->editColumn('status', function ($log) {

                $class = match ($log->status) {

                    'success' => 'success',

                    'failed' => 'danger',

                    'logout' => 'secondary',

                    default => 'secondary'
                };

                return '
                    <span class="login-status ' . $class . '">
                        ' . strtoupper($log->status) . '
                    </span>
                ';
            })

            ->addColumn('user_name', function ($log) {

                return '
                    <div class="login-user">
                        ' . e(
                        $log->user?->name
                        ?? 'Unknown User'
                    ) . '
                    </div>
                ';
            })

            ->editColumn('ip_address', function ($log) {

                return '
                    <div class="login-ip">
                        ' . e(
                        $log->ip_address
                    ) . '
                    </div>
                ';
            })

            ->addColumn('device_info', function ($log) {

                return '
                    <div class="device-info">

                        <div class="device-browser">
                            ' . e($log->browser) . '
                        </div>

                        <div class="device-platform">
                            ' . e($log->platform) . '
                        </div>

                    </div>
                ';
            })

            ->editColumn('logged_in_at', function ($log) {

                return '
                    <div class="login-date">
                        ' . optional(
                        $log->logged_in_at
                    )?->format('M d, Y h:i A') . '
                    </div>
                ';
            })

            ->editColumn('logged_out_at', function ($log) {

                if (!$log->logged_out_at) {

                    return '
                        <span class="session-active">
                            ACTIVE
                        </span>
                    ';
                }

                return '
                    <div class="login-date">
                        ' . $log->logged_out_at
                        ->format('M d, Y h:i A') . '
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

                if ($request->filled('status')) {

                    $query->where(
                        'status',
                        $request->status
                    );
                }
            })

            ->rawColumns([
                'status',
                'user_name',
                'ip_address',
                'device_info',
                'logged_in_at',
                'logged_out_at'
            ])

            ->make(true);
    }
}
