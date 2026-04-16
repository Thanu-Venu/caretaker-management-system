function openCancelModal(id) {
    document.getElementById('cancelBookingId').value = id;
    var m = document.getElementById('cancelModal');
    if (m) m.classList.add('show');
}

function closeCancelModal() {
    var m = document.getElementById('cancelModal');
    if (m) m.classList.remove('show');
}

function openRescheduleModal(id) {
    document.getElementById('rescheduleBookingId').value = id;
    const dateInput = document.querySelector('#rescheduleModal input[name="new_date"]');
    if (dateInput) {
        const today = new Date();
        today.setDate(today.getDate() + 5); // 5-day advance notice requirement
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        dateInput.min = `${yyyy}-${mm}-${dd}`;
        dateInput.value = '';
    }
    var m = document.getElementById('rescheduleModal');
    if (m) m.classList.add('show');
}

function closeRescheduleModal() {
    var m = document.getElementById('rescheduleModal');
    if (m) m.classList.remove('show');
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeCancelModal();
        closeRescheduleModal();
    }
});
