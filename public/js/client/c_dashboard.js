function openEmergencyModal() {
  document.getElementById('emergencyModal').style.display = 'flex';
}

function closeEmergencyModal() {
  document.getElementById('emergencyModal').style.display = 'none';
}

window.addEventListener('click', function (event) {
  const emergencyModal = document.getElementById('emergencyModal');

  if (event.target === emergencyModal) {
    emergencyModal.style.display = 'none';
  }
});

document.addEventListener('keydown', function (event) {
  if (event.key === 'Escape') {
    closeEmergencyModal();
  }
});
   
