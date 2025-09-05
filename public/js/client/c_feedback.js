// Get elements
const modal = document.getElementById("feedbackModal");
const openBtn = document.getElementById("openModalBtn");
const closeBtn = document.querySelector(".close");
const stars = document.querySelectorAll(".stars span");
const ratingText = document.getElementById("ratingText");
let rating = 0;

// Open modal
openBtn.onclick = () => {
  modal.style.display = "flex";
};

// Close modal
closeBtn.onclick = () => {
  modal.style.display = "none";
};

// Close if clicked outside modal-content
window.onclick = (event) => {
  if (event.target === modal) {
    modal.style.display = "none";
  }
};

// Star rating
stars.forEach((star, index) => {
  star.addEventListener("click", () => {
    rating = index + 1;
    stars.forEach((s, i) => {
      s.classList.toggle("active", i < rating);
    });
    ratingText.textContent = `${rating}/5 stars`;
  });
});

// Submit feedback
document.getElementById("submitBtn").addEventListener("click", () => {
  const feedback = document.getElementById("feedback").value;
  alert(`You rated ${rating}/5\nFeedback: ${feedback}`);
  modal.style.display = "none";
});
