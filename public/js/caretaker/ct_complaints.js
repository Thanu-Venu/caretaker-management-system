document.getElementById("clientName").addEventListener("change", function() {
    let selected = this.options[this.selectedIndex];

    // Auto-fill date of service
    let serviceDate = selected.getAttribute("data-booking-date");
    document.getElementById("dateOfService").value = serviceDate;

    // Auto-fill service type
    let serviceType = selected.getAttribute("data-service");
    document.getElementById("serviceType").value = serviceType;
}); 
