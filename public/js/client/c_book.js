document.addEventListener("DOMContentLoaded", () => {
    const basisSelect = document.getElementById("basis");
    const durationInput = document.getElementById("duration");
    const preferredTimeSelect = document.getElementById("preferredTime");
    const priceSpan = document.getElementById("price");
    const basePriceLabel = document.getElementById("basePrice");
    const basePriceAmount = document.getElementById("basePriceAmount");
    const customizationPriceSpan = document.getElementById("customizationPrice");
    const customizationHoursInput = document.getElementById("customization_hours");
    const totalPaymentInput = document.getElementById("total_payment");

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

    function calculatePrice() {
        const basis = basisSelect.value;
        const duration = parseInt(durationInput.value) || 0;
        const preferredTime = preferredTimeSelect.value;
        const customizationHours = parseInt(customizationHoursInput?.value || "0", 10) || 0;

        if (basis && duration > 0 && preferredTime) {
            const base = priceRates[basis] || 0;
            const modifier = timePriceModifier[preferredTime] || 1;
            const baseCost = base * duration * modifier;
            const customizationFee = Math.max(0, customizationHours) * 300;
            const total = baseCost + customizationFee;
            priceSpan.textContent = total.toLocaleString(); // show with commas
            if (basePriceLabel) basePriceLabel.textContent = baseCost.toLocaleString();
            if (basePriceAmount) basePriceAmount.textContent = baseCost.toLocaleString();
            if (customizationPriceSpan) customizationPriceSpan.textContent = customizationFee.toLocaleString();
            if (totalPaymentInput) totalPaymentInput.value = total.toFixed(2);
        } else {
            priceSpan.textContent = "0";
            if (basePriceLabel) basePriceLabel.textContent = "0";
            if (basePriceAmount) basePriceAmount.textContent = "0";
            if (customizationPriceSpan) customizationPriceSpan.textContent = "0";
            if (totalPaymentInput) totalPaymentInput.value = "0";
        }
    }

    basisSelect.addEventListener("change", calculatePrice);
    durationInput.addEventListener("input", calculatePrice);
    preferredTimeSelect.addEventListener("change", calculatePrice);
    if (customizationHoursInput) {
        customizationHoursInput.addEventListener("input", calculatePrice);
    }
});
