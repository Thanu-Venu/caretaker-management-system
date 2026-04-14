/**
 * Delete Confirmation Modal System
 * Shows a custom confirmation dialog instead of browser's confirm()
 */

let pendingDeleteUrl = null;

/**
 * Show Delete Confirmation Modal
 * @param {string} url - The delete URL to redirect to after confirmation
 * @param {string} itemName - The name of the item being deleted (e.g., caretaker name)
 */
function showDeleteConfirmation(url, itemName = 'this item') {
  pendingDeleteUrl = url;
  
  // Create modal HTML if it doesn't exist
  let confirmOverlay = document.getElementById('confirmDeleteOverlay');
  
  if (!confirmOverlay) {
    confirmOverlay = document.createElement('div');
    confirmOverlay.id = 'confirmDeleteOverlay';
    confirmOverlay.className = 'confirm-delete-overlay';
    confirmOverlay.innerHTML = `
      <div class="confirm-delete-modal">
        <div class="confirm-delete-header">
          <h2>Confirm Delete</h2>
          <button type="button" class="confirm-delete-close" onclick="closeDeleteConfirmation()">&times;</button>
        </div>
        <div class="confirm-delete-content">
          <p>Are you sure you want to delete <strong id="deleteItemName"></strong>?</p>
        </div>
        <div class="confirm-delete-footer">
          <button type="button" class="btn-cancel-delete" onclick="closeDeleteConfirmation()">No</button>
          <button type="button" class="btn-confirm-delete" onclick="confirmDelete()">Yes</button>
        </div>
      </div>
    `;
    document.body.appendChild(confirmOverlay);
  }
  
  // Set the item name
  document.getElementById('deleteItemName').textContent = itemName;
  
  // Show the overlay
  confirmOverlay.classList.add('show');
  
  // Prevent body scroll
  document.body.style.overflow = 'hidden';
}

/**
 * Close the delete confirmation modal
 */
function closeDeleteConfirmation() {
  const confirmOverlay = document.getElementById('confirmDeleteOverlay');
  if (confirmOverlay) {
    confirmOverlay.classList.remove('show');
  }
  
  // Restore body scroll
  document.body.style.overflow = 'auto';
  
  // Clear pending URL
  pendingDeleteUrl = null;
}

/**
 * Confirm and execute the delete action
 */
function confirmDelete() {
  if (pendingDeleteUrl) {
    // Redirect to the delete URL
    window.location.href = pendingDeleteUrl;
  }
}

/**
 * Close modal when clicking outside of it
 */
document.addEventListener('click', function(event) {
  const confirmOverlay = document.getElementById('confirmDeleteOverlay');
  if (confirmOverlay && event.target === confirmOverlay) {
    closeDeleteConfirmation();
  }
});

/**
 * Close modal on Escape key
 */
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    closeDeleteConfirmation();
  }
});
