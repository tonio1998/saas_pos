/**
 * Enterprise Signature Custom Confirmation & Alert Dialog
 * Returns Promise<boolean> for clean async/await usage across all POS & Management views.
 */

const ICON_MAP = {
    info: 'bi-info-circle-fill',
    primary: 'bi-info-circle-fill',
    danger: 'bi-exclamation-triangle-fill',
    warning: 'bi-exclamation-circle-fill',
    success: 'bi-check-circle-fill',
    question: 'bi-question-circle-fill',
};

let currentResolve = null;

export function appConfirm({
    title = 'Are you sure?',
    text = 'This action cannot be undone.',
    type = 'info', // 'info' | 'danger' | 'warning' | 'success' | 'question'
    confirmText = 'Confirm',
    cancelText = 'Cancel',
    showCancel = true,
} = {}) {
    return new Promise((resolve) => {
        const modal = document.getElementById('appConfirmModal');
        const box = document.getElementById('appConfirmBox');
        const titleEl = document.getElementById('appConfirmTitle');
        const messageEl = document.getElementById('appConfirmMessage');
        const iconEl = document.getElementById('appConfirmIcon');
        const btnCancel = document.getElementById('appConfirmBtnCancel');
        const btnOk = document.getElementById('appConfirmBtnOk');

        if (!modal) {
            // Fallback to native window.confirm if component not found
            resolve(window.confirm(`${title}\n\n${text}`));
            return;
        }

        // Clean up previous classes
        box.className = `app-confirm-box app-confirm-type-${type}`;
        titleEl.textContent = title;
        messageEl.textContent = text;
        btnOk.textContent = confirmText;
        btnCancel.textContent = cancelText;

        if (showCancel) {
            btnCancel.style.display = 'inline-flex';
        } else {
            btnCancel.style.display = 'none';
        }

        // Icon
        const iconClass = ICON_MAP[type] || ICON_MAP.info;
        iconEl.className = `bi ${iconClass}`;

        currentResolve = resolve;
        modal.classList.add('show');
    });
}

export function appAlert({
    title = 'Notification',
    text = '',
    type = 'info',
    confirmText = 'Got it',
} = {}) {
    return appConfirm({
        title,
        text,
        type,
        confirmText,
        showCancel: false,
    });
}

// Global attachment
window.appConfirm = appConfirm;
window.appAlert = appAlert;
window.showConfirm = (title, message, callback) => {
    appConfirm({ title, text: message }).then((confirmed) => {
        if (confirmed && typeof callback === 'function') {
            callback();
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('appConfirmModal');
    const backdrop = document.getElementById('appConfirmBackdrop');
    const btnCancel = document.getElementById('appConfirmBtnCancel');
    const btnOk = document.getElementById('appConfirmBtnOk');

    function closeModal(result) {
        if (modal) {
            modal.classList.remove('show');
        }
        if (currentResolve) {
            currentResolve(result);
            currentResolve = null;
        }
    }

    if (btnOk) {
        btnOk.addEventListener('click', () => closeModal(true));
    }
    if (btnCancel) {
        btnCancel.addEventListener('click', () => closeModal(false));
    }
    if (backdrop) {
        backdrop.addEventListener('click', () => closeModal(false));
    }

    // Keyboard ESC key closes modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal && modal.classList.contains('show')) {
            closeModal(false);
        }
    });

    // Auto-bind on any element with data-confirm
    document.addEventListener('click', async (e) => {
        const trigger = e.target.closest('[data-confirm]');
        if (!trigger || trigger.dataset.confirming) return;

        e.preventDefault();
        e.stopPropagation();

        const message = trigger.dataset.confirm || 'Are you sure you want to proceed?';
        const title = trigger.dataset.confirmTitle || 'Please Confirm';
        const type = trigger.dataset.confirmType || 'danger';
        const confirmText = trigger.dataset.confirmBtn || 'Confirm';

        const ok = await appConfirm({ title, text: message, type, confirmText });
        if (ok) {
            trigger.dataset.confirming = 'true';
            if (trigger.tagName === 'FORM') {
                trigger.submit();
            } else if (trigger.tagName === 'A' && trigger.href) {
                window.location.href = trigger.href;
            } else {
                trigger.click();
            }
            delete trigger.dataset.confirming;
        }
    }, true);
});
