<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class School extends Model
{
    protected $table = 'school';

    protected $fillable = [
        'SystemTitle',
        'SchoolName',
        'SchoolCode',
        'EducationLevel',
        'Region',
        'Division',
        'Address',
        'ContactNumber',
        'EmailAddress',
        'PrincipalID',
        'RegistrarID',
        'Logo',
        'OfficialTimeIn',
        'OfficialTimeOut',
        'LateGraceMinutes',
        'EnableNFC',
        'EnableQR',
        'EnableOfflineAttendance',
        'CurrentSchoolYearID',
        'ThemeColor',
        'cacert_path',
        'port_com',
        'python_path',
        'sms_failed_count',
        'sms_low_balance',
        'sms_last_failed_at',
        'total_sent'
    ];

    protected $casts = [
        'EnableNFC' => 'boolean',
        'EnableQR' => 'boolean',
        'EnableOfflineAttendance' => 'boolean',
        'OfficialTimeIn' => 'datetime:H:i',
        'OfficialTimeOut' => 'datetime:H:i',
    ];

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('school_settings_' . session('school_id'));
        });

        static::deleted(function () {
            Cache::forget('school_settings_' . session('school_id'));
        });
    }

    public function users()
    {
        return $this->hasMany(User::class, 'school_id');
    }

    public static function getSettings()
    {
        return Cache::rememberForever('school_settings', function () {

            return self::first();

        });
    }

    public function principal()
    {
        return $this->belongsTo(Employees::class, 'PrincipalID');
    }

    public function registrar()
    {
        return $this->belongsTo(Employees::class, 'RegistrarID');
    }

    public function schoolYear()
    {
        return $this->belongsTo(
            SchoolYear::class,
            'CurrentSchoolYearID'
        );
    }
}
