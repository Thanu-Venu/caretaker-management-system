function openEmergencyModal() {
    var el = document.getElementById('emergencyModal');
    if (el) el.classList.add('show');
}

function closeEmergencyModal() {
    var el = document.getElementById('emergencyModal');
    if (el) el.classList.remove('show');
}

window.addEventListener('click', function (event) {
    var emergencyModal = document.getElementById('emergencyModal');
    if (event.target === emergencyModal) {
        closeEmergencyModal();
    }
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeEmergencyModal();
        var adv = document.getElementById('advanceModal');
        if (adv) adv.classList.remove('show');
    }
});
