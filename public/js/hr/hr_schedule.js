document.addEventListener('DOMContentLoaded', function () {
  const appRoot = document.getElementById('hrScheduleApp');
  const calendarEl = document.getElementById('calendar');

  if (!appRoot || !calendarEl) return;

  const monthUrl = appRoot.dataset.monthUrl;
  const dayUrl = appRoot.dataset.dayUrl;
  const today = appRoot.dataset.today;

  const selectedDateText = document.getElementById('selectedDateText');
  const panel = document.getElementById('dayDetailsPanel');
  const panelTitle = document.getElementById('panelTitle');
  const panelCloseBtn = document.getElementById('panelCloseBtn');

  const summaryModal = document.getElementById('summaryListModal');
  const summaryModalClose = document.getElementById('summaryModalClose');
  const summaryModalTitle = document.getElementById('summaryModalTitle');
  const summaryModalBody = document.getElementById('summaryModalBody');

  const summaryCountEls = {
    active_bookings: document.getElementById('count-active'),
    pending_requests: document.getElementById('count-pending'),
    caregiver_leaves: document.getElementById('count-leave'),
    busy_caregivers: document.getElementById('count-busy'),
    available_caregivers: document.getElementById('count-available')
  };

  const listContainers = {
    active_bookings: document.getElementById('panel-active-bookings'),
    pending_requests: document.getElementById('panel-pending-requests'),
    leave_list: document.getElementById('panel-leave-list'),
    available_caregivers: document.getElementById('panel-available-caregivers')
  };

  const metricConfig = {
    active_bookings: { title: 'Active Bookings', listKey: 'active_bookings' },
    pending_requests: { title: 'Pending Requests', listKey: 'pending_requests' },
    caregiver_leaves: { title: 'Caregiver Leaves', listKey: 'leave_list' },
    busy_caregivers: { title: 'Busy Caregivers', listKey: 'busy_caregivers' },
    available_caregivers: { title: 'Available Caregivers', listKey: 'available_caregivers' }
  };

  let selectedDate = today;
  let selectedDayDetails = null;
  let monthMap = {};

  function toYmd(dateObj) {
    const y = dateObj.getFullYear();
    const m = String(dateObj.getMonth() + 1).padStart(2, '0');
    const d = String(dateObj.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
  }

  function formatHumanDate(ymd) {
    const dateObj = new Date(ymd + 'T00:00:00');
    return dateObj.toLocaleDateString('en-GB', {
      year: 'numeric', month: 'short', day: 'numeric', weekday: 'short'
    });
  }

  function setSelectedDateLabel() {
    selectedDateText.textContent = `Selected date: ${formatHumanDate(selectedDate)}`;
  }

  function setSummaryCounts(summary) {
    summaryCountEls.active_bookings.textContent = summary.active_bookings ?? 0;
    summaryCountEls.pending_requests.textContent = summary.pending_requests ?? 0;
    summaryCountEls.caregiver_leaves.textContent = summary.caregiver_leaves ?? 0;
    summaryCountEls.busy_caregivers.textContent = summary.busy_caregivers ?? 0;
    summaryCountEls.available_caregivers.textContent = summary.available_caregivers ?? 0;
  }

  function renderBadge(label, value, badgeClass) {
    return `<span class="day-badge ${badgeClass}">${label}: ${value}</span>`;
  }

  function applyDayCellBadges() {
    document.querySelectorAll('.fc-daygrid-day').forEach((cell) => {
      const dateKey = cell.getAttribute('data-date');
      if (!dateKey) return;

      const frame = cell.querySelector('.day-badges-wrap');
      if (frame) frame.remove();

      if (dateKey === today) {
        cell.classList.add('fc-custom-today');
      } else {
        cell.classList.remove('fc-custom-today');
      }

      if (dateKey === selectedDate) {
        cell.classList.add('fc-selected-date');
      } else {
        cell.classList.remove('fc-selected-date');
      }

      const dayData = monthMap[dateKey];
      if (!dayData) return;

      const hasData = (dayData.active + dayData.pending + dayData.leave + dayData.available) > 0;
      if (!hasData) return;

      const wrap = document.createElement('div');
      wrap.className = 'day-badges-wrap';

      wrap.innerHTML = [
        renderBadge('A', dayData.active, 'badge-active'),
        renderBadge('P', dayData.pending, 'badge-pending'),
        renderBadge('L', dayData.leave, 'badge-leave'),
        renderBadge('Av', dayData.available, 'badge-available')
      ].join('');

      const frameEl = cell.querySelector('.fc-daygrid-day-frame');
      frameEl.appendChild(wrap);
    });
  }

  function formatDateDisplay(dateStr) {
    if (!dateStr) return '-';
    const dateObj = new Date(dateStr + 'T00:00:00');
    return dateObj.toLocaleDateString('en-GB', {
      year: 'numeric', month: 'short', day: 'numeric'
    });
  }

  function itemRow(label, value) {
    return `<div class="list-item"><span class="list-key">${label}</span><span class="list-value">${value}</span></div>`;
  }

  function renderList(type, rows) {
    if (!rows || rows.length === 0) {
      return '<div class="list-empty">No records for this date.</div>';
    }

    if (type === 'active_bookings' || type === 'pending_requests') {
      return rows.map((row) => `
        <div class="list-card">
          ${itemRow('Booking #', row.booking_id)}
          ${itemRow('Caregiver', row.caretaker_name)}
          ${itemRow('Client', row.client_name)}
          ${itemRow('Service', row.booking_service_type || row.service_type || '-')}
          ${itemRow('Time', row.preferred_time || '-')}
          ${itemRow('Start Date', formatDateDisplay(row.effective_start || row.booking_date))}
          ${itemRow('End Date', formatDateDisplay(row.effective_end || row.booking_date))}
          ${itemRow('Status', row.status || '-')}
        </div>
      `).join('');
    }

    if (type === 'leave_list') {
      return rows.map((row) => `
        <div class="list-card">
          ${itemRow('Caregiver', row.caretaker_name)}
          ${itemRow('Type', row.leave_type || '-')}
          ${itemRow('Range', `${row.start_date || '-'} to ${row.end_date || '-'}`)}
          ${itemRow('Reason', row.reason || '-')}
        </div>
      `).join('');
    }

    if (type === 'busy_caregivers') {
      return rows.map((row) => `
        <div class="list-card">
          ${itemRow('Caregiver', row.caretaker_name)}
          ${itemRow('Service', row.service_type || '-')}
          ${itemRow('Location', row.location || '-')}
          ${itemRow('Active Bookings', row.booking_count || 0)}
        </div>
      `).join('');
    }

    return rows.map((row) => `
      <div class="list-card">
        ${itemRow('Caregiver', row.caretaker_name)}
        ${itemRow('Service', row.service_type || '-')}
        ${itemRow('Location', row.location || '-')}
      </div>
    `).join('');
  }

  function fillSidePanel(lists) {
    listContainers.active_bookings.innerHTML = renderList('active_bookings', lists.active_bookings || []);
    listContainers.pending_requests.innerHTML = renderList('pending_requests', lists.pending_requests || []);
    listContainers.leave_list.innerHTML = renderList('leave_list', lists.leave_list || []);
    listContainers.available_caregivers.innerHTML = renderList('available_caregivers', lists.available_caregivers || []);
  }

  function openPanel() {
    panel.classList.add('open');
    panel.setAttribute('aria-hidden', 'false');
  }

  function closePanel() {
    panel.classList.remove('open');
    panel.setAttribute('aria-hidden', 'true');
  }

  function openSummaryModal(type) {
    if (!selectedDayDetails || !metricConfig[type]) return;

    const conf = metricConfig[type];
    const rows = selectedDayDetails.lists[conf.listKey] || [];

    summaryModalTitle.textContent = `${conf.title} - ${formatHumanDate(selectedDate)}`;
    summaryModalBody.innerHTML = renderList(conf.listKey, rows);

    summaryModal.classList.add('open');
    summaryModal.setAttribute('aria-hidden', 'false');
  }

  function closeSummaryModal() {
    summaryModal.classList.remove('open');
    summaryModal.setAttribute('aria-hidden', 'true');
  }

  async function fetchMonthAggregates(startDate, endDate) {
    try {
      const qs = new URLSearchParams({ start: startDate, end: endDate });
      const response = await fetch(`${monthUrl}?${qs.toString()}`);
      const payload = await response.json();

      if (!payload.success) {
        console.error('Month aggregates error:', payload.message);
        throw new Error(payload.message || 'Failed to load month aggregates');
      }

      monthMap = payload.data?.dates || {};
      applyDayCellBadges();
    } catch (err) {
      console.error('fetchMonthAggregates error:', err);
      throw err;
    }
  }

  async function fetchDayDetails(date) {
    try {
      const qs = new URLSearchParams({ date });
      const response = await fetch(`${dayUrl}?${qs.toString()}`);
      const payload = await response.json();

      if (!payload.success) {
        console.error('Day details error:', payload.message);
        throw new Error(payload.message || 'Failed to load date details');
      }

      selectedDayDetails = payload.data;
      setSummaryCounts(payload.data.summary || {});
      fillSidePanel(payload.data.lists || {});
      setSelectedDateLabel();
      panelTitle.textContent = `Schedule Details - ${formatHumanDate(date)}`;
      applyDayCellBadges();
    } catch (err) {
      console.error('fetchDayDetails error:', err);
      throw err;
    }
  }

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height: 'auto',
    fixedWeekCount: false,
    showNonCurrentDates: true,
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth'
    },
    dateClick: async function (info) {
      selectedDate = info.dateStr;
      await fetchDayDetails(selectedDate);
      openPanel();
    },
    dayCellDidMount: function () {
      applyDayCellBadges();
    },
    datesSet: async function (info) {
      const viewStart = toYmd(info.start);
      const inclusiveEnd = new Date(info.end);
      inclusiveEnd.setDate(inclusiveEnd.getDate() - 1);
      const viewEnd = toYmd(inclusiveEnd);

      try {
        await fetchMonthAggregates(viewStart, viewEnd);
      } catch (err) {
        console.error(err);
      }
    }
  });

  calendar.render();

  panelCloseBtn.addEventListener('click', closePanel);
  summaryModalClose.addEventListener('click', closeSummaryModal);

  summaryModal.addEventListener('click', function (e) {
    if (e.target === summaryModal) closeSummaryModal();
  });

  document.getElementById('summaryCards').addEventListener('click', function (e) {
    const card = e.target.closest('.summary-card');
    if (!card) return;
    openSummaryModal(card.dataset.type);
  });

  (async function init() {
    try {
      selectedDate = today;
      await fetchDayDetails(selectedDate);
      applyDayCellBadges();
    } catch (err) {
      console.error(err);
      selectedDateText.textContent = 'Failed to load schedule data.';
    }
  })();
});
