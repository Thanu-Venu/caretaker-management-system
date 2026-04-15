document.addEventListener("DOMContentLoaded", function () {
    const clientSelect = document.getElementById("clientName");
    const dateInput = document.getElementById("dateOfService");
    const serviceSelect = document.getElementById("serviceType");
    const successMessage = document.getElementById("successMessage");

    // Auto-hide success message after 4 seconds
    if (successMessage) {
        setTimeout(function () {
            successMessage.style.display = "none";
        }, 4000);
    }

    if (clientSelect && dateInput && serviceSelect) {
        clientSelect.addEventListener("change", function () {
            const selected = this.options[this.selectedIndex];

            const serviceDate = selected.getAttribute("data-booking-date") || "";
            dateInput.value = serviceDate;

            const serviceType = selected.getAttribute("data-service") || "";
            serviceSelect.value = serviceType;
        });
    }
});
