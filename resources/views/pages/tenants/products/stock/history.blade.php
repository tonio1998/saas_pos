@extends('layouts.app')

@section('title', 'Stock History')

@section('content')

    <div class="container-fluid">

        <x-page-header
            title="Stock History"
            subtitle="{{ $product->name }}"
        >
            <x-slot:action>
                <a
                    href="{{ route('products.stock.receive', encrypt($product->id)) }}"
                    class="btn btn-success"
                >
                    <i class="bi bi-box-arrow-in-down"></i>
                    Receive Stock
                </a>
            </x-slot:action>
        </x-page-header>

        <div class="row mb-3">

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">

                        <small class="text-muted">
                            Current Stock
                        </small>

                        <h3 class="mb-0">
                            {{ number_format($product->stock_on_hand, 2) }}
                        </h3>

                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">

                        <small class="text-muted">
                            Cost Price
                        </small>

                        <h5 class="mb-0">
                            ₱{{ number_format($product->cost_price, 2) }}
                        </h5>

                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">

                        <small class="text-muted">
                            Selling Price
                        </small>

                        <h5 class="mb-0">
                            ₱{{ number_format($product->selling_price, 2) }}
                        </h5>

                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">

                        <small class="text-muted">
                            Reorder Level
                        </small>

                        <h5 class="mb-0">
                            {{ number_format($product->reorder_level, 2) }}
                        </h5>

                    </div>
                </div>
            </div>

        </div>

        <div class="card border-0 shadow-sm">

            <div class="card-header">
                Stock Transaction History
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table
                        id="stockHistoryTable"
                        class="table table-bordered table-hover align-middle w-100"
                    >
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Qty</th>
                            <th>Before</th>
                            <th>After</th>
                            <th>Unit Cost</th>
                            <th>Reference</th>
                            <th>Remarks</th>
                            <th>User</th>
                        </tr>
                        </thead>

                        <tbody>

                        @forelse($product->stocks()->latest()->get() as $stock)

                            <tr>

                                <td>
                                    {{ $stock->created_at?->format('M d, Y h:i A') }}
                                </td>

                                <td>

                                    @switch($stock->transaction_type)

                                        @case('IN')
                                            <span class="badge bg-success">
                                                STOCK IN
                                            </span>
                                            @break

                                        @case('OUT')
                                            <span class="badge bg-danger">
                                                STOCK OUT
                                            </span>
                                            @break

                                        @case('ADJUSTMENT')
                                            <span class="badge bg-warning text-dark">
                                                ADJUSTMENT
                                            </span>
                                            @break

                                        @case('RETURN_IN')
                                            <span class="badge bg-info">
                                                RETURN IN
                                            </span>
                                            @break

                                        @case('RETURN_OUT')
                                            <span class="badge bg-secondary">
                                                RETURN OUT
                                            </span>
                                            @break

                                        @default
                                            <span class="badge bg-light text-dark">
                                                {{ $stock->transaction_type }}
                                            </span>

                                    @endswitch

                                </td>

                                <td>
                                    {{ number_format($stock->quantity, 2) }}
                                </td>

                                <td>
                                    {{ number_format($stock->stock_before, 2) }}
                                </td>

                                <td>
                                    {{ number_format($stock->stock_after, 2) }}
                                </td>

                                <td>
                                    @if($stock->unit_cost)
                                        ₱{{ number_format($stock->unit_cost, 2) }}
                                    @endif
                                </td>

                                <td>

                                    @if($stock->reference_type)

                                        <div>
                                            {{ $stock->reference_type }}
                                        </div>

                                        @if($stock->reference_id)
                                            <small class="text-muted">
                                                #{{ $stock->reference_id }}
                                            </small>
                                        @endif

                                    @endif

                                </td>

                                <td>
                                    {{ $stock->remarks }}
                                </td>

                                <td>
                                    {{ $stock->creator?->name }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="9"
                                    class="text-center text-muted"
                                >
                                    No stock transactions found.
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

@endsection

@push('scripts')

    <script>

        $(function () {

            $('#stockHistoryTable').DataTable({
                responsive: true,
                order: [
                    [0, 'desc']
                ],
                pageLength: 25
            });

        });

    </script>

@endpush
