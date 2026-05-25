<?php

namespace App\Http\Controllers;

use App\Models\LoginActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ActiveSessionController extends Controller
{
    public function index()
    {
        return view(
            'pages.sa.security.active-sessions.index'
        );
    }

    public function data()
    {
        $query = LoginActivity::query()
            ->with('user')
            ->whereNull('logged_out_at')
            ->latest();

        return DataTables::of($query)

            ->addColumn('user_name', function ($session) {

                return '
                    <div class="session-user">
                        ' . e(
                        $session->user?->name
                    ) . '
                    </div>
                ';
            })

            ->addColumn('device', function ($session) {

                return '
                    <div class="session-device">

                        <div>
                            ' . e(
                        $session->browser
                    ) . '
                        </div>

                        <small>
                            ' . e(
                        $session->platform
                    ) . '
                        </small>

                    </div>
                ';
            })

            ->editColumn('ip_address', function ($session) {

                return '
                    <div class="session-ip">
                        ' . e(
                        $session->ip_address
                    ) . '
                    </div>
                ';
            })

            ->editColumn('logged_in_at', function ($session) {

                return '
                    <div class="session-date">
                        ' . optional(
                        $session->logged_in_at
                    )?->diffForHumans() . '
                    </div>
                ';
            })

            ->addColumn('status', function () {

                return '
                    <span class="session-status">
                        ACTIVE
                    </span>
                ';
            })

            ->addColumn('actions', function ($session) {

                return '
                    <button
                        type="button"
                        class="btn-revoke-session"
                        data-id="' . encrypt($session->id) . '"
                    >
                        <i class="bi bi-shield-x"></i>
                    </button>
                ';
            })

            ->rawColumns([
                'user_name',
                'device',
                'ip_address',
                'logged_in_at',
                'status',
                'actions'
            ])

            ->make(true);
    }

    public function revoke(string $id)
    {
        $id = decrypt($id);

        $session = LoginActivity::findOrFail($id);

        $session->update([
            'logged_out_at' => now(),
            'status' => 'logout',
        ]);

        DB::table('sessions')
            ->where(
                'id',
                $session->session_id
            )
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Session revoked successfully.',
        ]);
    }
}
