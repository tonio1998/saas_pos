<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeLevel extends Model
{
    protected $table = 'grade_levels';

    protected $fillable = [
        'GradeLevel',
        'EducationLevel',
        'HasSemester',
        'IsActive',
        'CreatedBy',
        'UpdatedBy',
    ];

    protected $casts = [
        'HasSemester' => 'boolean',
        'IsActive' => 'boolean',
    ];
}
