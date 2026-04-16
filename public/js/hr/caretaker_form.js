// Function to display error messages as modal popup
function displayError(message) {
  // Create modal HTML if it doesn't exist
  let errorOverlay = document.getElementById('errorOverlay');
  
  if (!errorOverlay) {
    errorOverlay = document.createElement('div');
    errorOverlay.id = 'errorOverlay';
    errorOverlay.className = 'error-overlay';
    errorOverlay.innerHTML = `
      <div class="error-modal">
        <div class="error-modal-header">
          <h2>Error</h2>
          <button type="button" class="error-modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="error-modal-content" id="errorModalContent"></div>
        <div class="error-modal-footer">
          <button type="button" class="error-modal-btn" onclick="closeModal()">OK</button>
        </div>
      </div>
    `;
    document.body.appendChild(errorOverlay);
  }
  
  // Set the error message
  document.getElementById('errorModalContent').textContent = message;
  
  // Show the overlay
  errorOverlay.classList.add('show');
}

// Function to close modal
function closeModal() {
  const errorOverlay = document.getElementById('errorOverlay');
  if (errorOverlay) {
    errorOverlay.classList.remove('show');
  }
}

// Function to clear error messages
function clearError() {
  closeModal();
}

// Form Validation for Add and Edit forms
document.addEventListener('DOMContentLoaded', function() {
  const caretakerForm = document.querySelector('.caretaker-form');
  
  if (caretakerForm) {
    caretakerForm.addEventListener('submit', function(e) {
      // Clear previous errors
      clearError();
      
      // Get form elements
      const name = document.querySelector('[name="name"]').value.trim();
      const email = document.querySelector('[name="email"]').value.trim();
      const phone = document.querySelector('[name="phone"]').value.trim();
      const experience = document.querySelector('[name="experience"]').value.trim();
      const location = document.querySelector('[name="location"]').value.trim();
      const qualifications = document.querySelector('[name="qualifications"]').value.trim();
      const serviceType = document.querySelector('[name="service_type"]').value.trim();
      
      // For add form only
      const passwordField = document.querySelector('[name="password"]');
      const statusField = document.querySelector('[name="status"]');
      
      // Check if all required fields are filled
      let requiredFieldsValid = name && email && phone && experience && location && qualifications && serviceType;
      
      // For add form, check password
      if (passwordField) {
        requiredFieldsValid = requiredFieldsValid && passwordField.value.trim();
      }
      
      // For edit form, check status
      if (statusField) {
        requiredFieldsValid = requiredFieldsValid && statusField.value.trim();
      }
      
      if (!requiredFieldsValid) {
        e.preventDefault();
        displayError('All fields are required. Please fill in all fields');
        return false;
      }

      // Email validation - must end with @gmail.com
      if (!email.endsWith('@gmail.com')) {
        e.preventDefault();
        displayError('Email must end with @gmail.com (e.g., abc@gmail.com)');
        return false;
      }

      // Phone number validation - exactly 10 digits
      const phoneDigits = phone.replace(/\D/g, '');
      if (phoneDigits.length !== 10) {
        e.preventDefault();
        displayError('Phone number must be exactly 10 digits');
        return false;
      }

      // Password strength (add form only; optional script if included)
      if (passwordField) {
        const passwordValue = passwordField.value.trim();
        if (passwordValue.length < 8) {
          e.preventDefault();
          displayError('Password must be at least 8 characters long');
          return false;
        }
        if (!/[A-Z]/.test(passwordValue)) {
          e.preventDefault();
          displayError('Password must include at least one uppercase letter');
          return false;
        }
        if (!/[a-z]/.test(passwordValue)) {
          e.preventDefault();
          displayError('Password must include at least one lowercase letter');
          return false;
        }
        if (!/[0-9]/.test(passwordValue)) {
          e.preventDefault();
          displayError('Password must include at least one number');
          return false;
        }
        if (!/[^A-Za-z0-9]/.test(passwordValue)) {
          e.preventDefault();
          displayError('Password must include at least one special character');
          return false;
        }
      }

      // If all validations pass, allow form submission
      // The form will submit normally to the server
    });
  }
});
