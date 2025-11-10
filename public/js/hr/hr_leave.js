document.addEventListener("DOMContentLoaded", () => {

  const statusSelect = document.querySelector(".filter-select");
  const applyBtn = document.querySelector(".apply-filters-btn");
  const cancelBtn = document.querySelector(".cancel-filters-btn");
  const tableRows = document.querySelectorAll(".leave-table tbody tr");

  // ✅ Apply Filters
  applyBtn.addEventListener("click", () => {
    const status = statusSelect.value;

    tableRows.forEach(row => {
      const leaveStatus = row.querySelector(".status").textContent.trim();
      if (status === "Select Status" || status === leaveStatus) {
        row.style.display = ""; // show
      } else {
        row.style.display = "none"; // hide
      }
    });
  });

  // ✅ Cancel Filters
  cancelBtn.addEventListener("click", () => {
    statusSelect.selectedIndex = 0; // reset to "Select Status"
    tableRows.forEach(row => (row.style.display = ""));
  });




  // ✅ Approve / Reject / View Actions
  document.querySelectorAll(".approve-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const row = btn.closest("tr");
      const statusCell = row.querySelector(".status");
      statusCell.textContent = "Approved";
      statusCell.className = "status approved";
      row.querySelectorAll(".action-btn").forEach(b => b.remove()); // remove old buttons
      row.cells[5].innerHTML = `<button class="action-btn view-btn">View</button>`;
      attachViewHandler(row.cells[5].querySelector(".view-btn"));
    });
  });

  document.querySelectorAll(".reject-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const row = btn.closest("tr");
      const statusCell = row.querySelector(".status");
      statusCell.textContent = "Rejected";
      statusCell.className = "status rejected";
      row.querySelectorAll(".action-btn").forEach(b => b.remove()); // remove old buttons
      row.cells[5].innerHTML = `<button class="action-btn view-btn">View</button>`;
      attachViewHandler(row.cells[5].querySelector(".view-btn"));
    });
  });

  document.querySelectorAll(".view-btn").forEach(btn => {
    attachViewHandler(btn);
  });

  function attachViewHandler(btn) {
    btn.addEventListener("click", () => {
      const row = btn.closest("tr");
      const caregiver = row.cells[0].textContent;
      const type = row.cells[1].textContent;
      const start = row.cells[2].textContent;
      const end = row.cells[3].textContent;
      const status = row.cells[4].textContent;

      alert(
        `Leave Request Details:\n\nCaregiver: ${caregiver}\nType: ${type}\nStart: ${start}\nEnd: ${end}\nStatus: ${status}`
      );
    });
  }
});
