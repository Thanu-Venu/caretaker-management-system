document.addEventListener("DOMContentLoaded", () => {
    const basisSelect = document.getElementById("basis");
    const durationInput = document.getElementById("duration");
    const preferredTimeSelect = document.getElementById("preferredTime");
    const priceSpan = document.getElementById("price");

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

        if (basis && duration > 0 && preferredTime) {
            const base = priceRates[basis] || 0;
            const modifier = timePriceModifier[preferredTime] || 1;
            const total = base * duration * modifier;
            priceSpan.textContent = total.toLocaleString(); // show with commas
        } else {
            priceSpan.textContent = "0";
        }
    }

    basisSelect.addEventListener("change", calculatePrice);
    durationInput.addEventListener("input", calculatePrice);
    preferredTimeSelect.addEventListener("change", calculatePrice);
});
