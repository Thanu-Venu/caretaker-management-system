/**
 * Booking list filters + View modal (full row including booking ID).
 */
function normalizeBookingDateCell(text) {
  const raw = (text || "").trim();
  if (!raw) return "";
  const iso = raw.match(/^(\d{4}-\d{2}-\d{2})/);
  if (iso) return iso[1];
  const d = new Date(raw);
  if (!Number.isNaN(d.getTime())) {
    return d.toISOString().slice(0, 10);
  }
  return raw.slice(0, 10);
}

function filterTable() {
  const typeEl = document.getElementById("type");
  const dateEl = document.getElementById("date");
  const statusEl = document.getElementById("status");
  if (!typeEl || !dateEl || !statusEl) return;

  const typeVal = typeEl.value.trim().toLowerCase();
  const dateFilter = dateEl.value.trim();
  const statusVal = statusEl.value.trim();
  const statusFilterKey = statusVal.toLowerCase().replace(/\s+/g, "_");

  const rows = document.querySelectorAll("#bookingTable tbody tr");

  rows.forEach((row) => {
    const cells = row.getElementsByTagName("td");
    if (!cells.length) return;

    const rowType = (cells[2]?.textContent || "").trim().toLowerCase();
    const dateNorm = normalizeBookingDateCell((cells[3]?.textContent || "").trim());
    const rowStatusKey = (cells[4]?.textContent || "").trim().toLowerCase().replace(/\s+/g, "_");

    const typeMatch = typeVal === "all" || rowType === typeVal;
    const dateMatch = dateFilter === "" || dateNorm === dateFilter;
    const statusMatch =
      statusFilterKey === "all" || rowStatusKey === statusFilterKey;

    row.style.display = typeMatch && dateMatch && statusMatch ? "" : "none";
  });
}

(function initBookingViewModal() {
  const LABEL_ORDER = [
    ["Booking ID", "booking_id"],
    ["Client Name", "client_name"],
    ["Caretaker Name", "caretaker_name"],
    ["Service Type", "service_type"],
    ["Booking Date", "booking_date"],
    ["Status", "status"],
  ];

  function labelForKey(key) {
    const found = LABEL_ORDER.find(([, k]) => k === key);
    if (found) return found[0];
    return key.replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
  }

  function ensureModal() {
    let modal = document.getElementById("adminRowDetailModal");
    if (modal) return modal;

    modal = document.createElement("div");
    modal.id = "adminRowDetailModal";
    modal.className = "modal admin-row-detail-modal";
    modal.setAttribute("aria-hidden", "true");
    modal.innerHTML =
      '<div class="modal-content admin-row-detail-modal__content" role="dialog" aria-modal="true" aria-labelledby="adminRowDetailTitle">' +
      '<button type="button" class="modal-close admin-row-detail-modal__close" aria-label="Close"><i class="bx bx-x" aria-hidden="true"></i></button>' +
      '<header class="admin-row-detail-modal__header">' +
      '<span class="admin-row-detail-modal__header-icon" aria-hidden="true"><i class="bx bx-show"></i></span>' +
      '<h3 id="adminRowDetailTitle" class="admin-row-detail-modal__title">Booking details</h3>' +
      "</header>" +
      '<dl class="admin-row-detail-modal__dl"></dl>' +
      "</div>";
    document.body.appendChild(modal);

    modal.querySelector(".admin-row-detail-modal__close").addEventListener("click", () => closeModal(modal));
    modal.addEventListener("click", (e) => {
      if (e.target === modal) closeModal(modal);
    });
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && modal.classList.contains("show")) closeModal(modal);
    });
    return modal;
  }

  function closeModal(modal) {
    modal.classList.remove("show");
    document.body.style.overflow = "";
  }

  function openBookingModal(detail) {
    const modal = ensureModal();
    const dl = modal.querySelector(".admin-row-detail-modal__dl");
    if (!dl) return;

    dl.innerHTML = "";
    const shown = new Set();

    LABEL_ORDER.forEach(([label, key]) => {
      if (detail[key] === undefined || detail[key] === null || detail[key] === "") return;
      const dt = document.createElement("dt");
      dt.textContent = label;
      const dd = document.createElement("dd");
      dd.textContent = String(detail[key]);
      dl.appendChild(dt);
      dl.appendChild(dd);
      shown.add(key);
    });

    Object.keys(detail).forEach((key) => {
      if (shown.has(key)) return;
      const val = detail[key];
      if (val === undefined || val === null || val === "") return;
      const dt = document.createElement("dt");
      dt.textContent = labelForKey(key);
      const dd = document.createElement("dd");
      dd.textContent = String(val);
      dl.appendChild(dt);
      dl.appendChild(dd);
    });

    modal.classList.add("show");
    document.body.style.overflow = "hidden";
  }

  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("#bookingTable tbody tr[data-booking-detail]").forEach((tr) => {
      const raw = tr.getAttribute("data-booking-detail");
      if (!raw) return;
      let detail;
      try {
        detail = JSON.parse(raw);
      } catch {
        return;
      }
      const actions = tr.querySelector("td.actions");
      if (!actions) return;

      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "btn secondary btn-sm action-view-btn action-view-btn--icon";
      btn.innerHTML = '<i class="bx bx-show" aria-hidden="true"></i>';
      btn.setAttribute("aria-label", "View full booking details");
      btn.setAttribute("title", "View details");
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        openBookingModal(detail);
      });
      actions.appendChild(btn);
    });
  });
})();
