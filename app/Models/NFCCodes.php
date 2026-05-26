<?php

namespace App\Models;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
class NFCCodes extends Model implements AuditableContract
{
    use Tenantable;
    use Auditable;

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
