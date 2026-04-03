
function openComplaintModal() {
    document.getElementById('complaintModal').style.display = 'flex';
}
function closeComplaintModal() {
    document.getElementById('complaintModal').style.display = 'none';
}
window.addEventListener('click', function(e) {
    const modal = document.getElementById('complaintModal');
    if (e.target === modal) modal.style.display = 'none';
});
