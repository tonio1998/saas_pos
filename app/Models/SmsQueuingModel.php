<?php

namespace App\Models;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;

class SmsQueuingModel extends Model
{
    use Tenantable;
    protected $table = 'sms_queues';

    protected $fillable = [
        'PhoneNumber',
        'Message',
        'remark',
    ];
}
