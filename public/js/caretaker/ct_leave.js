const leaveTypeSelect = document.getElementById("leave_type");
const startInput = document.getElementById("start_date");
const endInput = document.getElementById("end_date");
const startHint = document.getElementById("start_date_hint");
const endHint = document.getElementById("end_date_hint");
const reasonInput = document.querySelector('textarea[name="reason"]');

// Calculate days between two dates
function calculateDays(start, end) {
    if (!start || !end) return 0;
    const startDate = new Date(start);
    const endDate = new Date(end);
    const diffTime = Math.abs(endDate - startDate);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays + 1; // Include both start and end dates
}

// Format date to readable format
function formatDate(dateStr) {
    const date = new Date(dateStr + 'T00:00:00');
    const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// Update minimum start date based on leave type
function updateStartDateMin() {
    const leaveType = leaveTypeSelect.value;
    
    if (leaveType === "Sick Leave") {
        startInput.min = startInput.dataset.minSick;
        startHint.textContent = "🏥 Sick Leave: Available from tomorrow";
    } else if (leaveType) {
        startInput.min = startInput.dataset.minNormal;
        startHint.textContent = "📅 Other Leaves: Available from 5 days onwards";
    } else {
        startInput.min = "";
        startHint.textContent = "";
    }
    
    // Clear dates if they don't meet new minimum
    if (startInput.value && startInput.min && startInput.value < startInput.min) {
        startInput.value = "";
        endInput.value = "";
    }
    
    // Recalculate end date constraints
    if (startInput.value) {
        updateEndDateConstraints();
    }
}

// Update end date constraints based on start date
function updateEndDateConstraints() {
    if (!startInput.value) return;

    let start = startInput.value;

    // End date minimum = start date
    endInput.min = start;

    // End date maximum = start date + 27 days (= 28 days total)
    let maxDate = new Date(start);
    maxDate.setDate(maxDate.getDate() + 27);

    endInput.max = maxDate.toISOString().split("T")[0];

    // Clear end date if it doesn't meet constraints
    if (endInput.value && endInput.value < endInput.min) {
        endInput.value = "";
    } else if (endInput.value && endInput.value > endInput.max) {
        endInput.value = "";
    }

    // Update end date hint with duration and max date
    updateEndDateHint();
}

// Update end date hint and day counter
function updateEndDateHint() {
    const start = startInput.value;
    const end = endInput.value;
    
    if (!start) {
        endHint.textContent = "";
        return;
    }

    const maxEnd = endInput.max;
    const dayCount = end ? calculateDays(start, end) : 0;

    if (end) {
        endHint.innerHTML = `✓ <strong>${dayCount} days</strong> selected (${formatDate(start)} to ${formatDate(end)})`;
    } else {
        endHint.innerHTML = `Maximum 28 days allowed (until ${formatDate(maxEnd)})`;
    }
}

// Update form summary
function updateRequestSummary() {
    const leaveType = leaveTypeSelect.value;
    const start = startInput.value;
    const end = endInput.value;
    const reason = reasonInput.value.trim();
    const dayCount = start && end ? calculateDays(start, end) : 0;

    let summary = `<div style="background: #f0f9ff; border: 1px solid #0284c7; padding: 12px; border-radius: 4px; margin-top: 10px; font-size: 13px;">
        <strong>📋 Leave Request Summary:</strong><br>`;
    
    if (leaveType) summary += `Type: <strong>${leaveType}</strong><br>`;
    if (start) summary += `From: <strong>${formatDate(start)}</strong><br>`;
    if (end) summary += `To: <strong>${formatDate(end)}</strong><br>`;
    if (dayCount > 0) summary += `Duration: <strong>${dayCount} days</strong><br>`;
    if (reason) summary += `Reason: <strong>${reason.substring(0, 50)}${reason.length > 50 ? '...' : ''}</strong><br>`;
    
    summary += `</div>`;

    let summaryContainer = document.getElementById("leave_summary");
    if (!summaryContainer && leaveType && start && end) {
        summaryContainer = document.createElement('div');
        summaryContainer.id = "leave_summary";
        reasonInput.parentElement.appendChild(summaryContainer);
    }

    if (summaryContainer && leaveType && start && end) {
        summaryContainer.innerHTML = summary;
    } else if (summaryContainer) {
        summaryContainer.remove();
    }
}

// Event listeners
startInput.addEventListener("change", () => {
    updateEndDateConstraints();
    updateRequestSummary();
});

endInput.addEventListener("change", () => {
    updateEndDateHint();
    updateRequestSummary();
});

leaveTypeSelect.addEventListener("change", () => {
    updateStartDateMin();
    updateRequestSummary();
});

reasonInput.addEventListener("input", updateRequestSummary);

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
    updateStartDateMin();
    updateEndDateHint();
});
