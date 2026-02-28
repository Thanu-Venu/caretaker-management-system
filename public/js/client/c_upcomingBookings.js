function openCancelModal(id) {
    document.getElementById('cancelBookingId').value = id;
    document.getElementById('cancelModal').style.display = 'flex';
}

function closeCancelModal() {
    document.getElementById('cancelModal').style.display = 'none';
}

function openRescheduleModal(id) {
    document.getElementById('rescheduleBookingId').value = id;
    const dateInput = document.querySelector('#rescheduleModal input[name="new_date"]');
    if (dateInput) {
        const today = new Date();
        today.setDate(today.getDate() + 1); // 24-hour notice
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        dateInput.min = `${yyyy}-${mm}-${dd}`;
    }
    document.getElementById('rescheduleModal').style.display = 'flex';
}

function closeRescheduleModal() {
    document.getElementById('rescheduleModal').style.display = 'none';
}
