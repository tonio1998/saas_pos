<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'SchoolYearID',
        'GradeLevelID',
        'StrandID',
        'AdviserID',
        'SectionName',
        'Capacity',
        'Room',
        'IsActive',
        'CreatedBy',
        'UpdatedBy',
        'Semester',
        'AYFrom',
        'AYTo',
    ];

    protected $casts = [
        'IsActive' => 'boolean',
    ];

    public function schoolYear()
    {
        return $this->belongsTo(
            SchoolYear::class,
            'SchoolYearID'
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

    public function adviser()
    {
        return $this->belongsTo(
            Employees::class,
            'AdviserID'
        );
    }
}
