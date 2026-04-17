document.addEventListener("DOMContentLoaded", function () {
    const bookingSelect = document.getElementById("clientBooking");
    const dateHidden = document.getElementById("dateOfService");
    const complaintForm = document.getElementById("complaintForm");
    const periodHint = document.getElementById("complaintPeriodHint");
    const calendarWrap = document.getElementById("complaintCalendarWrap");
    const calGrid = document.getElementById("complaintCalGrid");
    const calTitle = document.getElementById("complaintCalTitle");
    const calPrev = document.getElementById("complaintCalPrev");
    const calNext = document.getElementById("complaintCalNext");
    const selectedLabel = document.getElementById("complaintSelectedDateLabel");

    let rangeStartStr = "";
    let rangeEndStr = "";
    let viewYear = new Date().getFullYear();
    let viewMonth = new Date().getMonth();
    let selectedIso = "";

    function pad2(n) {
        return String(n).padStart(2, "0");
    }

    function formatYMD(d) {
        return d.getFullYear() + "-" + pad2(d.getMonth() + 1) + "-" + pad2(d.getDate());
    }

    function parseYMD(s) {
        const p = (s || "").split("-").map(Number);
        if (p.length !== 3 || p.some((x) => !Number.isFinite(x))) return null;
        return new Date(p[0], p[1] - 1, p[2]);
    }

    function inRange(iso) {
        return iso && iso >= rangeStartStr && iso <= rangeEndStr;
    }

    function monthTuple(y, m) {
        return y * 12 + m;
    }

    function syncNavDisabled() {
        if (!rangeStartStr || !rangeEndStr) return;
        const rs = parseYMD(rangeStartStr);
        const re = parseYMD(rangeEndStr);
        if (!rs || !re) return;
        const minT = monthTuple(rs.getFullYear(), rs.getMonth());
        const maxT = monthTuple(re.getFullYear(), re.getMonth());
        const curT = monthTuple(viewYear, viewMonth);
        if (calPrev) calPrev.disabled = curT <= minT;
        if (calNext) calNext.disabled = curT >= maxT;
    }

    function renderCalendar() {
        if (!calGrid || !calTitle) return;
        calGrid.innerHTML = "";

        const monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December",
        ];
        calTitle.textContent = monthNames[viewMonth] + " " + viewYear;

        const first = new Date(viewYear, viewMonth, 1);
        const startWeekday = first.getDay();
        const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

        for (let i = 0; i < startWeekday; i++) {
            const pad = document.createElement("div");
            pad.className = "complaint-cal-cell complaint-cal-cell--pad";
            calGrid.appendChild(pad);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const d = new Date(viewYear, viewMonth, day);
            const iso = formatYMD(d);
            const cell = document.createElement("button");
            cell.type = "button";
            cell.className = "complaint-cal-day";
            cell.textContent = String(day);
            cell.setAttribute("aria-label", iso);

            if (!inRange(iso)) {
                cell.classList.add("complaint-cal-day--outside");
                cell.disabled = true;
            } else {
                cell.classList.add("complaint-cal-day--inrange");
                if (iso === selectedIso) {
                    cell.classList.add("complaint-cal-day--selected");
                }
                cell.addEventListener("click", function () {
                    selectedIso = iso;
                    if (dateHidden) dateHidden.value = iso;
                    if (selectedLabel) {
                        selectedLabel.textContent = d.toLocaleDateString(undefined, {
                            weekday: "short",
                            year: "numeric",
                            month: "short",
                            day: "numeric",
                        });
                    }
                    renderCalendar();
                });
            }
            calGrid.appendChild(cell);
        }

        syncNavDisabled();
    }

    function showBookingCalendar(startStr, endStr) {
        rangeStartStr = startStr;
        rangeEndStr = endStr;
        const rs = parseYMD(rangeStartStr);
        if (!rs) {
            hideBookingCalendar();
            return;
        }
        viewYear = rs.getFullYear();
        viewMonth = rs.getMonth();
        selectedIso = rangeStartStr;
        if (dateHidden) dateHidden.value = selectedIso;
        if (selectedLabel) {
            selectedLabel.textContent = rs.toLocaleDateString(undefined, {
                weekday: "short",
                year: "numeric",
                month: "short",
                day: "numeric",
            });
        }
        if (periodHint) {
            const re = parseYMD(rangeEndStr);
            const a = rs.toLocaleDateString(undefined, { month: "short", day: "numeric", year: "numeric" });
            const b = re
                ? re.toLocaleDateString(undefined, { month: "short", day: "numeric", year: "numeric" })
                : a;
            periodHint.textContent =
                rangeStartStr === rangeEndStr
                    ? "This booking covers " + a + ". That day is selected; change it below if needed."
                    : "Booking period: " + a + " – " + b + ". Highlighted days are in range — click one to set the complaint date.";
        }
        if (calendarWrap) calendarWrap.hidden = false;
        renderCalendar();
    }

    function hideBookingCalendar() {
        rangeStartStr = "";
        rangeEndStr = "";
        selectedIso = "";
        if (dateHidden) dateHidden.value = "";
        if (calendarWrap) calendarWrap.hidden = true;
        if (periodHint) periodHint.textContent = "Select a booking to see the service period on the calendar.";
        if (selectedLabel) selectedLabel.textContent = "—";
        if (calGrid) calGrid.innerHTML = "";
    }

    if (bookingSelect && dateHidden) {
        bookingSelect.addEventListener("change", function () {
            const opt = this.options[this.selectedIndex];
            if (!opt || !opt.value) {
                hideBookingCalendar();
                return;
            }
            const s = opt.getAttribute("data-booking-start") || "";
            const e = opt.getAttribute("data-booking-end") || s;
            showBookingCalendar(s, e || s);
        });
    }

    if (calPrev) {
        calPrev.addEventListener("click", function () {
            if (viewMonth === 0) {
                viewYear -= 1;
                viewMonth = 11;
            } else {
                viewMonth -= 1;
            }
            renderCalendar();
        });
    }
    if (calNext) {
        calNext.addEventListener("click", function () {
            if (viewMonth === 11) {
                viewYear += 1;
                viewMonth = 0;
            } else {
                viewMonth += 1;
            }
            renderCalendar();
        });
    }

    if (complaintForm) {
        let isSubmitting = false;

        complaintForm.addEventListener("submit", function (e) {
            if (!bookingSelect || !bookingSelect.value) {
                e.preventDefault();
                return;
            }
            if (!dateHidden || !dateHidden.value) {
                e.preventDefault();
                if (periodHint) periodHint.textContent = "Choose a day within the booking period on the calendar.";
                return;
            }

            e.preventDefault();

            if (isSubmitting) {
                return;
            }

            isSubmitting = true;

            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = "Submitting...";
            }

            const formData = new FormData(this);

            fetch(this.action, {
                method: "POST",
                body: formData,
                credentials: "same-origin",
            })
                .then(function (response) {
                    return response.text();
                })
                .then(function (data) {
                    if (data.trim() === "success") {
                        showSuccessPopup();
                        complaintForm.reset();
                        const hiddenSvc = document.getElementById("caretakerServiceType");
                        const fixed = complaintForm.getAttribute("data-caretaker-service") || "";
                        if (hiddenSvc && fixed) {
                            hiddenSvc.value = fixed;
                        }
                        hideBookingCalendar();
                    } else {
                        showErrorPopup();
                    }
                })
                .catch(function (err) {
                    console.error("Error:", err);
                    showErrorPopup();
                })
                .finally(function () {
                    isSubmitting = false;
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = "Submit Complaint";
                    }
                });
        });
    }
});

function showSuccessPopup() {
    const popup = document.getElementById("successPopup");
    if (popup) {
        popup.style.display = "flex";
        popup.classList.add("is-success");

        setTimeout(function () {
            closeSuccessPopup();
        }, 4000);
    }
}

function showErrorPopup() {
    const popup = document.getElementById("successPopup");
    if (popup) {
        popup.style.display = "flex";
        popup.classList.add("is-error");
        popup.querySelector(".complaint-popup__title").textContent = "Error!";
        popup.querySelector(".complaint-popup__message").textContent =
            "There was an error submitting your complaint. Please try again.";

        setTimeout(function () {
            closeSuccessPopup();
        }, 4000);
    }
}

function closeSuccessPopup() {
    const popup = document.getElementById("successPopup");
    if (popup) {
        popup.style.display = "none";
        popup.classList.remove("is-success", "is-error");

        popup.querySelector(".complaint-popup__title").textContent = "Success!";
        popup.querySelector(".complaint-popup__message").textContent =
            "Your complaint has been submitted successfully.";
    }
}
