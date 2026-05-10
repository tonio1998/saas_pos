<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'filepath',
        'password',
        'avatar',
        'google_id',
        'verified',
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
        ];
    }

    public function studentInfo()
    {
        return $this->hasOne(Students::class, 'UserID');
    }

    public function teacherInfo()
    {
        return $this->hasOne(Teachers::class, 'UserID');
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

    public function getInfoAttribute()
    {
        return $this->infoRelation()?->first(); // avoids eager loading
    }
}
