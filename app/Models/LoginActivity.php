<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginActivity extends Model
{

    protected $table = 'login_activities';
    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'device',
        'platform',
        'browser',
        'status',
        'logged_in_at',
        'logged_out_at',
    ];

    protected $casts = [
        'logged_in_at' => 'datetime',
        'logged_out_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
