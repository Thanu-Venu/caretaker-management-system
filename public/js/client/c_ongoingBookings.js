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
    today.setDate(today.getDate() + 5); // 5-day advance notice requirement
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, "0");
    const dd = String(today.getDate()).padStart(2, "0");
    dateInput.min = `${yyyy}-${mm}-${dd}`;
    dateInput.value = ""; // Clear any previously selected date
  }
  document.getElementById("rescheduleModal").style.display = "flex";
}

function closeRescheduleModal() {
  document.getElementById("rescheduleModal").style.display = "none";
}

let availableCaretakers = []; // will be set in openChangeModal()

  function renderCaretakerCards(list){
    const grid = document.getElementById("caretakerGrid");
    const msg = document.getElementById("noCaretakersMsg");
    const hidden = document.getElementById("selectedCaretakerId");

    grid.innerHTML = "";
    hidden.value = "";

    if(!list || list.length === 0){
      msg.textContent = "No alternative caregivers available for this booking right now.";
      return;
    }
    msg.textContent = "";

    list.forEach(ct => {
      const card = document.createElement("div");
      card.className = "caretaker-card";
      card.dataset.id = ct.id;

      // fallback values to avoid breaking UI
      const rating = (ct.rating ?? 0).toFixed ? ct.rating.toFixed(1) : (ct.rating ?? "0.0");
      const ratingCount = ct.rating_count ?? 0;
      const exp = ct.experience_years ?? 0;
      const qual = ct.qualification ?? "—";
      const profileImage = ct.profile_image ? `uploads/${ct.profile_image}` : "uploads/default.png";

      card.innerHTML = `
        <div class="ct-top">
          <div>
            <p class="ct-name">${escapeHtml(ct.name ?? "Caretaker")}</p>
            <div class="ct-meta">
              <img src="${profileImage}" alt="Profile Image" class="ct-profile-image">
              <div><b>Experience:</b> ${escapeHtml(String(exp))} years</div>
              <div><b>Qualification:</b> ${escapeHtml(String(qual))}</div>
              ${ct.location ? `<div><b>Location:</b> ${escapeHtml(ct.location)}</div>` : ``}
            </div>
          </div>

          <span class="ct-badge">Available</span>
        </div>

        <div class="ct-rating">
          <span class="star">★</span>
          <span><b>${escapeHtml(String(rating))}</b> (${escapeHtml(String(ratingCount))} reviews)</span>
        </div>
      `;

      card.addEventListener("click", () => {
        // unselect others
        document.querySelectorAll(".caretaker-card").forEach(c => c.classList.remove("selected"));
        // select current
        card.classList.add("selected");
        hidden.value = ct.id;
      });

      grid.appendChild(card);
    });
  }

  // Call this when opening the modal with booking_id
  // caretakers should come from backend (filtered by availability)
  function openChangeModal(bookingId, caretakersJson){
    document.getElementById("changeBookingId").value = bookingId;

    // set data then render
    availableCaretakers = Array.isArray(caretakersJson) ? caretakersJson : [];
    renderCaretakerCards(availableCaretakers);

    document.getElementById("changeModal").style.display = "block";
  }

  function closeChangeModal(){
    document.getElementById("changeModal").style.display = "none";
  }

  // small helper to prevent HTML injection
  function escapeHtml(str){
    return String(str).replace(/[&<>"']/g, s => ({
      "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"
    }[s]));
  }

  // Optional: block submit if no caretaker selected
  document.addEventListener("submit", function(e){
    const form = e.target;
    if(form && form.action && form.action.includes("/client/requestChangeCaretaker")){
      const hidden = document.getElementById("selectedCaretakerId");
      const msg = document.getElementById("noCaretakersMsg");
      if(!hidden.value){
        e.preventDefault();
        msg.textContent = "Please select a caregiver card before submitting.";
      }
    }
  }, true);

