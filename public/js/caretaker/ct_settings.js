// Profile Picture Preview
const profileImg = document.getElementById('profile_image');
const profileFile = document.getElementById('profileFile');

if (profileFile) {
    profileFile.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                profileImg.src = event.target.result; // show preview
            }
            reader.readAsDataURL(file);
        }
    });
}

// Profile form validation (submit event avoids double-submit from click + native submit)
const editDetailsForm = document.getElementById('edit-details-form');
if (editDetailsForm) {
    editDetailsForm.addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const experience = document.getElementById('experience').value.trim();
        const qualifications = document.getElementById('qualifications').value.trim();

        if (!name || !phone || !experience || !qualifications) {
            e.preventDefault();
            alert('Please fill all required fields!');
        }
    });
}

// Optional: Password Form Validation
const passwordForm = document.getElementById('passwordForm');
if (passwordForm) {
    passwordForm.addEventListener('submit', function(e) {
        const inputs = passwordForm.querySelectorAll('input[type="password"]');
        if (inputs[1].value !== inputs[2].value) {
            e.preventDefault();
            alert("New password and confirm password do not match!");
        }
    });
}

// ----------------------------
// New Code: Auto-hide success message
const successMsg = document.getElementById('successMsg');
if (successMsg) {
    setTimeout(() => {
        successMsg.style.display = 'none';
    }, 3000); // hide after 3 seconds
}
