function confirmDelete(id) {
  if (!id) return;
  if (confirm("Delete complaint #" + id + "? This action cannot be undone.")) {
    window.location.href = "<?php echo URLROOT; ?>" + "/public/index.php?url=ComplaintController/delete&id=" + id;
  }
}

document.addEventListener('DOMContentLoaded', function () {
  var form = document.getElementById('complaintForm');
  if (form) {
    form.addEventListener('submit', function (e) {
      var client = document.getElementById('client_name').value.trim();
      var caretaker = document.getElementById('caretaker_name').value.trim();
      var category = document.getElementById('category').value;
      if (!client || !caretaker || !category) {
        e.preventDefault();
        alert('Client name, caretaker name, and category are required.');
      }
    });
  }

  var editForm = document.getElementById('complaintEditForm');
  if (editForm) {
    editForm.addEventListener('submit', function (e) {
      var details = document.getElementById('details').value.trim();
      if (!details) {
        e.preventDefault();
        alert('Details cannot be empty.');
      }
    });
  }
});


function showTab(tabId, event) {
  // Hide all tabs
  document.querySelectorAll(".tab-content").forEach(tab => {
    tab.classList.remove("active");
  });

  // Remove active class from buttons
  document.querySelectorAll(".top button").forEach(btn => {
    btn.classList.remove("active");
  });

  // Show selected tab
  document.getElementById(tabId).classList.add("active");

  // Activate clicked button
  event.target.classList.add("active");
}
