const basisSelect = document.getElementById("basis");
const durationInput = document.getElementById("duration");

const durationLimits = {
    "Hourly": 23,
    "Daily": 30,
    "Monthly": 11,
    "Yearly": 5 // change if needed
};

basisSelect.addEventListener("change", () => {
    const basis = basisSelect.value;

    if (durationLimits[basis]) {
        durationInput.max = durationLimits[basis];
        durationInput.value = 1; // reset
    } else {
        durationInput.removeAttribute("max");
    }
});

const dateInput = document.getElementById("date");

// Get today
const today = new Date();

// Add 5 days
today.setDate(today.getDate() + 5);

// Format YYYY-MM-DD
const minDate = today.toISOString().split("T")[0];

// Set minimum selectable date
dateInput.setAttribute("min", minDate);

