<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NFCCodes extends Model
{
    protected $table = 'nfc_codes';
    protected $fillable = [
        'school_id',
        'UserID',
        'NFC',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'status',
        'archived',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'UserID');
    }
}
