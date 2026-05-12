<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $table = 'enrollments';

    protected $fillable = [
        'StudentID',
        'AYFrom',
        'AYTo',
        'Semester',
        'GradeLevelID',
        'StrandID',
        'ClassID',
        'EnrollmentStatus',
        'EnrollmentDate',
        'Remarks',
        'IsActive',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'EnrollmentDate' => 'date',
        'IsActive' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(
            Students::class,
            'StudentID'
        );
    }

    public function gradeLevel()
    {
        return $this->belongsTo(
            GradeLevel::class,
            'GradeLevelID'
        );
    }

    public function strand()
    {
        return $this->belongsTo(
            Strands::class,
            'StrandID'
        );
    }

    public function classes()
    {
        return $this->belongsTo(
            Classes::class,
            'ClassID'
        );
    }
}
