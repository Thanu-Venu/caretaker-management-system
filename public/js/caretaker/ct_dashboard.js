function openProfile() {
  const profileModal = document.getElementById("profileModal");
  const dashboard = document.getElementById("dashboard");
  if (profileModal) profileModal.style.display = "flex";
  if (dashboard) dashboard.classList.add("blur");
}

function closeProfile() {
  const profileModal = document.getElementById("profileModal");
  const dashboard = document.getElementById("dashboard");
  if (profileModal) profileModal.style.display = "none";
  if (dashboard) dashboard.classList.remove("blur");
}

function renderCalendar() {
  const calendarDates = document.getElementById("calendarDates");
  const monthLabel = document.getElementById("calendarMonthLabel");
  if (!calendarDates || !monthLabel) return;

  const month = (window.dashboardData && window.dashboardData.calendarMonth) || (new Date().getMonth() + 1);
  const year = (window.dashboardData && window.dashboardData.calendarYear) || new Date().getFullYear();
  const workingDates = (window.dashboardData && window.dashboardData.workingDates) || [];
  const workingSet = new Set(workingDates);

  const firstDay = new Date(year, month - 1, 1);
  const daysInMonth = new Date(year, month, 0).getDate();
  const monthName = firstDay.toLocaleString("en-US", { month: "long" });
  monthLabel.textContent = `${monthName} ${year}`;

  calendarDates.innerHTML = "";

  for (let i = 0; i < firstDay.getDay(); i++) {
    calendarDates.appendChild(document.createElement("div"));
  }

  for (let day = 1; day <= daysInMonth; day++) {
    const dateEl = document.createElement("div");
    dateEl.classList.add("date");
    dateEl.textContent = day;

    const yyyyMmDd = `${year}-${String(month).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
    if (workingSet.has(yyyyMmDd)) {
      dateEl.classList.add("active");
      dateEl.title = "Working day";
    }

    calendarDates.appendChild(dateEl);
  }
}

function setupAvailabilityToggle() {
  const toggle = document.getElementById("availabilityToggle");
  const availabilityText = document.getElementById("availabilityText");
  if (!toggle) return;

  toggle.addEventListener("change", function () {
    const isAvailable = this.checked ? "1" : "0";
    const previousValue = !this.checked;

    fetch(window.dashboardData.updateAvailabilityUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: new URLSearchParams({ is_available: isAvailable }).toString()
    })
      .then((res) => res.json())
      .then((data) => {
        if (!data.success) {
          throw new Error(data.message || "Could not update availability");
        }

        if (availabilityText) {
          availabilityText.textContent = this.checked
            ? "You're visible to clients and can receive new bookings"
            : "You're hidden from clients and won't receive new bookings";
        }
      })
      .catch(() => {
        this.checked = previousValue;
        if (availabilityText) {
          availabilityText.textContent = this.checked
            ? "You're visible to clients and can receive new bookings"
            : "You're hidden from clients and won't receive new bookings";
        }
        alert("Failed to update availability. Please try again.");
      });
  });
}

function setupLeaveModal() {
  const leaveModal = document.getElementById("leaveModal");
  const openLeaveBtn = document.getElementById("openLeaveModal");
  const closeLeaveBtn = document.getElementById("closeLeaveModal");
  const dashboard = document.getElementById("dashboard");
  const leaveForm = document.getElementById("leaveForm");

  if (openLeaveBtn && leaveModal) {
    openLeaveBtn.addEventListener("click", function () {
      leaveModal.style.display = "flex";
      if (dashboard) dashboard.classList.add("blur");
    });
  }

  if (closeLeaveBtn && leaveModal) {
    closeLeaveBtn.addEventListener("click", function () {
      leaveModal.style.display = "none";
      if (dashboard) dashboard.classList.remove("blur");
    });
  }

  if (leaveForm) {
    leaveForm.addEventListener("submit", function () {
      if (leaveModal) leaveModal.style.display = "none";
      if (dashboard) dashboard.classList.remove("blur");
    });
  }
}

document.addEventListener("DOMContentLoaded", function () {
  renderCalendar();
  setupAvailabilityToggle();
  setupLeaveModal();
});
