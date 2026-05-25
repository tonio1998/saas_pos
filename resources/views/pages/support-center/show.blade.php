@extends('layouts.sa')

@section('content')

    <div class="container-fluid py-3">

        @if(session('success'))

            <div class="alert alert-success border-0 rounded-4 mb-3">

                {{ session('success') }}

            </div>

        @endif

        <div class="support-card mb-3">

            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">

                <div class="flex-grow-1">

                    <div class="small text-muted mb-2">

                        {{ $ticket->ticket_no }}

                    </div>

                    <h3 class="fw-bold mb-2">

                        {{ $ticket->subject }}

                    </h3>

                    <div class="text-muted">

                        {{ $ticket->description }}

                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-3">

                        @php

                            $priorityClass = match($ticket->priority){

                                'critical' => 'danger',

                                'high' => 'warning',

                                'medium' => 'primary',

                                default => 'secondary'
                            };

                            $statusClass = match($ticket->status){

                                'resolved' => 'success',

                                'closed' => 'dark',

                                'pending' => 'warning',

                                'in_progress' => 'primary',

                                default => 'secondary'
                            };

                        @endphp

                        <span class="badge bg-{{ $priorityClass }}-subtle text-{{ $priorityClass }} border border-{{ $priorityClass }}-subtle">

                        {{ ucfirst($ticket->priority) }}

                    </span>

                        <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }} border border-{{ $statusClass }}-subtle">

                        {{ str_replace('_', ' ', ucfirst($ticket->status)) }}

                    </span>

                        @if($ticket->category)

                            <span class="badge bg-light text-dark border">

                            {{ $ticket->category }}

                        </span>

                        @endif

                    </div>

                    <div class="small text-muted mt-3">

                        Created by

                        <strong>

                            {{ $ticket->user?->name ?? 'Unknown User' }}

                        </strong>

                        •

                        {{ $ticket->created_at?->format('M d, Y h:i A') }}

                    </div>

                </div>

                <div class="status-box">

                    <form
                        method="POST"
                        action="{{ route('support-center.update-status', $ticket->id) }}"
                    >

                        @csrf
                        @method('PUT')

                        <label class="form-label">

                            Ticket Status

                        </label>

                        <div class="d-flex gap-2">

                            <select
                                name="status"
                                class="form-select"
                            >

                                <option
                                    value="open"
                                    {{ $ticket->status === 'open' ? 'selected' : '' }}
                                >
                                    Open
                                </option>

                                <option
                                    value="pending"
                                    {{ $ticket->status === 'pending' ? 'selected' : '' }}
                                >
                                    Pending
                                </option>

                                <option
                                    value="in_progress"
                                    {{ $ticket->status === 'in_progress' ? 'selected' : '' }}
                                >
                                    In Progress
                                </option>

                                <option
                                    value="resolved"
                                    {{ $ticket->status === 'resolved' ? 'selected' : '' }}
                                >
                                    Resolved
                                </option>

                                <option
                                    value="closed"
                                    {{ $ticket->status === 'closed' ? 'selected' : '' }}
                                >
                                    Closed
                                </option>

                            </select>

                            <button
                                class="btn btn-dark rounded-3 px-4"
                            >
                                Update
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

        <div class="support-card mb-3">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h5 class="fw-bold mb-1">

                        Ticket Conversation

                    </h5>

                    <div class="small text-muted">

                        Discussion and support updates

                    </div>

                </div>

                <div class="reply-count">

                    {{ $ticket->replies->count() }}

                    Replies

                </div>

            </div>

            @forelse($ticket->replies as $reply)

                <div class="reply-item">

                    <div class="d-flex align-items-start justify-content-between gap-3">

                        <div class="d-flex align-items-start gap-3 flex-grow-1">

                            <div class="reply-avatar">

                                {{ strtoupper(substr($reply->user?->name ?? 'S', 0, 1)) }}

                            </div>

                            <div class="flex-grow-1">

                                <div class="fw-semibold mb-1">

                                    {{ $reply->user?->name ?? 'System User' }}

                                </div>

                                <div class="small text-muted mb-2">

                                    {{ $reply->created_at?->format('M d, Y h:i A') }}

                                </div>

                                <div class="reply-message">

                                    {{ $reply->message }}

                                </div>

                            </div>

                        </div>

                        @if($reply->is_internal)

                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">

                            Internal

                        </span>

                        @endif

                    </div>

                </div>

            @empty

                <div class="empty-state">

                    <div class="empty-icon">

                        <i class="bi bi-chat-left-text"></i>

                    </div>

                    <div class="fw-semibold mb-1">

                        No replies yet

                    </div>

                    <div class="small text-muted">

                        Start the conversation below

                    </div>

                </div>

            @endforelse

        </div>

        <div class="support-card">

            <div class="mb-4">

                <h5 class="fw-bold mb-1">

                    Add Reply

                </h5>

                <div class="small text-muted">

                    Post updates or respond to this ticket

                </div>

            </div>

            <form
                method="POST"
                action="{{ route('support-center.reply', $ticket->id) }}"
            >

                @csrf

                <div class="mb-3">

                <textarea
                    name="message"
                    rows="6"
                    class="form-control"
                    placeholder="Type your reply here..."
                    required
                ></textarea>

                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                    <div class="form-check">

                        <input
                            type="checkbox"
                            name="is_internal"
                            value="1"
                            class="form-check-input"
                            id="internalReply"
                        >

                        <label
                            class="form-check-label small text-muted"
                            for="internalReply"
                        >
                            Internal Note
                        </label>

                    </div>

                    <button
                        class="btn btn-dark rounded-3 px-4"
                    >
                        Send Reply
                    </button>

                </div>

            </form>

        </div>

    </div>

    <style>

        .support-card{
            background:#fff;
            border-radius:24px;
            border:1px solid #eef1f4;
            padding:24px;
            box-shadow:
                0 2px 10px rgba(15,23,42,.03);
        }

        .form-control,
        .form-select{
            border-radius:14px;
            border:1px solid #dbe2ea;
            min-height:48px;
            box-shadow:none;
            font-size:.92rem;
        }

        .form-control:focus,
        .form-select:focus{
            box-shadow:none;
            border-color:#cbd5e1;
        }

        .form-label{
            font-size:.75rem;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:.04em;
            color:#64748b;
            margin-bottom:.5rem;
        }

        .reply-item{
            padding-bottom:20px;
            margin-bottom:20px;
            border-bottom:1px solid #eef1f4;
        }

        .reply-item:last-child{
            border-bottom:0;
            margin-bottom:0;
            padding-bottom:0;
        }

        .reply-avatar{
            width:42px;
            height:42px;
            border-radius:14px;
            background:#0f172a;
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:700;
            font-size:.9rem;
            flex-shrink:0;
        }

        .reply-message{
            color:#334155;
            line-height:1.7;
            font-size:.92rem;
        }

        .reply-count{
            font-size:.75rem;
            font-weight:700;
            color:#64748b;
            text-transform:uppercase;
            letter-spacing:.05em;
        }

        .empty-state{
            padding:50px 20px;
            text-align:center;
        }

        .empty-icon{
            width:70px;
            height:70px;
            margin:0 auto 16px;
            border-radius:22px;
            background:#f8fafc;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:1.4rem;
            color:#94a3b8;
        }

        .status-box{
            min-width:320px;
        }

        .alert{
            padding:14px 18px;
        }

        @media(max-width:768px){

            .support-card{
                padding:18px;
            }

            .status-box{
                min-width:100%;
            }

        }

    </style>

@endsection
