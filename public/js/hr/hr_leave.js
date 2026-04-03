document.addEventListener("DOMContentLoaded", () => {

  console.log("JS Loaded ✅");

  // ✅ Get base URL from PHP
  const app = document.getElementById("leaveApp");
  const rejectBaseUrl = app.dataset.rejectUrl;

  // =========================
  // ✅ FILTER FUNCTION (SAFE)
  // =========================
  const typeSelect = document.getElementById("type");
  const statusSelect = document.getElementById("status");
  const tableRows = document.querySelectorAll("#leaveTable tbody tr");

  if (typeSelect && statusSelect) {
    window.filterTable = function () {
      const typeFilter = typeSelect.value.toLowerCase();
      const statusFilter = statusSelect.value.toLowerCase();

      tableRows.forEach(row => {
        const type = row.cells[1].innerText.toLowerCase();
        const status = row.cells[8].innerText.toLowerCase();

        const typeMatch = typeFilter === "all" || type === typeFilter;
        const statusMatch = statusFilter === "all" || status === statusFilter;

        row.style.display = (typeMatch && statusMatch) ? "" : "none";
      });
    };
  }

  // =========================
  // ✅ VIEW BUTTON HANDLER
  // =========================
  function attachViewHandler(btn) {
    btn.addEventListener("click", () => {
      const row = btn.closest("tr");

      const caregiver = row.cells[0].textContent;
      const type = row.cells[1].textContent;
      const start = row.cells[2].textContent;
      const end = row.cells[3].textContent;
      const status = row.cells[8].textContent;

      alert(
        `Leave Request Details:\n\nCaregiver: ${caregiver}\nType: ${type}\nStart: ${start}\nEnd: ${end}\nStatus: ${status}`
      );
    });
  }

  document.querySelectorAll(".view-btn").forEach(btn => {
    attachViewHandler(btn);
  });

  // =========================
  // ✅ REJECT MODAL LOGIC
  // =========================
  const modal = document.getElementById("rejectModal");
  const confirmBtn = document.getElementById("confirmReject");
  const cancelBtn = document.getElementById("cancelReject");
  const reasonInput = document.getElementById("rejectReason");

  let selectedLeaveId = null;

  // 👉 Open modal
  document.querySelectorAll(".reject-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      selectedLeaveId = btn.dataset.id;
      modal.classList.add("open");
      reasonInput.value = ""; // clear previous input
    });
  });

  // 👉 Cancel modal
  if (cancelBtn) {
    cancelBtn.addEventListener("click", () => {
      modal.classList.remove("open");
    });
  }

  // 👉 Confirm reject
  if (confirmBtn) {
    confirmBtn.addEventListener("click", () => {
      const reason = reasonInput.value.trim();

      if (!reason) {
        alert("Please enter a reason!");
        return;
      }

      if (selectedLeaveId) {
        // Redirect with reason
        window.location.href =
          rejectBaseUrl + selectedLeaveId + "&reason=" + encodeURIComponent(reason);
      }
    });
  }

});