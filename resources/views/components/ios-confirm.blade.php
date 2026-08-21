{{-- Reusable Enterprise Confirmation / Alert Modal (Pixel-Perfect Signature UI) --}}
<div id="appConfirmModal" class="app-confirm-overlay" aria-hidden="true" role="dialog">
    <div class="app-confirm-backdrop" id="appConfirmBackdrop"></div>

    <div class="app-confirm-box app-confirm-type-info" id="appConfirmBox">
        <div class="app-confirm-stripe"></div>

        <div class="app-confirm-icon-wrap" id="appConfirmIconWrap">
            <i class="bi bi-info-circle-fill" id="appConfirmIcon"></i>
        </div>

        <h5 class="app-confirm-title" id="appConfirmTitle">Are you sure?</h5>
        <p class="app-confirm-text" id="appConfirmMessage">This action cannot be undone.</p>

        <div class="app-confirm-actions">
            <button type="button" class="app-confirm-btn btn-cancel" id="appConfirmBtnCancel">Cancel</button>
            <button type="button" class="app-confirm-btn btn-confirm" id="appConfirmBtnOk">Confirm</button>
        </div>
    </div>
</div>
