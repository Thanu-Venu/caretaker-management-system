document.addEventListener('DOMContentLoaded', function () {

    // APPROVE FLOW (existing modal)
    const forms = document.querySelectorAll('.confirm-action-form');
    const confirmModal = document.getElementById('actionConfirmModal');
    const confirmText = document.getElementById('confirmModalText');
    const confirmBtn = document.getElementById('confirmModalProceed');
    const cancelBtn = document.getElementById('confirmModalCancel');

    let targetForm = null;

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            targetForm = form;

            confirmText.textContent = "Approve this payment?";
            confirmModal.classList.add('show');
        });
    });

    confirmBtn.addEventListener('click', function () {
        if (targetForm) targetForm.submit();
    });

    cancelBtn.addEventListener('click', () => {
        confirmModal.classList.remove('show');
    });


    // =========================
    // 🚨 REJECT FLOW (NEW)
    // =========================
    const rejectModal = document.getElementById('rejectModal');
    const rejectButtons = document.querySelectorAll('.open-reject-modal');
    const rejectConfirm = document.getElementById('rejectConfirm');
    const rejectCancel = document.getElementById('rejectCancel');
    const reasonInput = document.getElementById('rejectReason');

    let selectedPaymentId = null;

    rejectButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            selectedPaymentId = this.dataset.paymentId;
            reasonInput.value = '';
            rejectModal.classList.add('show');
        });
    });

    rejectConfirm.addEventListener('click', function () {
        const reason = reasonInput.value.trim();

        if (!reason) {
            alert("Please enter a reason");
            return;
        }

        // Create form dynamically
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= URLROOT ?>/hr/rejectPayment';

        form.innerHTML = `
            <input type="hidden" name="payment_id" value="${selectedPaymentId}">
            <input type="hidden" name="reason" value="${reason}">
        `;

        document.body.appendChild(form);
        form.submit();
    });

    rejectCancel.addEventListener('click', () => {
        rejectModal.classList.remove('show');
    });

    rejectModal.addEventListener('click', (e) => {
        if (e.target === rejectModal) {
            rejectModal.classList.remove('show');
        }
    });

});