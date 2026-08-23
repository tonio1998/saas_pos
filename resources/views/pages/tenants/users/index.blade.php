@extends('layouts.app')

@section('title', 'Store Users & Staff Management | LikhaPOS - Cloud POS & CRM')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Executive Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <div class="kpi-icon-box blue" style="width:40px;height:40px;font-size:1.15rem;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.4px;">Store Users & Staff Management</h4>
                <p class="text-muted extra-small mb-0">Manage cashier accounts, store admins, password resets, and subscription user limits</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('subscription.checkout') }}" class="btn btn-light border rounded-3 px-3 py-1.5 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-rocket-takeoff-fill text-warning me-1"></i>
                <span>Subscription Plan</span>
            </a>

            {{-- Triggers Add User Modal --}}
            <button type="button" class="btn btn-success rounded-3 px-3 py-1.5 fw-bold d-flex align-items-center gap-2 shadow-xs hover-lift" id="btnOpenAddUser" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:0.85rem;">
                <i class="bi bi-person-plus-fill fs-6"></i>
                <span>+ Add New User</span>
            </button>
        </div>
    </div>

    {{-- Subscription Account Limits KPI Banner --}}
    <div class="row g-3 mb-3">
        {{-- Total Users Usage --}}
        <div class="col-6 col-md-4">
            <div class="likha-kpi-card blue h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Active User Accounts</span>
                    <div class="kpi-icon-box blue"><i class="bi bi-people-fill"></i></div>
                </div>
                <div class="kpi-value font-mono">
                    {{ $usage['total_users']['current'] ?? 1 }} / <span class="fs-4 text-muted">{{ $usage['total_users']['limit'] ?? 3 }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-primary extra-small fw-bold">Plan: {{ $usage['plan_name'] ?? 'Free Trial' }}</span>
                </div>
            </div>
        </div>

        {{-- Store Admins Limit --}}
        <div class="col-6 col-md-4">
            <div class="likha-kpi-card purple h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Store Admins / Owners</span>
                    <div class="kpi-icon-box purple"><i class="bi bi-shield-lock-fill"></i></div>
                </div>
                <div class="kpi-value font-mono">
                    {{ $usage['admins']['current'] ?? 1 }} / <span class="fs-4 text-muted">{{ $usage['admins']['limit'] ?? 1 }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-purple extra-small fw-bold" style="color:#7e22ce;"><i class="bi bi-person-badge me-1"></i>Admin limit</span>
                </div>
            </div>
        </div>

        {{-- Cashiers Limit --}}
        <div class="col-6 col-md-4">
            <div class="likha-kpi-card emerald h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Cashier Staff Accounts</span>
                    <div class="kpi-icon-box emerald"><i class="bi bi-person-check-fill"></i></div>
                </div>
                <div class="kpi-value font-mono text-success">
                    {{ $usage['cashiers']['current'] ?? 0 }} / <span class="fs-4 text-muted">{{ $usage['cashiers']['limit'] ?? 2 }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-success extra-small fw-bold"><i class="bi bi-shop me-1"></i>POS Terminal Users</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Standard DataTable Card --}}
    <x-card>
        <x-datatable
            id="usersTable"
            :columns="[
                'Actions',
                'User Profile',
                'Username / Login ID',
                'System Role',
                'Account Status',
                'Created At'
            ]"
            :ajax="route('users.data')"
            :datatableColumns="[
                ['data' => 'actions', 'orderable' => false, 'searchable' => false, 'width' => '75px'],
                ['data' => 'user_info', 'width' => '220px'],
                ['data' => 'username', 'width' => '200px'],
                ['data' => 'role_badge', 'width' => '130px'],
                ['data' => 'status', 'width' => '90px'],
                ['data' => 'createdAt', 'width' => '140px']
            ]"
        />
    </x-card>

</div>

{{-- Add User Modal --}}
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="addUserForm" method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-header border-bottom p-3.5">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                            <i class="bi bi-person-plus-fill fs-6"></i>
                        </div>
                        <h6 class="modal-title font-mono fw-bold text-dark">Add New Store User</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- User Name --}}
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control font-mono text-dark" placeholder="e.g. Maria Santos" required autofocus>
                    </div>

                    {{-- Username with Store Prefix --}}
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Username (Store Login ID) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text font-mono extra-small bg-light fw-bold text-primary border-end-0">
                                {{ strtolower(preg_replace('/[^a-zA-Z0-9]/', '', auth()->user()->tenant?->business_code ?? 'store')) }}_
                            </span>
                            <input type="text" name="username" class="form-control font-mono text-dark border-start-0" placeholder="cashier1" required>
                        </div>
                        <small class="text-muted extra-small d-block mt-1 font-mono">Store code prefix is automatically prepended for quick login.</small>
                    </div>

                    {{-- Role Selection --}}
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">System Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select font-mono" required>
                            <option value="cashier">🟢 Cashier Staff (POS Terminal Cashier)</option>
                            <option value="manager">🟡 Store Manager (Reports & Inventory)</option>
                            <option value="admin">🔵 Store Admin (Full Access)</option>
                        </select>
                        <small class="text-muted extra-small d-block mt-1">User creation will validate against your subscription plan limits.</small>
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control font-mono text-dark" placeholder="Minimum 6 characters" required>
                    </div>

                    {{-- Status --}}
                    <div class="mb-2">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Account Status</label>
                        <select name="status" class="form-select font-mono">
                            <option value="active">🟢 Active</option>
                            <option value="inactive">🔴 Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-light border rounded-3 px-3 py-1.5 extra-small fw-bold text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4 py-2 extra-small fw-bold d-flex align-items-center gap-1.5 shadow-xs" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Save Account</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reset Password Modal --}}
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="resetPasswordForm" method="POST">
                @csrf
                <div class="modal-header border-bottom p-3.5">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 bg-warning bg-opacity-10 text-warning-emphasis p-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                            <i class="bi bi-key-fill fs-6"></i>
                        </div>
                        <h6 class="modal-title font-mono fw-bold text-dark">Reset User Password</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <p class="text-muted extra-small mb-3">Resetting password for: <strong class="text-dark fs-6 font-mono" id="resetUserName">User</strong></p>

                    {{-- New Password --}}
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control font-mono text-dark" placeholder="Minimum 6 characters" required autofocus>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-2">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control font-mono text-dark" placeholder="Re-enter password" required>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-light border rounded-3 px-3 py-1.5 extra-small fw-bold text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-3 px-4 py-2 extra-small fw-bold text-dark d-flex align-items-center gap-1.5 shadow-xs" style="background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%);border:none;color:#fff !important;">
                        <i class="bi bi-shield-check"></i>
                        <span>Reset Password</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit User Modal --}}
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom p-3.5">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                            <i class="bi bi-pencil fs-6"></i>
                        </div>
                        <h6 class="modal-title font-mono fw-bold text-dark">Edit User Account</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- User Name --}}
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Full Name <span class="text-danger">*</span></label>
                        <input type="text" id="editName" name="name" class="form-control font-mono text-dark" required>
                    </div>

                    {{-- User Email --}}
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Email Address <span class="text-danger">*</span></label>
                        <input type="email" id="editEmail" name="email" class="form-control font-mono text-dark" required>
                    </div>

                    {{-- Role Selection --}}
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">System Role <span class="text-danger">*</span></label>
                        <select id="editRole" name="role" class="form-select font-mono" required>
                            <option value="cashier">🟢 Cashier Staff</option>
                            <option value="manager">🟡 Store Manager</option>
                            <option value="admin">🔵 Store Admin</option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="mb-2">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Account Status</label>
                        <select id="editStatus" name="status" class="form-select font-mono">
                            <option value="active">🟢 Active</option>
                            <option value="inactive">🔴 Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-light border rounded-3 px-3 py-1.5 extra-small fw-bold text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 extra-small fw-bold d-flex align-items-center gap-1.5 shadow-xs">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Update Account</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
    const editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
    const resetPasswordModal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));

    function reloadUsersTable() {
        if (window.LaravelDataTables && window.LaravelDataTables['usersTable']) {
            window.LaravelDataTables['usersTable'].ajax.reload(null, false);
        } else if ($.fn.DataTable.isDataTable('#usersTable')) {
            $('#usersTable').DataTable().ajax.reload(null, false);
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
                confirmText: 'OK'
            });
        } else {
            alert(`${title}: ${text}`);
        }
    }

    // Open Add User Modal
    document.getElementById('btnOpenAddUser')?.addEventListener('click', function () {
        document.getElementById('addUserForm').reset();
        addUserModal.show();
    });

    // Handle Add User Form AJAX Submit (Checks subscription limits)
    document.getElementById('addUserForm').addEventListener('submit', async function (e) {
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
                addUserModal.hide();
                await notifyAlert('Success!', data.message, 'success');
                window.location.reload();
            } else {
                addUserModal.hide();
                await notifyAlert('Subscription Limit Reached', data.message || 'Subscription user limit reached.', 'warning');
            }
        } catch (err) {
            console.error('Add user error:', err);
            await notifyAlert('Error', 'An error occurred while creating user.', 'danger');
        }
    });

    // Open Edit User Modal
    $(document).on('click', '.btn-edit-user', function () {
        const encId = $(this).data('id');
        const name = $(this).data('name');
        const email = $(this).data('email');
        const role = $(this).data('role') || 'cashier';
        const status = $(this).data('status') || 'active';

        document.getElementById('editName').value = name;
        document.getElementById('editEmail').value = email;
        document.getElementById('editRole').value = role;
        document.getElementById('editStatus').value = status;

        const editForm = document.getElementById('editUserForm');
        editForm.action = "{{ url('users/update') }}/" + encId;

        editUserModal.show();
    });

    // Handle Edit Form AJAX Submit
    document.getElementById('editUserForm').addEventListener('submit', async function (e) {
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
                editUserModal.hide();
                await notifyAlert('Success!', data.message, 'success');
                reloadUsersTable();
            } else {
                await notifyAlert('Error', data.message || 'Unable to update account.', 'danger');
            }
        } catch (err) {
            console.error('Edit user error:', err);
            await notifyAlert('Error', 'An error occurred while updating account.', 'danger');
        }
    });

    // Open Reset Password Modal
    $(document).on('click', '.btn-reset-password', function () {
        const encId = $(this).data('id');
        const name = $(this).data('name');

        document.getElementById('resetUserName').textContent = name;
        const resetForm = document.getElementById('resetPasswordForm');
        resetForm.reset();
        resetForm.action = "{{ url('users/reset-password') }}/" + encId;

        resetPasswordModal.show();
    });

    // Handle Reset Password AJAX Submit
    document.getElementById('resetPasswordForm').addEventListener('submit', async function (e) {
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
                resetPasswordModal.hide();
                await notifyAlert('Password Reset!', data.message, 'success');
            } else {
                await notifyAlert('Error', data.message || 'Unable to reset password.', 'danger');
            }
        } catch (err) {
            console.error('Reset password error:', err);
            await notifyAlert('Error', 'An error occurred while resetting password.', 'danger');
        }
    });

    // Handle Delete User Click
    $(document).on('click', '.btn-delete-user', async function () {
        const encId = $(this).data('id');
        const name = $(this).data('name');
        const deleteUrl = "{{ url('users/delete') }}/" + encId;

        let confirmed = false;
        if (typeof appConfirm === 'function') {
            confirmed = await appConfirm({
                title: `Delete Account "${name}"?`,
                text: 'This user account will be permanently removed from your store user directory.',
                type: 'danger',
                confirmText: 'Delete Account',
                cancelText: 'Cancel'
            });
        } else {
            confirmed = confirm(`Are you sure you want to delete user "${name}"?`);
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
                window.location.reload();
            } else {
                await notifyAlert('Failed', data.message || 'Unable to delete user account.', 'danger');
            }
        } catch (err) {
            console.error('Delete user error:', err);
            await notifyAlert('Error', 'An error occurred while deleting account.', 'danger');
        }
    });
});
</script>
@endpush
@endsection
