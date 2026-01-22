// Profile Save
document.getElementById("saveProfile").addEventListener("click", () => {
    const name = document.getElementById("name").value;
    const email = document.getElementById("email").value;
    const phone = document.getElementById("phone").value;
    const exp = document.getElementById("experience").value;
    const qual = document.getElementById("qualifications").value;
    
    // Dummy save alert
    alert(`Profile updated!\nName: ${name}\nEmail: ${email}\nPhone: ${phone}\nExperience: ${exp}\nQualifications: ${qual}`);
});

// Change Password
document.getElementById("passwordForm").addEventListener("submit", function(e){
    e.preventDefault();
    alert("Password updated successfully!");
});

// Save notification settings
document.querySelectorAll(".btn-save").forEach(btn => {
    btn.addEventListener("click", () => {
        alert("Settings saved!");
    });
});

document.getElementById("preferencesForm").addEventListener("submit", function(e){
  e.preventDefault();
  alert("Preferences saved!");
});
