const timeOptions = {
    "Elder Care": ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)", "Night (6pm - 10pm)"],
    "Babysitter": ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)"],
    "Maid": ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)"],
    "Disability Support": ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)"]
};

const serviceOptions = {
    "Elder Care": ["Monthly", "Yearly"],
    "Babysitter": ["Daily", "Monthly", "Yearly"],
    "Maid": ["Hourly", "Daily", "Monthly", "Yearly"],
    "Disability Support": ["Daily", "Monthly"]
};

function updatePopupOptions(service) {
    const timeSelect = document.getElementById('preferredTimeSelect');
    const basisSelect = document.getElementById('basisFilter');

    if (timeSelect) {
        timeSelect.innerHTML = '<option value="">Select Time</option>';
        if(service && timeOptions[service]) {
            timeOptions[service].forEach(time => {
                const option = document.createElement('option');
                option.value = time;
                option.textContent = time;
                timeSelect.appendChild(option);
            });
        }
    }

    if (basisSelect) {
        basisSelect.innerHTML = '<option value="">Select Basis</option>';
        if(service && serviceOptions[service]) {
            serviceOptions[service].forEach(basis => {
                const option = document.createElement('option');
                option.value = basis;
                option.textContent = basis;
                basisSelect.appendChild(option);
            });
        }
    }

    const durationInput = document.querySelector('input[name="duration"]');
    if (durationInput) {
        durationInput.value = '';
        durationInput.max = '';
        durationInput.placeholder = "Enter duration";
    }
}



window.onload = function() {

    // Set Start Date: min = today + 4 days
    const startDateInput = document.querySelector('input[name="start_date"]');
    if (!startDateInput) {
        return;
    }
    const today = new Date();

    const minDate = new Date(today);
    minDate.setDate(today.getDate() + 4); // +4 days

    const formatDate = (d) => {
        let month = '' + (d.getMonth() + 1);
        let day = '' + d.getDate();
        const year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    };

    startDateInput.min = formatDate(minDate);
};



const popupFormEl = document.querySelector('#searchPopup form');
if (popupFormEl) {
    popupFormEl.addEventListener('submit', function() {
        const overlayEl = document.getElementById('popupOverlay');
        const popupEl = document.getElementById('searchPopup');
        if (overlayEl) overlayEl.style.display = 'none';
        if (popupEl) popupEl.style.display = 'none';
    });
}


// Update Preferred Time & Basis
const popupServiceFilter = document.getElementById('popupServiceFilter');
if (popupServiceFilter) {
    const onServiceChange = function() {
        updatePopupOptions(this.value);
    };
    popupServiceFilter.addEventListener('change', onServiceChange);
    popupServiceFilter.addEventListener('input', onServiceChange);
}

// Limit duration based on Basis
const basisFilter = document.getElementById('basisFilter');
if (basisFilter) {
    basisFilter.addEventListener('change', function() {
        const basis = this.value;
        const durationInput = document.querySelector('input[name="duration"]');
        if (!durationInput) {
            return;
        }

    if(basis === "Hourly") {
        durationInput.max = 23;
    } else if(basis === "Daily") {
        durationInput.max = 30;
    } else if(basis === "Monthly") {
        durationInput.max = 11;
    } else if(basis === "Yearly") {
        durationInput.max = 5; // optional
    }

        durationInput.placeholder = `Enter 1 - ${durationInput.max}`;
    });
}


const serviceFilter = document.getElementById('serviceFilter');
if (serviceFilter) {
    serviceFilter.addEventListener('change', function() {
        const service = this.value;
        const timeSelect = document.getElementById('preferredTimeSelect');
        if (!timeSelect) {
            return;
        }
        timeSelect.innerHTML = '<option value="">Select Time</option>'; // reset

        if(service && timeOptions[service]) {
            timeOptions[service].forEach(time => {
                const option = document.createElement('option');
                option.value = time;
                option.textContent = time;
                timeSelect.appendChild(option);
            });
        }
    });
}

const cancelBtn = document.getElementById("cancelPopupBtn");
const popup = document.getElementById("searchPopup");
const overlay = document.getElementById("popupOverlay");

if (cancelBtn && popup && overlay) {
    cancelBtn.addEventListener("click", () => {
        popup.style.display = "none";
        overlay.style.display = "none";
    });
}



function applyFilters() {
    const serviceEl = document.getElementById("serviceFilter");
    const locationEl = document.getElementById("locationFilter");
    const ratingEl = document.getElementById("ratingFilter");

    const service = serviceEl ? serviceEl.value.trim().toLowerCase() : "";
    const location = locationEl ? locationEl.value.trim().toLowerCase() : "";
    const minRating = ratingEl ? parseFloat(ratingEl.value) : NaN;

  const cards = document.querySelectorAll(".card");

  cards.forEach(card => {
    const cardService = (card.dataset.service || "").trim().toLowerCase();
    const cardLocation = (card.dataset.location || "").trim().toLowerCase();
    const cardRating = parseFloat(card.dataset.rating) || 0;

    const serviceMatch = service === "" || cardService === service;
    const locationMatch = location === "" || cardLocation === location;
    const ratingMatch = isNaN(minRating) || cardRating >= minRating;

    if (serviceMatch && locationMatch && ratingMatch) {
      card.style.display = "flex";
      card.style.flexDirection = "column";
      card.style.minHeight = "550px";
    } else {
      card.style.display = "none";
    }
  });
}


function clearFilters() {
    const serviceEl = document.getElementById("serviceFilter");
    const locationEl = document.getElementById("locationFilter");
    const ratingEl = document.getElementById("ratingFilter");

    if (serviceEl) serviceEl.value = "";
    if (locationEl) locationEl.value = "";
    if (ratingEl) ratingEl.value = "0";
  applyFilters();
}

const locationFilter = document.getElementById("locationFilter");
const ratingFilter = document.getElementById("ratingFilter");

if (serviceFilter) serviceFilter.addEventListener("change", applyFilters);
if (locationFilter) locationFilter.addEventListener("change", applyFilters);
if (ratingFilter) ratingFilter.addEventListener("change", applyFilters);

document.addEventListener("DOMContentLoaded", function () {

    const openBtn = document.getElementById("openPopupBtn");
    const overlay = document.getElementById("popupOverlay");
    const popup = document.getElementById("searchPopup");
    const form = document.getElementById("popupForm");

    if (openBtn && overlay && popup) {
        // 🔹 OPEN popup on button click
        openBtn.addEventListener("click", function () {
            overlay.style.display = "block";
            popup.style.display = "block";
            if (popupServiceFilter) {
                updatePopupOptions(popupServiceFilter.value);
            }
        });

        // 🔹 CLOSE popup when clicking overlay
        overlay.addEventListener("click", function () {
            overlay.style.display = "none";
            popup.style.display = "none";
        });
    }

    if (form) {
        form.addEventListener('submit', () => {
            if (overlay) overlay.style.display = 'none';
            if (popup) popup.style.display = 'none';
        });
    }
});



