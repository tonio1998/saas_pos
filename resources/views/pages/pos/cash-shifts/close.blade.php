@extends('layouts.app')

@section('title', 'Cash Count')
@section('shortText', 'Count the physical cash before closing the shift')

@section('content')

    <x-page-header />

    <div class="row justify-content-center">

        <div class="col-xl-9">

            <form
                action="{{ route('cashiering.cash-shifts.close.store') }}"
                method="POST"
                id="cashCountForm"
            >

                @csrf

                <input
                    type="hidden"
                    name="shift_id"
                    value="{{ encryptId($shift->id) }}"
                >

                <x-card class="border-0 shadow-sm">

                    <div class="text-center mb-4">

                        <div class="display-5 text-danger mb-2">

                            <i class="bi bi-safe2"></i>

                        </div>

                        <h3 class="fw-bold mb-1">

                            Cash Count

                        </h3>

                        <p class="text-muted">

                            Count the physical cash inside the drawer.
                            Do not use the POS total while counting.

                        </p>

                    </div>

                    <div class="border rounded-3 bg-light p-3 mb-4">

                        <div class="row">

                            <div class="col-md-3">

                                <small class="text-muted">

                                    Shift Code

                                </small>

                                <div class="fw-semibold">

                                    {{ $shift->shift_code }}

                                </div>

                            </div>

                            <div class="col-md-3">

                                <small class="text-muted">

                                    Drawer

                                </small>

                                <div class="fw-semibold">

                                    {{ $shift->drawer?->drawer_name }}

                                </div>

                            </div>

                            <div class="col-md-3">

                                <small class="text-muted">

                                    Cashier

                                </small>

                                <div class="fw-semibold">

                                    {{ $shift->cashier?->name }}

                                </div>

                            </div>

                            <div class="col-md-3">

                                <small class="text-muted">

                                    Opened At

                                </small>

                                <div class="fw-semibold">

                                    {{ \App\Helpers\StatusHelper::formatDateTime($shift->opened_at) }}

                                </div>

                            </div>

                        </div>

                    </div>

                    <h5 class="fw-bold mb-3">

                        Bills

                    </h5>

                    <div class="table-responsive">

                        <table class="table table-bordered align-middle">

                            <thead class="table-light">

                            <tr>

                                <th width="25%">

                                    Denomination

                                </th>

                                <th width="30%">

                                    Pieces

                                </th>

                                <th class="text-end">

                                    Amount

                                </th>

                            </tr>

                            </thead>

                            <tbody>

                            @foreach([
                                1000,
                                500,
                                200,
                                100,
                                50,
                                20
                            ] as $bill)

                                <tr>

                                    <td>

                                        <strong>

                                            ₱{{ number_format($bill) }}

                                        </strong>

                                    </td>

                                    <td>

                                        <input
                                            type="number"
                                            min="0"
                                            value="0"
                                            class="form-control denomination"
                                            data-value="{{ $bill }}"
                                            name="bills[{{ $bill }}]"
                                        >

                                    </td>

                                    <td
                                        class="text-end fw-semibold amount"
                                    >

                                        ₱0.00

                                    </td>

                                </tr>

                            @endforeach

                            </tbody>

                            <tfoot>

                            <tr class="table-light">

                                <th colspan="2">

                                    Bills Total

                                </th>

                                <th
                                    class="text-end text-primary"
                                    id="billsTotal"
                                >

                                    ₱0.00

                                </th>

                            </tr>

                            </tfoot>

                        </table>

                    </div>

                    <hr class="my-4">

                    <h5 class="fw-bold mb-3">

                        Coins

                    </h5>

                    <div class="table-responsive">

                        <table class="table table-bordered align-middle">

                            <thead class="table-light">

                            <tr>

                                <th width="25%">

                                    Denomination

                                </th>

                                <th width="30%">

                                    Pieces

                                </th>

                                <th class="text-end">

                                    Amount

                                </th>

                            </tr>

                            </thead>

                            <tbody>

                            @foreach([
                                20,
                                10,
                                5,
                                1
                            ] as $coin)

                                <tr>

                                    <td>

                                        <strong>

                                            ₱{{ number_format($coin) }}

                                        </strong>

                                    </td>

                                    <td>

                                        <input
                                            type="number"
                                            min="0"
                                            value="0"
                                            class="form-control denomination"
                                            data-value="{{ $coin }}"
                                            name="coins[{{ $coin }}]"
                                        >

                                    </td>

                                    <td
                                        class="text-end fw-semibold amount"
                                    >

                                        ₱0.00

                                    </td>

                                </tr>

                            @endforeach

                            </tbody>
                            <tfoot>

                            <tr class="table-light">

                                <th colspan="2">

                                    Coins Total

                                </th>

                                <th
                                    class="text-end text-primary"
                                    id="coinsTotal"
                                >

                                    ₱0.00

                                </th>

                            </tr>

                            </tfoot>

                        </table>

                    </div>
                    <hr class="my-4">

                    <h5 class="fw-bold mb-3">

                        Centavos

                    </h5>

                    <div class="row mb-4">

                        <div class="col-md-6">

                            <label class="form-label">

                                Total Centavos

                            </label>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                value="0"
                                class="form-control centavos"
                                id="centavos"
                                name="centavos"
                            >

                            <div class="form-text">

                                Enter the total value of all centavo coins (e.g. ₱18.75).

                            </div>

                        </div>

                    </div>

                    <div class="border border-success rounded-3 bg-success bg-opacity-10 p-4 mb-4">

                        <div class="row align-items-center">

                            <div class="col-md-4 text-center">

                                <small class="text-muted d-block">

                                    Bills Total

                                </small>

                                <div
                                    id="displayBills"
                                    class="fw-bold fs-4 text-primary"
                                >

                                    ₱0.00

                                </div>

                            </div>

                            <div class="col-md-4 text-center">

                                <small class="text-muted d-block">

                                    Coins + Centavos

                                </small>

                                <div
                                    id="displayCoins"
                                    class="fw-bold fs-4 text-warning"
                                >

                                    ₱0.00

                                </div>

                            </div>

                            <div class="col-md-4 text-center">

                                <small class="text-success fw-semibold">

                                    TOTAL CASH COUNT

                                </small>

                                <div
                                    id="grandTotal"
                                    class="display-5 fw-bold text-success"
                                >

                                    ₱0.00

                                </div>

                            </div>

                        </div>

                    </div>

                    <input
                        type="hidden"
                        name="actual_cash"
                        id="actualCash"
                    >

                    <x-form.textarea
                        name="remarks"
                        label="Remarks"
                        rows="3"
                        placeholder="Optional remarks..."
                    />

                    <div class="alert alert-warning mt-4">

                        <div class="d-flex">

                            <i class="bi bi-info-circle-fill me-2"></i>

                            <div>

                                Count the actual cash inside the drawer carefully.
                                After submitting, your cash count will be reviewed
                                by the supervisor or store owner.

                            </div>

                        </div>

                    </div>

                    <div class="d-flex justify-content-center gap-2">

                        <a
                            href="{{ route('cashiering.cash-shifts.index') }}"
                            class="btn btn-light px-4"
                        >

                            Cancel

                        </a>

                        <button
                            type="submit"
                            class="btn btn-danger px-4"
                        >

                            <i class="bi bi-check-circle me-1"></i>

                            Submit Cash Count

                        </button>

                    </div>

                </x-card>

            </form>

        </div>

    </div>

    <script>

        document.addEventListener('DOMContentLoaded',function(){

            const billInputs=document.querySelectorAll('input[name^="bills"]');

            const coinInputs=document.querySelectorAll('input[name^="coins"]');

            const centavos=document.getElementById('centavos');

            const billsTotal=document.getElementById('billsTotal');

            const coinsTotal=document.getElementById('coinsTotal');

            const displayBills=document.getElementById('displayBills');

            const displayCoins=document.getElementById('displayCoins');

            const grandTotal=document.getElementById('grandTotal');

            const actualCash=document.getElementById('actualCash');

            function money(value){

                return '₱'+value.toLocaleString(undefined,{
                    minimumFractionDigits:2,
                    maximumFractionDigits:2
                });

            }

            function update(){

                let bills=0;

                let coins=0;

                billInputs.forEach(function(input){

                    const qty=parseFloat(input.value)||0;

                    const value=parseFloat(input.dataset.value);

                    const amount=qty*value;

                    input
                        .closest('tr')
                        .querySelector('.amount')
                        .innerHTML=money(amount);

                    bills+=amount;

                });

                coinInputs.forEach(function(input){

                    const qty=parseFloat(input.value)||0;

                    const value=parseFloat(input.dataset.value);

                    const amount=qty*value;

                    input
                        .closest('tr')
                        .querySelector('.amount')
                        .innerHTML=money(amount);

                    coins+=amount;

                });

                const cents=parseFloat(centavos.value)||0;

                const coinTotal=coins+cents;

                const total=bills+coinTotal;

                billsTotal.innerHTML=money(bills);

                coinsTotal.innerHTML=money(coinTotal);

                displayBills.innerHTML=money(bills);

                displayCoins.innerHTML=money(coinTotal);

                grandTotal.innerHTML=money(total);

                actualCash.value=total.toFixed(2);

            }

            document.querySelectorAll('.denomination').forEach(function(input){

                input.addEventListener('input',update);

            });

            centavos.addEventListener('input', update);

            update();

        });

    </script>

@endsection
