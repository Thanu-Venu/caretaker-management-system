/**
 * HR caregiver change requests — detail modal; approve / reject POST to existing routes.
 */
(function () {
    'use strict';

    var DETAIL_FIELDS = [
        ['request_id', 'Request ID'],
        ['status', 'Change request status'],
        ['booking_id', 'Booking ID'],
        ['booking_status', 'Booking status'],
        ['client_id', 'Client ID'],
        ['client_name', 'Client name'],
        ['old_caretaker_id', 'Previous caregiver ID'],
        ['old_caretaker', 'Previous caregiver'],
        ['new_caretaker_id', 'Requested caregiver ID'],
        ['new_caretaker', 'Requested caregiver'],
        ['booking_caretaker_id', 'Assigned caregiver ID (booking)'],
        ['assigned_caretaker_name', 'Assigned caregiver name'],
        ['service_type', 'Service type'],
        ['basis', 'Basis'],
        ['duration', 'Duration (units)'],
        ['booking_date', 'Booking date'],
        ['service_start_date', 'Service start date'],
        ['preferred_time', 'Preferred time'],
        ['district', 'District'],
        ['street', 'Street'],
        ['address_line1', 'Address line 1'],
        ['address_line2', 'Address line 2'],
        ['postal_code', 'Postal code'],
        ['customization', 'Customization'],
        ['customization_hours', 'Customization hours'],
        ['customization_price', 'Customization price'],
        ['total_payment', 'Total payment'],
        ['advance_months', 'Advance months'],
        ['total_months', 'Total months'],
        ['advance_balance', 'Advance balance'],
        ['caretaker_changed_once', 'Caretaker changed once (flag)'],
        ['cancellation_reason', 'Cancellation reason'],
        ['cancelled_at', 'Cancelled at'],
        ['booking_created_at', 'Booking created at'],
        ['reason', 'Client reason (change)'],
        ['hr_note', 'HR note'],
        ['created_at', 'Change request submitted at'],
        ['reviewed_at', 'Change request reviewed at']
    ];

    function getEndpoints() {
        var el = document.getElementById('hr-change-requests-endpoints');
        if (!el) {
            return { approveUrl: '', rejectUrl: '' };
        }
        return {
            approveUrl: el.getAttribute('data-approve-url') || '',
            rejectUrl: el.getAttribute('data-reject-url') || ''
        };
    }

    function formatMoney(val) {
        var x = parseFloat(val);
        if (isNaN(x)) {
            return '—';
        }
        return 'LKR ' + x.toFixed(2);
    }

    var MONEY_KEYS = {
        total_payment: true,
        customization_price: true,
        advance_balance: true
    };

    function formatValue(key, val) {
        if (MONEY_KEYS[key]) {
            return formatMoney(val);
        }
        if (key === 'caretaker_changed_once') {
            if (val === null || val === undefined || val === '') {
                return '—';
            }
            return String(val) === '1' || val === true ? 'Yes' : 'No';
        }
        if (val === null || val === undefined || val === '') {
            return '—';
        }
        return String(val);
    }

    function fillDetailModal(d) {
        var dl = document.getElementById('changeRequestDetailDl');
        var titleEl = document.getElementById('changeRequestDetailTitle');
        if (!dl) {
            return;
        }
        dl.innerHTML = '';
        DETAIL_FIELDS.forEach(function (pair) {
            var key = pair[0];
            var label = pair[1];
            var text = formatValue(key, d[key]);
            var dt = document.createElement('dt');
            dt.textContent = label;
            var dd = document.createElement('dd');
            dd.textContent = text;
            if (key === 'reason' || key === 'hr_note' || key === 'customization' || key === 'cancellation_reason') {
                dd.className = 'change-request-detail-dd--multiline';
            }
            dl.appendChild(dt);
            dl.appendChild(dd);
        });
        if (titleEl) {
            var rid = d.request_id != null ? d.request_id : '';
            titleEl.textContent = rid ? 'Change request #' + rid : 'Change request';
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
        var raw = el.getAttribute('data-change-row') || '{}';
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
        var detailModal = document.getElementById('changeRequestDetailModal');
        var approveModal = document.getElementById('changeRequestApproveModal');
        var rejectModal = document.getElementById('changeRequestRejectModal');
        var approveNote = document.getElementById('changeApproveNote');
        var rejectNote = document.getElementById('changeRejectNote');
        var urls = getEndpoints();
        var activeRequestId = null;

        document.querySelectorAll('.js-change-detail').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                fillDetailModal(parseRow(btn));
                openModal(detailModal);
            });
        });

        document.querySelectorAll('[data-close-change-detail]').forEach(function (btn) {
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

        document.querySelectorAll('.js-change-approve').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (btn.disabled) {
                    return;
                }
                var id = btn.getAttribute('data-request-id');
                if (!id) {
                    return;
                }
                activeRequestId = id;
                if (approveNote) {
                    approveNote.value = '';
                }
                openModal(approveModal);
            });
        });

        document.querySelectorAll('.js-change-reject').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (btn.disabled) {
                    return;
                }
                var id = btn.getAttribute('data-request-id');
                if (!id) {
                    return;
                }
                activeRequestId = id;
                if (rejectNote) {
                    rejectNote.value = '';
                }
                openModal(rejectModal);
            });
        });

        var approveCancel = document.getElementById('changeApproveCancel');
        var approveSubmit = document.getElementById('changeApproveSubmit');
        var rejectCancel = document.getElementById('changeRejectCancel');
        var rejectSubmit = document.getElementById('changeRejectSubmit');

        if (approveCancel) {
            approveCancel.addEventListener('click', function () {
                closeModal(approveModal);
            });
        }
        if (approveModal) {
            approveModal.addEventListener('click', function (e) {
                if (e.target === approveModal) {
                    closeModal(approveModal);
                }
            });
        }
        if (approveSubmit) {
            approveSubmit.addEventListener('click', function () {
                if (!activeRequestId) {
                    return;
                }
                submitPost(urls.approveUrl, {
                    request_id: activeRequestId,
                    hr_note: approveNote ? approveNote.value.trim() : ''
                });
            });
        }

        if (rejectCancel) {
            rejectCancel.addEventListener('click', function () {
                closeModal(rejectModal);
            });
        }
        if (rejectModal) {
            rejectModal.addEventListener('click', function (e) {
                if (e.target === rejectModal) {
                    closeModal(rejectModal);
                }
            });
        }
        if (rejectSubmit) {
            rejectSubmit.addEventListener('click', function () {
                if (!activeRequestId) {
                    return;
                }
                var note = rejectNote ? rejectNote.value.trim() : '';
                if (!note) {
                    window.alert('Please enter a reason or note for the client.');
                    return;
                }
                submitPost(urls.rejectUrl, {
                    request_id: activeRequestId,
                    hr_note: note
                });
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') {
                return;
            }
            if (detailModal && detailModal.classList.contains('show')) {
                closeModal(detailModal);
            } else if (approveModal && approveModal.classList.contains('show')) {
                closeModal(approveModal);
            } else if (rejectModal && rejectModal.classList.contains('show')) {
                closeModal(rejectModal);
            }
        });
    });
})();
