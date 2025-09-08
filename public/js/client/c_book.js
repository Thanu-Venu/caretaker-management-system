document.addEventListener("DOMContentLoaded", () => {
  const serviceType = decodeURIComponent(new URLSearchParams(window.location.search).get("service")) || "";
  const basisSelect = document.getElementById("basis");
  const durationInput = document.getElementById("duration");
  const preferredTimeSelect = document.getElementById("preferredTime");
  const priceSpan = document.getElementById("price");
  const basePriceSpan = document.getElementById("basePrice");
  const availabilityMsg = document.getElementById("availabilityMsg");
  const bookBtn = document.getElementById("bookBtn");
  const bookingDate = document.getElementById("date");
const otherCaretakersContainer = document.getElementById("otherCaretakers");

  const basePrices = {
    "Elderly Care": { "Monthly": 40000, "Yearly": 450000 },
    "Child Care": { "Daily": 3000, "Weekly": 15000, "Monthly": 40000, "Yearly": 450000 },
    "Maid": { "Hourly": 500, "Daily": 3000, "Weekly": 15000, "Monthly": 40000, "Yearly": 450000 }
  };

  const serviceOptions = {
    "Elderly Care": ["Monthly", "Yearly"],
    "Child Care": ["Daily", "Weekly", "Monthly", "Yearly"],
    "Maid": ["Hourly", "Daily", "Weekly", "Monthly", "Yearly"],
    "Disability Support": ["Daily", "Weekly", "Monthly"]
  };

  const maxDuration = {
    "Hourly": 8,
    "Daily": 6,
    "Weekly": 3,
    "Monthly": 11,
    "Yearly": 1
  };

  const placeholderText = {
    "Hourly": "Enter hours",
    "Daily": "Enter days",
    "Weekly": "Enter weeks",
    "Monthly": "Enter months",
    "Yearly": "Enter years"
  };

  const priceRates = {
    "Hourly": 500,
    "Daily": 3000,
    "Weekly": 15000,
    "Monthly": 40000,
    "Yearly": 450000
  };

  const timePriceModifier = {
    "Full Time (8am - 5pm)": 1.0,
    "Morning (8am - 12pm)": 0.6,
    "Evening (1pm - 5pm)": 0.6,
    "Night (6pm - 10pm)": 1.2
  };

  const timeOptions = {
    "Elderly Care": ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)", "Night (6pm - 10pm)"],
    "Child Care": ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)"],
    "Maid": ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)"],
    "Disability Support": ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)"]
  };

  // ---- Initialize Basis Options ----
  if (serviceOptions[serviceType]) {
    basisSelect.innerHTML = '<option value="">Select Basis</option>';
    serviceOptions[serviceType].forEach(opt => {
      const option = document.createElement("option");
      option.value = opt;
      option.textContent = opt;
      basisSelect.appendChild(option);
    });
  }

  // ---- Initialize Preferred Time Options ----
  preferredTimeSelect.innerHTML = '<option value="">Select Time</option>';
  if (timeOptions[serviceType]) {
    timeOptions[serviceType].forEach(time => {
      const option = document.createElement("option");
      option.value = time;
      option.textContent = time;
      preferredTimeSelect.appendChild(option);
    });
  }

  // ---- Show Base Price ----
  function showBasePrice(service) {
    if (!service || !basePrices[service]) {
      basePriceSpan.textContent = "No pricing available";
      return;
    }
    const prices = basePrices[service];
    let text = "";
    for (const basis in prices) {
      text += `${basis}: ${prices[basis]} LKR  `;
    }
    basePriceSpan.textContent = text;
  }
  showBasePrice(serviceType);

  // ---- Update Duration Input based on Basis ----
  basisSelect.addEventListener("change", () => {
    const selectedBasis = basisSelect.value;
    if (selectedBasis) {
      durationInput.placeholder = placeholderText[selectedBasis] || "Enter duration";
      durationInput.max = maxDuration[selectedBasis] || 100;
      durationInput.value = "";
    }
    calculatePrice();
    checkAvailability();
  });

  durationInput.addEventListener("input", () => {
    const max = parseInt(durationInput.max);
    if (parseInt(durationInput.value) > max) durationInput.value = max;
    calculatePrice();
    checkAvailability();
  });

  preferredTimeSelect.addEventListener("change", () => {
    calculatePrice();
    checkAvailability();
  });

  // ---- Price Calculation ----
  function calculatePrice() {
    const basis = basisSelect.value;
    const duration = parseInt(durationInput.value) || 0;
    const preferredTime = preferredTimeSelect.value;

    if (basis && duration > 0) {
      const baseRate = priceRates[basis] || 0;
      const modifier = timePriceModifier[preferredTime] || 1;
      priceSpan.textContent = baseRate * duration * modifier;
    } else {
      priceSpan.textContent = 0;
    }
  }


  // ---- Availability Check (Dummy Example) ----
  function checkAvailability() {
    const basis = basisSelect.value;
    const duration = parseInt(durationInput.value) || 0;
    const time = preferredTimeSelect.value;
    const selectedDate = bookingDate.value;

    if (!basis || duration === 0 || !time || !selectedDate) {
      availabilityMsg.textContent = "";
      bookBtn.disabled = true;
      return;
    }

    // Example: unavailable scenario
    const unavailable = basis === "Daily" && duration > 3 && time === "Morning (8am - 12pm)" && selectedDate === "2024-12-25";

    if (unavailable) {
      availabilityMsg.textContent = "⚠️ Caretaker not available for selected slot.";
      availabilityMsg.style.color = "red";
      bookBtn.disabled = true;
      otherCaretakersContainer.style.display = "block"; 
    } else {
      availabilityMsg.textContent = "✅ Caretaker available!";
      availabilityMsg.style.color = "green";
      bookBtn.disabled = false;
    }
    renderOtherCaretakers(selectedDate, time, serviceType);
  }

const caretakers = [
  { id: 1, name: "Sarah Johnson", service: "Elderly Care", img: "public/images/find.png", location: "Jaffna", rating: 5.0, exp: "5 Years", about: "Experienced caregiver specializing in elderly care,offering compassionate support,daily assistance,and ensuring comfort and dignity for seniors." ,bookedSlots: [{ date: "2025-09-10", time: "Morning (8am - 12pm)" }]},
  { id: 2, name: "Emily Brown", service: "Child Care", img: "public/images/find.png", location: "Colombo", rating: 4.8, exp: "4 Years", about: "Experienced caregiver specializing in child care,offering compassionate support,daily assistance,and ensuring comfort and dignity for children.",bookedSlots: [] },
  { id: 3, name: "John Smith", service: "Child Care", img: "public/images/find-male.jpg", location: "Kandy", rating: 4.2, exp: "6 Years", about: "Experienced caregiver specializing in elderly care,offering compassionate support,daily assistance,and ensuring comfort and dignity for seniors.",bookedSlots: [] },
  { id: 4, name: "Maya Silva", service: "Elderly Care", img: "public/images/find.png", location: "Colombo", rating: 3.9, exp: "3 Years", about: "Experienced caregiver specializing in elderly care,offering compassionate support,daily assistance,and ensuring comfort and dignity for seniors." ,bookedSlots: [{ date: "2025-09-10", time: "Morning (8am - 12pm)" }]},
  { id: 5, name: "Raj Kumar", service: "Elderly Care", img: "public/images/find-male.jpg", location: "Jaffna", rating: 3.5, exp: "2 Years", about: "Experienced caregiver specializing in elderly care,offering compassionate support,daily assistance,and ensuring comfort and dignity for seniors." ,bookedSlots: []},
  { id: 6, name: "Kala Mass", service: "Maid", img: "public/images/find.png", location: "Matara", rating: 4.5, exp: "4 Years", about: "Experienced maid specializing in household chores, cleaning, and assisting families with daily routines, ensuring a neat and comfortable home environment.",bookedSlots: [] }
];
   // ---- Other available caretakers ----
  function getOtherAvailableCaretakers(date, time, service) {
    return caretakers.filter(c =>
      c.service === service &&
      !c.bookedSlots.some(slot => slot.date === date && slot.time === time)
    );
  }

  function renderOtherCaretakers(date, time, service) {
    otherCaretakersContainer.innerHTML = "";
    const availableCaretakers = getOtherAvailableCaretakers(date, time, service);

    if (availableCaretakers.length === 0) {
      otherCaretakersContainer.innerHTML = "<p>No other caretakers available for this slot.</p>";
      return;
    }

    availableCaretakers.forEach(c => {
      const div = document.createElement("div");
      div.className = "caretaker-card";
      div.innerHTML = `
        <h4>${c.name}</h4>
        <p>Service: ${c.service}</p>
        <img src="/CMA/${c.img}" alt="${c.name}" style="width:100%;">
        <p>Location: ${c.location}</p>
        <p>Rating: ${c.rating}</p>
        <p>Exp: ${c.exp}</p>
        <p>About: ${c.about}</p>
        <button onclick="alert('Book caretaker ${c.name} (frontend only)')"><a href="Book</button>
      `;
      otherCaretakersContainer.appendChild(div);
    });
  }

  // ---- Event Listeners ----
  basisSelect.addEventListener("change", () => { calculatePrice(); checkAvailability(); });
  durationInput.addEventListener("input", () => { calculatePrice(); checkAvailability(); });
  preferredTimeSelect.addEventListener("change", () => { calculatePrice(); checkAvailability(); });
  bookingDate.addEventListener("change", checkAvailability);


  // ---- Form Submit ----
  document.getElementById("bookingForm").addEventListener("submit", e => {
    e.preventDefault();
    // Redirect (UI only)
    window.location.href = "http://localhost/CMA/public/?url=client/c_upcomingBookings"; 
    alert("Booking confirmed! (frontend only)");
  });

  

});
