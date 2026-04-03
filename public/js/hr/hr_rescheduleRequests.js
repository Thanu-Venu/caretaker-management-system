function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.remove('active');
            });

            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked button
            event.target.closest('.tab-button').classList.add('active');
        }

function openModal(type, requestId) {
    const modal = document.getElementById('modalOverlay');
    const title = document.getElementById('modalTitle');
    const message = document.getElementById('modalMessage');
    const textarea = document.getElementById('modalTextarea');
    const form = document.getElementById('modalForm');
    const confirmBtn = document.getElementById('confirmBtn');

    document.getElementById('modalRequestId').value = requestId;
    textarea.value = "";

    if (type === 'approve') {
        title.innerText = "Approve Request";
        message.innerText = "Are you sure you want to approve this reschedule request?";
        textarea.placeholder = "Optional note (visible to client)";
        textarea.required = false;
        form.action = "<?= URLROOT ?>/hr/approveReschedule";

        confirmBtn.innerText = "Yes, Approve";
        confirmBtn.className = "confirm-btn approve-style";

    } else {
        title.innerText = "Reject Request";
        message.innerText = "Are you sure you want to reject this request?";
        textarea.placeholder = "Reason for rejection (required)";
        textarea.required = true;
        form.action = "<?= URLROOT ?>/hr/rejectReschedule";

        confirmBtn.innerText = "Yes, Reject";
        confirmBtn.className = "confirm-btn reject-style";
    }

    modal.style.display = "flex";
}

function closeModal() {
    document.getElementById('modalOverlay').style.display = "none";
}

// click outside to close
window.onclick = function(e) {
    const modal = document.getElementById('modalOverlay');
    if (e.target === modal) {
        closeModal();
    }
};