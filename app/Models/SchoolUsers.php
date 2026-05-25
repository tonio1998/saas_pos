<?php

namespace App\Models;

use App\Traits\Tenantable;
use App\Traits\UserTenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class SchoolUsers extends Model
{
    use UserTenantable;
    use Tenantable;
    use SoftDeletes;
    use HasFactory, Notifiable;
    use HasRoles;

    protected $table = 'users';

    protected $fillable = [
        'school_id',
        'name',
        'email',
        'filepath',
        'password',
        'avatar',
        'google_id',
        'verified',
        'nfc_code',
    ];

    public function getMorphClass()
    {
        return User::class;
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function logs()
    {
        return $this->hasMany(ScanLogs::class, 'UserID');
    }

    public function getInfoAttribute()
    {
        return $this->infoRelation()?->first();
    }

    public function infoRelation()
    {
        return match ($this->role) {
            'students' => $this->studentInfo(),
            'employees' => $this->teacherInfo(),
            'guardians' => $this->guardianInfo(),
            default => null,
        };
    }

    public function studentInfo()
    {
        return $this->hasOne(Students::class, 'UserID');
    }

    public function teacherInfo()
    {
        return $this->hasOne(Employees::class, 'UserID');
    }

    public function guardianInfo()
    {
        return $this->hasOne(Parents::class, 'UserID');
    }
}
