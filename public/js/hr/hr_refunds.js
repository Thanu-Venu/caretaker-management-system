/**
 * HR Refunds list — admin-style detail modal; approve / decline / complete via existing POST routes.
 */
(function () {
    'use strict';

    var DETAIL_KEYS = [
        ['id', 'Refund ID'],
        ['booking_id', 'Booking ID'],
        ['client_id', 'Client ID'],
        ['client_name', 'Client name'],
        ['client_email', 'Client email'],
        ['caretaker_name', 'Caregiver name'],
        ['service_type', 'Service type'],
        ['basis', 'Basis'],
        ['duration', 'Duration (units)'],
        ['booking_date', 'Booking date'],
        ['cancellation_type', 'Cancellation type'],
        ['total_paid', 'Total paid (LKR)'],
        ['service_used_amount', 'Service used amount (LKR)'],
        ['cancellation_fee', 'Cancellation fee (LKR)'],
        ['refund_amount', 'Refund amount (LKR)'],
        ['status', 'Status'],
        ['created_at', 'Created at'],
        ['updated_at', 'Updated at'],
        ['approved_at', 'Approved at'],
        ['processed_at', 'Processed at'],
        ['approved_by_name', 'Approved by'],
        ['refund_method', 'Refund method'],
        ['refund_reference', 'Refund reference'],
        ['admin_notes', 'Admin notes']
    ];

    function getEndpoints() {
        var el = document.getElementById('hr-refunds-endpoints');
        if (!el) {
            return { processUrl: '', completeUrl: '' };
        }
        return {
            processUrl: el.getAttribute('data-process-url') || '',
            completeUrl: el.getAttribute('data-complete-url') || ''
        };
    }

    function formatMoneyLKR(val) {
        var x = parseFloat(val);
        if (isNaN(x)) {
            return '—';
        }
        return 'LKR ' + x.toFixed(2);
    }

    function formatValue(key, val) {
        if (val === null || val === undefined || val === '') {
            return '—';
        }
        if (
            key === 'total_paid' ||
            key === 'service_used_amount' ||
            key === 'cancellation_fee' ||
            key === 'refund_amount'
        ) {
            return formatMoneyLKR(val);
        }
        if (key === 'cancellation_type') {
            return String(val).replace(/_/g, ' ');
        }
        return String(val);
    }

    function appendDl(dl, label, text, multiline) {
        var dt = document.createElement('dt');
        dt.textContent = label;
        var dd = document.createElement('dd');
        dd.textContent = text;
        if (multiline) {
            dd.className = 'refund-detail-dd--multiline';
        }
        dl.appendChild(dt);
        dl.appendChild(dd);
    }

    function fillDetailModal(d) {
        var dl = document.getElementById('refundDetailDl');
        var titleEl = document.getElementById('refundDetailTitle');
        if (!dl) {
            return;
        }
        dl.innerHTML = '';

        DETAIL_KEYS.forEach(function (pair) {
            var key = pair[0];
            var label = pair[1];
            appendDl(dl, label, formatValue(key, d[key]), key === 'admin_notes');
        });

        var calc = d._calculation;
        if (calc && typeof calc === 'object' && Object.keys(calc).length > 0) {
            try {
                appendDl(dl, 'Calculation breakdown', JSON.stringify(calc, null, 2), true);
            } catch (err) {
                appendDl(dl, 'Calculation breakdown', '—', false);
            }
        } else {
            var raw = d.refund_calculation;
            if (typeof raw === 'string' && raw.trim() !== '') {
                appendDl(dl, 'Refund calculation (raw)', raw, true);
            }
        }

        if (titleEl) {
            titleEl.textContent = d.id != null ? 'Refund #' + d.id : 'Refund details';
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
        var raw = el.getAttribute('data-refund-row') || '{}';
        try {
            return JSON.parse(raw);
        } catch (e) {
            return {};
        }
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
        var detailModal = document.getElementById('refundDetailModal');
        var processModal = document.getElementById('refundProcessModal');
        var processTitle = document.getElementById('refundProcessTitle');
        var processText = document.getElementById('refundProcessText');
        var processNotes = document.getElementById('refundProcessNotes');
        var processSubmit = document.getElementById('refundProcessSubmit');
        var processCancel = document.getElementById('refundProcessCancel');

        var completeModal = document.getElementById('refundCompleteModal');
        var completeMethod = document.getElementById('refundCompleteMethod');
        var completeReference = document.getElementById('refundCompleteReference');
        var completeNotes = document.getElementById('refundCompleteNotes');
        var completeSubmit = document.getElementById('refundCompleteSubmit');
        var completeCancel = document.getElementById('refundCompleteCancel');

        var urls = getEndpoints();
        var processAction = '';
        var processRefundId = null;

        document.querySelectorAll('.js-refund-detail').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                fillDetailModal(parseRow(btn));
                openModal(detailModal);
            });
        });

        document.querySelectorAll('[data-close-refund-modal]').forEach(function (btn) {
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

        function openProcessModal(action, refundId) {
            processAction = action;
            processRefundId = refundId;
            if (!processModal || !processTitle || !processText || !processNotes) {
                return;
            }
            processNotes.value = '';
            if (action === 'approve') {
                processTitle.textContent = 'Approve refund';
                processText.textContent =
                    'Approve this refund? The client will be notified that the refund is approved for processing.';
            } else {
                processTitle.textContent = 'Decline refund';
                processText.textContent =
                    'Decline this refund request? The client will be notified.';
            }
            openModal(processModal);
        }

        document.querySelectorAll('.js-refund-approve').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (btn.disabled) {
                    return;
                }
                var id = btn.getAttribute('data-refund-id');
                if (!id) {
                    return;
                }
                openProcessModal('approve', id);
            });
        });

        document.querySelectorAll('.js-refund-decline').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (btn.disabled) {
                    return;
                }
                var id = btn.getAttribute('data-refund-id');
                if (!id) {
                    return;
                }
                openProcessModal('decline', id);
            });
        });

        if (processCancel) {
            processCancel.addEventListener('click', function () {
                closeModal(processModal);
            });
        }

        if (processModal) {
            processModal.addEventListener('click', function (e) {
                if (e.target === processModal) {
                    closeModal(processModal);
                }
            });
        }

        if (processSubmit) {
            processSubmit.addEventListener('click', function () {
                if (!processRefundId || !processAction) {
                    return;
                }
                submitPost(urls.processUrl, {
                    refund_id: processRefundId,
                    action: processAction,
                    notes: processNotes ? processNotes.value.trim() : ''
                });
            });
        }

        var completeRefundId = null;

        document.querySelectorAll('.js-refund-complete').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (btn.disabled) {
                    return;
                }
                var id = btn.getAttribute('data-refund-id');
                if (!id) {
                    return;
                }
                completeRefundId = id;
                if (completeMethod) {
                    completeMethod.value = '';
                }
                if (completeReference) {
                    completeReference.value = '';
                }
                if (completeNotes) {
                    completeNotes.value = '';
                }
                openModal(completeModal);
            });
        });

        if (completeCancel) {
            completeCancel.addEventListener('click', function () {
                closeModal(completeModal);
            });
        }

        if (completeModal) {
            completeModal.addEventListener('click', function (e) {
                if (e.target === completeModal) {
                    closeModal(completeModal);
                }
            });
        }

        if (completeSubmit) {
            completeSubmit.addEventListener('click', function () {
                if (!completeRefundId) {
                    return;
                }
                var method = completeMethod ? completeMethod.value.trim() : '';
                if (!method) {
                    window.alert('Please select a refund method.');
                    return;
                }
                submitPost(urls.completeUrl, {
                    refund_id: completeRefundId,
                    refund_method: method,
                    refund_reference: completeReference ? completeReference.value.trim() : '',
                    notes: completeNotes ? completeNotes.value.trim() : ''
                });
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') {
                return;
            }
            if (detailModal && detailModal.classList.contains('show')) {
                closeModal(detailModal);
            } else if (processModal && processModal.classList.contains('show')) {
                closeModal(processModal);
            } else if (completeModal && completeModal.classList.contains('show')) {
                closeModal(completeModal);
            }
        });
    });
})();
