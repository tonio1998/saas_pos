<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
class SupportTicket extends Model implements AuditableContract
{
    use Auditable;
    protected $fillable = [

        'ticket_no',

        'school_id',

        'user_id',

        'assigned_to',

        'subject',

        'description',

        'priority',

        'status',

        'category',

        'resolved_at',

        'attachment',

        'response_time_minutes',

        'is_incident',

        'source',

        'ip_address',

        'user_agent',

        'status_record',

        'archived',

        'created_by',

        'updated_by',
    ];

    protected $casts = [

        'resolved_at' => 'datetime',

        'is_incident' => 'boolean',

        'archived' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(
            School::class
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function assignee()
    {
        return $this->belongsTo(
            User::class,
            'assigned_to'
        );
    }

    public function replies()
    {
        return $this->hasMany(
            SupportTicketReply::class,
            'ticket_id'
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
