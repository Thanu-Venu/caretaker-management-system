function openCancelModal(id) {
  document.getElementById("cancelBookingId").value = id;
  document.getElementById("cancelModal").style.display = "flex";
}

function closeCancelModal() {
  document.getElementById("cancelModal").style.display = "none";
}

function openRescheduleModal(id) {
  document.getElementById("rescheduleBookingId").value = id;
  const dateInput = document.querySelector(
    '#rescheduleModal input[name="new_date"]',
  );
  if (dateInput) {
    const today = new Date();
    today.setDate(today.getDate() + 4);
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, "0");
    const dd = String(today.getDate()).padStart(2, "0");
    dateInput.min = `${yyyy}-${mm}-${dd}`;
  }
  document.getElementById("rescheduleModal").style.display = "flex";
}

function closeRescheduleModal() {
  document.getElementById("rescheduleModal").style.display = "none";
}

// New functions for change caretaker
function openChangeModal(id) {
  document.getElementById("changeBookingId").value = id;

  // fetch available caretakers for this booking
  // use front controller routing via ?url=
  fetch(`?url=client/fetchAvailableCaretakers&booking_id=${id}`)
    .then((res) => res.text())
    .then((txt) => {
      // debug logging
      console.log("availability raw response:", txt);
      let data;
      try {
        data = JSON.parse(txt);
      } catch (e) {
        console.error("json parse failed", e);
        const msg = document.getElementById("noCaretakersMsg");
        if (msg) msg.textContent = "Unable to load caregivers (see console).";
        return;
      }

      const select = document.getElementById("newCaretakerSelect");
      const submitBtn = document.querySelector("#changeModal .cancel1-btn");
      select.innerHTML = '<option value="">-- pick a caregiver --</option>';
      const msg = document.getElementById("noCaretakersMsg");
      if (msg) msg.textContent = "";
      if (submitBtn) submitBtn.disabled = false;
      if (Array.isArray(data) && data.length > 0) {
        data.forEach((ct) => {
          const opt = document.createElement("option");
          opt.value = ct.id;
          opt.textContent = ct.name;
          select.appendChild(opt);
        });
      } else {
        // no available caretakers
        if (msg)
          msg.textContent = "No other caregivers are available for this slot.";
        if (submitBtn) submitBtn.disabled = true;
      }
    })
    .catch((err) => {
      console.error("error loading caretakers", err);
    });

  document.getElementById("changeModal").style.display = "flex";
}

function closeChangeModal() {
  document.getElementById("changeModal").style.display = "none";
}
