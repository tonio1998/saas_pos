<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    protected $table = 'school_years';
    protected $fillable = [
        'SchoolYear',
        'StartDate',
        'EndDate',
        'IsActive',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'status',
        'archived',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function students()
    {
        return $this->hasMany(Students::class, 'YearLevel');
    }

    public function teachers()
    {
        return $this->hasMany(Employees::class, 'YearLevel');
    }
}
