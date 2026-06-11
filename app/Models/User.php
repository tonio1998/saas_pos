<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\UserTenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Auditable;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
class User extends Authenticatable implements AuditableContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'filepath',
        'password',
        'avatar',
        'google_id',
        'verified',
        'nfc_code',
        'last_activity_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_activity_at' => 'datetime',
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
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

    public function infoRelation()
    {
        return match ($this->role) {
            'student' => $this->studentInfo(),
            'teacher' => $this->teacherInfo(),
            'guardian' => $this->guardianInfo(),
            default => null,
        };
    }

    public function logs(){
        return $this->hasMany(ScanLogs::class, 'UserID');
    }

    public function getInfoAttribute()
    {
        return $this->infoRelation()?->first(); // avoids eager loading
    }
}
