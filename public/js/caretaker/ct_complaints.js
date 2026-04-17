document.addEventListener("DOMContentLoaded", function () {
    const clientSelect = document.getElementById("clientName");
    const dateInput = document.getElementById("dateOfService");
    const serviceSelect = document.getElementById("serviceType");
    const complaintForm = document.getElementById("complaintForm");

    if (clientSelect && dateInput && serviceSelect) {
        clientSelect.addEventListener("change", function () {
            const selected = this.options[this.selectedIndex];
            
            console.log('Client selected:', selected.value);
            console.log('Service date from data:', selected.getAttribute("data-booking-date"));
            console.log('Service type from data:', selected.getAttribute("data-service"));

            // Auto-fill date of service
            const serviceDate = selected.getAttribute("data-booking-date") || "";
            if (serviceDate) {
                dateInput.value = serviceDate;
                console.log('Date set to:', serviceDate);
            } else {
                dateInput.value = "";
                console.log('No date available');
            }

            // Auto-fill service type
            const serviceType = selected.getAttribute("data-service") || "";
            if (serviceType) {
                console.log('Setting service type to:', serviceType);
                console.log('Available service type options:');
                for (let i = 0; i < serviceSelect.options.length; i++) {
                    console.log('Option', i, ':', serviceSelect.options[i].value);
                }
                
                // Clear current selection first
                serviceSelect.selectedIndex = 0;
                
                // Try to find exact match
                for (let i = 0; i < serviceSelect.options.length; i++) {
                    if (serviceSelect.options[i].value === serviceType) {
                        serviceSelect.selectedIndex = i;
                        console.log('Found exact match at index:', i);
                        break;
                    }
                }
                
                // If no exact match, try case-insensitive match
                if (serviceSelect.selectedIndex === 0) {
                    for (let i = 0; i < serviceSelect.options.length; i++) {
                        if (serviceSelect.options[i].value.toLowerCase() === serviceType.toLowerCase()) {
                            serviceSelect.selectedIndex = i;
                            console.log('Found case-insensitive match at index:', i);
                            break;
                        }
                    }
                }
                
                // If still no match, try partial match
                if (serviceSelect.selectedIndex === 0) {
                    for (let i = 0; i < serviceSelect.options.length; i++) {
                        if (serviceSelect.options[i].value.toLowerCase().includes(serviceType.toLowerCase()) ||
                            serviceSelect.options[i].text.toLowerCase().includes(serviceType.toLowerCase())) {
                            serviceSelect.selectedIndex = i;
                            console.log('Found partial match at index:', i);
                            break;
                        }
                    }
                }
                
                console.log('Service type dropdown value after setting:', serviceSelect.value);
            } else {
                serviceSelect.selectedIndex = 0;
                console.log('No service type available');
            }
        });
    }

    // Handle form submission
    if (complaintForm) {
        let isSubmitting = false;
        
        complaintForm.addEventListener("submit", function (e) {
            e.preventDefault();
            
            // Prevent multiple submissions
            if (isSubmitting) {
                console.log('Form already submitting...');
                return;
            }
            
            isSubmitting = true;
            
            // Disable submit button to prevent double clicks
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Submitting...';
            }
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'success') {
                    showSuccessPopup();
                    complaintForm.reset();
                } else {
                    showErrorPopup();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorPopup();
            })
            .finally(() => {
                // Re-enable submit button and reset submission flag
                isSubmitting = false;
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Submit Complaint';
                }
            });
        });
    }
});

function showSuccessPopup() {
    const popup = document.getElementById("successPopup");
    if (popup) {
        popup.style.display = "flex";
        popup.classList.add("is-success");
        
        // Auto-hide after 4 seconds
        setTimeout(function () {
            closeSuccessPopup();
        }, 4000);
    }
}

function showErrorPopup() {
    const popup = document.getElementById("successPopup");
    if (popup) {
        popup.style.display = "flex";
        popup.classList.add("is-error");
        popup.querySelector(".complaint-popup__title").textContent = "Error!";
        popup.querySelector(".complaint-popup__message").textContent = "There was an error submitting your complaint. Please try again.";
        
        // Auto-hide after 4 seconds
        setTimeout(function () {
            closeSuccessPopup();
        }, 4000);
    }
}

function closeSuccessPopup() {
    const popup = document.getElementById("successPopup");
    if (popup) {
        popup.style.display = "none";
        popup.classList.remove("is-success", "is-error");
        
        // Reset popup content for success
        popup.querySelector(".complaint-popup__title").textContent = "Success!";
        popup.querySelector(".complaint-popup__message").textContent = "Your complaint has been submitted successfully.";
    }
}