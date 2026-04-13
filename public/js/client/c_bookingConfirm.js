(function () {
  var root = typeof window.URLROOT === "string" ? window.URLROOT.replace(/\/$/, "") : "";

  function goToUpcoming() {
    if (root) {
      window.location.href = root + "/public?url=client/c_upcomingBookings";
    } else {
      window.location.href = "index.php?url=client/c_upcomingBookings";
    }
  }

  function goHome() {
    if (root) {
      window.location.href = root + "/public?url=client/c_dashboard";
    } else {
      window.location.href = "index.php?url=client/c_dashboard";
    }
  }

  document.addEventListener("DOMContentLoaded", function () {
    var u = document.getElementById("bookingConfirmUpcoming");
    var h = document.getElementById("bookingConfirmHome");
    if (u) u.addEventListener("click", goToUpcoming);
    if (h) h.addEventListener("click", goHome);
  });
})();
