function openCancelModal(id) {
    document.getElementById('cancelBookingId').value = id;
    document.getElementById('cancelModal').style.display = 'flex';
}

function closeCancelModal() {
    document.getElementById('cancelModal').style.display = 'none';
}

function openRescheduleModal(id) {
    document.getElementById('rescheduleBookingId').value = id;
    document.getElementById('rescheduleModal').style.display = 'flex';
}

function closeRescheduleModal() {
    document.getElementById('rescheduleModal').style.display = 'none';
}
