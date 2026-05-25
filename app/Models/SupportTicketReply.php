<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicketReply extends Model
{
    protected $fillable = [

        'ticket_id',

        'user_id',

        'message',

        'attachment',

        'is_internal',

        'status_record',

        'archived',

        'created_by',

        'updated_by',
    ];

    protected $casts = [

        'is_internal' => 'boolean',

        'archived' => 'boolean',
    ];

    public function ticket()
    {
        return $this->belongsTo(
            SupportTicket::class,
            'ticket_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }
}
