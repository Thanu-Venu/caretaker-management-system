document.addEventListener("DOMContentLoaded", () => {
    const feedbackModal = document.getElementById("feedbackModal");
    const closeBtn = document.querySelector(".feedback-close");
    const cancelBtn = document.getElementById("cancelFeedback");
    const feedbackForm = document.getElementById("feedbackForm");
    const stars = document.querySelectorAll(".stars span");
    const ratingText = document.getElementById("ratingText");
    let rating = 0;

    // Open modal for each feedback button
    document.querySelectorAll(".feedback-btn").forEach(button => {
        button.addEventListener("click", (e) => {
            const bookingId = e.target.dataset.bookingId;
            document.getElementById("bookingId").value = bookingId;
            feedbackModal.style.display = "flex";
        });
    });

    // Close modal
    closeBtn.addEventListener("click", () => {
        feedbackModal.style.display = "none";
    });
    cancelBtn.addEventListener("click", () => {
        feedbackModal.style.display = "none";
    });

    // Close modal if click outside content
    window.addEventListener("click", (e) => {
        if (e.target === feedbackModal) {
            feedbackModal.style.display = "none";
        }
    });

    // Star rating
    stars.forEach((star, index) => {
        star.addEventListener("click", () => {
            rating = index + 1;
            stars.forEach((s, i) => s.classList.toggle("active", i < rating));
            ratingText.textContent = `${rating}/5 stars`;
        });
    });

    // Submit feedback
    feedbackForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const bookingId = document.getElementById("bookingId").value;
        const feedbackText = document.getElementById("feedback").value.trim();

        if (!rating) {
            alert("Please select a rating!");
            return;
        }

        alert(`Feedback submitted for booking ${bookingId}!\nRating: ${rating}/5\nFeedback: ${feedbackText}`);
        feedbackForm.reset();
        rating = 0;
        stars.forEach(s => s.classList.remove("active"));
        ratingText.textContent = "0/5 stars";
        feedbackModal.style.display = "none";
    });
});
