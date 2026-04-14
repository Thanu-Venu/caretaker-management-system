document.addEventListener("DOMContentLoaded", () => {

    const modal = document.getElementById("feedbackModal");
    const closeBtn = document.querySelector(".feedback-close");
    const cancelBtn = document.getElementById("cancelFeedback");

    const bookingIdInput = document.getElementById("bookingId");
    const caretakerIdInput = document.getElementById("caretakerId");
    const ratingInput = document.getElementById("ratingInput");

    const stars = document.querySelectorAll(".stars span");
    const ratingText = document.getElementById("ratingText");

    let rating = 0;

    /* OPEN MODAL */
    document.querySelectorAll(".feedback-btn").forEach(btn => {
        btn.addEventListener("click", () => {

            const row = btn.closest("tr");

            bookingIdInput.value = row.dataset.bookingId;
            caretakerIdInput.value = row.dataset.caretakerId;

            modal.classList.add("show");
        });
    });

    /* CLOSE MODAL */
    closeBtn.onclick = cancelBtn.onclick = () => {
        resetForm();
        modal.classList.remove("show");
    };

    window.onclick = (e) => {
        if (e.target === modal) {
            resetForm();
            modal.classList.remove("show");
        }
    };

    /* STAR RATING */
    stars.forEach((star, index) => {
        star.addEventListener("click", () => {
            rating = index + 1;
            ratingInput.value = rating;

            stars.forEach((s, i) => {
                s.classList.toggle("active", i < rating);
            });

            ratingText.textContent = rating + " / 5";
        });
    });

    function resetForm() {
        rating = 0;
        ratingInput.value = "";
        ratingText.textContent = "0 / 5";
        stars.forEach(s => s.classList.remove("active"));
        document.getElementById("feedback").value = "";
    }
});
