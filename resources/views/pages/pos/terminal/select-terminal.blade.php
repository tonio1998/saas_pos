@extends('layouts.app')

@section('title', 'Select POS Terminal')
@section('shortText', 'Select a terminal to continue')

@section('content')

    <x-page-header />

    <form
        action="{{ route('terminal.select') }}"
        method="POST"
    >

        @csrf

        <div class="row justify-content-center">

            <div class="col-xl-9">

                <x-card>

                    <div class="text-center mb-5">

                        <div class="terminal-icon mx-auto mb-3">

                            <i class="bi bi-pc-display-horizontal"></i>

                        </div>

                        <h2 class="fw-bold mb-2">
                            Select POS Terminal
                        </h2>

                        <p class="text-muted mb-3">
                            Select the terminal where you will perform cashiering transactions.
                        </p>

                        <span class="badge rounded-pill bg-primary-subtle text-primary border px-3 py-2">

                        {{ $terminals->count() }}

                        Terminal{{ $terminals->count() > 1 ? 's' : '' }}

                        Available

                    </span>

                    </div>

                    <div class="row g-4">

                        @forelse($terminals as $terminal)

                            <div class="col-lg-6">

                                <label
                                    for="terminal{{ $terminal->id }}"
                                    class="terminal-card"
                                >

                                    <input
                                        type="radio"
                                        id="terminal{{ $terminal->id }}"
                                        name="terminal_id"
                                        value="{{ encryptId($terminal->id) }}"
                                        required
                                    >

                                    <div class="terminal-check">

                                        <i class="bi bi-check-circle-fill"></i>

                                    </div>

                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="fw-semibold mb-1">
                                                <i class="bi bi-pc-display-horizontal text-primary me-2"></i>
                                                {{ $terminal->terminal_name }}
                                            </h5>
                                            <div class="text-muted small">
                                                {{ $terminal->terminal_code }}
                                            </div>
                                        </div>

                                        <div class="me-4">
                                            @if($terminal->status == 'active')
                                                <span class="badge bg-success-subtle text-success border">
                                                    Ready
                                                </span>
                                                    @elseif($terminal->status == 'maintenance')
                                                        <span class="badge bg-warning-subtle text-warning border">
                                                    Maintenance
                                                </span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger border">
                                                    Inactive
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    <div class="d-flex align-items-center">

                                        <div class="drawer-icon me-3">

                                            <i class="bi bi-safe2"></i>

                                        </div>

                                        <div>

                                            <small class="text-muted d-block">

                                                Assigned Cash Drawer

                                            </small>

                                            <div class="fw-semibold">

                                                {{ $terminal->drawer?->drawer_name ?? 'No Drawer Assigned' }}

                                            </div>

                                        </div>

                                    </div>

                                </label>

                            </div>

                        @empty

                            <div class="col-12">

                                <div class="alert alert-warning text-center mb-0">

                                    <i class="bi bi-exclamation-circle me-2"></i>

                                    No POS terminals available.

                                </div>

                            </div>

                        @endforelse

                    </div>

                </x-card>

                @if($terminals->count())

                    <div class="d-flex justify-content-between align-items-center mt-4">

                        <div
                            id="selectedTerminal"
                            class="text-muted"
                        >

                            No terminal selected.

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary px-5"
                            id="btnContinue"
                            disabled
                        >

                            <i class="bi bi-arrow-right-circle me-2"></i>

                            Open Terminal

                        </button>

                    </div>

                @endif

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

        .terminal-card{
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

        .terminal-card:hover{
            transform:translateY(-4px);
            border-color:#0d6efd;
            box-shadow:0 10px 24px rgba(0,0,0,.08);
        }

        .terminal-card input{
            display:none;
        }

        .terminal-check{
            position:absolute;
            top:18px;
            right:18px;
            font-size:1.3rem;
            color:#d0d5dd;
            transition:.2s;
        }

        .terminal-card:has(input:checked){
            border:2px solid #0d6efd;
            background:#f8fbff;
            box-shadow:0 12px 30px rgba(13,110,253,.12);
        }

        .terminal-card:has(input:checked) .terminal-check{
            color:#0d6efd;
        }

        .drawer-icon{
            width:42px;
            height:42px;
            border-radius:10px;
            background:#f5f7fb;
            display:flex;
            align-items:center;
            justify-content:center;
            color:#0d6efd;
            font-size:1.1rem;
        }

    </style>

    <script>

        document.addEventListener('DOMContentLoaded', () => {

            const radios = document.querySelectorAll(
                'input[name="terminal_id"]'
            );

            const button = document.getElementById(
                'btnContinue'
            );

            const selected = document.getElementById(
                'selectedTerminal'
            );

            radios.forEach(radio => {

                radio.addEventListener('change', () => {

                    button.disabled = false;

                    const card = radio.closest('.terminal-card');

                    const title = card.querySelector('h5').innerText.trim();

                    selected.innerHTML =
                        '<strong>Selected Terminal:</strong> ' + title;

                });

            });

        });

    </script>

@endsection
