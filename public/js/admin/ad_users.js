// Example actions (for demo)
document.querySelectorAll('.link-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    alert(`${btn.innerText} clicked!`);
  });
});

document.querySelector('.add-btn').addEventListener('click', () => {
  alert("Add User button clicked!");
});
