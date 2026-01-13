document.addEventListener("DOMContentLoaded", function () {

    const notifBtn = document.getElementById("notifBtn");
    const notifWrapper = document.querySelector(".notification-wrapper");

    if (notifBtn && notifWrapper) {
        notifBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            notifWrapper.classList.toggle("active");
        });
    }

    // Close when clicking outside
    document.addEventListener("click", function () {
        notifWrapper.classList.remove("active");
    });
});
