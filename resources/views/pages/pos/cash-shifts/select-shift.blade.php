@extends('layouts.app')

@section('title', 'Resume Cash Shift')
@section('shortText', 'Select a shift to continue')

@section('content')

    <x-page-header />

    <form
        action="{{ route('terminal.resumeShift') }}"
        method="POST"
    >

        @csrf

        <input
            type="hidden"
            name="terminal_id"
            value="{{ encryptId($terminal->id) }}"
        >

        <div class="row justify-content-center">

            <div class="col-xl-9">

                <x-card>

                    <div class="text-center mb-5">

                        <div class="terminal-icon mx-auto mb-3">

                            <i class="bi bi-clock-history"></i>

                        </div>

                        <h2 class="fw-bold mb-2">
                            Resume Cash Shift
                        </h2>

                        <p class="text-muted mb-3">
                            Multiple open shifts were found for
                            <strong>{{ $terminal->terminal_name }}</strong>.
                            Select the shift you want to continue.
                        </p>

                        <span class="badge bg-primary-subtle text-primary border px-3 py-2">

                        {{ $shifts->count() }}

                        Open Shift{{ $shifts->count() > 1 ? 's' : '' }}

                    </span>

                    </div>

                    <div class="row g-4">

                        @foreach($shifts as $shift)

                            <div class="col-lg-6">

                                <label
                                    class="shift-card"
                                    for="shift{{ $shift->id }}"
                                >

                                    <input
                                        type="radio"
                                        id="shift{{ $shift->id }}"
                                        name="shift_id"
                                        value="{{ encryptId($shift->id) }}"
                                        required
                                    >

                                    <div class="shift-check">

                                        <i class="bi bi-check-circle-fill"></i>

                                    </div>

                                    <div class="d-flex justify-content-between align-items-start">

                                        <div>

                                            <h5 class="fw-semibold mb-1">

                                                {{ $shift->shift_code }}

                                            </h5>

                                            <div class="text-muted small">

                                                {{ $terminal->drawer?->drawer_name }}

                                            </div>

                                        </div>

                                        <span class="badge bg-success-subtle text-success border">

                                        OPEN

                                    </span>

                                    </div>

                                    <hr>

                                    <div class="small">

                                        <div class="d-flex justify-content-between mb-2">

                                        <span class="text-muted">

                                            Cashier

                                        </span>

                                            <strong>

                                                {{ $shift->cashier?->name }}

                                            </strong>

                                        </div>

                                        <div class="d-flex justify-content-between mb-2">

                                        <span class="text-muted">

                                            Opening Cash

                                        </span>

                                            <strong>

                                                ₱{{ number_format($shift->opening_cash,2) }}

                                            </strong>

                                        </div>

                                        <div class="d-flex justify-content-between">

                                        <span class="text-muted">

                                            Opened At

                                        </span>

                                            <strong>

                                                {{ \Carbon\Carbon::parse($shift->opened_at)->format('M d, Y h:i A') }}

                                            </strong>

                                        </div>

                                    </div>

                                </label>

                            </div>

                        @endforeach

                    </div>

                </x-card>

                <div class="d-flex justify-content-between align-items-center mt-4">

                    <div
                        id="selectedShift"
                        class="text-muted"
                    >

                        No shift selected.

                    </div>

                    <button
                        type="submit"
                        id="btnResume"
                        class="btn btn-primary px-5"
                        disabled
                    >

                        <i class="bi bi-play-circle me-2"></i>

                        Resume Shift

                    </button>

                </div>

            </div>

        </div>

    </form>

    <style>

        .terminal-icon{
            width:74px;
            height:74px;
            border-radius:50%;
            background:#eef4ff;
            color:#0d6efd;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:2rem;
        }

        .shift-card{
            position:relative;
            display:block;
            padding:1.5rem;
            border:1px solid #dee2e6;
            border-radius:14px;
            background:#fff;
            cursor:pointer;
            transition:.2s;
            height:100%;
        }

        .shift-card:hover{
            border-color:#0d6efd;
            transform:translateY(-3px);
            box-shadow:0 10px 25px rgba(0,0,0,.08);
        }

        .shift-card input{
            display:none;
        }

        .shift-check{
            position:absolute;
            top:18px;
            right:18px;
            font-size:1.35rem;
            color:#ced4da;
            transition:.2s;
        }

        .shift-card:has(input:checked){
            border:2px solid #0d6efd;
            background:#f8fbff;
            box-shadow:0 12px 30px rgba(13,110,253,.12);
        }

        .shift-card:has(input:checked) .shift-check{
            color:#0d6efd;
        }

    </style>

    <script>

        document.addEventListener('DOMContentLoaded',function(){

            const radios=document.querySelectorAll('input[name="shift_id"]');
            const btn=document.getElementById('btnResume');
            const selected=document.getElementById('selectedShift');

            radios.forEach(r=>{

                r.addEventListener('change',function(){

                    btn.disabled=false;

                    const card=this.closest('.shift-card');

                    const title=card.querySelector('h5').innerText;

                    selected.innerHTML=
                        '<strong>Selected Shift:</strong> '+title;

                });

            });

        });

    </script>

@endsection
