
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("complaintForm");

  form.addEventListener("submit", function (e) {
    // client-side validation before letting the browser submit
    const clientName   = document.getElementById("client_name").value.trim();
    const caretaker    = document.getElementById("caretaker_name").value.trim();
    const serviceDate  = document.getElementById("complaint_date").value.trim();
    const category     = document.getElementById("category").value.trim();
    const description  = document.getElementById("details").value.trim();

    if (!clientName || !caretaker || !serviceDate || !category || !description) {
      // prevent actual POST and show the message
      e.preventDefault();
      alert("plx fill this field");
      return false;
    }

    // If we reach here, allow the normal form submission to the server
    // No e.preventDefault() so the POST goes to controller
  });

  // Check URL params for server redirection feedback
  const params = new URLSearchParams(window.location.search);
  if (params.get('submitted') === '1') {
    alert('✅ Complaint submitted successfully!');
    // remove the query param so refresh doesn't show it again
    params.delete('submitted');
    const newUrl = window.location.pathname + (params.toString() ? ('?' + params.toString()) : '');
    history.replaceState(null, '', newUrl);
  } else if (params.get('error') === '1') {
    // server-side validation failed (missing fields)
    alert('⚠️ Please fill all required fields (server validation).');
    params.delete('error');
    const newUrl = window.location.pathname + (params.toString() ? ('?' + params.toString()) : '');
    history.replaceState(null, '', newUrl);
  } else if (params.get('error') === '2') {
    alert('⚠️ Server error while saving. Please try again later.');
    params.delete('error');
    const newUrl = window.location.pathname + (params.toString() ? ('?' + params.toString()) : '');
    history.replaceState(null, '', newUrl);
  }
});
