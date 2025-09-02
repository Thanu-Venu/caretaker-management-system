// Tab navigation
function openTab(evt, tabName) {
  const tabContents = document.querySelectorAll(".tab-content");
  tabContents.forEach(tc => tc.classList.remove("active"));

  const tabLinks = document.querySelectorAll(".tab-link");
  tabLinks.forEach(tl => tl.classList.remove("active"));

  document.getElementById(tabName).classList.add("active");
  evt.currentTarget.classList.add("active");
}

// Dummy form submission handlers
document.getElementById("profileForm").addEventListener("submit", function(e){
  e.preventDefault();
  alert("Profile changes saved!");
});

document.getElementById("passwordForm").addEventListener("submit", function(e){
  e.preventDefault();
  alert("Password changed successfully!");
});

document.getElementById("preferencesForm").addEventListener("submit", function(e){
  e.preventDefault();
  alert("Preferences saved!");
});
