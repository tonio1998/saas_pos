<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\ScanLogs;
use App\Models\School;
use App\Models\SmsQueuingModel;
use App\Models\Students;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OwenIt\Auditing\Models\Audit;

class SADashboardController extends Controller
{
    public $data = [];

    public function index()
    {
        return view(
            'pages.sa.dashboard.index',
            $this->data
        );
    }

    public function data()
    {
        $today = now()->toDateString();

        return response()->json([

            'schools' => School::count(),

            'users' => User::count(),

            'smsSent' => SmsQueuingModel::whereDate(
                'created_at',
                $today
            )
                ->where(
                    'remark',
                    'sent'
                )
                ->count(),

            'activeSchools' => School::where(
                'status',
                'active'
            )->count(),

            'onlineUsers' => User::whereNotNull(
                'last_activity_at'
            )
                ->where(
                    'last_activity_at',
                    '>=',
                    now()->subMinutes(15)
                )
                ->count(),

            'failedSms' => SmsQueuingModel::where(
                'remark',
                'failed'
            )->count(),

            'securityLogs' => ScanLogs::count(),

            'recentActivities' => Audit::query()
                ->with('user')
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($audit) {
                    $event = match ($audit->event) {
                        'created' => 'created a record',
                        'updated' => 'updated a record',
                        'deleted' => 'deleted a record',
                        'restored' => 'restored a record',
                        default => $audit->event,
                    };
                    return [
                        'name' => optional(
                                $audit->user
                            )->name ?? 'System',
                        'description' => $this->formatAuditMessage(
                            $audit
                        ),
                        'time' => $audit->created_at
                            ->diffForHumans(),

                        'event' => $audit->event,

                        'ip' => $audit->ip_address,
                    ];
                }),
        ]);
    }

    protected function formatAuditMessage($audit) {
        $user = optional(
            $audit->user
        )->name ?? 'System';

        $model = Str::of(
            class_basename(
                $audit->auditable_type
            )
        )
            ->snake()
            ->replace('_', ' ')
            ->singular()
            ->lower();

        $article = in_array(
            substr($model, 0, 1),
            ['a', 'e', 'i', 'o', 'u']
        )
            ? 'an'
            : 'a';

        return match ($audit->event) {

            'created' =>

                $user .
                ' created ' .
                $article .
                ' ' .
                $model,

            'updated' =>

                $user .
                ' updated ' .
                $article .
                ' ' .
                $model,

            'deleted' =>

                $user .
                ' deleted ' .
                $article .
                ' ' .
                $model,

            'restored' =>

                $user .
                ' restored ' .
                $article .
                ' ' .
                $model,

            default =>

                $user .
                ' performed an action on ' .
                $article .
                ' ' .
                $model,
        };
    }
}
