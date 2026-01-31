// ===== Price + Availability + Auto-select Alternatives (c_book.js) =====
// REQUIRED in c_book.php before this file:
// <script>const URLROOT="..."; window.SERVICE_TYPE="...";</script>

const basisEl = document.getElementById("basis");
const durationEl = document.getElementById("duration");
const dateEl = document.getElementById("date");
const timeEl = document.getElementById("preferredTime");

const basePriceEl = document.getElementById("basePrice");
const priceEl = document.getElementById("price");
const totalPaymentInput = document.getElementById("total_payment");

const checkBtn = document.getElementById("checkBtn");
const bookBtn = document.getElementById("bookBtn");

const availabilityMsg = document.getElementById("availabilityMsg");
const availabilityBox = document.getElementById("availabilityBox");
const endDateInput = document.getElementById("end_date");

const altWrap = document.getElementById("otherCaretakers");
const altGrid =
  document.getElementById("altGrid") || (altWrap ? altWrap.querySelector(".caretaker-grid") : null);

// caretaker summary elements (need IDs in c_book.php)
const ctNameEl = document.getElementById("ctName");
const ctServiceEl = document.getElementById("ctService");
const ctLocationEl = document.getElementById("ctLocation");
const ctRatingEl = document.getElementById("ctRating");

// hidden inputs (need IDs in c_book.php)
const caretakerIdInput = document.getElementById("caretaker_id") || document.querySelector('input[name="caretaker_id"]');
const serviceTypeInput = document.getElementById("service_type") || document.querySelector('input[name="service_type"]');

function money(n) {
  const num = Number(n || 0);
  return num.toLocaleString("en-LK");
}

function escapeAttr(s) {
  // for safe embedding into data-* attributes
  return String(s ?? "").replace(/&/g, "&amp;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

function getCaretakerId() {
  return caretakerIdInput ? caretakerIdInput.value : "";
}

// ---------- Service normalize ----------
function normalizeServiceType(raw) {
  const s = String(raw || "").trim().toLowerCase();

  if (["elderly care", "elder care", "eldercare"].includes(s)) return "Elder Care";
  if (["babysitter", "baby sitter", "baby sitting", "babysitting"].includes(s)) return "Babysitter";
  if (["maid", "maid service", "maid services"].includes(s)) return "Maid";

  return String(raw || "").trim();
}

function getServiceType() {
  if (typeof window.SERVICE_TYPE !== "undefined" && String(window.SERVICE_TYPE).trim() !== "") {
    return normalizeServiceType(window.SERVICE_TYPE);
  }
  return normalizeServiceType(serviceTypeInput ? serviceTypeInput.value : "");
}

// ---------- Pricing ----------
const servicePriceRates = {
  "Elder Care": { Monthly: 45000, Yearly: 500000 },
  Babysitter: { Daily: 3200, Monthly: 42000, Yearly: 480000 },
  Maid: { Hourly: 500, Daily: 3000, Monthly: 38000, Yearly: 450000 }
};

const timeModifier = {
  "Full Time (8am - 5pm)": 1.0,
  "Morning (8am - 12pm)": 0.6,
  "Evening (1pm - 5pm)": 0.6,
  "Night (6pm - 10pm)": 1.2
};

function getBaseRate(serviceType, basis) {
  const s = normalizeServiceType(serviceType);
  const rates = servicePriceRates[s];
  if (!rates) return 0;
  return Number(rates[basis] || 0);
}

// ---------- 5-days advance rule ----------
function setMinBookingDate() {
  if (!dateEl) return;

  const today = new Date();
  today.setHours(0, 0, 0, 0);

  const min = new Date(today);
  min.setDate(min.getDate() + 5);

  const yyyy = min.getFullYear();
  const mm = String(min.getMonth() + 1).padStart(2, "0");
  const dd = String(min.getDate()).padStart(2, "0");
  const minStr = `${yyyy}-${mm}-${dd}`;

  dateEl.min = minStr;

  if (dateEl.value && dateEl.value < minStr) dateEl.value = "";
}

// ---------- UI helpers ----------
function showAvailability(ok, message, endDate = "") {
  if (availabilityBox) {
    availabilityBox.style.display = "block";
    availabilityBox.className = "availability-box " + (ok ? "ok" : "bad");
    availabilityBox.textContent = message;
  } else if (availabilityMsg) {
    availabilityMsg.textContent = message;
  }

  if (endDateInput) endDateInput.value = endDate || "";
  if (bookBtn) bookBtn.disabled = !ok;
}

function hideAlternatives() {
  if (altWrap) altWrap.style.display = "none";
  if (altGrid) altGrid.innerHTML = "";
}

function resetAvailabilityUI() {
  if (availabilityBox) {
    availabilityBox.style.display = "none";
    availabilityBox.textContent = "";
  }
  if (availabilityMsg) availabilityMsg.textContent = "";
  if (endDateInput) endDateInput.value = "";
  if (bookBtn) bookBtn.disabled = true;
  hideAlternatives();
}

// ✅ Update caretaker summary + hidden fields (auto-fill)
function applySelectedCaretaker(ct) {
  if (!ct || !ct.id) return;

  // update caretaker_id hidden input
  if (caretakerIdInput) caretakerIdInput.value = String(ct.id);

  // update service type hidden + global
  const normService = normalizeServiceType(ct.service_type);
  if (serviceTypeInput) serviceTypeInput.value = normService;
  window.SERVICE_TYPE = normService;

  // update summary UI
  if (ctNameEl) ctNameEl.textContent = ct.name ?? "N/A";
  if (ctServiceEl) ctServiceEl.textContent = normService ?? "N/A";
  if (ctLocationEl) ctLocationEl.textContent = ct.location ?? "N/A";
  if (ctRatingEl) ctRatingEl.textContent = ct.rating ?? "N/A";

  // district should match caretaker district (your form is readonly)
  const districtEl = document.getElementById("district");
  if (districtEl) districtEl.value = ct.location ?? districtEl.value;

  // reset + recalc + auto check
  resetAvailabilityUI();
  calcEstimatedPrice();

  const basis = String(basisEl?.value || "");
  const duration = Number(durationEl?.value || 0);
  const bookingDate = String(dateEl?.value || "");
  if (basis && duration > 0 && bookingDate) {
    checkAvailability();
  }
}

function renderAlternatives(list) {
  if (!altWrap || !altGrid) return;

  altGrid.innerHTML = "";
  altWrap.style.display = "block";

  if (!list || list.length === 0) {
    altGrid.innerHTML =
      `<p style="color:#777;margin:0;">No other caretakers available for this period. Try changing date/basis/duration.</p>`;
    return;
  }

  list.forEach((ct) => {
    const img = ct.profile_image
      ? `${URLROOT}/uploads/${ct.profile_image}`
      : `${URLROOT}/uploads/default.png`;

    altGrid.innerHTML += `
      <div class="caretaker-card">
        <img src="${img}" onerror="this.src='${URLROOT}/uploads/default.png';" alt="${escapeAttr(ct.name)}">
        <h4>${ct.name ?? ""}</h4>
        <p class="small">${ct.service_type ?? ""} • ${ct.location ?? ""}</p>
        <p class="small">⭐ ${ct.rating ?? "N/A"}</p>

        <!-- ✅ Button (no page navigation). Data attributes allow auto-fill -->
        <button
          type="button"
          class="book-alt-btn"
          data-id="${escapeAttr(ct.id)}"
          data-name="${escapeAttr(ct.name)}"
          data-service="${escapeAttr(ct.service_type)}"
          data-location="${escapeAttr(ct.location)}"
          data-rating="${escapeAttr(ct.rating ?? "N/A")}"
          data-img="${escapeAttr(ct.profile_image ?? "")}"
        >
          Select this caretaker
        </button>
      </div>
    `;
  });
}

// ✅ Click handler for alternative caretaker cards (event delegation)
if (altGrid) {
  altGrid.addEventListener("click", (e) => {
    const btn = e.target.closest(".book-alt-btn");
    if (!btn) return;

    const ct = {
      id: btn.dataset.id,
      name: btn.dataset.name,
      service_type: btn.dataset.service,
      location: btn.dataset.location,
      rating: btn.dataset.rating,
      profile_image: btn.dataset.img
    };

    applySelectedCaretaker(ct);
  });
}

// ---------- Price calc ----------
function calcEstimatedPrice() {
  const SERVICE = getServiceType();

  const basis = String(basisEl?.value || "");
  const duration = Number(durationEl?.value || 0);
  const time = String(timeEl?.value || "");

  if (!SERVICE) {
    if (basePriceEl) basePriceEl.textContent = "Service type missing (check hidden input/serviceType)";
    if (priceEl) priceEl.textContent = "0";
    if (totalPaymentInput) totalPaymentInput.value = "0";
    return;
  }

  const base = getBaseRate(SERVICE, basis);
  const mod = timeModifier[time] ?? 1;

  if (!basis || !duration) {
    if (basePriceEl) basePriceEl.textContent = "Select basis & duration to see price";
    if (priceEl) priceEl.textContent = "0";
    if (totalPaymentInput) totalPaymentInput.value = "0";
    return;
  }

  if (!base) {
    const allowed = servicePriceRates[SERVICE] ? Object.keys(servicePriceRates[SERVICE]).join(", ") : "";
    if (basePriceEl) basePriceEl.textContent = `Invalid basis for ${SERVICE}. Allowed: ${allowed}`;
    if (priceEl) priceEl.textContent = "0";
    if (totalPaymentInput) totalPaymentInput.value = "0";
    return;
  }

  if (basePriceEl) basePriceEl.textContent = `LKR ${money(base)} (${basis})`;

  const total = base * duration * mod;

  if (priceEl) priceEl.textContent = money(total);
  if (totalPaymentInput) totalPaymentInput.value = String(total.toFixed(2));
}

// ---------- Availability check ----------
async function checkAvailability() {
  const SERVICE = getServiceType();

  const caretakerId = getCaretakerId();
  const basis = String(basisEl?.value || "");
  const duration = String(durationEl?.value || "");
  const bookingDate = String(dateEl?.value || "");

  if (!caretakerId) return showAvailability(false, "❌ Caretaker ID missing.");
  if (!SERVICE) return showAvailability(false, "❌ Service type missing. (hidden input name=service_type)");
  if (!basis || !duration || !bookingDate) return showAvailability(false, "❌ Please select Basis, Duration and Date first.");

  if (dateEl?.min && bookingDate < dateEl.min) {
    return showAvailability(false, `❌ Bookings must be made at least 5 days in advance. Earliest date is ${dateEl.min}.`);
  }

  // Validate basis allowed for service (client-side)
  const base = getBaseRate(SERVICE, basis);
  if (!base) {
    const allowed = servicePriceRates[SERVICE] ? Object.keys(servicePriceRates[SERVICE]).join(", ") : "";
    return showAvailability(false, `❌ Invalid basis for ${SERVICE}. Allowed: ${allowed}`);
  }

  const formData = new FormData();
  formData.append("caretaker_id", caretakerId);
  formData.append("basis", basis);
  formData.append("duration", duration);
  formData.append("booking_date", bookingDate);

  try {
    const res = await fetch(URLROOT + "/public/?url=Client/checkAvailability", {
      method: "POST",
      headers: { "X-Requested-With": "XMLHttpRequest" },
      body: formData
    });

    const raw = await res.text();
    console.log("RAW RESPONSE:", raw);

    if (!raw) return showAvailability(false, "❌ Empty server response. Check PHP errors / redirects.");

    // if server returned HTML (redirect/login/error)
    if (raw.includes("<html") || raw.includes("<!DOCTYPE")) {
      return showAvailability(false, "❌ Redirected/HTML response (session expired or PHP warning). Please login again or check PHP errors.");
    }

    let data;
    try {
      data = JSON.parse(raw);
    } catch {
      return showAvailability(false, "❌ Server returned invalid JSON. Check console RAW RESPONSE.");
    }

    if (!data.ok) return showAvailability(false, "❌ " + (data.message || "Error"));

    if (data.available) {
      hideAlternatives();
      showAvailability(true, `✅ Available (${data.start} to ${data.end})`, data.end);
    } else {
      showAvailability(false, data.message ? `❌ ${data.message}` : `❌ Not available (${data.start} to ${data.end})`, data.end);
      renderAlternatives(data.alternatives || []);
    }
  } catch (e) {
    console.log("FETCH ERROR:", e);
    showAvailability(false, "❌ Network/route error. Check console + Network tab.");
  }
}

// ---------- Events ----------
[basisEl, durationEl, dateEl].forEach((el) => {
  if (!el) return;
  el.addEventListener("change", resetAvailabilityUI);
  el.addEventListener("input", resetAvailabilityUI);
});

[basisEl, durationEl, timeEl].forEach((el) => {
  if (!el) return;
  el.addEventListener("change", calcEstimatedPrice);
  el.addEventListener("input", calcEstimatedPrice);
});

// Ensure check button never submits form
if (checkBtn) {
  checkBtn.addEventListener("click", (e) => {
    e.preventDefault();
    checkAvailability();
  });
}

// ---------- Init ----------
setMinBookingDate();
calcEstimatedPrice();
resetAvailabilityUI();
