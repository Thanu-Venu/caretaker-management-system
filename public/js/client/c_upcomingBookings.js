document.addEventListener("DOMContentLoaded", () => {
    const bookingsList = document.getElementById("bookingsList");
    const noBookings = document.getElementById("noBookings");

    function checkBookings() {
        const bookingCards = bookingsList.querySelectorAll(".booking-card");

        if (bookingCards.length === 0) {
            noBookings.style.display = "block";
            bookingsList.style.display = "none";
        } else {
            noBookings.style.display = "none";
            bookingsList.style.display = "block";
        }
    }

    // Handle Cancel + Reschedule
    bookingsList.addEventListener("click", (e) => {
        if (e.target.classList.contains("cancel-btn")) {
            const card = e.target.closest(".booking-card");
            card.remove();
            checkBookings();
        }

        if (e.target.classList.contains("reschedule-btn")) {
            alert("🔄 Reschedule booking flow will go here (UI only for now).");
        }
    });

    // Initial check
    checkBookings();
});

document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("rescheduleModal");
    const closeBtn = document.querySelector(".close");
    const rescheduleForm = document.getElementById("rescheduleForm");
    let activeCard = null;

    // Open modal on Reschedule button
    document.querySelectorAll(".reschedule-btn").forEach(button => {
        button.addEventListener("click", (e) => {
            activeCard = e.target.closest(".booking-card"); // store which booking is being rescheduled
            modal.style.display = "flex";
        });
    });

    // Close modal
    closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });

    // Close modal if clicked outside content
    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });

    // Handle form submit (UI only)
    rescheduleForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const newDate = document.getElementById("newDate").value;
        const newTime = document.getElementById("newTime").value;
        const newDuration = document.getElementById("newDuration").value;

        if (activeCard) {
            activeCard.querySelector("p:nth-child(3)").innerHTML = `<strong>Date:</strong> ${newDate}`;
            activeCard.querySelector("p:nth-child(4)").innerHTML = `<strong>Time:</strong> ${newTime}`;
            activeCard.querySelector("p:nth-child(5)").innerHTML = `<strong>Duration:</strong> ${newDuration} Hours/Days`;
            activeCard.querySelector(".status").textContent = "Rescheduled";
            activeCard.querySelector(".status").className = "status rescheduled";
        }

        modal.style.display = "none";
        alert("✅ Booking rescheduled successfully (UI only).");
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const cancelModal = document.getElementById("cancelModal");
    const cancelForm = document.getElementById("cancelForm");
    const cancelClose = document.querySelector(".cancel-close");
    const cancelNo = document.getElementById("cancelNo");
    let cancelTarget = null; // will store the card to cancel

    // Open Cancel Confirmation Modal
    document.querySelectorAll(".cancel-btn").forEach(button => {
        button.addEventListener("click", (e) => {
            // Make sure we only pick the cancel buttons inside booking cards, not the modal itself
            if (!e.target.closest(".booking-card")) return;

            cancelTarget = e.target.closest(".booking-card");
            cancelModal.style.display = "flex";
        });
    });

    // Close modal (X or No button)
    cancelClose.addEventListener("click", () => {
        cancelModal.style.display = "none";
    });

    cancelNo.addEventListener("click", () => {
        cancelModal.style.display = "none";
    });

    // Handle Cancel Form submit
    cancelForm.addEventListener("submit", (e) => {
        e.preventDefault();

        if (cancelTarget) {
            cancelTarget.remove(); // remove the card from UI
            cancelTarget = null;

            // Check if there are no more bookings
            const remainingBookings = document.querySelectorAll(".booking-card");
            if (remainingBookings.length === 0) {
                document.getElementById("noBookings").style.display = "block";
            }
        }

        cancelModal.style.display = "none";
        alert("❌ Booking cancelled successfully.");
    });

    // Close modal if click outside
    window.addEventListener("click", (e) => {
        if (e.target === cancelModal) {
            cancelModal.style.display = "none";
        }
    });
});

document.addEventListener("DOMContentLoaded", () => {
    // Payment button dummy redirect
    const paymentButtons = document.querySelectorAll(".payment-btn");

    paymentButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            const bookingId = btn.getAttribute("data-booking-id");
           
            // Later replace alert with actual redirect
             window.location.href = `http://localhost/CMA/public/?url=client/c_makePayment`;
        });
    });

    // Existing Cancel/Reschedule modal JS can remain here
});

