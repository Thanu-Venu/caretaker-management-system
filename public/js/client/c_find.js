const timeOptions = {
  "Elder Care": [
    "Full Time (8am - 5pm)",
    "Morning (8am - 12pm)",
    "Evening (1pm - 5pm)",
    "Night (6pm - 10pm)",
  ],
  Babysitter: [
    "Full Time (8am - 5pm)",
    "Morning (8am - 12pm)",
    "Evening (1pm - 5pm)",
  ],
  Maid: [
    "Full Time (8am - 5pm)",
    "Morning (8am - 12pm)",
    "Evening (1pm - 5pm)",
  ],
  "Disability Support": [
    "Full Time (8am - 5pm)",
    "Morning (8am - 12pm)",
    "Evening (1pm - 5pm)",
  ],
};

const serviceOptions = {
  "Elder Care": ["Monthly", "Yearly"],
  Babysitter: ["Daily", "Monthly", "Yearly"],
  Maid: ["Hourly", "Daily", "Monthly", "Yearly"]
};

// utility ensures preferredTimeSelect is a <select>; replaces input if present
function ensureTimeSelect() {
  let elem = document.getElementById("preferredTimeSelect");
  if (elem && elem.tagName.toLowerCase() === "select") {
    return elem;
  }
  const container = document.getElementById("timeContainer");
  if (!container) return null;
  const select = document.createElement("select");
  select.name = "preferred_time";
  select.id = "preferredTimeSelect";
  select.required = true;
  container.innerHTML = "";
  container.appendChild(select);
  return select;
}

function updatePopupOptions(service) {
  const timeSelect = ensureTimeSelect();
  const basisSelect = document.getElementById("basisFilter");
  const locSelect = document.getElementById("popupLocationFilter");

  if (timeSelect) {
    timeSelect.innerHTML = '<option value="">Select Time</option>';
    if (service && timeOptions[service]) {
      timeOptions[service].forEach((time) => {
        const option = document.createElement("option");
        option.value = time;
        option.textContent = time;
        timeSelect.appendChild(option);
      });
    }
  }

  if (basisSelect) {
    basisSelect.innerHTML = '<option value="">Select Basis</option>';
    if (service && serviceOptions[service]) {
      serviceOptions[service].forEach((basis) => {
        const option = document.createElement("option");
        option.value = basis;
        option.textContent = basis;
        basisSelect.appendChild(option);
      });
    }
  }

  // disable location options not available for chosen service
  if (locSelect && window.serviceLocations) {
    const allowed = service ? window.serviceLocations[service] || [] : [];
    // Convert allowed locations to lowercase for case-insensitive comparison
    const allowedLower = allowed.map(loc => loc.toLowerCase());
    for (const opt of locSelect.options) {
      if (opt.value === "") continue;
      opt.disabled = service && allowedLower.indexOf(opt.value.toLowerCase()) === -1;
      if (opt.disabled && opt.selected) {
        opt.selected = false;
      }
    }
  }

  const durationInput = document.querySelector('input[name="duration"]');
  if (durationInput) {
    durationInput.value = "";
    durationInput.max = "";
    durationInput.placeholder = "Enter duration";
    durationInput.setAttribute('min', 1);
  }
}

function enforceDurationRange() {
  const durationInput = document.querySelector('input[name="duration"]');
  if (!durationInput) {
    return;
  }

  const min = parseInt(durationInput.min, 10) || 1;
  const max = parseInt(durationInput.max, 10);
  let value = parseInt(durationInput.value, 10);

  if (Number.isNaN(value)) {
    return;
  }

  if (!Number.isNaN(max) && value > max) {
    durationInput.value = max;
  } else if (value < min) {
    durationInput.value = min;
  }
}

function updateDurationLimits(basis) {
  const durationInput = document.querySelector('input[name="duration"]');
  if (!durationInput) {
    return;
  }

  durationInput.setAttribute('min', 1);
  let max = "";

  if (basis === "Hourly") {
    max = 15;
  } else if (basis === "Daily") {
    max = 30;
  } else if (basis === "Monthly") {
    max = 11;
  } else if (basis === "Yearly") {
    max = 5;
  }

  if (max !== "") {
    durationInput.setAttribute('max', max);
    durationInput.placeholder = `Enter 1 - ${max}`;
    if (durationInput.value !== "" && parseInt(durationInput.value, 10) > max) {
      durationInput.value = max;
    }
  } else {
    durationInput.removeAttribute('max');
    durationInput.placeholder = 'Enter duration';
  }
}

window.onload = function () {
  // Set Start Date window: today to today + 4 days
  const startDateInput = document.querySelector('input[name="start_date"]');
  if (!startDateInput) {
    return;
  }
  const today = new Date();

  const minDate = new Date(today);
  minDate.setDate(today.getDate() + 4); // +4 days

  const formatDate = (d) => {
    let month = "" + (d.getMonth() + 1);
    let day = "" + d.getDate();
    const year = d.getFullYear();

    if (month.length < 2) month = "0" + month;
    if (day.length < 2) day = "0" + day;

    return [year, month, day].join("-");
  };

  startDateInput.min = formatDate(minDate);
  startDateInput.max = formatDate(maxDate);
};



// Update Preferred Time & Basis
const popupServiceFilter = document.getElementById("popupServiceFilter");
if (popupServiceFilter) {
  const onServiceChange = function () {
    updatePopupOptions(this.value);
  };
  popupServiceFilter.addEventListener("change", onServiceChange);
  popupServiceFilter.addEventListener("input", onServiceChange);
}

// Limit duration based on Basis
const basisFilter = document.getElementById("basisFilter");
if (basisFilter) {
  basisFilter.addEventListener("change", function () {
    const basis = this.value;
    updateDurationLimits(basis);

    // if maid + hourly, change preferred time options to hourly start times and update label
    const serviceSelect = document.getElementById("popupServiceFilter");
    const service = serviceSelect ? serviceSelect.value : "";
    const labelEl = document.getElementById("preferredTimeLabel");
    let timeSelect = document.getElementById("preferredTimeSelect");
    // if maid/hourly we switch to time input
    if (service === "Maid" && basis === "Hourly") {
      if (labelEl) labelEl.textContent = "Start Time";
      const container = document.getElementById("timeContainer");
      if (container) {
        const input = document.createElement("input");
        input.type = "time";
        input.name = "preferred_time";
        input.id = "preferredTimeSelect";
        input.required = true;
        container.innerHTML = "";
        container.appendChild(input);
        timeSelect = input;
      }
    } else {
      if (labelEl) labelEl.textContent = "Preferred Time";
      // ensure it is a select
      timeSelect = ensureTimeSelect();
      if (timeSelect && service) {
        timeSelect.innerHTML = '<option value="">Select Time</option>';
        if (timeOptions[service]) {
          timeOptions[service].forEach((time) => {
            const option = document.createElement("option");
            option.value = time;
            option.textContent = time;
            timeSelect.appendChild(option);
          });
        }
      }
    }
  });
}

const durationInput = document.querySelector('input[name="duration"]');
if (durationInput) {
  durationInput.addEventListener('input', enforceDurationRange);
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

function ratingInSelectedRange(cardRating, selectedRating) {
  if (isNaN(selectedRating) || selectedRating <= 0) {
    return true;
  }

  const key = selectedRating.toFixed(1);
  if (key === "3.5") {
    return cardRating >= 3.5 && cardRating < 4.0;
  }
  if (key === "4.0") {
    return cardRating >= 4.0 && cardRating < 4.5;
  }
  if (key === "4.5") {
    return cardRating >= 4.5 && cardRating <= 5.0;
  }

  return cardRating >= selectedRating;
}

function experienceInSelectedRange(cardExp, selectedExp) {
  if (isNaN(selectedExp) || selectedExp <= 0) {
    return true;
  }
  const key = String(selectedExp);
  if (key === "1") {
    return cardExp >= 1;
  }
  if (key === "2") {
    return cardExp >= 2;
  }
  if (key === "3") {
    return cardExp >= 3;
  }

  return cardExp >= selectedExp;
}


function applyFilters() {
  const serviceEl = document.getElementById("serviceFilter");
  const locationEl = document.getElementById("locationFilter");
  const expEl = document.getElementById("expFilter");
  const ratingEl = document.getElementById("ratingFilter");

  const service = serviceEl ? serviceEl.value.trim().toLowerCase() : "";
  const location = locationEl ? locationEl.value.trim().toLowerCase() : "";
  const minExperience = expEl ? parseInt(expEl.value) : 0;
  const minRating = ratingEl ? parseFloat(ratingEl.value) : NaN;

  const cards = Array.from(document.querySelectorAll("#caretakersList .caretaker-card"));
  const matchedCards = [];

  cards.forEach((card) => {
    const cardService = (card.dataset.service || "").trim().toLowerCase();
    const cardLocation = (card.dataset.location || "").trim().toLowerCase();
    const cardExperience = parseInt(card.dataset.experience) || 0;
    const cardRating = parseFloat(card.dataset.rating) || 0;

    const serviceMatch = service === "" || cardService === service;
    const locationMatch = location === "" || cardLocation === location;
    const experienceMatch = experienceInSelectedRange(cardExperience, minExperience);
    const ratingMatch = ratingInSelectedRange(cardRating, minRating);

    if (serviceMatch && locationMatch && experienceMatch && ratingMatch) {
      matchedCards.push(card);
    }
  });

  renderCaretakerPagination(cards, matchedCards, true);
}

let caretakerCurrentPage = 1;
const caretakerPageSize = 6;

function renderCaretakerPagination(allCards, matchedCards, resetPage = false) {
  const noCaretakerMessage = document.getElementById("noCaretakerMessage");
  const paginationWrap = document.getElementById("caretakerPagination");
  const prevBtn = document.getElementById("caretakerPrevBtn");
  const nextBtn = document.getElementById("caretakerNextBtn");
  const pageInfo = document.getElementById("caretakerPageInfo");

  if (resetPage) {
    caretakerCurrentPage = 1;
  }

  const totalItems = matchedCards.length;
  const totalPages = Math.max(1, Math.ceil(totalItems / caretakerPageSize));
  if (caretakerCurrentPage > totalPages) {
    caretakerCurrentPage = totalPages;
  }

  const start = (caretakerCurrentPage - 1) * caretakerPageSize;
  const end = start + caretakerPageSize;

  allCards.forEach((card) => {
    card.style.display = "none";
  });

  matchedCards.slice(start, end).forEach((card) => {
    card.style.display = "block";
  });

  if (noCaretakerMessage) {
    if (allCards.length > 0 && totalItems === 0) {
      noCaretakerMessage.classList.remove("hidden");
    } else {
      noCaretakerMessage.classList.add("hidden");
    }
  }

  if (paginationWrap) {
    if (totalItems > caretakerPageSize) {
      paginationWrap.classList.remove("hidden");
    } else {
      paginationWrap.classList.add("hidden");
    }
  }

  if (pageInfo) {
    pageInfo.textContent = `Page ${caretakerCurrentPage} of ${totalPages}`;
  }

  if (prevBtn) {
    prevBtn.disabled = caretakerCurrentPage <= 1;
  }

  if (nextBtn) {
    nextBtn.disabled = caretakerCurrentPage >= totalPages;
  }
}

function changeCaretakerPage(direction) {
  const allCards = Array.from(document.querySelectorAll("#caretakersList .caretaker-card"));
  const visibleFilteredCards = allCards.filter((card) => {
    const serviceEl = document.getElementById("serviceFilter");
    const locationEl = document.getElementById("locationFilter");
    const expEl = document.getElementById("expFilter");
    const ratingEl = document.getElementById("ratingFilter");

    const service = serviceEl ? serviceEl.value.trim().toLowerCase() : "";
    const location = locationEl ? locationEl.value.trim().toLowerCase() : "";
    const minExperience = expEl ? parseInt(expEl.value) : 0;
    const minRating = ratingEl ? parseFloat(ratingEl.value) : NaN;

    const cardService = (card.dataset.service || "").trim().toLowerCase();
    const cardLocation = (card.dataset.location || "").trim().toLowerCase();
    const cardExperience = parseInt(card.dataset.experience) || 0;
    const cardRating = parseFloat(card.dataset.rating) || 0;

    const serviceMatch = service === "" || cardService === service;
    const locationMatch = location === "" || cardLocation === location;
    const experienceMatch = experienceInSelectedRange(cardExperience, minExperience);
    const ratingMatch = ratingInSelectedRange(cardRating, minRating);

    return serviceMatch && locationMatch && experienceMatch && ratingMatch;
  });

  caretakerCurrentPage += direction;
  renderCaretakerPagination(allCards, visibleFilteredCards, false);
}

function clearFilters() {
  const serviceEl = document.getElementById("serviceFilter");
  const locationEl = document.getElementById("locationFilter");
  const expEl = document.getElementById("expFilter");
  const ratingEl = document.getElementById("ratingFilter");

  if (serviceEl) serviceEl.value = "";
  if (locationEl) locationEl.value = "";
  if (expEl) expEl.value = "0";
  if (ratingEl) ratingEl.value = "0";
  applyFilters();
}

const serviceFilter = document.getElementById("serviceFilter");
const locationFilter = document.getElementById("locationFilter");
const expFilter = document.getElementById("expFilter");
const ratingFilter = document.getElementById("ratingFilter");

if (serviceFilter) serviceFilter.addEventListener("change", applyFilters);
if (locationFilter) locationFilter.addEventListener("change", applyFilters);
if (expFilter) expFilter.addEventListener("change", applyFilters);
if (ratingFilter) ratingFilter.addEventListener("change", applyFilters);

document.addEventListener("DOMContentLoaded", function () {
  const popupFormEl = document.querySelector("#searchPopup form");
  if (popupFormEl) {
    popupFormEl.addEventListener("submit", function () {
      const overlayEl = document.getElementById("popupOverlay");
      const popupEl = document.getElementById("searchPopup");
      if (overlayEl) overlayEl.style.display = "none";
      if (popupEl) popupEl.style.display = "none";
    });
  }
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
    form.addEventListener("submit", () => {
      if (overlay) overlay.style.display = "none";
      if (popup) popup.style.display = "none";
    });
  }

  const prevBtn = document.getElementById("caretakerPrevBtn");
  const nextBtn = document.getElementById("caretakerNextBtn");

  if (prevBtn) {
    prevBtn.addEventListener("click", function () {
      changeCaretakerPage(-1);
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener("click", function () {
      changeCaretakerPage(1);
    });
  }

  applyFilters();
});