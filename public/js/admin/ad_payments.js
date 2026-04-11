/**
 * Admin payments list: icon opens premium row-detail modal (aligned with ad_bookings / admin-ui).
 */
(function () {
    'use strict';

    var MODAL_ID = 'adminPaymentDetailModal';

    function ucfirst(value) {
        var s = String(value || '').trim();
        if (!s) {
            return '-';
        }
        return s.charAt(0).toUpperCase() + s.slice(1);
    }

    function formatMethod(value) {
        return ucfirst(String(value || '').replace(/_/g, ' '));
    }

    function formatMoney(value) {
        var num = Number(value || 0);
        return (
            'LKR ' +
            num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
        );
    }

    function formatId(value) {
        if (value === undefined || value === null || value === '') {
            return '-';
        }
        return '#' + value;
    }

    /** [key, label, formatFn optional] — primary order matches admin detail page groupings */
    var FIELD_ORDER = [
        ['payment_id', 'Payment ID', formatId],
        ['booking_id', 'Booking ID', formatId],
        ['status', 'Payment status', ucfirst],
        ['payment_type', 'Payment type', ucfirst],
        ['payment_method', 'Payment method', formatMethod],
        ['amount', 'Amount', formatMoney],
        ['remaining_balance', 'Remaining balance', formatMoney],
        ['total_booking_amount', 'Total booking amount', formatMoney],
        ['customization_price', 'Customization price', formatMoney],
        ['booking_total_payment', 'Booking total payment', formatMoney],
        ['due_date', 'Due date'],
        ['paid_date', 'Paid date'],
        ['created_at', 'Created at'],
        ['approved_at', 'Approved at'],
        ['client_name', 'Client'],
        ['client_email', 'Client email'],
        ['client_phone', 'Client phone'],
        ['caretaker_name', 'Caretaker'],
        ['service_type', 'Service type'],
        ['basis', 'Basis'],
        ['duration', 'Duration'],
        ['booking_status', 'Booking status'],
        ['booking_date', 'Booking date'],
        ['service_start_date', 'Service start date'],
        ['preferred_time', 'Preferred time'],
        ['district', 'District'],
        ['street', 'Street'],
        ['address_line1', 'Address line 1'],
        ['address_line2', 'Address line 2'],
        ['postal_code', 'Postal code'],
        ['customization', 'Customization notes'],
    ];

    function labelForKey(key) {
        var map = {
            client_id: 'Client ID',
            caretaker_id: 'Caretaker ID',
        };
        if (map[key]) {
            return map[key];
        }
        return key
            .replace(/_/g, ' ')
            .replace(/\b\w/g, function (c) {
                return c.toUpperCase();
            });
    }

    function formatField(key, raw, formatFn) {
        if (raw === undefined || raw === null || raw === '') {
            return '-';
        }
        if (typeof formatFn === 'function') {
            return formatFn(raw);
        }
        return String(raw);
    }

    function appendRow(dl, label, text) {
        var dt = document.createElement('dt');
        dt.textContent = label;
        var dd = document.createElement('dd');
        dd.textContent = text;
        dl.appendChild(dt);
        dl.appendChild(dd);
    }

    function closeModal(modal) {
        if (!modal) {
            return;
        }
        modal.classList.remove('show');
        document.body.style.overflow = '';
        modal.setAttribute('aria-hidden', 'true');
    }

    function ensureModal() {
        var modal = document.getElementById(MODAL_ID);
        if (modal) {
            return modal;
        }
        modal = document.createElement('div');
        modal.id = MODAL_ID;
        modal.className = 'modal admin-row-detail-modal';
        modal.setAttribute('aria-hidden', 'true');
        modal.innerHTML =
            '<div class="modal-content admin-row-detail-modal__content" role="dialog" aria-modal="true" aria-labelledby="adminPaymentDetailTitle">' +
            '<button type="button" class="modal-close admin-row-detail-modal__close" aria-label="Close"><i class="bx bx-x" aria-hidden="true"></i></button>' +
            '<header class="admin-row-detail-modal__header">' +
            '<span class="admin-row-detail-modal__header-icon" aria-hidden="true"><i class="bx bx-show"></i></span>' +
            '<h3 id="adminPaymentDetailTitle" class="admin-row-detail-modal__title">Payment details</h3>' +
            '</header>' +
            '<dl class="admin-row-detail-modal__dl"></dl>' +
            '</div>';
        document.body.appendChild(modal);

        modal.querySelector('.admin-row-detail-modal__close').addEventListener('click', function () {
            closeModal(modal);
        });
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal(modal);
            }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.classList.contains('show')) {
                closeModal(modal);
            }
        });
        return modal;
    }

    function openPaymentModal(payload) {
        var modal = ensureModal();
        var dl = modal.querySelector('.admin-row-detail-modal__dl');
        if (!dl) {
            return;
        }
        dl.innerHTML = '';

        var shown = {};
        for (var i = 0; i < FIELD_ORDER.length; i++) {
            var entry = FIELD_ORDER[i];
            var key = entry[0];
            var label = entry[1];
            var fmt = entry[2];
            shown[key] = true;
            var text = formatField(key, payload[key], fmt);
            appendRow(dl, label, text);
        }

        Object.keys(payload).forEach(function (key) {
            if (shown[key]) {
                return;
            }
            var val = payload[key];
            if (val === undefined || val === null || val === '') {
                return;
            }
            appendRow(dl, labelForKey(key), String(val));
        });

        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function init() {
        var buttons = document.querySelectorAll('.js-payment-detail');
        if (!buttons.length) {
            return;
        }

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var raw = btn.getAttribute('data-payment') || '{}';
                var payload;
                try {
                    payload = JSON.parse(raw);
                } catch (err) {
                    payload = {};
                }
                openPaymentModal(payload);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
