<?php

namespace App\Http\Controllers;

use App\Models\SuspiciousActivity;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SuspiciousActivityController extends Controller
{
    public function index()
    {
        return view(
            'pages.sa.security.suspicious-activities.index'
        );
    }

    public function data()
    {
        $query = SuspiciousActivity::query()
            ->with('user')
            ->latest();

        return DataTables::of($query)

            ->editColumn('severity', function ($item) {

                return '
                    <span class="security-severity '
                    . e($item->severity) .
                    '">
                        ' . strtoupper(
                        $item->severity
                    ) . '
                    </span>
                ';
            })

            ->addColumn('user_name', function ($item) {

                return '
                    <div class="security-user">
                        ' . e(
                        $item->user?->name
                        ?? 'Unknown'
                    ) . '
                    </div>
                ';
            })

            ->editColumn('type', function ($item) {

                return '
                    <div class="security-type">
                        ' . str(
                        $item->type
                    )->replace('_', ' ')
                        ->title() . '
                    </div>
                ';
            })

            ->editColumn('description', function ($item) {

                return '
                    <div class="security-description">
                        ' . e(
                        $item->description
                    ) . '
                    </div>
                ';
            })

            ->editColumn('ip_address', function ($item) {

                return '
                    <div class="security-ip">
                        ' . e(
                        $item->ip_address
                    ) . '
                    </div>
                ';
            })

            ->editColumn('detected_at', function ($item) {

                return '
                    <div class="security-date">
                        ' . optional(
                        $item->detected_at
                    )?->diffForHumans() . '
                    </div>
                ';
            })

            ->rawColumns([
                'severity',
                'user_name',
                'type',
                'description',
                'ip_address',
                'detected_at'
            ])

            ->make(true);
    }
}
