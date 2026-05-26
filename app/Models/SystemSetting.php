<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
class SystemSetting extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'system_settings';
    protected $fillable = [
        'cacert_path',
        'python_path',
        'port_com',
        'sms_failed_count',
        'sms_low_balance',
        'total_sent',
        'sms_provider',
        'sms_api_url',
        'sms_api_key',
        'sms_api_device_id',
        'sms_last_failed_at',
        'gsm_enabled',
        'sms_enabled',
    ];

    protected $casts = [

        'gsm_enabled' => 'boolean',
        'sms_enabled' => 'boolean',

        'sms_last_failed_at' => 'datetime',
    ];
}
