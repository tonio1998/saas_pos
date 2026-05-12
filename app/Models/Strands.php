<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Strands extends Model
{
    protected $table = 'strands';

    protected $fillable = [
        'StrandCode',
        'StrandName',
        'Description',
        'IsActive',
        'CreatedBy',
        'UpdatedBy',
    ];

    protected $casts = [
        'IsActive' => 'boolean',
    ];
}
