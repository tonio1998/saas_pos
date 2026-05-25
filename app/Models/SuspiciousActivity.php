<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuspiciousActivity extends Model
{
    protected $table = 'suspicious_activities';
    protected $fillable = [
        'user_id',
        'type',
        'severity',
        'description',
        'ip_address',
        'meta',
        'detected_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'detected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }
}
