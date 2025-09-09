document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("confirmModal");
    const modalText = document.getElementById("modalText");
    const closeBtn = document.querySelector(".modal .close");
    const confirmYes = document.getElementById("confirmYes");
    const confirmNo = document.getElementById("confirmNo");

    let currentAction = null;
    let currentRow = null;

    // Open modal on button click
    document.querySelectorAll(".approve, .reject").forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            currentRow = e.target.closest("tr");
            currentAction = e.target.classList.contains("approve") ? "approve" : "reject";
            modalText.textContent = `Are you sure you want to ${currentAction} this request?`;
            modal.style.display = "block";
        });
    });

    // Close modal
    closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });
    confirmNo.addEventListener("click", () => {
        modal.style.display = "none";
    });

    // Confirm action
    confirmYes.addEventListener("click", () => {
        if (currentRow) {
            const statusCell = currentRow.querySelector("td:nth-child(5)");
            statusCell.textContent = currentAction === "approve" ? "Approved" : "Rejected";
            modal.style.display = "none";
        }
    });

    // Close modal if clicking outside
    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });
});
