// Cleaned up faulty static mocks

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
