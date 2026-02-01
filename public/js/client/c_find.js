


function applyFilters() {
  const service = document.getElementById("serviceFilter").value.trim();
  const location = document.getElementById("locationFilter").value.trim();
  const minRating = parseFloat(document.getElementById("ratingFilter").value);

  const cards = document.querySelectorAll(".card");

  cards.forEach(card => {
    const cardService = card.dataset.service.trim();
    const cardLocation = card.dataset.location.trim();
    const cardRating = parseFloat(card.dataset.rating) || 0;

    const serviceMatch = service === "" || cardService === service;
    const locationMatch = location === "" || cardLocation === location;
    const ratingMatch = isNaN(minRating) || cardRating >= minRating;

    if (serviceMatch && locationMatch && ratingMatch) {
      card.style.display = "block";
    } else {
      card.style.display = "none";
    }
  });
}


function clearFilters() {
  document.getElementById("serviceFilter").value = "";
  document.getElementById("locationFilter").value = "";
  document.getElementById("ratingFilter").value = "0";
  applyFilters();
}

document.getElementById("serviceFilter").addEventListener("change", applyFilters);
document.getElementById("locationFilter").addEventListener("change", applyFilters);
document.getElementById("ratingFilter").addEventListener("change", applyFilters);

