document.addEventListener("DOMContentLoaded", () => {
  const cancelledList = document.querySelector(".cancelled-list");
  const noCancelledMsg = document.getElementById("noCancelled");

  // If no cards in cancelled list → show message
  if (cancelledList.children.length === 0) {
    noCancelledMsg.style.display = "block";
  }
});
