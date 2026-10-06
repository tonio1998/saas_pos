<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Services\Support\TicketService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SupportTicketController extends Controller
{
    public function __construct(
        protected TicketService $service
    ) {}

    public function index()
    {
        return view(
            'pages.support-center.index'
        );
    }

    public function datatable()
    {
        $query = SupportTicket::query()
            ->latest();

        return DataTables::eloquent($query)

            ->addIndexColumn()

            ->addColumn('priority_badge', function ($row) {

                $class = match ($row->priority) {

                    'critical' => 'danger',

                    'high' => 'warning',

                    'medium' => 'primary',

                    default => 'secondary'
                };

                return '
                    <span class="badge bg-'
                    . $class .
                    '-subtle text-'
                    . $class .
                    ' border border-'
                    . $class .
                    '-subtle">'
                    . ucfirst($row->priority) .
                    '</span>
                ';
            })

            ->addColumn('status_badge', function ($row) {

                $class = match ($row->status) {

                    'resolved' => 'success',

                    'closed' => 'dark',

                    'pending' => 'warning',

                    'in_progress' => 'primary',

                    default => 'secondary'
                };

                return '
                    <span class="badge bg-'
                    . $class .
                    '-subtle text-'
                    . $class .
                    ' border border-'
                    . $class .
                    '-subtle">'
                    . str_replace('_', ' ', ucfirst($row->status)) .
                    '</span>
                ';
            })

            ->addColumn('action', function ($row) {

                return '
                    <a
                        href="' .
                    route(
                        'support-center.show',
                        $row->id
                    ) .
                    '"
                        class="btn btn-sm btn-light border"
                    >
                        View
                    </a>
                ';
            })

            ->rawColumns([
                'priority_badge',
                'status_badge',
                'action',
            ])

            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([

            'subject' => [
                'required',
                'string',
                'max:255'
            ],

            'description' => [
                'required',
                'string',
                'min:5'
            ],

            'priority' => [
                'required',
                'in:low,medium,high,critical'
            ],

            'category' => [
                'nullable',
                'string',
                'max:255'
            ],
        ]);

        try {

            $ticket = $this->service
                ->create($validated);

            return redirect()

                ->route(
                    'support-center.show',
                    $ticket->id
                )

                ->with(
                    'success',
                    'Ticket created successfully.'
                );

        } catch (\Throwable $e) {

            report($e);

            return redirect()

                ->back()

                ->withInput()

                ->with(
                    'error',
                    config('app.debug')
                        ? $e->getMessage()
                        : 'Unable to create ticket.'
                );
        }
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load([

            'user:id,name',

            'replies' => function ($query) {

                $query->latest();
            },

            'replies.user:id,name',
        ]);

        return view(
            'pages.support-center.show',
            compact('ticket')
        );
    }

    public function reply(
        Request $request,
        SupportTicket $ticket
    ) {

        $validated = $request->validate([

            'message' => ['required'],
        ]);

        $this->service->reply(
            $ticket,
            $validated
        );

        return redirect()
            ->route(
                'support-center.show',
                $ticket->id
            )
            ->with(
                'success',
                'Reply added successfully.'
            );
    }

    public function updateStatus(
        Request $request,
        SupportTicket $ticket
    ) {

        $validated = $request->validate([

            'status' => ['required'],
        ]);

        $this->service->updateStatus(
            $ticket,
            $validated['status']
        );

        return redirect()
            ->route(
                'support-center.show',
                $ticket->id
            )
            ->with(
            'success',
            'Ticket status updated.'
        );
    }

    public function requestCallback(Request $request)
    {
        $validated = $request->validate([
            'phone'    => ['required', 'string', 'max:50'],
            'issue'    => ['required', 'string', 'max:500'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $tenantName = auth()->user()?->tenant?->business_name ?? 'Tenant Store';

        $ticket = $this->service->create([
            'subject'            => "[URGENT 24/7 CALLBACK] {$tenantName} - {$validated['issue']}",
            'description'        => "24/7 Emergency Callback requested by " . (auth()->user()?->name ?? 'User') . ".\nStore: {$tenantName}\nPhone: {$validated['phone']}\nIssue Details: {$validated['issue']}",
            'priority'           => 'critical',
            'category'           => $validated['category'] ?? '24/7 Urgent Callback',
            'contact_phone'      => $validated['phone'],
            'callback_requested' => true,
            'source'             => '24_7_hotline_widget',
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Your 24/7 emergency callback request was received! Our on-call support team will call you at ' . $validated['phone'] . ' shortly.',
            'ticket_no' => $ticket->ticket_no,
        ]);
    }

    public function storeTenantTicket(Request $request)
    {
        $validated = $request->validate([
            'subject'     => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:5'],
            'priority'    => ['required', 'in:low,medium,high,critical'],
            'category'    => ['nullable', 'string', 'max:100'],
            'phone'       => ['nullable', 'string', 'max:50'],
        ]);

        $ticket = $this->service->create([
            'subject'       => $validated['subject'],
            'description'   => $validated['description'],
            'priority'      => $validated['priority'],
            'category'      => $validated['category'] ?? 'POS Support',
            'contact_phone' => $validated['phone'] ?? null,
            'source'        => 'pos_tenant_portal',
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Support ticket #' . $ticket->ticket_no . ' created successfully. Our 24/7 team is on it!',
            'ticket_no' => $ticket->ticket_no,
        ]);
    }
}
