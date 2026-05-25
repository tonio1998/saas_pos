<?php

namespace App\Models;

use App\Models\User;
use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
class Parents extends Model implements AuditableContract
{
    use SoftDeletes;
    use Auditable;
    use Tenantable;

    protected $table = 'parents';
    protected $fillable = [
        'school_id',
        'UserID',
        'PhoneNumber',
        'FirstName',
        'MiddleName',
        'LastName',
        'Suffix',
        'Address',
        'created_by',
        'updated_by',
        'created_at',
        'status',
        'archived',
    ];

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    public function students()
    {
        return $this->hasMany(Students::class, 'GuardianID');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parentUser()
    {
        return $this->hasOne(User::class, 'conn_id', 'id')->whereHas('roles', function ($q) {
            $q->where('name', 'parents');
        });
    }
}
