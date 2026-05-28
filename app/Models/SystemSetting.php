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

        'sms_provider',

        'sms_api_url',

        'sms_api_key',

        'sms_api_device_id',

        'gsm_enabled',

        'sms_enabled',

        'sms_status',

        'sms_signal_status',

        'sms_last_error',

        'sms_failed_count',

        'sms_low_balance',

        'total_sent',

        'sms_last_failed_at',

        'remark',
    ];

    protected $casts = [

        'gsm_enabled'
        => 'boolean',

        'sms_enabled'
        => 'boolean',

        'sms_failed_count'
        => 'integer',

        'sms_low_balance'
        => 'integer',

        'total_sent'
        => 'integer',

        'sms_last_failed_at'
        => 'datetime',
    ];

    protected $attributes = [

        'sms_provider'
        => 'gsm',

        'sms_status'
        => 'unknown',

        'sms_signal_status'
        => 'unknown',

        'sms_failed_count'
        => 0,

        'sms_low_balance'
        => 0,

        'total_sent'
        => 0,

        'gsm_enabled'
        => true,

        'sms_enabled'
        => true,
    ];
}
