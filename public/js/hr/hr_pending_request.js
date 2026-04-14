/**
 * HR pending service requests: admin-style detail modal; accept / reject with confirm.
 */
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
        if (d.booking_id != null && d.booking_id !== '') {
            return d.booking_id;
        }
        if (d.id != null && d.id !== '') {
            return d.id;
        }
        return '';
    }

    function formatMoney(n) {
        var x = parseFloat(n);
        if (isNaN(x)) {
            return '—';
        }
        return 'Rs ' + x.toFixed(2);
    }

    function stringifyConflict(c) {
        if (!c || typeof c !== 'object') {
            return '';
        }
        var parts = [];
        if (c.conflict_booking_id != null) {
            parts.push('Conflicting booking #' + c.conflict_booking_id);
        }
        if (c.status) {
            parts.push('status: ' + c.status);
        }
        if (c.start_date && c.end_date) {
            parts.push(c.start_date + ' → ' + c.end_date);
        }
        return parts.join(' · ') || JSON.stringify(c);
    }

    function formatValue(key, val, d) {
        if (key === '_booking_id') {
            return pickBookingId(d) !== '' ? String(pickBookingId(d)) : '—';
        }
        if (key === 'availability_ok') {
            if (val === true || val === 1 || val === '1') {
                return 'Yes';
            }
            if (val === false || val === 0 || val === '0') {
                return 'No';
            }
            return val == null || val === '' ? '—' : String(val);
        }
        if (key === 'caretaker_overlap') {
            if (val === true || val === 1 || val === '1') {
                return 'Yes';
            }
            return 'No';
        }
        if (key === 'availability_conflict') {
            var s = stringifyConflict(val);
            return s || '—';
        }
        if (MONEY_KEYS[key]) {
            if (val === null || val === undefined || val === '') {
                return '—';
            }
            return formatMoney(val);
        }
        if (val === null || val === undefined || val === '') {
            return '—';
        }
        return String(val);
    }

    function fillDetailModal(d) {
        var dl = document.getElementById('bookingDetailDl');
        var titleEl = document.getElementById('bookingDetailTitle');
        if (!dl) {
            return;
        }
        dl.innerHTML = '';
        DETAIL_ROWS.forEach(function (row) {
            var key = row.key;
            var label = row.label;
            var raw = key === '_booking_id' ? pickBookingId(d) : d[key];
            var text = formatValue(key, raw, d);
            var dt = document.createElement('dt');
            dt.textContent = label;
            var dd = document.createElement('dd');
            dd.textContent = text;
            if (MULTILINE_KEYS[key] || key === 'availability_conflict') {
                dd.className = 'booking-detail-dd--multiline';
            }
            dl.appendChild(dt);
            dl.appendChild(dd);
        });
        if (titleEl) {
            var bid = pickBookingId(d);
            titleEl.textContent = bid ? 'Booking #' + bid : 'Booking details';
        }
    }

    function setBodyScroll(lock) {
        document.body.style.overflow = lock ? 'hidden' : '';
    }

    function openModal(el) {
        if (!el) {
            return;
        }
        el.classList.add('show');
        el.setAttribute('aria-hidden', 'false');
        setBodyScroll(true);
    }

    function closeModal(el) {
        if (!el) {
            return;
        }
        el.classList.remove('show');
        el.setAttribute('aria-hidden', 'true');
        setBodyScroll(false);
    }

    function parseBooking(el) {
        var raw = el.getAttribute('data-booking') || '{}';
        try {
            return JSON.parse(raw);
        } catch (e) {
            return {};
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var detailModal = document.getElementById('bookingDetailModal');

        document.querySelectorAll('.js-booking-detail').forEach(function (btn) {
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

        if (detailModal) {
            detailModal.addEventListener('click', function (e) {
                if (e.target === detailModal) {
                    closeModal(detailModal);
                }
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') {
                return;
            }
            if (detailModal && detailModal.classList.contains('show')) {
                closeModal(detailModal);
            }
        });

        document.querySelectorAll('.js-booking-accept-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                var btn = form.querySelector('button[type="submit"]');
                if (btn && btn.disabled) {
                    e.preventDefault();
                    return;
                }
                if (!window.confirm('Request advance payment from the client for this booking?')) {
                    e.preventDefault();
                }
            });
        });

        document.querySelectorAll('.js-booking-reject-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                var btn = form.querySelector('button[type="submit"]');
                if (btn && btn.disabled) {
                    e.preventDefault();
                    return;
                }
                if (!window.confirm('Reject this booking request? The client will be notified.')) {
                    e.preventDefault();
                }
            });
        });
    });
})();
