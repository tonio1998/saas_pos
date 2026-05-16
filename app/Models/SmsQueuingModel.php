<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsQueuingModel extends Model
{
    protected $table = 'sms_queues';

    protected $fillable = [
        'PhoneNumber',
        'Message',
        'remark',
    ];
}
