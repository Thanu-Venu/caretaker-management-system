/**
 * HR payment management: admin-style detail modal; approve/reject with confirm (safe POST).
 */
(function () {
    'use strict';

    var DETAIL_ROWS = [
        { key: 'id', label: 'Payment ID' },
        { key: 'booking_id', label: 'Booking ID' },
        { key: 'client_id', label: 'Client ID' },
        { key: 'client_name', label: 'Client name' },
        { key: 'client_phone', label: 'Client phone' },
        { key: 'caretaker_id', label: 'Caregiver ID' },
        { key: 'caretaker_name', label: 'Caregiver name' },
        { key: 'status', label: 'Payment status' },
        { key: 'payment_type', label: 'Payment type' },
        { key: 'payment_method', label: 'Payment method' },
        { key: 'amount', label: 'This payment (Rs)' },
        { key: 'total_booking_amount', label: 'Total booking amount (Rs)' },
        { key: 'remaining_balance', label: 'Remaining balance (Rs)' },
        { key: 'payment_customization_price', label: 'Payment record customization (Rs)' },
        { key: 'due_date', label: 'Due date' },
        { key: 'paid_date', label: 'Paid date' },
        { key: 'created_at', label: 'Payment created at' },
        { key: 'approved_at', label: 'Approved at' },
        { key: 'service_type', label: 'Service type' },
        { key: 'booking_status', label: 'Booking status' },
        { key: 'booking_date', label: 'Booking date' },
        { key: 'preferred_time', label: 'Preferred time' },
        { key: 'basis', label: 'Basis' },
        { key: 'duration', label: 'Duration (units)' },
        { key: 'total_payment', label: 'Booking total payment (Rs)' },
        { key: 'booking_service_location', label: 'Service location' },
        { key: 'booking_district', label: 'District' },
        { key: 'booking_street', label: 'Street' },
        { key: 'booking_address_line1', label: 'Address line 1' },
        { key: 'booking_address_line2', label: 'Address line 2' },
        { key: 'booking_postal_code', label: 'Postal code' },
        { key: 'booking_customization', label: 'Customization' },
        { key: 'booking_customization_hours', label: 'Customization hours' },
        { key: 'booking_customization_price', label: 'Customization price on booking (Rs)' },
        { key: 'booking_service_start_date', label: 'Service start date' },
        { key: 'booking_advance_paid_date', label: 'Advance paid date' },
        { key: 'booking_advance_amount', label: 'Advance amount (Rs)' },
        { key: 'booking_advance_balance', label: 'Advance balance (Rs)' },
        { key: 'booking_advance_months', label: 'Advance months' },
        { key: 'booking_total_months', label: 'Total months' },
        { key: 'booking_refund_status', label: 'Refund status' },
        { key: 'booking_created_at', label: 'Booking created at' }
    ];

    var MONEY_KEYS = {
        amount: true,
        total_booking_amount: true,
        remaining_balance: true,
        total_payment: true,
        payment_customization_price: true,
        booking_customization_price: true,
        booking_advance_amount: true,
        booking_advance_balance: true
    };

    var MULTILINE_KEYS = {
        booking_customization: true,
        booking_address_line1: true,
        booking_address_line2: true,
        booking_service_location: true
    };

    function formatMoney(n) {
        var x = parseFloat(n);
        if (isNaN(x)) {
            return '—';
        }
        return 'Rs ' + x.toFixed(2);
    }

    function formatMethod(val) {
        if (val == null || val === '') {
            return '—';
        }
        return String(val).replace(/_/g, ' ');
    }

    function formatValue(key, val) {
        if (key === 'payment_method') {
            return formatMethod(val);
        }
        if (key === 'payment_type' || key === 'status') {
            if (val == null || val === '') {
                return '—';
            }
            var s = String(val).replace(/_/g, ' ');
            return s.charAt(0).toUpperCase() + s.slice(1);
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
        var dl = document.getElementById('paymentDetailDl');
        var titleEl = document.getElementById('paymentDetailTitle');
        if (!dl) {
            return;
        }
        dl.innerHTML = '';
        DETAIL_ROWS.forEach(function (row) {
            var key = row.key;
            var label = row.label;
            var text = formatValue(key, d[key]);
            var dt = document.createElement('dt');
            dt.textContent = label;
            var dd = document.createElement('dd');
            dd.textContent = text;
            if (MULTILINE_KEYS[key]) {
                dd.className = 'ddBlock';
            }
            dl.appendChild(dt);
            dl.appendChild(dd);
        });
        if (titleEl) {
            var pid = d.id != null ? d.id : '';
            var bid = d.booking_id != null ? d.booking_id : '';
            titleEl.textContent = pid ? 'Payment #' + pid + (bid ? ' · Booking #' + bid : '') : 'Payment details';
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

    function parsePayment(el) {
        var raw = el.getAttribute('data-payment') || '{}';
        try {
            return JSON.parse(raw);
        } catch (e) {
            return {};
        }
    }

    function getConfig() {
        var el = document.getElementById('hr-payments-config');
        if (!el) {
            return { approveUrl: '', rejectUrl: '' };
        }
        return {
            approveUrl: el.getAttribute('data-approve-url') || '',
            rejectUrl: el.getAttribute('data-reject-url') || ''
        };
    }

    function submitPost(url, fields) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        Object.keys(fields).forEach(function (name) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = fields[name] != null ? String(fields[name]) : '';
            form.appendChild(input);
        });
        document.body.appendChild(form);
        form.submit();
    }

    document.addEventListener('DOMContentLoaded', function () {
        var detailModal = document.getElementById('paymentDetailModal');
        var confirmModal = document.getElementById('paymentConfirmModal');
        var confirmTitle = document.getElementById('paymentConfirmTitle');
        var confirmText = document.getElementById('paymentConfirmText');
        var rejectReason = document.getElementById('paymentRejectReason');
        var confirmSubmit = document.getElementById('paymentConfirmSubmit');
        var confirmCancel = document.getElementById('paymentConfirmCancel');
        var cfg = getConfig();

        var actionType = '';
        var selectedPaymentId = null;

        document.querySelectorAll('.payView').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                fillDetailModal(parsePayment(btn));
                openModal(detailModal);
            });
        });

        document.querySelectorAll('[data-close-payment-detail]').forEach(function (btn) {
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

        function openConfirm(type, paymentId) {
            actionType = type;
            selectedPaymentId = paymentId;
            if (!confirmModal || !confirmText || !confirmTitle || !rejectReason || !confirmSubmit) {
                return;
            }
            rejectReason.value = '';
            if (type === 'approve') {
                confirmTitle.textContent = 'Approve payment';
                confirmText.textContent = 'Approve this payment? The booking may be updated and parties notified.';
                rejectReason.style.display = 'none';
                confirmSubmit.textContent = 'Approve';
                confirmSubmit.className = 'btn primary';
                confirmSubmit.classList.remove('reject');
            } else {
                confirmTitle.textContent = 'Reject payment';
                confirmText.textContent = 'Reject this payment? The client will be notified.';
                rejectReason.style.display = 'block';
                confirmSubmit.textContent = 'Reject';
                confirmSubmit.className = 'btn primary';
                confirmSubmit.classList.add('reject');
            }
            openModal(confirmModal);
        }

        document.querySelectorAll('.payOK').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (btn.disabled) {
                    return;
                }
                var id = btn.getAttribute('data-payment-id');
                if (!id) {
                    return;
                }
                openConfirm('approve', id);
            });
        });

        document.querySelectorAll('.payNo').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (btn.disabled) {
                    return;
                }
                var id = btn.getAttribute('data-payment-id');
                if (!id) {
                    return;
                }
                openConfirm('reject', id);
            });
        });

        if (confirmCancel) {
            confirmCancel.addEventListener('click', function () {
                closeModal(confirmModal);
            });
        }

        if (confirmModal) {
            confirmModal.addEventListener('click', function (e) {
                if (e.target === confirmModal) {
                    closeModal(confirmModal);
                }
            });
        }

        if (confirmSubmit) {
            confirmSubmit.addEventListener('click', function () {
                if (!selectedPaymentId) {
                    return;
                }
                if (actionType === 'reject') {
                    var reason = rejectReason ? rejectReason.value.trim() : '';
                    if (!reason) {
                        window.alert('Please enter a rejection reason.');
                        return;
                    }
                    submitPost(cfg.rejectUrl, { payment_id: selectedPaymentId, reason: reason });
                    return;
                }
                submitPost(cfg.approveUrl, { payment_id: selectedPaymentId });
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') {
                return;
            }
            if (detailModal && detailModal.classList.contains('show')) {
                closeModal(detailModal);
            } else if (confirmModal && confirmModal.classList.contains('show')) {
                closeModal(confirmModal);
            }
        });
    });
})();
