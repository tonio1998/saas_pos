<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\ScanLogs;
use App\Models\School;
use App\Models\SmsQueuingModel;
use App\Models\Students;
use App\Models\SystemSetting;
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

    $smsSettings = SystemSetting::query()
        ->where('sms_enabled', true)
        ->first();

    return response()->json([

        'store' => School::query()
            ->count(),

        'users' => User::query()
            ->count(),

        'smsSent' => SmsQueuingModel::query()
            ->whereDate(
                'created_at',
                $today
            )
            ->where(
                'remark',
                'sent'
            )
            ->count(),

        'activeSchools' => School::query()
            ->where(
                'status',
                'active'
            )
            ->count(),

        'onlineUsers' => User::query()
            ->whereNotNull(
                'last_activity_at'
            )
            ->where(
                'last_activity_at',
                '>=',
                now()->subMinutes(15)
            )
            ->count(),

        'failedSms' => SmsQueuingModel::query()
            ->where(
                'remark',
                'failed'
            )
            ->count(),

        'securityLogs' => ScanLogs::query()
            ->count(),

        'smsSettings' => [

            'enabled'
                => (bool) (
                    $smsSettings?->sms_enabled
                ),

            'provider'
                => $smsSettings?->sms_provider
                ?? 'gsm',

            'status'
                => $smsSettings?->sms_status
                ?? 'unknown',

            'signal_status'
                => $smsSettings?->sms_signal_status
                ?? 'unknown',

            'last_error'
                => $smsSettings?->sms_last_error,

            'failed_count'
                => $smsSettings?->sms_failed_count
                ?? 0,

            'last_failed_at'
                => $smsSettings?->sms_last_failed_at
                ?->diffForHumans(),
        ],

        'recentActivities' => Audit::query()
            ->with('user')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($audit) {

                return [

                    'name'
                        => optional(
                            $audit->user
                        )->name
                        ?? 'System',

                    'description'
                        => $this->formatAuditMessage(
                            $audit
                        ),

                    'time'
                        => $audit->created_at
                        ->diffForHumans(),

                    'event'
                        => $audit->event,

                    'ip'
                        => $audit->ip_address,
                ];
            })
            ->values(),
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
