document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("complaintForm");

  form.addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent form from reloading page

    // Get form values
    const caretaker = document.getElementById("caretaker").value.trim();
    const serviceDate = document.getElementById("serviceDate").value.trim();
    const category = document.getElementById("category").value.trim();
    const description = document.getElementById("description").value.trim();

    // Validation
    if (!caretaker || !serviceDate || !category || !description) {
      alert("⚠️ Please fill in all fields before submitting.");
      return;
    }

    // (Optional) Create a complaint object
    const complaintData = {
      caretaker,
      serviceDate,
      category,
      description,
      submittedAt: new Date().toLocaleString()
    };

    console.log("Complaint submitted:", complaintData);

    // Show success message
    alert("✅ Complaint submitted successfully!");

    // Reset form
    form.reset();
  });
});
