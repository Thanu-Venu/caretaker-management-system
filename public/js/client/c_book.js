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

  const preferredTimeLabel = document.getElementById("preferredTimeLabel");

  // Use the serviceType from PHP (already set in view script)
  // const serviceType = "...";

  // ✅ NEW: service + basis fixed rates
  const serviceBasisRates = {
    "Elder Care": {
      Monthly: 45000,
      Yearly: 500000,
    },
    Babysitter: {
      Daily: 3200,
      Monthly: 42000,
      Yearly: 480000,
    },
    Maid: {
      Hourly: 500,
      Daily: 3000,
      Monthly: 38000,
      Yearly: 450000,
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
    const basis = (basisSelect?.value || "").trim();
    const duration = Math.max(1, parseInt(durationInput?.value || "1", 10));
    const preferredTime = getPreferredTimeValue();
    const hours = Math.max(0, parseInt(customizationHoursInput?.value || "0", 10));
    const applyMode = (customizationApplySelect?.value || "once").trim();

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
    const basis = (basisSelect?.value || "").trim();
    if (!preferredTimeLabel) return;
    preferredTimeLabel.textContent = basis === "Hourly" ? "Start Time" : "Preferred Time";
  }

  // listeners
  if (basisSelect) basisSelect.addEventListener("change", () => {
    updateTimeLabelForHourly();
    calculatePrice();
  });

  if (durationInput) durationInput.addEventListener("input", calculatePrice);

  if (timeContainer) {
    timeContainer.addEventListener("change", calculatePrice);
    timeContainer.addEventListener("input", calculatePrice);
  }

  if (customizationHoursInput) customizationHoursInput.addEventListener("input", calculatePrice);
  if (customizationApplySelect) customizationApplySelect.addEventListener("change", calculatePrice);

  // initial compute
  updateTimeLabelForHourly();
  calculatePrice();
});