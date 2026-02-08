document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("confirmModal");
    const modalText = document.getElementById("modalText");
    const closeBtn = document.querySelector(".modal .close");
    const confirmYes = document.getElementById("confirmYes");
    const confirmNo = document.getElementById("confirmNo");

    let currentForm = null;

    // Open modal on form submit
    document.querySelectorAll(".advance-payment-form").forEach(form => {
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            currentForm = form;
            modal.style.display = "block";
        });
    });

    // Close modal
    closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
        currentForm = null;
    });

    confirmNo.addEventListener("click", () => {
        modal.style.display = "none";
        currentForm = null;
    });

    // Confirm and submit form
    confirmYes.addEventListener("click", () => {
        if (currentForm) {
            currentForm.submit();
            modal.style.display = "none";
        }
    });

    // Close modal if clicking outside
    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
            currentForm = null;
        }
    });
});