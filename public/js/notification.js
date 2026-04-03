document.addEventListener("DOMContentLoaded", function () {

    const notifBtn = document.getElementById("notifBtn");
    const notifWrapper = document.querySelector(".notification-wrapper");
    const notifDropdown = document.getElementById("notifDropdown");
    const notifCount = document.querySelector(".notif-count");
    const profileMenuBtn = document.getElementById("profileMenuBtn");
    const profileWrapper = document.querySelector(".profile-wrapper");

    if (notifBtn && notifWrapper) {
        notifBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            notifWrapper.classList.toggle("active");
            if (profileWrapper) {
                profileWrapper.classList.remove("active");
            }
            
            // Mark notifications as read when dropdown is opened
            if (notifWrapper.classList.contains("active")) {
                markNotificationsAsRead();
            }
        });
    }

    if (profileMenuBtn && profileWrapper) {
        profileMenuBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            profileWrapper.classList.toggle("active");
            profileMenuBtn.setAttribute(
                "aria-expanded",
                profileWrapper.classList.contains("active") ? "true" : "false"
            );
            if (notifWrapper) {
                notifWrapper.classList.remove("active");
            }
        });
    }

    // Close when clicking outside
    document.addEventListener("click", function () {
        if (notifWrapper) {
            notifWrapper.classList.remove("active");
        }
        if (profileWrapper) {
            profileWrapper.classList.remove("active");
        }
        if (profileMenuBtn) {
            profileMenuBtn.setAttribute("aria-expanded", "false");
        }
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
