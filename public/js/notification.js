document.addEventListener("DOMContentLoaded", function () {

    const notifBtn = document.getElementById("notifBtn");
    const notifWrapper = document.querySelector(".notification-wrapper");
    const notifDropdown = document.getElementById("notifDropdown");
    const notifCount = document.querySelector(".notif-count");

    if (notifBtn && notifWrapper) {
        notifBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            notifWrapper.classList.toggle("active");
            
            // Mark notifications as read when dropdown is opened
            if (notifWrapper.classList.contains("active")) {
                markNotificationsAsRead();
            }
        });
    }

    // Close when clicking outside
    document.addEventListener("click", function () {
        notifWrapper.classList.remove("active");
    });

    // Function to mark all notifications as read
    function markNotificationsAsRead() {
        fetch(window.location.origin + '/CMA/public/index.php?url=notification/markAllRead', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Hide notification count badge completely
                if (notifCount) {
                    notifCount.style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Error marking notifications as read:', error));
    }
});
