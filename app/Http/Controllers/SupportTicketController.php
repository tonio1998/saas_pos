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
}
