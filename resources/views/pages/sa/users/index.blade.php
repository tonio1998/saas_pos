@extends('layouts.sa')

@section('title', 'Platform Users & Staff Directory — SuperAdmin Console')

@section('content')
<div class="container-fluid py-2 px-3">

    {{-- ── Page Header ────────────────────────────────────── --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning text-dark fw-bold px-2 py-0.5" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                    <i class="bi bi-shield-lock-fill me-1"></i> PLATFORM RBAC
                </span>
                <span class="text-muted extra-small">Global Directory</span>
            </div>
            <h3 class="fw-black text-dark font-mono mb-1" style="font-size: 1.6rem; letter-spacing: -0.02em;">
                Platform Users &amp; Staff Directory
            </h3>
            <p class="text-muted small mb-0">
                Manage all accounts across all registered tenant stores, store owners, cashiers, and system administrators.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill fw-bold px-3 shadow-xs d-flex align-items-center gap-1.5" id="btnRefreshTable">
                <i class="bi bi-arrow-clockwise"></i>
                <span>Refresh</span>
            </button>
            <button type="button" class="btn btn-primary btn-sm rounded-pill fw-bold px-3.5 shadow-sm d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalCreateUser" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); border: none;">
                <i class="bi bi-person-plus-fill fs-6"></i>
                <span>Create Platform User</span>
            </button>
        </div>
    </div>

    {{-- ── Metric KPI Cards ───────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-xs p-3.5 h-100 bg-white position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small font-mono fw-bold text-uppercase" style="font-size: 0.72rem;">Total Users</span>
                    <div class="rounded-3 p-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-black text-dark font-mono mb-1">{{ number_format($kpis['total_users']) }}</div>
                <div class="extra-small text-muted d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5 fw-bold">{{ $kpis['active_users'] }} Active</span>
                    <span>across platform</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-xs p-3.5 h-100 bg-white position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small font-mono fw-bold text-uppercase" style="font-size: 0.72rem;">SuperAdmins</span>
                    <div class="rounded-3 p-2 bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-shield-shaded fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-black text-dark font-mono mb-1">{{ number_format($kpis['super_admins']) }}</div>
                <div class="extra-small text-muted d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2 py-0.5 fw-bold">Full Root</span>
                    <span>System-wide access</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-xs p-3.5 h-100 bg-white position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small font-mono fw-bold text-uppercase" style="font-size: 0.72rem;">Store Owners</span>
                    <div class="rounded-3 p-2 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-shop fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-black text-dark font-mono mb-1">{{ number_format($kpis['store_owners']) }}</div>
                <div class="extra-small text-muted d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                    <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-2 py-0.5 fw-bold">Tenant Admins</span>
                    <span>Store management</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-xs p-3.5 h-100 bg-white position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small font-mono fw-bold text-uppercase" style="font-size: 0.72rem;">Cashiers &amp; Staff</span>
                    <div class="rounded-3 p-2 bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-calculator-fill fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-black text-dark font-mono mb-1">{{ number_format($kpis['cashiers_staff']) }}</div>
                <div class="extra-small text-muted d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                    <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 fw-bold">{{ $kpis['google_users'] }} Google Login</span>
                    <span>POS Terminal registers</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filter Strip Card ───────────────────────────────── --}}
    <div class="card border-0 rounded-4 shadow-xs bg-white mb-4">
        <div class="card-body p-3.5">
            <div class="row g-2.5 align-items-center">
                <div class="col-12 col-md-4">
                    <label class="form-label extra-small text-muted fw-bold mb-1 font-mono text-uppercase" style="font-size: 0.68rem;">
                        <i class="bi bi-buildings me-1"></i> Filter By Store / Tenant
                    </label>
                    <select id="filterTenant" class="form-select form-select-sm rounded-3">
                        <option value="">All Stores &amp; Platform (Global)</option>
                        <option value="root">Platform Root / SuperAdmins (No Tenant)</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}">{{ $t->business_name }} ({{ $t->business_code ?? 'STORE' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label extra-small text-muted fw-bold mb-1 font-mono text-uppercase" style="font-size: 0.68rem;">
                        <i class="bi bi-person-badge me-1"></i> Filter By Role
                    </label>
                    <select id="filterRole" class="form-select form-select-sm rounded-3">
                        <option value="">All System Roles</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}">{{ strtoupper($r->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label extra-small text-muted fw-bold mb-1 font-mono text-uppercase" style="font-size: 0.68rem;">
                        <i class="bi bi-activity me-1"></i> Filter By Status
                    </label>
                    <select id="filterStatus" class="form-select form-select-sm rounded-3">
                        <option value="">All Account Statuses</option>
                        <option value="active">Active Accounts</option>
                        <option value="inactive">Inactive / Suspended</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex align-items-end pt-md-3">
                    <button type="button" class="btn btn-light border btn-sm rounded-3 w-100 fw-bold py-1.5" id="btnResetFilters">
                        <i class="bi bi-x-circle me-1"></i> Reset Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Users DataTable Card ───────────────────────────── --}}
    <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 px-3.5 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-person-lines-fill text-success fs-5"></i>
                <h5 class="fw-bold text-dark font-mono fs-6 mb-0">Platform User Directory</h5>
            </div>
            <span class="badge bg-light text-muted border rounded-pill px-3 py-1 font-mono extra-small">
                Live Server-Side Search
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100" id="saUsersTable">
                    <thead class="bg-light font-mono text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.6px;">
                        <tr>
                            <th class="ps-3.5 py-3 text-muted">User Account</th>
                            <th class="py-3 text-muted">Email &amp; Auth</th>
                            <th class="py-3 text-muted">Store / Tenant</th>
                            <th class="py-3 text-muted">Assigned Roles</th>
                            <th class="py-3 text-muted">Status</th>
                            <th class="py-3 text-muted">Registered</th>
                            <th class="pe-3.5 py-3 text-end text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="font-mono"></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ── MODAL: Create User ────────────────────────────────── --}}
<div class="modal fade" id="modalCreateUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark font-mono mb-0">Create Platform User</h6>
                        <small class="text-muted extra-small">Register a new store staff or platform administrator</small>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>

            <form id="formCreateUser">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label extra-small font-mono fw-bold text-uppercase">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Juan Dela Cruz" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label class="form-label extra-small font-mono fw-bold text-uppercase">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control rounded-3" placeholder="user@domain.com" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label extra-small font-mono fw-bold text-uppercase">Username</label>
                            <input type="text" name="username" class="form-control rounded-3" placeholder="juandc">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small font-mono fw-bold text-uppercase">Store / Tenant Assignment</label>
                        <select name="tenant_id" class="form-select rounded-3">
                            <option value="">None — Platform Root (SuperAdmin Level)</option>
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}">{{ $t->business_name }} ({{ $t->business_code }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted extra-small">Leave unassigned for SuperAdmins or root staff.</small>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label class="form-label extra-small font-mono fw-bold text-uppercase">Primary Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select rounded-3" required>
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}" {{ $r->name === 'cashier' ? 'selected' : '' }}>
                                        {{ strtoupper($r->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label extra-small font-mono fw-bold text-uppercase">Account Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select rounded-3" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label extra-small font-mono fw-bold text-uppercase d-flex justify-content-between align-items-center">
                            <span>Initial Password <span class="text-danger">*</span></span>
                            <button type="button" class="btn btn-link p-0 extra-small text-decoration-none btn-generate-password" data-target="#createPasswordInput">
                                <i class="bi bi-dice-5-fill me-1"></i> Generate
                            </button>
                        </label>
                        <div class="input-group">
                            <input type="password" name="password" id="createPasswordInput" class="form-control rounded-start-3" placeholder="Minimum 6 characters" minlength="6" required>
                            <button class="btn btn-outline-secondary btn-toggle-password" type="button" data-target="#createPasswordInput">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2.5 px-4">
                    <button type="button" class="btn btn-light rounded-3 px-3.5" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-bold px-4" style="background:#059669; border:none;">
                        <i class="bi bi-check-circle-fill me-1"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── MODAL: Edit User ──────────────────────────────────── --}}
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark font-mono mb-0">Edit User Account</h6>
                        <small class="text-muted extra-small">Update user details and tenant assignment</small>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>

            <form id="formEditUser">
                @csrf
                @method('PUT')
                <input type="hidden" name="user_id" id="editUserId">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label extra-small font-mono fw-bold text-uppercase">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editUserName" class="form-control rounded-3" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label class="form-label extra-small font-mono fw-bold text-uppercase">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="editUserEmail" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label extra-small font-mono fw-bold text-uppercase">Username</label>
                            <input type="text" name="username" id="editUserUsername" class="form-control rounded-3">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small font-mono fw-bold text-uppercase">Store / Tenant Assignment</label>
                        <select name="tenant_id" id="editUserTenant" class="form-select rounded-3">
                            <option value="">None — Platform Root (SuperAdmin Level)</option>
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}">{{ $t->business_name }} ({{ $t->business_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label class="form-label extra-small font-mono fw-bold text-uppercase">Primary Role <span class="text-danger">*</span></label>
                            <select name="role" id="editUserRole" class="form-select rounded-3" required>
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}">{{ strtoupper($r->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label extra-small font-mono fw-bold text-uppercase">Status <span class="text-danger">*</span></label>
                            <select name="status" id="editUserStatus" class="form-select rounded-3" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 border">
                        <div class="extra-small fw-bold text-muted font-mono mb-1 text-uppercase">
                            <i class="bi bi-shield-lock me-1"></i> Change Password (Optional)
                        </div>
                        <input type="password" name="password" class="form-control form-control-sm rounded-2" placeholder="Leave blank to keep current password">
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2.5 px-4">
                    <button type="button" class="btn btn-light rounded-3 px-3.5" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-bold px-4">
                        <i class="bi bi-check-circle me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── MODAL: Reset Password ─────────────────────────────── --}}
<div class="modal fade" id="modalResetPassword" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-key-fill"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark font-mono mb-0">Reset Password</h6>
                        <small class="text-muted extra-small" id="resetPasswordUserLabel"></small>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>

            <form id="formResetPassword">
                @csrf
                <input type="hidden" name="user_id" id="resetPasswordUserId">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label extra-small font-mono fw-bold text-uppercase d-flex justify-content-between align-items-center">
                            <span>New Password <span class="text-danger">*</span></span>
                            <button type="button" class="btn btn-link p-0 extra-small text-decoration-none btn-generate-password" data-target="#resetPasswordInput">
                                <i class="bi bi-dice-5-fill me-1"></i> Generate
                            </button>
                        </label>
                        <div class="input-group">
                            <input type="password" name="password" id="resetPasswordInput" class="form-control rounded-start-3" minlength="6" placeholder="Min 6 characters" required>
                            <button class="btn btn-outline-secondary btn-toggle-password" type="button" data-target="#resetPasswordInput">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label extra-small font-mono fw-bold text-uppercase">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="resetPasswordConfirmInput" class="form-control rounded-3" minlength="6" placeholder="Re-type password" required>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2.5 px-4">
                    <button type="button" class="btn btn-light rounded-3 px-3.5" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-3 fw-bold px-4 text-dark shadow-xs" style="background:#fbbf24; border:1px solid #d97706;">
                        <i class="bi bi-check-circle-fill me-1"></i> Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableEl = $('#saUsersTable');
    
    // Initialize DataTables
    const dataTable = tableEl.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('sa.users.data') }}",
            data: function (d) {
                d.tenant_id = $('#filterTenant').val();
                d.role = $('#filterRole').val();
                d.status = $('#filterStatus').val();
            }
        },
        columns: [
            { data: 'user_info', name: 'name' },
            { data: 'email_info', name: 'email' },
            { data: 'tenant_info', name: 'tenant.business_name' },
            { data: 'role_info', name: 'roles.name', orderable: false },
            { data: 'status_info', name: 'status' },
            { data: 'created_date', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'pe-3.5 text-end' }
        ],
        order: [[5, 'desc']],
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50, 100],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search users by name, email...",
            processing: '<div class="spinner-border text-success spinner-border-sm me-2"></div> Loading users directory...',
            emptyTable: "No users found matching the selected criteria.",
        }
    });

    // Filters event
    $('#filterTenant, #filterRole, #filterStatus').on('change', function () {
        dataTable.draw();
    });

    $('#btnResetFilters').on('click', function () {
        $('#filterTenant').val('');
        $('#filterRole').val('');
        $('#filterStatus').val('');
        dataTable.draw();
    });

    $('#btnRefreshTable').on('click', function () {
        dataTable.ajax.reload(null, false);
    });

    // Password generator helper
    $('.btn-generate-password').on('click', function () {
        const targetInput = $($(this).data('target'));
        const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%';
        let generated = '';
        for (let i = 0; i < 10; i++) {
            generated += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        targetInput.val(generated).attr('type', 'text');
        
        // If in reset password modal, sync confirm input
        if (targetInput.attr('id') === 'resetPasswordInput') {
            $('#resetPasswordConfirmInput').val(generated).attr('type', 'text');
        }
    });

    // Toggle password visibility
    $('.btn-toggle-password').on('click', function () {
        const targetInput = $($(this).data('target'));
        const isPassword = targetInput.attr('type') === 'password';
        targetInput.attr('type', isPassword ? 'text' : 'password');
        $(this).find('i').toggleClass('bi-eye bi-eye-slash');
    });

    // Handle Create User
    $('#formCreateUser').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).prepend('<span class="spinner-border spinner-border-sm me-1"></span>');

        $.ajax({
            url: "{{ route('sa.users.store') }}",
            type: "POST",
            data: form.serialize(),
            success: function (res) {
                $('#modalCreateUser').modal('hide');
                form[0].reset();
                dataTable.ajax.reload(null, false);
                Swal.fire({
                    icon: 'success',
                    title: 'User Created',
                    text: res.message,
                    timer: 2500,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Failed to create user account. Please check inputs.';
                Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
            },
            complete: function () {
                submitBtn.prop('disabled', false).find('.spinner-border').remove();
            }
        });
    });

    // Open Edit User Modal
    $(document).on('click', '.btn-edit-user', function () {
        const btn = $(this);
        const encId = btn.data('id');
        $('#editUserId').val(encId);
        $('#editUserName').val(btn.data('name'));
        $('#editUserEmail').val(btn.data('email'));
        $('#editUserUsername').val(btn.data('username'));
        $('#editUserTenant').val(btn.data('tenant-id') || '');
        $('#editUserRole').val(btn.data('role'));
        $('#editUserStatus').val(btn.data('status') || 'active');

        $('#modalEditUser').modal('show');
    });

    // Handle Update User
    $('#formEditUser').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const encId = $('#editUserId').val();
        const updateUrl = "{{ route('sa.users.update', ':id') }}".replace(':id', encId);
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).prepend('<span class="spinner-border spinner-border-sm me-1"></span>');

        $.ajax({
            url: updateUrl,
            type: "POST",
            data: form.serialize(),
            success: function (res) {
                $('#modalEditUser').modal('hide');
                dataTable.ajax.reload(null, false);
                Swal.fire({
                    icon: 'success',
                    title: 'Updated',
                    text: res.message,
                    timer: 2500,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Failed to update user account.';
                Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
            },
            complete: function () {
                submitBtn.prop('disabled', false).find('.spinner-border').remove();
            }
        });
    });

    // Open Reset Password Modal
    $(document).on('click', '.btn-reset-password', function () {
        const btn = $(this);
        const encId = btn.data('id');
        const name = btn.data('name');
        const email = btn.data('email');

        $('#resetPasswordUserId').val(encId);
        $('#resetPasswordUserLabel').text(name + ' (' + email + ')');
        $('#formResetPassword')[0].reset();
        $('#modalResetPassword').modal('show');
    });

    // Handle Reset Password Submit
    $('#formResetPassword').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const encId = $('#resetPasswordUserId').val();
        const resetUrl = "{{ route('sa.users.reset-password', ':id') }}".replace(':id', encId);
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).prepend('<span class="spinner-border spinner-border-sm me-1"></span>');

        $.ajax({
            url: resetUrl,
            type: "POST",
            data: form.serialize(),
            success: function (res) {
                $('#modalResetPassword').modal('hide');
                form[0].reset();
                Swal.fire({
                    icon: 'success',
                    title: 'Password Reset',
                    text: res.message,
                    timer: 2500,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Password reset failed.';
                Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
            },
            complete: function () {
                submitBtn.prop('disabled', false).find('.spinner-border').remove();
            }
        });
    });

    // Handle Delete User
    $(document).on('click', '.btn-delete-user', function () {
        const encId = $(this).data('id');
        const name = $(this).data('name');
        const deleteUrl = "{{ route('sa.users.destroy', ':id') }}".replace(':id', encId);

        Swal.fire({
            title: 'Delete User Account?',
            text: 'Are you sure you want to permanently delete user "' + name + '"? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete Account'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (res) {
                        dataTable.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: res.message,
                            timer: 2500,
                            showConfirmButton: false
                        });
                    },
                    error: function (xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Failed to delete user account.';
                        Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
                    }
                });
            }
        });
    });
});
</script>
@endpush
