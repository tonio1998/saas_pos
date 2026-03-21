<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
class Teachers extends Model implements AuditableContract
{
    use SoftDeletes;
    use Auditable;

    protected $table = 'teachers';

    protected $fillable = [
        'UserID',
        'PhoneNumber',
        'FirstName',
        'MiddleName',
        'LastName',
        'Suffix',
        'Section',
        'Address',
        'YearLevel',
        'created_by',
        'updated_by',
        'Address',
        'created_at',
        'status',
        'archived',
    ];

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function students()
    {
        return $this->hasMany(Students::class, 'GuardianID');
    }

    public function teacherUser()
    {
        return $this->hasOne(User::class, 'conn_id', 'id')->whereHas('roles', function ($q) {
            $q->where('name', 'teachers');
        });
    }
}
