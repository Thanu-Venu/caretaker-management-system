let caretakers = [];

async function loadCaretakers() {
    try {
        const response = await fetch('http://localhost/CMA/public/?url=client/getCaretakers');
        const data = await response.json();
        caretakers = data.map(c => ({
            id: c.id,
            name: c.name,
            gender: 'female', // default
            service: c.service_type,
            img: c.profile_image ? `/CMA/public/uploads/${c.profile_image}` : '/CMA/public/images/find.png',
            location: c.location || 'Colombo', // default if not set
            rating: 4.0, // default
            exp: c.experience || '2 Years',
            about: c.qualifications || 'Experienced caregiver.'
        }));
        renderCaretakers(caretakers);
    } catch (error) {
        console.error('Error loading caretakers:', error);
        // fallback to static if needed
    }
}


const listContainer = document.getElementById("caretakersList");

function renderCaretakers(data) {
  listContainer.innerHTML = "";
  if (data.length === 0) {
    listContainer.innerHTML = "<p>No caretakers found.</p>";
    return;
  }

  data.forEach(c => {
  const imgSrc = c.img; // use the dynamic img

  const card = document.createElement("div");
  card.className = "card";
  card.innerHTML = `
      <h3>${c.name}</h3>
      <p>${c.service} Specialist</p>
      <img src="${imgSrc}" alt="${c.name}">

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

loadCaretakers();

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
