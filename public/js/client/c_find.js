const caretakers = [
  { id: 1, name: "Sarah Johnson", service: "Elderly Care", img: "public/images/find.png", location: "Jaffna", rating: 5.0, exp: "5 Years", about: "Experienced caregiver specializing in elderly care,offering compassionate support,daily assistance,and ensuring comfort and dignity for seniors." },
  { id: 2, name: "Emily Brown", service: "Child Care", img: "public/images/find.png", location: "Colombo", rating: 4.8, exp: "4 Years", about: "Experienced caregiver specializing in child care,offering compassionate support,daily assistance,and ensuring comfort and dignity for children." },
  { id: 3, name: "John Smith", service: "Child Care", img: "public/images/find-male.jpg", location: "Kandy", rating: 4.2, exp: "6 Years", about: "Experienced caregiver specializing in elderly care,offering compassionate support,daily assistance,and ensuring comfort and dignity for seniors." },
  { id: 4, name: "Maya Silva", service: "Elderly Care", img: "public/images/find.png", location: "Colombo", rating: 3.9, exp: "3 Years", about: "Experienced caregiver specializing in elderly care,offering compassionate support,daily assistance,and ensuring comfort and dignity for seniors." },
  { id: 5, name: "Raj Kumar", service: "Elderly Care", img: "public/images/find-male.jpg", location: "Jaffna", rating: 3.5, exp: "2 Years", about: "Experienced caregiver specializing in elderly care,offering compassionate support,daily assistance,and ensuring comfort and dignity for seniors." },
  { id: 6, name: "Kala Mass", service: "Maid", img: "public/images/find.jpg", location: "Matara", rating: 4.5, exp: "4 Years", about: "Experienced maid specializing in household chores, cleaning, and assisting families with daily routines, ensuring a neat and comfortable home environment." }
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
      <img src="/CMA/public/images/find.png" alt="${c.name}">

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
      const url = `http://localhost/CMA/public/?url=client/c_ctprofileview`;
      window.location.href = url;
  });

  // Book Now button -> go to booking page
 bookBtn.addEventListener("click", (e) => {
  e.stopPropagation();

  // Make sure to use backticks ` ` for template literal
  const url = `http://localhost/CMA/public/?url=client/c_book&id=${c.id}&name=${encodeURIComponent(c.name)}&service=${encodeURIComponent(c.service)}&location=${encodeURIComponent(c.location)}&rating=${c.rating}`;

  window.location.href = url;
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

function renderCaretakerCard(caretaker) {
  return `
    <div class="caretaker-card">
      <h3>${caretaker.name}</h3>
      <p>Service: ${caretaker.service}</p>
      <p>Location: ${caretaker.location}</p>
      <p>Rating: ⭐ ${caretaker.rating}</p>
      <button onclick="bookCaretaker('${caretaker.id}', '${caretaker.service}')">
        Book Now
      </button>
    </div>
  `;
}

function bookCaretaker(id, service) {
  // Redirect to booking page with caretaker id + service
  window.location.href = `${URLROOT}/client/book/${id}?service=${encodeURIComponent(service)}`;
}
