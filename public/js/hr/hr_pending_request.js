(function () {
    'use strict';

    var DETAIL_ROWS = [
        { key: '_booking_id', label: 'Booking ID' },
        { key: 'client_id', label: 'Client ID' },
        { key: 'client_name', label: 'Client name' },
        { key: 'caretaker_id', label: 'Caregiver ID' },
        { key: 'caretaker_name', label: 'Caregiver name' },
        { key: 'service_type', label: 'Service type' },
        { key: 'basis', label: 'Basis' },
        { key: 'duration', label: 'Duration (units)' },
        { key: 'preferred_time', label: 'Preferred time' },
        { key: 'booking_date', label: 'Booking date' },
        { key: 'service_start_date', label: 'Service start date' },
        { key: 'service_location', label: 'Service location' },
        { key: 'district', label: 'District' },
        { key: 'street', label: 'Street' },
        { key: 'address_line1', label: 'Address line 1' },
        { key: 'address_line2', label: 'Address line 2' },
        { key: 'postal_code', label: 'Postal code' },
        { key: 'customization', label: 'Customization' },
        { key: 'customization_hours', label: 'Customization hours' },
        { key: 'customization_price', label: 'Customization price (Rs)' },
        { key: 'total_payment', label: 'Total payment (Rs)' },
        { key: 'status', label: 'Status' },
        { key: 'created_at', label: 'Created at' },
        { key: 'advance_paid_date', label: 'Advance paid date' },
        { key: 'advance_months', label: 'Advance months' },
        { key: 'total_months', label: 'Total months' },
        { key: 'advance_balance', label: 'Advance balance (Rs)' },
        { key: 'advance_amount', label: 'Advance amount (Rs)' },
        { key: 'cancellation_reason', label: 'Cancellation reason' },
        { key: 'cancelled_at', label: 'Cancelled at' },
        { key: 'caretaker_changed_once', label: 'Caregiver changed once' },
        { key: 'refund_status', label: 'Refund status' },
        { key: 'service_days_used', label: 'Service days used' },
        { key: 'availability_ok', label: 'Schedule available (no conflict)' },
        { key: 'caretaker_overlap', label: 'Overlap with other rows in list' },
        { key: 'availability_conflict', label: 'Caretaker schedule conflict' }
    ];

    var MONEY_KEYS = {
        customization_price: true,
        total_payment: true,
        advance_balance: true,
        advance_amount: true
    };

    var MULTILINE_KEYS = {
        customization: true,
        cancellation_reason: true,
        address_line1: true,
        address_line2: true,
        service_location: true
    };

    function pickBookingId(d) {
        return d.booking_id || d.id || '';
    }

    function formatMoney(n) {
        var x = parseFloat(n);
        return isNaN(x) ? '—' : 'Rs ' + x.toFixed(2);
    }

    function formatValue(key, val, d) {
        if (key === '_booking_id') return pickBookingId(d) || '—';

        if (MONEY_KEYS[key]) return val ? formatMoney(val) : '—';

        return val || '—';
    }

    function fillDetailModal(d) {
        var dl = document.getElementById('bookingDetailDl');
        var titleEl = document.getElementById('bookingDetailTitle');

        dl.innerHTML = '';

        DETAIL_ROWS.forEach(function (row) {
            var dt = document.createElement('dt');
            dt.textContent = row.label;

            var dd = document.createElement('dd');
            dd.textContent = formatValue(row.key, d[row.key], d);

            if (MULTILINE_KEYS[row.key]) dd.className = 'ddBlock';

            dl.appendChild(dt);
            dl.appendChild(dd);
        });

        titleEl.textContent = 'Booking #' + pickBookingId(d);
    }

    function openModal(el) {
        el.classList.add('show');
        el.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(el) {
        el.classList.remove('show');
        el.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function parseBooking(el) {
        try {
            return JSON.parse(el.getAttribute('data-booking') || '{}');
        } catch {
            return {};
        }
    }

    document.addEventListener('DOMContentLoaded', function () {

        var detailModal = document.getElementById('bookingDetailModal');

        // VIEW
        document.querySelectorAll('.bkView').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                fillDetailModal(parseBooking(btn));
                openModal(detailModal);
            });
        });

        document.querySelectorAll('[data-close-booking-modal]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                closeModal(detailModal);
            });
        });

        // ===============================
        // ✅ NEW CONFIRM MODAL (IMPORTANT)
        // ===============================

        var confirmModal = document.getElementById('bookingConfirmModal');
        var confirmTitle = document.getElementById('bookingConfirmTitle');
        var confirmText = document.getElementById('bookingConfirmText');
        var confirmSubmit = document.getElementById('bookingConfirmSubmit');
        var confirmCancel = document.getElementById('bookingConfirmCancel');
        var rejectReason = document.getElementById('bookingRejectReason');

        var selectedForm = null;
        var actionType = '';

        function openConfirm(type, form) {
            selectedForm = form;
            actionType = type;

            rejectReason.value = '';

            if (type === 'accept') {
                confirmTitle.textContent = 'Request Advance Payment';
                confirmText.textContent = 'Request advance payment from client?';
                rejectReason.style.display = 'none';
                confirmSubmit.textContent = 'Confirm';

                confirmSubmit.classList.remove('reject');
            } else {
                confirmTitle.textContent = 'Reject Booking';
                confirmText.textContent = 'Reject this booking request?';
                rejectReason.style.display = 'block';
                confirmSubmit.textContent = 'Reject';

                confirmSubmit.classList.add('reject');
            }

            openModal(confirmModal);
        }

        function closeConfirm() {
            closeModal(confirmModal);
            selectedForm = null;
        }

        // ACCEPT
        document.querySelectorAll('.bkAccept').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                openConfirm('accept', form);
            });
        });

        // REJECT
        document.querySelectorAll('.bkReject').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                openConfirm('reject', form);
            });
        });

        // CONFIRM BUTTON
        confirmSubmit.addEventListener('click', function () {
            if (!selectedForm) return;

            if (actionType === 'reject') {
                var reason = rejectReason.value.trim();

                if (!reason) {
                    alert('Please enter a rejection reason.');
                    return;
                }

                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'reason';
                input.value = reason;
                selectedForm.appendChild(input);
            }

            selectedForm.submit();
        });

        // CANCEL
        confirmCancel.addEventListener('click', closeConfirm);

        // OUTSIDE CLICK
        confirmModal.addEventListener('click', function (e) {
            if (e.target === confirmModal) closeConfirm();
        });

        // ESC KEY
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && confirmModal.classList.contains('show')) {
                closeConfirm();
            }
        });

    });

})();