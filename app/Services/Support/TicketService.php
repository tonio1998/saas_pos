<?php

namespace App\Services\Support;

use App\Models\SupportTicket;
use App\Models\SupportTicketReply;

class TicketService
{
    public function create(array $data): SupportTicket
    {
        $ticketNo = 'ST-'
            . now()->format('Y')
            . '-'
            . str_pad(
                SupportTicket::count() + 1,
                6,
                '0',
                STR_PAD_LEFT
            );

        return SupportTicket::create([

            'ticket_no' => $ticketNo,

            'school_id' => session('school_id'),

            'user_id' => auth()->id(),

            'subject' => $data['subject'],

            'description' => $data['description'],

            'priority' => $data['priority'],

            'category' => $data['category'],

            'status' => 'open',

            'source' => 'web',

            'ip_address' => request()->ip(),

            'user_agent' => request()->userAgent(),

            'created_by' => auth()->id(),

            'updated_by' => auth()->id(),
        ]);
    }

    public function reply(
        SupportTicket $ticket,
        array $data
    ): SupportTicketReply {

        return SupportTicketReply::create([

            'ticket_id' => $ticket->id,

            'user_id' => auth()->id(),

            'message' => $data['message'],

            'is_internal' => $data['is_internal'] ?? false,

            'created_by' => auth()->id(),

            'updated_by' => auth()->id(),
        ]);
    }

    public function updateStatus(
        SupportTicket $ticket,
        string $status
    ): void {

        $payload = [
            'status' => $status,
            'updated_by' => auth()->id(),
        ];

        if ($status === 'resolved') {

            $payload['resolved_at'] = now();
        }

        $ticket->update($payload);
    }
}
