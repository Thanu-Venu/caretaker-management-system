/**
 * HR Payment Monitor — admin-style detail modal for recurring rows and payment rows.
 */
(function () {
    'use strict';

    var REC_FIELDS = [
        { key: 'monitor_kind', label: 'Record type' },
        { key: 'id', label: 'Recurring row ID' },
        { key: 'booking_id', label: 'Booking ID' },
        { key: 'client_id', label: 'Client ID' },
        { key: 'client_name', label: 'Client name' },
        { key: 'caretaker_id', label: 'Caregiver ID' },
        { key: 'caretaker_name', label: 'Caregiver name' },
        { key: 'service_type', label: 'Service type' },
        { key: 'basis', label: 'Basis' },
        { key: 'booking_duration', label: 'Duration (units)' },
        { key: 'booking_date', label: 'Booking date' },
        { key: 'preferred_time', label: 'Preferred time' },
        { key: 'booking_total_payment', label: 'Booking total (Rs)' },
        { key: 'booking_status', label: 'Booking status' },
        { key: 'cycle_number', label: 'Cycle #' },
        { key: 'cycle_type', label: 'Cycle type' },
        { key: 'amount', label: 'This cycle amount (Rs)' },
        { key: 'due_date', label: 'Due date' },
        { key: 'status', label: 'Recurring status' },
        { key: 'paid_at', label: 'Paid at' },
        { key: 'payment_id', label: 'Linked payment ID' },
        { key: 'grace_period_end', label: 'Grace period end' },
        { key: 'reminder_7_days_sent', label: 'Reminder 7d sent' },
        { key: 'reminder_3_days_sent', label: 'Reminder 3d sent' },
        { key: 'reminder_due_date_sent', label: 'Reminder due sent' },
        { key: 'recurring_created_at', label: 'Created at' },
        { key: 'recurring_updated_at', label: 'Updated at' },
        { key: 'booking_service_location', label: 'Service location' },
        { key: 'booking_district', label: 'District' },
        { key: 'booking_street', label: 'Street' },
        { key: 'booking_address_line1', label: 'Address line 1' },
        { key: 'booking_address_line2', label: 'Address line 2' },
        { key: 'booking_postal_code', label: 'Postal code' },
        { key: 'booking_customization', label: 'Customization' },
        { key: 'booking_customization_hours', label: 'Customization hours' },
        { key: 'booking_customization_price', label: 'Customization price (Rs)' }
    ];

    var PAY_FIELDS = [
        { key: 'monitor_kind', label: 'Record type' },
        { key: 'id', label: 'Payment ID' },
        { key: 'booking_id', label: 'Booking ID' },
        { key: 'client_id', label: 'Client ID' },
        { key: 'client_name', label: 'Client name' },
        { key: 'caretaker_id', label: 'Caregiver ID' },
        { key: 'caretaker_name', label: 'Caregiver name' },
        { key: 'service_type', label: 'Service type' },
        { key: 'basis', label: 'Basis' },
        { key: 'booking_duration', label: 'Duration (units)' },
        { key: 'booking_date', label: 'Booking date' },
        { key: 'preferred_time', label: 'Preferred time' },
        { key: 'booking_total_payment', label: 'Booking total (Rs)' },
        { key: 'booking_status', label: 'Booking status' },
        { key: 'amount', label: 'This payment (Rs)' },
        { key: 'total_booking_amount', label: 'Total booking amount (Rs)' },
        { key: 'remaining_balance', label: 'Remaining balance (Rs)' },
        { key: 'payment_type', label: 'Payment type' },
        { key: 'payment_method', label: 'Payment method' },
        { key: 'status', label: 'Payment status' },
        { key: 'due_date', label: 'Due date' },
        { key: 'paid_date', label: 'Paid date' },
        { key: 'created_at', label: 'Created at' },
        { key: 'approved_at', label: 'Approved at' },
        { key: 'payment_customization_price', label: 'Payment customization (Rs)' },
        { key: 'booking_service_location', label: 'Service location' },
        { key: 'booking_district', label: 'District' },
        { key: 'booking_street', label: 'Street' },
        { key: 'booking_address_line1', label: 'Address line 1' },
        { key: 'booking_address_line2', label: 'Address line 2' },
        { key: 'booking_postal_code', label: 'Postal code' },
        { key: 'booking_customization', label: 'Customization' },
        { key: 'booking_customization_hours', label: 'Customization hours' },
        { key: 'booking_customization_price', label: 'Booking customization price (Rs)' }
    ];

    var MONEY_KEYS = {
        booking_total_payment: true,
        amount: true,
        total_booking_amount: true,
        remaining_balance: true,
        payment_customization_price: true,
        booking_customization_price: true
    };

    var MULTILINE_KEYS = {
        booking_customization: true,
        booking_address_line1: true,
        booking_address_line2: true,
        booking_service_location: true
    };

    var BOOLISH_KEYS = {
        reminder_7_days_sent: true,
        reminder_3_days_sent: true,
        reminder_due_date_sent: true
    };

    function formatMoney(n) {
        var x = parseFloat(n);
        if (isNaN(x)) {
            return '—';
        }
        return 'Rs ' + x.toFixed(2);
    }

    function formatValue(key, val) {
        if (key === 'monitor_kind') {
            if (val === 'recurring') {
                return 'Recurring installment';
            }
            if (val === 'payment') {
                return 'Payment';
            }
        }
        if (key === 'payment_method' || key === 'payment_type' || key === 'cycle_type') {
            if (val == null || val === '') {
                return '—';
            }
            return String(val).replace(/_/g, ' ');
        }
        if (BOOLISH_KEYS[key]) {
            if (val === true || val === 1 || val === '1') {
                return 'Yes';
            }
            if (val === false || val === 0 || val === '0') {
                return 'No';
            }
            return val == null || val === '' ? '—' : String(val);
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
        var dl = document.getElementById('paymentMonitorDetailDl');
        var titleEl = document.getElementById('paymentMonitorDetailTitle');
        if (!dl) {
            return;
        }
        var kind = d.monitor_kind === 'payment' ? 'payment' : 'recurring';
        var fields = kind === 'payment' ? PAY_FIELDS : REC_FIELDS;
        dl.innerHTML = '';
        fields.forEach(function (row) {
            var key = row.key;
            var text = formatValue(key, d[key]);
            var dt = document.createElement('dt');
            dt.textContent = row.label;
            var dd = document.createElement('dd');
            dd.textContent = text;
            if (MULTILINE_KEYS[key]) {
                dd.className = 'monitor-detail-dd--multiline';
            }
            dl.appendChild(dt);
            dl.appendChild(dd);
        });
        if (titleEl) {
            if (kind === 'payment') {
                titleEl.textContent = d.id != null ? 'Payment #' + d.id : 'Payment details';
            } else {
                titleEl.textContent =
                    d.booking_id != null ? 'Recurring · Booking #' + d.booking_id : 'Recurring details';
            }
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
        if (document.querySelectorAll('.modal.show').length === 0) {
            setBodyScroll(false);
        }
    }

    function parseRow(el) {
        var raw = el.getAttribute('data-monitor-row') || '{}';
        try {
            return JSON.parse(raw);
        } catch (e) {
            return {};
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var modal = document.getElementById('paymentMonitorDetailModal');

        document.querySelectorAll('.js-monitor-detail').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                fillDetailModal(parseRow(btn));
                openModal(modal);
            });
        });

        document.querySelectorAll('[data-close-monitor-detail]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                closeModal(modal);
            });
        });

        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeModal(modal);
                }
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') {
                return;
            }
            if (modal && modal.classList.contains('show')) {
                closeModal(modal);
            }
        });
    });
})();
