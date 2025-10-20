// Show alert for table buttons
document.querySelectorAll('.link-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    alert(`${btn.innerText} clicked!`);
  });
});

// Modal functionality
const addBtn = document.querySelector('.add-btn');
const modal = document.getElementById('addUserModal');
const cancelBtn = document.querySelector('.cancel-btn');
const form = document.getElementById('addUserForm');

addBtn.addEventListener('click', () => {
  modal.style.display = 'block';
});

cancelBtn.addEventListener('click', () => {
  modal.style.display = 'none';
  form.reset();
});

window.addEventListener('click', (e) => {
  if (e.target == modal) {
    modal.style.display = 'none';
    form.reset();
  }
});

// Handle form submission
form.addEventListener('submit', (e) => {
  e.preventDefault();
  alert('User added successfully!');
  modal.style.display = 'none';
  form.reset();
});
