<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use App\Models\ScanLogs;
use App\Models\School;
use App\Models\Students;
use App\Models\Employees;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolDashboardController extends Controller
{
    public $data = [];

    public function index()
    {
        return view(
            'pages.schools.dashboard.index',
            $this->data
        );
    }

    public function data(Request $request)
    {
        $today = Carbon::today();

        $settings = School::getSettings();

        $officialTimeIn = Carbon::parse(
            $settings->OfficialTimeIn
        )->addMinutes(
            (int) $settings->LateGraceMinutes
        );

        $officialTimeOut = Carbon::parse(
            $settings->OfficialTimeOut
        );

        $students = Students::query()
            ->count();

        $employees = Employees::query()
            ->count();

        $latestLogs = ScanLogs::query()
            ->with('user.roles')
            ->select(
                'id',
                'UserID',
                'Mode',
                'direction',
                'attendance_status',
                'created_at'
            )
            ->whereDate(
                'created_at',
                $today
            )
            ->whereIn(
                'id',
                function ($query) use ($today) {

                    $query->selectRaw('MAX(id)')
                        ->from('scan_logs')
                        ->whereDate(
                            'created_at',
                            $today
                        )
                        ->groupBy('UserID');

                }
            )
            ->get();

        $insideCampus = $latestLogs
            ->where('Mode', 1)
            ->count();

        $outsideCampus = $latestLogs
            ->where('Mode', 0)
            ->count();

        $studentEntries = $latestLogs
            ->filter(function ($log) {

                return $log->Mode == 1
                    && $log->user
                    && $log->user->hasRole('students');

            })
            ->count();

        $studentExits = $latestLogs
            ->filter(function ($log) {

                return $log->Mode == 0
                    && $log->user
                    && $log->user->hasRole('students');

            })
            ->count();

        $employeesPresent = $latestLogs
            ->filter(function ($log) {

                return $log->Mode == 1
                    && $log->user
                    && $log->user->hasRole('employees');

            })
            ->count();

        $studentLogs = ScanLogs::query()
            ->with('user.roles')
            ->select(
                'UserID',
                'Mode',
                'created_at'
            )
            ->whereDate(
                'created_at',
                $today
            )
            ->whereHas(
                'user.roles',
                function ($q) {

                    $q->where(
                        'name',
                        'students'
                    );

                }
            )
            ->orderBy('created_at')
            ->get()
            ->groupBy('UserID');

        $lateStudents = 0;

        $earlyOutStudents = 0;

        foreach ($studentLogs as $logs) {

            $firstEntry = null;

            $lastExit = null;

            foreach ($logs as $log) {

                if (
                    $log->Mode == 1 &&
                    !$firstEntry
                ) {

                    $firstEntry = $log;

                }

                if ($log->Mode == 0) {

                    $lastExit = $log;

                }

            }

            if (
                $firstEntry &&
                Carbon::parse(
                    $firstEntry->created_at
                )->gt($officialTimeIn)
            ) {

                $lateStudents++;

            }

            if (
                $lastExit &&
                Carbon::parse(
                    $lastExit->created_at
                )->lt($officialTimeOut)
            ) {

                $earlyOutStudents++;

            }

        }

        $visitors = ScanLogs::query()
            ->whereDate(
                'created_at',
                $today
            )
            ->where(
                'remarks',
                'LIKE',
                '%visitor%'
            )
            ->distinct('UserID')
            ->count('UserID');

        $recentLogs = ScanLogs::query()
            ->with('user')
            ->latest()
            ->limit(15)
            ->get()
            ->map(function ($log) {

                return [

                    'id' => $log->id,

                    'name' => $log->user
                        ? $log->user->name
                        : 'Unknown User',

                    'mode' => strtoupper(
                        $log->direction
                    ),

                    'time' => Carbon::parse(
                        $log->created_at
                    )->format('h:i A'),

                    'scan_type' => strtoupper(
                        $log->scan_type
                    ),

                    'attendance_status' => strtoupper(
                        $log->attendance_status
                    ),

                    'gate_name' => $log->gate_name
                        ?? 'Main Gate'

                ];

            });

        return response()->json([

            'students' => $students,

            'employees' => $employees,

            'insideCampus' => $insideCampus,

            'outsideCampus' => $outsideCampus,

            'lateStudents' => $lateStudents,

            'earlyOutStudents' => $earlyOutStudents,

            'studentEntries' => $studentEntries,

            'studentExits' => $studentExits,

            'employeesPresent' => $employeesPresent,

            'visitors' => $visitors,

            'recentLogs' => $recentLogs,

        ]);
    }
}
