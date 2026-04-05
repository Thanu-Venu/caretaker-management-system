document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('modalOverlay');
    const modalTitle = document.getElementById('modalTitle');
    const modalText = document.getElementById('modalText');
    const modalReason = document.getElementById('modalReason');
    const modalConfirm = document.getElementById('modalConfirm');
    const modalCancel = document.getElementById('modalCancel');

    let actionType = "";
    let selectedPaymentId = null;

    const appRoot = (() => {
        const parts = window.location.pathname.split('/').filter(Boolean);
        return parts.length > 0 ? `/${parts[0]}` : '';
    })();

    function buildRouteUrl(route) {
        return `${window.location.origin}${appRoot}/public/index.php?url=${route}`;
    }

    // ================= APPROVE =================
    document.querySelectorAll('.open-approve-modal').forEach(btn => {
        btn.addEventListener('click', function () {
            actionType = "approve";
            selectedPaymentId = this.dataset.paymentId;

            modalTitle.innerText = "Approve Payment";
            modalText.innerText = "Are you sure you want to approve this payment?";
            
            modalReason.style.display = "none";
            modalReason.value = "";

            modalConfirm.innerText = "Yes, Approve";
            modalConfirm.className = "btn btn-success";

            modal.style.display = "flex";
        });
    });

    // ================= REJECT =================
    document.querySelectorAll('.open-reject-modal').forEach(btn => {
        btn.addEventListener('click', function () {
            actionType = "reject";
            selectedPaymentId = this.dataset.paymentId;

            modalTitle.innerText = "Reject Payment";
            modalText.innerText = "Are you sure you want to reject this payment?";
            
            modalReason.style.display = "block";
            modalReason.placeholder = "Enter rejection reason (required)";
            modalReason.value = "";

            modalConfirm.innerText = "Yes, Reject";
            modalConfirm.className = "btn btn-danger";

            modal.style.display = "flex";
        });
    });

    // ================= CONFIRM =================
    modalConfirm.addEventListener('click', function () {

        if (actionType === "reject" && modalReason.value.trim() === "") {
            alert("Please enter a reason");
            return;
        }

        const form = document.createElement('form');
        form.method = "POST";

        if (actionType === "approve") {
            form.action = buildRouteUrl('hr/approvePayment');
        } else {
            form.action = buildRouteUrl('hr/rejectPayment');
        }

        form.innerHTML = `
            <input type="hidden" name="payment_id" value="${selectedPaymentId}">
            ${actionType === "reject" ? `<input type="hidden" name="reason" value="${modalReason.value}">` : ""}
        `;

        document.body.appendChild(form);
        form.submit();
    });

    // ================= CANCEL =================
    modalCancel.addEventListener('click', () => {
        modal.style.display = "none";
    });

    // ================= OUTSIDE CLICK =================
    window.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });

});