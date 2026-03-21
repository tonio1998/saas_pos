<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
class Students extends Model implements AuditableContract
{
    use SoftDeletes;
    use Auditable;

    protected $table = 'students';

    protected $fillable = [
        'UserID',
        'FirstName',
        'MiddleName',
        'LastName',
        'Suffix',
        'LRN',
        'Section',
        'GuardianID',
        'YearLevel',
        'PhoneNumber',
        'filepath',
        'created_by',
        'updated_by',
        'created_at',
        'status',
        'archived',
    ];

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    public function guardian()
    {
        return $this->belongsTo(Parents::class, 'GuardianID');
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function studentUser()
    {
        return $this->hasOne(User::class, 'conn_id', 'id')->whereHas('roles', function ($q) {
            $q->where('name', 'students');
        });
    }


}
