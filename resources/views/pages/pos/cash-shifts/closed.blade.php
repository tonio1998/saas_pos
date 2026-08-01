@extends('layouts.app')

@section('title', 'Cash Shift Closed')

@section('content')

    <x-page-header />

    <div class="row justify-content-center">

        <div class="col-lg-6">

            <x-card>

                <div class="text-center py-5">

                    <div class="display-1 text-success mb-3">

                        <i class="bi bi-check-circle-fill"></i>

                    </div>

                    <h3 class="fw-bold">

                        Cash Shift Already Closed

                    </h3>

                    <p class="text-muted">

                        This cash shift has already been closed and can no longer be modified.

                    </p>

                    <div class="mt-4">

                        <a
                            href="{{ route('cashiering.cash-shifts.show', encryptId($shift->id)) }}"
                            class="btn btn-primary"
                        >

                            View Shift Details

                        </a>

                        <a
                            href="{{ route('cashiering.cash-shifts.index') }}"
                            class="btn btn-light"
                        >

                            Back

                        </a>

                    </div>

                </div>

            </x-card>

        </div>

    </div>

@endsection
