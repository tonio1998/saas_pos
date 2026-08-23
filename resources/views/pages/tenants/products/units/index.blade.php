@extends('layouts.app')

@section('title', 'Product Measurement Units | LikhaPOS - Cloud POS & CRM')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Executive Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <div class="kpi-icon-box blue" style="width:40px;height:40px;font-size:1.15rem;">
                <i class="bi bi-rulers"></i>
            </div>
            <div>
                <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.4px;">Product Units & Measurement Units</h4>
                <p class="text-muted extra-small mb-0">Manage measurement units, loose quantity sales rules, and inventory count metrics</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('products.index') }}" class="btn btn-light border rounded-3 px-3 py-1.5 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-box-seam me-1"></i>
                <span>Products Catalog</span>
            </a>

            {{-- Triggers Add Unit Modal --}}
            <button type="button" class="btn btn-success rounded-3 px-3 py-1.5 fw-bold d-flex align-items-center gap-2 shadow-xs hover-lift" id="btnOpenAddUnit" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:0.85rem;">
                <i class="bi bi-plus-circle-fill fs-6"></i>
                <span>+ Add New Unit</span>
            </button>
        </div>
    </div>

    {{-- 4 Dashboard-Style KPI Cards --}}
    <div class="row g-3 mb-3">
        {{-- Total Units --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card blue h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Total Measurement Units</span>
                    <div class="kpi-icon-box blue"><i class="bi bi-rulers"></i></div>
                </div>
                <div class="kpi-value font-mono" id="kpiTotalUnits">{{ number_format($totalUnits) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-primary extra-small fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Master measurement metrics</span>
                </div>
            </div>
        </div>

        {{-- Active Units --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card emerald h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Active POS Units</span>
                    <div class="kpi-icon-box emerald"><i class="bi bi-check-circle-fill"></i></div>
                </div>
                <div class="kpi-value font-mono text-success" id="kpiActiveUnits">{{ number_format($activeUnits) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-success extra-small fw-bold"><i class="bi bi-shop me-1"></i>Active in POS terminal</span>
                </div>
            </div>
        </div>

        {{-- Products Assigned --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card purple h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Assigned Product SKUs</span>
                    <div class="kpi-icon-box purple"><i class="bi bi-box-seam"></i></div>
                </div>
                <div class="kpi-value font-mono">{{ number_format($totalAssignedProducts) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-secondary extra-small fw-bold"><i class="bi bi-tag-fill me-1"></i>Items using units</span>
                </div>
            </div>
        </div>

        {{-- Most Used Unit --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card amber h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Most Popular Unit</span>
                    <div class="kpi-icon-box amber"><i class="bi bi-award-fill"></i></div>
                </div>
                <div class="kpi-value font-mono text-amber fs-5 text-truncate" title="{{ $topUnitName }}">{{ $topUnitName }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-warning extra-small fw-bold"><i class="bi bi-star-fill me-1"></i>{{ number_format($topUnitCount) }} Products</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Standard DataTable Card --}}
    <x-card>
        <x-datatable
            id="unitsTable"
            :columns="[
                'Actions',
                'Unit Name',
                'Description',
                'Products Linked',
                'Status',
                'Created At',
                'Created By'
            ]"
            :ajax="route('products.units.data')"
            :datatableColumns="[
                ['data' => 'actions', 'orderable' => false, 'searchable' => false, 'width' => '75px'],
                ['data' => 'name', 'width' => '180px'],
                ['data' => 'description'],
                ['data' => 'products_count', 'className' => 'text-center', 'width' => '140px'],
                ['data' => 'status', 'width' => '90px'],
                ['data' => 'createdAt', 'width' => '130px'],
                ['data' => 'createdBy', 'width' => '140px']
            ]"
        />
    </x-card>

</div>

{{-- Add / Edit Unit Modal --}}
<div class="modal fade" id="unitModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="unitForm" method="POST">
                @csrf
                <input type="hidden" id="unitMethod" name="_method" value="POST">
                <input type="hidden" id="unitId" name="id" value="">

                <div class="modal-header border-bottom p-3.5">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                            <i class="bi bi-rulers fs-6"></i>
                        </div>
                        <h6 class="modal-title font-mono fw-bold text-dark" id="unitModalTitle">Add Measurement Unit</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Unit Name --}}
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Unit Name <span class="text-danger">*</span></label>
                        <input type="text" id="unitName" name="name" class="form-control font-mono text-dark" placeholder="e.g. pcs, kg, bottle, pack, box" required autofocus>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Description / Notes <span class="text-muted fw-normal">(Optional)</span></label>
                        <textarea id="unitDescription" name="description" rows="3" class="form-control font-mono" placeholder="Enter short description or unit count remarks..."></textarea>
                    </div>

                    {{-- Status --}}
                    <div class="mb-2">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Status</label>
                        <select id="unitStatus" name="status" class="form-select font-mono">
                            <option value="active">🟢 Active (Visible in POS)</option>
                            <option value="inactive">🔴 Inactive (Hidden from POS)</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-light border rounded-3 px-3 py-1.5 extra-small fw-bold text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4 py-2 extra-small fw-bold d-flex align-items-center gap-1.5 shadow-xs" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;">
                        <i class="bi bi-check-circle-fill"></i>
                        <span id="btnUnitSubmitText">Save Unit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.hover-lift {
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.06) !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const unitModalEl = document.getElementById('unitModal');
    const unitModal = new bootstrap.Modal(unitModalEl);
    const unitForm = document.getElementById('unitForm');
    const unitModalTitle = document.getElementById('unitModalTitle');
    const unitMethod = document.getElementById('unitMethod');
    const unitId = document.getElementById('unitId');
    const unitName = document.getElementById('unitName');
    const unitDescription = document.getElementById('unitDescription');
    const unitStatus = document.getElementById('unitStatus');
    const btnUnitSubmitText = document.getElementById('btnUnitSubmitText');

    function reloadUnitsTable() {
        if (window.LaravelDataTables && window.LaravelDataTables['unitsTable']) {
            window.LaravelDataTables['unitsTable'].ajax.reload(null, false);
        } else if ($.fn.DataTable.isDataTable('#unitsTable')) {
            $('#unitsTable').DataTable().ajax.reload(null, false);
        }
    }

    async function notifyAlert(title, text, type = 'success') {
        if (typeof appAlert === 'function') {
            await appAlert({
                title: title,
                text: text,
                type: type,
                confirmText: 'Done'
            });
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type,
                title: title,
                text: text,
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert(`${title}: ${text}`);
        }
    }

    // Open Add Unit Modal
    document.getElementById('btnOpenAddUnit')?.addEventListener('click', function () {
        unitForm.reset();
        unitModalTitle.textContent = 'Add Measurement Unit';
        unitMethod.value = 'POST';
        unitId.value = '';
        unitStatus.value = 'active';
        btnUnitSubmitText.textContent = 'Save Unit';
        unitForm.action = "{{ route('products.units.store') }}";
        unitModal.show();
    });

    // Handle Edit Unit Click
    $(document).on('click', '.btn-edit-unit', function () {
        const encId = $(this).data('id');
        const name = $(this).data('name');
        const desc = $(this).data('description');
        const status = $(this).data('status') || 'active';

        unitForm.reset();
        unitModalTitle.textContent = 'Edit Measurement Unit';
        unitMethod.value = 'PUT';
        unitId.value = encId;
        unitName.value = name;
        unitDescription.value = desc;
        unitStatus.value = status;
        btnUnitSubmitText.textContent = 'Update Unit';
        unitForm.action = "{{ url('products/units/update') }}/" + encId;
        unitModal.show();
    });

    // Handle Unit Form AJAX Submit
    unitForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
            const res = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();
            if (res.ok && data.success) {
                unitModal.hide();
                await notifyAlert('Success!', data.message, 'success');
                reloadUnitsTable();
            } else {
                await notifyAlert('Error', data.message || 'An error occurred while saving the unit.', 'danger');
            }
        } catch (err) {
            console.error('Save Unit error:', err);
            await notifyAlert('Error', 'An unexpected error occurred.', 'danger');
        }
    });

    // Handle Delete Unit Click with appConfirm Modal
    $(document).on('click', '.btn-delete-unit', async function () {
        const encId = $(this).data('id');
        const name = $(this).data('name');
        const deleteUrl = "{{ url('products/units/delete') }}/" + encId;

        let confirmed = false;
        if (typeof appConfirm === 'function') {
            confirmed = await appConfirm({
                title: `Delete Unit "${name}"?`,
                text: 'Products assigned to this unit will be unlinked in the POS catalog.',
                type: 'danger',
                confirmText: 'Delete Unit',
                cancelText: 'Cancel'
            });
        } else {
            confirmed = confirm(`Are you sure you want to delete unit "${name}"?`);
        }

        if (!confirmed) return;

        try {
            const res = await fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();
            if (data.success) {
                await notifyAlert('Deleted!', data.message, 'success');
                reloadUnitsTable();
            } else {
                await notifyAlert('Failed', data.message || 'Unable to delete unit.', 'danger');
            }
        } catch (err) {
            console.error('Delete unit error:', err);
            await notifyAlert('Error', 'An error occurred while deleting unit.', 'danger');
        }
    });
});
</script>
@endpush
@endsection
