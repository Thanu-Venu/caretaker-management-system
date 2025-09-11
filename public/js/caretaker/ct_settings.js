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

// Preview selected profile image
document.getElementById("profileFile").addEventListener("change", function(e){
    const file = e.target.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(event){
            document.getElementById("profileImg").src = event.target.result;
        }
        reader.readAsDataURL(file);
    }
});
