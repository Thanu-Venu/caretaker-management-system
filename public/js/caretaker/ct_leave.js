const startInput = document.getElementById("start_date");
const endInput = document.getElementById("end_date");

startInput.addEventListener("change", () => {

    if (!startInput.value) return;

    let start = startInput.value;

    // End date minimum = start date
    endInput.min = start;

    // End date maximum = start date + 27 days
    let maxDate = new Date(start);
    maxDate.setDate(maxDate.getDate() + 27);

    endInput.max = maxDate.toISOString().split("T")[0];

    console.log("End Date Min:", endInput.min);
    console.log("End Date Max:", endInput.max);
});
