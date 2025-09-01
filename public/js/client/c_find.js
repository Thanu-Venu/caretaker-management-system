const caretakers = [
  { name: "Sarah Johnson", service: "Elderly Care", img: "public\images\find.png", location: "Jaffna", rating: 5.0, exp: "5 Years", about: "Experienced caregiver specializing in elderly care,offering compassionate support,daily assistance,and ensuring comfort and dignity for seniors." },
  { name: "Emily Brown", service: "Child Care", img: "public\images\find.png", location: "Colombo", rating: 4.8, exp: "4 Years", about: "Experienced caregiver specializing in child care,offering compassionate support,daily assistance,and ensuring comfort and dignity for children." },
  { name: "John Smith", service: "Disability Support", img: "public\images\find.png", location: "Kandy", rating: 4.2, exp: "6 Years", about: "Experienced caregiver specializing in elderly care,offering compassionate support,daily assistance,and ensuring comfort and dignity for seniors." },
  { name: "Maya Silva", service: "Elderly Care", img: "public\images\find.png", location: "Colombo", rating: 3.9, exp: "3 Years", about: "Experienced caregiver specializing in elderly care,offering compassionate support,daily assistance,and ensuring comfort and dignity for seniors." },
  { name: "Raj Kumar", service: "Elderly Care", img: "public\images\find.png", location: "Jaffna", rating: 3.5, exp: "2 Years", about: "Experienced caregiver specializing in elderly care,offering compassionate support,daily assistance,and ensuring comfort and dignity for seniors." }
];

const listContainer = document.getElementById("caretakersList");

function renderCaretakers(data) {
  listContainer.innerHTML = "";
  if (data.length === 0) {
    listContainer.innerHTML = "<p>No caretakers found.</p>";
    return;
  }

  data.forEach(c => {
    const card = document.createElement("div");
    card.className = "card";
    card.innerHTML = `
      
      <h3>${c.name}</h3>
      <p>${c.service} Specialist</p>
      <img src="public\images\find.png" alt="${c.name}">

      <div class="exp-loc">
        <p>Exp: ${c.exp}</p>
        <p>Location: ${c.location}</p>
      </div>

      <p class="rating">⭐ ${c.rating}</p>
      <p>${c.about}</p>
      
      <div class="card-buttons">
       <button class="view-btn">View Profile</button>
       <button class="book-btn">Book Now</button>
      </div>

    `;
   
    // Select the buttons individually
  const viewBtn = card.querySelector(".view-btn");
  const bookBtn = card.querySelector(".book-btn");

  // View Profile button -> popup message
  viewBtn.addEventListener("click", (e) => {
    e.stopPropagation(); // prevent event bubbling to card
    alert(`Viewing profile of ${c.name}`);
  });

  // Book Now button -> go to booking page
  bookBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    // Change URL to your booking page
    window.location.href = "c_booking.html"; 
  });

  listContainer.appendChild(card);
});


}

function applyFilters() {
  const service = document.getElementById("serviceFilter").value;
  const location = document.getElementById("locationFilter").value;
  const minRating = parseFloat(document.getElementById("ratingFilter").value);

  const filtered = caretakers.filter(c => {
    return (
      (service === "" || c.service === service) &&
      (location === "" || c.location === location) &&
      (isNaN(minRating) || c.rating >= minRating)
    );
  });

  renderCaretakers(filtered);
}

function clearFilters() {
  document.getElementById("serviceFilter").value = "";
  document.getElementById("locationFilter").value = "";
  document.getElementById("ratingFilter").value = "0";
  renderCaretakers(caretakers);
}

document.getElementById("serviceFilter").addEventListener("change", applyFilters);
document.getElementById("locationFilter").addEventListener("change", applyFilters);
document.getElementById("ratingFilter").addEventListener("change", applyFilters);

renderCaretakers(caretakers);