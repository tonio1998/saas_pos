@extends('layouts.app')

@section('title', 'Product Categories & Master Catalog Taxonomy | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Executive Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <div class="kpi-icon-box blue" style="width:40px;height:40px;font-size:1.15rem;">
                <i class="bi bi-grid-fill"></i>
            </div>
            <div>
                <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.4px;">Product Categories & Catalog Taxonomy</h4>
                <p class="text-muted extra-small mb-0">Organize merchandise, catalog grouping, inventory classification, and point-of-sale menus</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('products.index') }}" class="btn btn-light border rounded-3 px-3 py-1.5 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-box-seam me-1"></i>
                <span>Products Catalog</span>
            </a>

            {{-- Triggers Add Category Modal --}}
            <button type="button" class="btn btn-success rounded-3 px-3 py-1.5 fw-bold d-flex align-items-center gap-2 shadow-xs hover-lift" id="btnOpenAddCategory" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:0.85rem;">
                <i class="bi bi-plus-circle-fill fs-6"></i>
                <span>+ Add New Category</span>
            </button>
        </div>
    </div>

    {{-- 4 Dashboard-Style KPI Cards --}}
    <div class="row g-3 mb-3">
        {{-- Total Categories --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card blue h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Master Categories</span>
                    <div class="kpi-icon-box blue"><i class="bi bi-folder-fill"></i></div>
                </div>
                <div class="kpi-value font-mono" id="kpiTotalCategories">{{ number_format($totalCategories) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-primary extra-small fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Catalog taxonomy groups</span>
                </div>
            </div>
        </div>

        {{-- Active Categories --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card emerald h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Active POS Categories</span>
                    <div class="kpi-icon-box emerald"><i class="bi bi-grid-3x3-gap-fill"></i></div>
                </div>
                <div class="kpi-value font-mono text-success" id="kpiActiveCategories">{{ number_format($activeCategories) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-success extra-small fw-bold"><i class="bi bi-shop me-1"></i>Visible in Cashier Terminal</span>
                </div>
            </div>
        </div>

        {{-- Products Assigned --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card purple h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Assigned SKU Items</span>
                    <div class="kpi-icon-box purple"><i class="bi bi-box-seam"></i></div>
                </div>
                <div class="kpi-value font-mono">{{ number_format($totalAssignedProducts) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-secondary extra-small fw-bold"><i class="bi bi-tag-fill me-1"></i>Categorized products</span>
                </div>
            </div>
        </div>

        {{-- Top Category Share --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card amber h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Top Category Share</span>
                    <div class="kpi-icon-box amber"><i class="bi bi-award-fill"></i></div>
                </div>
                <div class="kpi-value font-mono text-amber fs-5 text-truncate" title="{{ $topCategoryName }}">{{ $topCategoryName }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-warning extra-small fw-bold"><i class="bi bi-star-fill me-1"></i>{{ number_format($topCategoryCount) }} Products</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Standard DataTable Card --}}
    <x-card>
        <x-datatable
            id="categoriesTable"
            :columns="[
                'Actions',
                'Category Name',
                'Description',
                'Products Linked',
                'Status',
                'Created At',
                'Created By'
            ]"
            :ajax="route('products.categories.data')"
            :datatableColumns="[
                ['data' => 'actions', 'orderable' => false, 'searchable' => false],
                ['data' => 'name'],
                ['data' => 'description'],
                ['data' => 'products_count', 'className' => 'text-center'],
                ['data' => 'status'],
                ['data' => 'createdAt'],
                ['data' => 'createdBy']
            ]"
        />
    </x-card>

</div>

{{-- Add / Edit Category Modal --}}
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="categoryForm" method="POST">
                @csrf
                <input type="hidden" id="categoryMethod" name="_method" value="POST">
                <input type="hidden" id="categoryId" name="id" value="">

                <div class="modal-header border-bottom p-3.5">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                            <i class="bi bi-folder-plus fs-6"></i>
                        </div>
                        <h6 class="modal-title font-mono fw-bold text-dark" id="categoryModalTitle">Add Product Category</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Category Name --}}
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Category Name <span class="text-danger">*</span></label>
                        <input type="text" id="categoryName" name="name" class="form-control font-mono text-dark" placeholder="e.g. Beverages, Bakery, Canned Goods" required autofocus>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Description / Notes <span class="text-muted fw-normal">(Optional)</span></label>
                        <textarea id="categoryDescription" name="description" rows="3" class="form-control font-mono" placeholder="Enter short description or menu category remarks..."></textarea>
                    </div>

                    {{-- Status --}}
                    <div class="mb-2">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Status</label>
                        <select id="categoryStatus" name="status" class="form-select font-mono">
                            <option value="active">🟢 Active (Visible in Cashier POS)</option>
                            <option value="inactive">🔴 Inactive (Hidden from POS)</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-light border rounded-3 px-3 py-1.5 extra-small fw-bold text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4 py-2 extra-small fw-bold d-flex align-items-center gap-1.5 shadow-xs" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;">
                        <i class="bi bi-check-circle-fill"></i>
                        <span id="btnCategorySubmitText">Save Category</span>
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
    const categoryModalEl = document.getElementById('categoryModal');
    const categoryModal = new bootstrap.Modal(categoryModalEl);
    const categoryForm = document.getElementById('categoryForm');
    const categoryModalTitle = document.getElementById('categoryModalTitle');
    const categoryMethod = document.getElementById('categoryMethod');
    const categoryId = document.getElementById('categoryId');
    const categoryName = document.getElementById('categoryName');
    const categoryDescription = document.getElementById('categoryDescription');
    const categoryStatus = document.getElementById('categoryStatus');
    const btnCategorySubmitText = document.getElementById('btnCategorySubmitText');

    function reloadCategoriesTable() {
        if (window.LaravelDataTables && window.LaravelDataTables['categoriesTable']) {
            window.LaravelDataTables['categoriesTable'].ajax.reload(null, false);
        } else if ($.fn.DataTable.isDataTable('#categoriesTable')) {
            $('#categoriesTable').DataTable().ajax.reload(null, false);
        }
    }

    // Helper for appConfirm / appAlert fallback
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

    // Open Add Category Modal
    document.getElementById('btnOpenAddCategory')?.addEventListener('click', function () {
        categoryForm.reset();
        categoryModalTitle.textContent = 'Add Product Category';
        categoryMethod.value = 'POST';
        categoryId.value = '';
        categoryStatus.value = 'active';
        btnCategorySubmitText.textContent = 'Save Category';
        categoryForm.action = "{{ route('products.categories.store') }}";
        categoryModal.show();
    });

    // Handle Edit Category Click (Delegated on DataTable)
    $(document).on('click', '.btn-edit-category', function () {
        const encId = $(this).data('id');
        const name = $(this).data('name');
        const desc = $(this).data('description');
        const status = $(this).data('status') || 'active';

        categoryForm.reset();
        categoryModalTitle.textContent = 'Edit Product Category';
        categoryMethod.value = 'PUT';
        categoryId.value = encId;
        categoryName.value = name;
        categoryDescription.value = desc;
        categoryStatus.value = status;
        btnCategorySubmitText.textContent = 'Update Category';
        categoryForm.action = "{{ url('products/categories/update') }}/" + encId;
        categoryModal.show();
    });

    // Handle Category Form AJAX Submit
    categoryForm.addEventListener('submit', async function (e) {
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
                categoryModal.hide();
                await notifyAlert('Success!', data.message, 'success');
                reloadCategoriesTable();
            } else {
                await notifyAlert('Error', data.message || 'An error occurred while saving the category.', 'danger');
            }
        } catch (err) {
            console.error('Save Category error:', err);
            await notifyAlert('Error', 'An unexpected error occurred.', 'danger');
        }
    });

    // Handle Delete Category Click with Project's Custom appConfirm Modal
    $(document).on('click', '.btn-delete-category', async function () {
        const encId = $(this).data('id');
        const name = $(this).data('name');
        const deleteUrl = "{{ url('products/categories/delete') }}/" + encId;

        let confirmed = false;
        if (typeof appConfirm === 'function') {
            confirmed = await appConfirm({
                title: `Delete Category "${name}"?`,
                text: 'All items assigned to this category will be unassigned in the POS terminal catalog.',
                type: 'danger',
                confirmText: 'Delete Category',
                cancelText: 'Cancel'
            });
        } else {
            confirmed = confirm(`Are you sure you want to delete category "${name}"?`);
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
                reloadCategoriesTable();
            } else {
                await notifyAlert('Failed', data.message || 'Unable to delete category.', 'danger');
            }
        } catch (err) {
            console.error('Delete category error:', err);
            await notifyAlert('Error', 'An error occurred while deleting category.', 'danger');
        }
    });
});
</script>
@endpush
@endsection
