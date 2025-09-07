// Search functionality
const searchInput = document.getElementById("searchInput");
const table = document.getElementById("caretakerTable");
const rows = table.getElementsByTagName("tr");

searchInput.addEventListener("input", function() {
  let filter = searchInput.value.toLowerCase();

  for (let i = 1; i < rows.length; i++) {
    let rowText = rows[i].innerText.toLowerCase();
    if (rowText.includes(filter)) {
      rows[i].style.display = "";
    } else {
      rows[i].style.display = "none";
    }
  }
});
