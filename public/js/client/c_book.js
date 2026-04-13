document.addEventListener("DOMContentLoaded", () => {
  const basisSelect = document.getElementById("basis");
  const durationInput = document.getElementById("duration");
  const timeContainer = document.getElementById("timeContainer");

  const basisHidden = document.getElementById("basis_hidden");
  const preferredHidden = document.getElementById("preferred_time_hidden");

  const basePriceLabel = document.getElementById("basePrice"); // top label
  const basePriceAmount = document.getElementById("basePriceAmount"); // base cost
  const customizationPriceSpan = document.getElementById("customizationPrice");
  const customizationHoursInput = document.getElementById("customization_hours");
  const customizationApplySelect = document.getElementById("customization_apply");

  const priceSpan = document.getElementById("price");
  const totalPaymentInput = document.getElementById("total_payment");

  const dateInput = document.getElementById("date");
  const shouldLockPrefilled = typeof lockPrefilledFields !== "undefined" ? !!lockPrefilledFields : true;

  const preferredTimeLabel = document.getElementById("preferredTimeLabel");

  function normalizeBasis(value) {
    const v = (value || "").trim().toLowerCase();
    if (v === "hourly") return "Hourly";
    if (v === "daily") return "Daily";
    if (v === "monthly") return "Monthly";
    if (v === "yearly") return "Yearly";
    return (value || "").trim();
  }

  // Use the serviceType from PHP (already set in view script)
  // const serviceType = "...";

  // ✅ NEW: service + basis fixed rates
  const serviceBasisRates = {
    "Elder Care": {
      Monthly: 50000,
      Yearly: 550000,
    },
    Babysitter: {
      Daily: 2200,
      Monthly: 45000,
      Yearly: 500000,
    },
    Maid: {
      Hourly: 500,
      Daily: 2000,
      Monthly: 38000,
      Yearly: 420000,
    },
  };

  const timePriceModifier = {
    "Full Time (8am - 5pm)": 1.0,
    "Morning (8am - 12pm)": 0.6,
    "Evening (1pm - 5pm)": 0.6,
    "Night (6pm - 10pm)": 1.2,
  };

  function getPreferredTimeValue() {
    const el = document.getElementById("preferredTime");
    if (!el) return "";
    return (el.value || "").trim();
  }

  function calcCustomizationMultiplier(basis, duration, applyMode) {
    if (applyMode !== "per_unit") return 1;

    switch (basis) {
      case "Hourly":
        return 1;
      case "Daily":
        return duration; // extra hours charged per day
      case "Monthly":
        return duration * 30; // monthly treated as 30 days
      case "Yearly":
        return duration * 365;
      default:
        return 1;
    }
  }

  function formatLKR(n) {
    const num = Number(n) || 0;
    return num.toLocaleString(undefined, { maximumFractionDigits: 0 });
  }

  function calculatePrice() {
    const basis = normalizeBasis(basisSelect?.value || "");
    const duration = Math.max(1, parseInt(durationInput?.value || "1", 10));
    const preferredTime = getPreferredTimeValue();
    const hours = Math.max(0, parseInt(customizationHoursInput?.value || "0", 10));
    const applyMode = (customizationApplySelect?.value || "per_unit").trim();

    // keep hidden fields synced (important because selects are disabled)
    if (basisHidden) basisHidden.value = basis;
    if (preferredHidden) preferredHidden.value = preferredTime;

    const rate = serviceBasisRates?.[serviceType]?.[basis] ?? 0;

    // modifier works only if preferredTime is one of the labels; otherwise fallback 1
    const modifier = timePriceModifier[preferredTime] ?? 1.0;

    const baseCost = rate * duration * modifier;

    const mult = calcCustomizationMultiplier(basis, duration, applyMode);
    const customizationFee = hours * 300 * mult;

    const total = baseCost + customizationFee;

    // UI updates
   if (basePriceLabel) {
  basePriceLabel.textContent = rate > 0 ? `LKR ${formatLKR(rate)} / ${basis}` : "Select a basis to see price";
}
    if (basePriceAmount) basePriceAmount.textContent = formatLKR(baseCost);
    if (customizationPriceSpan) customizationPriceSpan.textContent = formatLKR(customizationFee);
    if (priceSpan) priceSpan.textContent = formatLKR(total);
    if (totalPaymentInput) totalPaymentInput.value = total.toFixed(2);
  }

  // If your Hourly flow uses <input type="time">, keep modifier=1 automatically.
  // Optional: show label "Start Time" for Hourly only.
  function updateTimeLabelForHourly() {
    const basis = normalizeBasis(basisSelect?.value || "");
    if (!preferredTimeLabel) return;
    preferredTimeLabel.textContent = basis === "Hourly" ? "Start Time" : "Preferred Time";

    // Switch between select (for non-Hourly) and time input (for Hourly)
    const timeContainer = document.getElementById("timeContainer");
    if (!timeContainer) return;

    if (basis === "Hourly") {
      // Show time input for Hourly bookings
      timeContainer.innerHTML = `
        <input type="time" id="preferredTime" required
               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
      `;
      // Set default time if available
      const prefillTime = preferredHidden?.value || "09:00";
      const timeInput = document.getElementById("preferredTime");
      if (timeInput) {
        if (shouldLockPrefilled) timeInput.disabled = true;
        timeInput.value = prefillTime;
        timeInput.addEventListener("input", () => {
          if (preferredHidden) preferredHidden.value = timeInput.value;
          calculatePrice();
        });
        // Initial sync
        if (preferredHidden) preferredHidden.value = prefillTime;
      }
    } else {
      // Show select dropdown for non-Hourly bookings (keep options in sync with client/c_book.php $timeOptions)
      const currentValue = preferredHidden?.value || "";
      const nightOption =
        serviceType === "Elder Care" || serviceType === "Maid"
          ? `<option value="Night (6pm - 10pm)" ${currentValue === "Night (6pm - 10pm)" ? "selected" : ""}>Night (6pm - 10pm)</option>`
          : "";
      timeContainer.innerHTML = `
        <select id="preferredTime" required>
          <option value="">Select Time</option>
          <option value="Full Time (8am - 5pm)" ${currentValue === "Full Time (8am - 5pm)" ? "selected" : ""}>Full Time (8am - 5pm)</option>
          <option value="Morning (8am - 12pm)" ${currentValue === "Morning (8am - 12pm)" ? "selected" : ""}>Morning (8am - 12pm)</option>
          <option value="Evening (1pm - 5pm)" ${currentValue === "Evening (1pm - 5pm)" ? "selected" : ""}>Evening (1pm - 5pm)</option>
          ${nightOption}
        </select>
      `;
      const selectElem = document.getElementById("preferredTime");
      if (selectElem) {
        if (shouldLockPrefilled) selectElem.disabled = true;
        selectElem.addEventListener("change", () => {
          if (preferredHidden) preferredHidden.value = selectElem.value;
          calculatePrice();
        });
        // Initial sync
        if (preferredHidden) preferredHidden.value = currentValue;
      }
    }

    calculatePrice();
  }

  // listeners
  if (basisSelect) {
    basisSelect.addEventListener("change", () => {
      // Sync hidden field
      if (basisHidden) basisHidden.value = basisSelect.value;
      updateTimeLabelForHourly();
      calculatePrice();
    });
  }

  if (durationInput) durationInput.addEventListener("input", calculatePrice);

  if (timeContainer) {
    timeContainer.addEventListener("change", calculatePrice);
    timeContainer.addEventListener("input", calculatePrice);
  }

  if (customizationHoursInput) customizationHoursInput.addEventListener("input", calculatePrice);
  if (customizationApplySelect) customizationApplySelect.addEventListener("change", calculatePrice);

  if (shouldLockPrefilled) {
    if (basisSelect) basisSelect.disabled = true;
    if (durationInput) durationInput.readOnly = true;
    if (dateInput) dateInput.readOnly = true;
  }

  // initial compute
  updateTimeLabelForHourly();
  calculatePrice();
});