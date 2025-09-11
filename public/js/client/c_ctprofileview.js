document.addEventListener("DOMContentLoaded", () => {
  const bookBtn = document.querySelector(".book-btn");

  bookBtn.addEventListener("click", () => {
    alert("Redirecting to booking page...");
    // You can redirect to your booking form here
    // window.location.href = "<?php echo URLROOT; ?>/client/c_book";
  });
});
