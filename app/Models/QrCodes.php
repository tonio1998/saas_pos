<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCodes extends Model
{
    protected $table = 'qr_codes';

    protected $fillable = [
        'school_id',
        'prefix',
        'UserID',
        'last_number',
    ];
}
