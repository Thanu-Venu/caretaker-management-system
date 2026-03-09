(() => {
  const modal = document.getElementById('paymentModal');
  const modalBody = document.getElementById('paymentModalBody');
  const closeBtn = document.getElementById('modalCloseBtn');
  const detailButtons = document.querySelectorAll('.js-view-detail');

  if (!modal || !modalBody || !closeBtn || detailButtons.length === 0) {
    return;
  }

  const fields = [
    ['payment_id', 'Payment ID', (v) => `#${v}`],
    ['booking_id', 'Booking ID', (v) => `#${v}`],
    ['client_name', 'Client'],
    ['caretaker_name', 'Caretaker'],
    ['service_type', 'Service'],
    ['basis', 'Basis'],
    ['payment_type', 'Payment Type', (v) => ucfirst(v)],
    ['payment_method', 'Payment Method', (v) => ucfirst(String(v || '').replace(/_/g, ' '))],
    ['status', 'Payment Status', (v) => ucfirst(v)],
    ['booking_status', 'Booking Status'],
    ['amount', 'Amount', (v) => toMoney(v)],
    ['remaining_balance', 'Remaining Balance', (v) => toMoney(v)],
    ['created_at', 'Created At'],
    ['paid_date', 'Paid Date'],
  ];

  detailButtons.forEach((button) => {
    button.addEventListener('click', () => {
      let payload = {};
      try {
        payload = JSON.parse(button.dataset.payment || '{}');
      } catch (error) {
        payload = {};
      }

      modalBody.innerHTML = fields
        .map(([key, label, formatter]) => {
          const value = payload[key] ?? '-';
          const formatted = typeof formatter === 'function' ? formatter(value) : value;
          return `<dt>${escapeHtml(label)}</dt><dd>${escapeHtml(String(formatted || '-'))}</dd>`;
        })
        .join('');

      modal.classList.add('show');
      modal.setAttribute('aria-hidden', 'false');
    });
  });

  closeBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', (event) => {
    if (event.target === modal) {
      closeModal();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && modal.classList.contains('show')) {
      closeModal();
    }
  });

  function closeModal() {
    modal.classList.remove('show');
    modal.setAttribute('aria-hidden', 'true');
  }

  function ucfirst(value) {
    const normalized = String(value || '').trim();
    if (!normalized) {
      return '-';
    }
    return normalized.charAt(0).toUpperCase() + normalized.slice(1);
  }

  function toMoney(value) {
    const num = Number(value || 0);
    return `LKR ${num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  }

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }
})();
