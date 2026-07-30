import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;
document.addEventListener('DOMContentLoaded', () => {

    const STORE_URL = window.schoolYearStoreUrl;

    const form = document.getElementById('schoolYearForm');
    const modalElement = document.getElementById('schoolYearModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);

    const formMethod = document.getElementById('formMethod');
    const modalTitle = document.getElementById('modalTitle');
    const preview = document.getElementById('SchoolYearPreview');

    const ayFrom = document.getElementById('AYFrom');
    const ayTo = document.getElementById('AYTo');
    const startDate = document.getElementById('StartDate');
    const endDate = document.getElementById('EndDate');
    const isActive = document.getElementById('IsActive');

    const resetForm = () => {
        form.reset();

        form.action = STORE_URL;
        formMethod.value = 'POST';

        modalTitle.textContent = 'Add School Year';

        ayTo.value = '';
        preview.textContent = '----';

        isActive.checked = true;
    };

    function updateSchoolYear() {

        const year = parseInt(ayFrom.value);

        if (isNaN(year)) {
            ayTo.value = '';
            preview.textContent = '----';
            return;
        }

        ayTo.value = year + 1;

        preview.textContent = `${year}-${year + 1}`;
    }

    const validate = () => {

        const from = Number(ayFrom.value);
        const to = Number(ayTo.value);

        if (!Number.isInteger(from)) {
            return 'Academic Year From is required.';
        }

        if (to !== from + 1) {
            return 'Academic Year To must be one year after Academic Year From.';
        }

        if (!startDate.value || !endDate.value) {
            return 'Start Date and End Date are required.';
        }

        if (new Date(endDate.value) <= new Date(startDate.value)) {
            return 'End Date must be later than Start Date.';
        }

        return null;
    };

    ayFrom.addEventListener('input', function () {
        console.log('Input:', this.value);
        updateSchoolYear();
    });

    $(document).on('click', '.editBtn', function () {

        const btn = $(this);

        form.action = `/portal/school-years/${btn.data('id')}`;
        formMethod.value = 'PUT';

        modalTitle.textContent = 'Edit School Year';

        ayFrom.value = btn.data('ayfrom');
        ayTo.value = btn.data('ayto');
        startDate.value = btn.data('startdate');
        endDate.value = btn.data('enddate');
        isActive.checked = Number(btn.data('active')) === 1;

        updateSchoolYear();

        modal.show();

    });

    modalElement.addEventListener('hidden.bs.modal', resetForm);

    form.addEventListener('submit', e => {

        const error = validate();

        if (!error) {
            return;
        }

        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Validation',
            text: error
        });

    });

    resetForm();

});
