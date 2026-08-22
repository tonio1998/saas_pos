{{--
    Partial: history_table
    Variables:
      $rows          — Collection of ['stock' => Stocks, 'variant' => string|null, 'clean_remarks' => string|null]
      $product       — POSProducts model
      $tableId       — unique HTML table id string
      $showVariantCol — bool, whether to show the Variant column
--}}
<div class="table-responsive rounded-3 border overflow-hidden">
    <table id="{{ $tableId }}" class="likha-data-table align-middle w-100">
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Movement Type</th>
                @if($showVariantCol)
                    <th>Variant</th>
                @endif
                <th class="text-end">Qty Change</th>
                <th class="text-center">Stock Transition</th>
                <th class="text-end">Unit Cost</th>
                <th class="text-end">Value Impact</th>
                <th>Reference</th>
                <th>Remarks</th>
                <th>Recorded By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                @php
                    $stock        = $row['stock'];
                    $variantLabel = $row['variant'] ?? null;
                    $cleanRemarks = $row['clean_remarks'] ?? null;
                    $isOut        = in_array($stock->transaction_type, ['OUT', 'RETURN_OUT']);
                    $qtyVal       = (float) $stock->quantity;
                    $costVal      = (float) ($stock->unit_cost ?: $product->cost_price);
                    $valueImpact  = $qtyVal * $costVal;
                @endphp
                <tr>
                    <td class="font-mono text-dark fw-semibold" style="font-size:0.8rem;">
                        <i class="bi bi-clock me-1 text-muted"></i>{{ $stock->created_at?->format('M d, Y h:i A') }}
                    </td>
                    <td>
                        @switch($stock->transaction_type)
                            @case('IN')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-arrow-down-left me-1"></i>STOCK IN</span>
                                @break
                            @case('OUT')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-arrow-up-right me-1"></i>STOCK OUT</span>
                                @break
                            @case('ADJUSTMENT')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-sliders me-1"></i>ADJUSTMENT</span>
                                @break
                            @case('RETURN_IN')
                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-arrow-return-left me-1"></i>RETURN IN</span>
                                @break
                            @case('RETURN_OUT')
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-arrow-return-right me-1"></i>RETURN OUT</span>
                                @break
                            @default
                                <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fw-bold">{{ $stock->transaction_type }}</span>
                        @endswitch
                    </td>
                    @if($showVariantCol)
                        <td>
                            @if($variantLabel)
                                <span class="badge font-mono fw-bold d-inline-flex align-items-center gap-1" style="background:#f3e8ff;color:#7e22ce;border:1px solid #e9d5ff;font-size:0.72rem;">
                                    <i class="bi bi-tag-fill"></i> {{ $variantLabel }}
                                </span>
                            @else
                                <span class="text-muted extra-small">—</span>
                            @endif
                        </td>
                    @endif
                    <td class="text-end font-mono fw-bold {{ $isOut ? 'text-danger' : 'text-success' }}">
                        {{ $isOut ? '-' : '+' }}{{ number_format($qtyVal) }}
                    </td>
                    <td class="text-center font-mono extra-small">
                        <span class="text-muted">{{ number_format($stock->stock_before) }}</span>
                        <i class="bi bi-arrow-right mx-1 text-muted"></i>
                        <strong class="text-dark fw-black">{{ number_format($stock->stock_after) }}</strong>
                    </td>
                    <td class="text-end font-mono fw-bold text-dark">
                        {{ $stock->unit_cost ? '₱' . number_format($stock->unit_cost, 2) : '-' }}
                    </td>
                    <td class="text-end font-mono fw-bold {{ $isOut ? 'text-danger' : 'text-dark' }}">
                        ₱{{ number_format($valueImpact, 2) }}
                    </td>
                    <td class="small">
                        @if($stock->reference_type)
                            <span class="fw-bold text-dark font-mono d-block" style="font-size:0.75rem;">{{ $stock->reference_type }}</span>
                            @if($stock->reference_id)
                                <span class="text-muted font-mono extra-small">#{{ $stock->reference_id }}</span>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="small text-muted">
                        {{ $cleanRemarks ?: '-' }}
                    </td>
                    <td class="small fw-semibold text-dark">
                        <div class="d-flex align-items-center gap-1.5">
                            <div class="rounded-circle bg-secondary bg-opacity-20 text-secondary d-flex align-items-center justify-content-center fw-bold extra-small" style="width:22px;height:22px;font-size:0.65rem;">
                                {{ strtoupper(substr($stock->creator?->name ?? 'S', 0, 1)) }}
                            </div>
                            <span>{{ $stock->creator?->name ?: 'System' }}</span>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $showVariantCol ? 10 : 9 }}" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary opacity-50"></i>
                        No stock transaction records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
