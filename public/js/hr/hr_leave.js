/**
 * HR leave management — filters, detail modal, reject POST.
 */
(function () {
    'use strict';

    var DETAIL_FIELDS = [
        ['id', 'Leave ID'],
        ['caretaker_id', 'Caregiver ID'],
        ['caretaker_name', 'Caregiver name'],
        ['leave_type', 'Leave type'],
        ['start_date', 'Start date'],
        ['end_date', 'End date'],
        ['start_time', 'Start time'],
        ['end_time', 'End time'],
        ['request_days', 'Total days (request, month overlap)'],
        ['monthly_used_before_request', 'Monthly leave used before (days)'],
        ['monthly_used_after_request', 'Monthly leave after request (days)'],
        ['monthly_limit', 'Monthly leave limit (days)'],
        ['affected_booking_count', 'Affected active bookings'],
        ['replacement_required', 'Overlapping active bookings'],
        ['replacement_caretaker_id', 'Replacement caregiver ID'],
        ['replacement_caretaker_name', 'Replacement caregiver name'],
        ['reason', 'Caregiver reason'],
        ['status', 'Status'],
        ['hr_note', 'HR note'],
        ['approved_by', 'Approved by (user ID)'],
        ['approved_at', 'Approved at'],
        ['can_edit_until', 'Can edit until'],
        ['user_id', 'User ID (caretaker)']
    ];

    function getRejectUrl() {
        var el = document.getElementById('hr-leave-endpoints');
        return el ? (el.getAttribute('data-reject-url') || '') : '';
    }

    function formatValue(key, val) {
        if (key === 'replacement_required') {
            if (val === null || val === undefined || val === '') {
                return '—';
            }
            return val === true || val === 1 || val === '1' ? 'Yes' : 'No';
        }
        if (key === 'request_days') {
            if (val === null || val === undefined || val === '') {
                return '—';
            }
            var n = parseInt(val, 10);
            return isNaN(n) ? String(val) : n + ' day(s)';
        }
        if (val === null || val === undefined || val === '') {
            return '—';
        }
        if (key === 'start_time' || key === 'end_time') {
            var s = String(val);
            if (s === '00:00:00') {
                return '—';
            }
        }
        return String(val);
    }

    function fillDetailModal(d) {
        var dl = document.getElementById('leaveDetailDl');
        var titleEl = document.getElementById('leaveDetailTitle');
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
            if (key === 'reason' || key === 'hr_note') {
                dd.className = 'ddBlock';
            }
            dl.appendChild(dt);
            dl.appendChild(dd);
        });
        if (titleEl) {
            var id = d.id != null ? d.id : '';
            titleEl.textContent = id ? 'Leave request #' + id : 'Leave request';
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

    function parseRow(btn) {
        var raw = btn.getAttribute('data-leave-row') || '{}';
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

    function filterTable() {
        var typeSelect = document.getElementById('leave-type-filter');
        var statusSelect = document.getElementById('leave-status-filter');
        var table = document.getElementById('leaveTable');
        if (!typeSelect || !statusSelect || !table) {
            return;
        }
        var typeFilter = typeSelect.value.toLowerCase();
        var statusFilter = statusSelect.value.toLowerCase();
        var rows = table.querySelectorAll('tbody tr');
        rows.forEach(function (row) {
            if (row.querySelector('.leave-table-empty')) {
                return;
            }
            var cells = row.cells;
            if (!cells || cells.length < 6) {
                return;
            }
            var type = (cells[1].innerText || '').toLowerCase().trim();
            var status = (cells[5].innerText || '').toLowerCase().trim();
            var typeMatch = typeFilter === 'all' || type === typeFilter;
            var statusMatch = statusFilter === 'all' || status === statusFilter;
            row.style.display = typeMatch && statusMatch ? '' : 'none';
        });
    }

    window.hrLeaveFilterTable = filterTable;

    document.addEventListener('DOMContentLoaded', function () {
        var detailModal = document.getElementById('leaveDetailModal');
        var rejectModal = document.getElementById('leaveRejectModal');
        var rejectNote = document.getElementById('leaveRejectNote');
        var rejectUrl = getRejectUrl();
        var activeLeaveId = null;

        document.querySelectorAll('.lvView').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                fillDetailModal(parseRow(btn));
                openModal(detailModal);
            });
        });

        document.querySelectorAll('[data-close-leave-detail]').forEach(function (btn) {
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

        document.querySelectorAll('.lvReject').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (btn.disabled) {
                    return;
                }
                var id = btn.getAttribute('data-leave-id');
                if (!id) {
                    return;
                }
                activeLeaveId = id;
                if (rejectNote) {
                    rejectNote.value = '';
                }
                openModal(rejectModal);
            });
        });

        var rejectCancel = document.getElementById('leaveRejectCancel');
        var rejectSubmit = document.getElementById('leaveRejectSubmit');

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
                if (!activeLeaveId || !rejectUrl) {
                    return;
                }
                var note = rejectNote ? rejectNote.value.trim() : '';
                if (!note) {
                    window.alert('Please enter a reason for rejection.');
                    return;
                }
                submitPost(rejectUrl, {
                    leave_id: activeLeaveId,
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
            } else if (rejectModal && rejectModal.classList.contains('show')) {
                closeModal(rejectModal);
            }
        });
    });
})();
