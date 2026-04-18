document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('leaveRequestForm');
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    const startHint = document.getElementById('start_date_hint');
    const endHint = document.getElementById('end_date_hint');
    const durationWrap = document.getElementById('durationWrap');
    const durationBadge = document.getElementById('durationBadge');
    const inlineErrors = document.getElementById('inlineErrors');
    const bookingImpactPreview = document.getElementById('bookingImpactPreview');
    const impactMessage = document.getElementById('impactMessage');
    const impactCount = document.getElementById('impactCount');
    const impactIds = document.getElementById('impactIds');
    const leaveTypeInput = document.getElementById('leave_type');
    const impactIdsLine = document.getElementById('impactIdsLine');
    const impactBookingList = document.getElementById('impactBookingList');

    if (!form || !startInput || !endInput) {
        return;
    }

    const policy = window.leavePolicy || {
        advanceNoticeDays: 3,
        maxPerRequest: 5,
        monthlyLimit: 5
    };
    const previewConfig = window.leavePreview || { impactUrl: '' };
    let impactDebounceTimer = null;
    let activeImpactController = null;
    let lastPreviewStartDate = '';
    let serverWindow = {
        maxEndDate: null,
        startMonthUsed: null,
        startMonthRemaining: null,
        canRequest: true
    };

    function parseDate(value) {
        return value ? new Date(value + 'T00:00:00') : null;
    }

    function formatDate(value) {
        const date = parseDate(value);
        if (!date) return '';
        return date.toLocaleDateString('en-GB', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    function getInclusiveDays(startValue, endValue) {
        const startDate = parseDate(startValue);
        const endDate = parseDate(endValue);
        if (!startDate || !endDate || endDate < startDate) {
            return 0;
        }
        const diffMs = endDate.getTime() - startDate.getTime();
        return Math.floor(diffMs / 86400000) + 1;
    }

    function toLocalIsoDate(dateObj) {
        if (!(dateObj instanceof Date) || Number.isNaN(dateObj.getTime())) {
            return '';
        }
        const y = dateObj.getFullYear();
        const m = String(dateObj.getMonth() + 1).padStart(2, '0');
        const d = String(dateObj.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function getFallbackMaxEndDate(startValue) {
        const startDateObj = parseDate(startValue);
        if (!startDateObj) {
            return '';
        }
        const maxDays = Math.max(1, Number(policy.maxPerRequest) || 5);
        const maxEndDateObj = new Date(startDateObj);
        maxEndDateObj.setDate(maxEndDateObj.getDate() + (maxDays - 1));
        return toLocalIsoDate(maxEndDateObj);
    }

    function applyServerWindow(payload) {
        if (!payload || typeof payload !== 'object') {
            serverWindow = {
                maxEndDate: null,
                startMonthUsed: null,
                startMonthRemaining: null,
                canRequest: true
            };
            return;
        }

        serverWindow = {
            maxEndDate: payload.max_end_date || null,
            startMonthUsed: (typeof payload.start_month_used === 'number') ? payload.start_month_used : null,
            startMonthRemaining: (typeof payload.start_month_remaining === 'number') ? payload.start_month_remaining : null,
            canRequest: payload.can_request !== false
        };
    }

    function getMinAdvanceDate() {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const leaveType = leaveTypeInput ? leaveTypeInput.value : '';
        const notice = (leaveType === 'Sick Leave') ? 0 : Number(policy.advanceNoticeDays);

        today.setDate(today.getDate() + notice);
        return today;
    }

    function syncDateHints() {
        const minAdvance = getMinAdvanceDate();
        const minAdvanceIso = toLocalIsoDate(minAdvance);

        const leaveType = leaveTypeInput ? leaveTypeInput.value : '';
        if (leaveType === 'Sick Leave') {
            startHint.textContent = `Sick leave can be requested starting from today.`;
            startInput.min = toLocalIsoDate(new Date());
        } else {
            startHint.textContent = `Leave must be requested at least ${policy.advanceNoticeDays} days in advance. Earliest start: ${formatDate(minAdvanceIso)}.`;
            startInput.min = minAdvanceIso;
        }

        // Auto-correct invalid start dates
        if (startInput.value && startInput.value < startInput.min && document.activeElement !== startInput) {
            startInput.value = startInput.min;
        }

        if (startInput.value) {
            endInput.min = startInput.value;
            const fallbackMaxEndIso = getFallbackMaxEndDate(startInput.value);
            let maxEndIso = fallbackMaxEndIso;

            if (serverWindow.maxEndDate && serverWindow.maxEndDate < maxEndIso) {
                maxEndIso = serverWindow.maxEndDate;
            }

            if (serverWindow.canRequest === false) {
                endInput.max = startInput.value;
                endHint.textContent = 'No leave days remaining for the selected month.';
                if (endInput.value) {
                    endInput.value = '';
                }
                return;
            }

            if (!maxEndIso) {
                maxEndIso = fallbackMaxEndIso;
            }
            endInput.max = maxEndIso;
            const remainingHint = (typeof serverWindow.startMonthRemaining === 'number')
                ? ` Start-month remaining: ${serverWindow.startMonthRemaining} day(s).`
                : '';
            endHint.textContent = `End date must be between ${formatDate(startInput.value)} and ${formatDate(maxEndIso)} (maximum ${policy.maxPerRequest} days per request).${remainingHint}`;

            // Auto-correct end date if it violates max
            if (endInput.value && endInput.value > maxEndIso && document.activeElement !== endInput) {
                endInput.value = maxEndIso;
            }
        } else {
            endInput.min = startInput.min;
            endInput.max = '';
            endHint.textContent = '';
        }

        // Auto-correct invalid end dates (min bound)
        if (endInput.value && endInput.value < endInput.min && document.activeElement !== endInput) {
            endInput.value = endInput.min;
        }
    }

    function collectClientErrors() {
        const errors = [];
        const todayIso = toLocalIsoDate(new Date());
        const minAdvanceIso = toLocalIsoDate(getMinAdvanceDate());
        const leaveType = leaveTypeInput ? leaveTypeInput.value : '';

        if (startInput.value && startInput.value < todayIso) {
            errors.push('Leave cannot be requested for past dates.');
        }

        if (endInput.value && endInput.value < todayIso) {
            errors.push('Leave cannot be requested for past dates.');
        }

        if (leaveType !== 'Sick Leave' && startInput.value && startInput.value < minAdvanceIso) {
            errors.push(`Advance notice of ${policy.advanceNoticeDays} days is required for ${leaveType || 'this leave type'}.`);
        }

        if (startInput.value && endInput.value && endInput.value < startInput.value) {
            errors.push('End date must be the same as or later than the start date.');
        }

        const days = getInclusiveDays(startInput.value, endInput.value);
        if (days > 0) {
            if (startInput.value && serverWindow.canRequest === false) {
                errors.push('No leave days remaining for the selected month.');
            }

            if (startInput.value && endInput.value && serverWindow.maxEndDate && endInput.value > serverWindow.maxEndDate) {
                errors.push('Selected range exceeds your available leave days for the selected month.');
            }

            if (leaveType === 'Sick Leave') {
                if (days > Number(policy.maxPerRequest || 5)) {
                    errors.push('Sick leave cannot exceed 5 days.');
                }
            } else {
                if (days > Number(policy.maxPerRequest || 5)) {
                    errors.push('Other leave types cannot exceed 5 days per request.');
                }
            }
        }

        return Array.from(new Set(errors));
    }

    function renderInlineErrors(errors) {
        if (!inlineErrors) {
            return;
        }

        if (!errors.length) {
            inlineErrors.innerHTML = '';
            inlineErrors.style.display = 'none';
            return;
        }

        inlineErrors.style.display = 'block';
        inlineErrors.innerHTML = errors.map((message) => `<p>${message}</p>`).join('');
    }

    function updateDurationBadge() {
        const days = getInclusiveDays(startInput.value, endInput.value);

        if (!durationWrap || !durationBadge) {
            return;
        }

        if (days <= 0) {
            durationWrap.hidden = true;
            return;
        }

        durationWrap.hidden = false;
        durationBadge.textContent = `${days} ${days === 1 ? 'day' : 'days'}`;
    }

    function hideImpactPreview() {
        if (!bookingImpactPreview) {
            return;
        }
        bookingImpactPreview.hidden = true;
        if (impactIdsLine) {
            impactIdsLine.hidden = true;
        }
        if (impactBookingList) {
            impactBookingList.innerHTML = '';
            impactBookingList.hidden = true;
        }
    }

    function renderImpactPreview(data) {
        if (!bookingImpactPreview || !impactCount || !impactMessage) {
            return;
        }

        if (!data || !data.hasImpact) {
            hideImpactPreview();
            return;
        }

        bookingImpactPreview.hidden = false;
        impactCount.textContent = String(data.count || 0);
        impactMessage.textContent = data.message
            || 'You have scheduled bookings in this leave period. HR may arrange cover if your leave is approved.';

        if (impactBookingList) {
            if (Array.isArray(data.service_dates) && data.service_dates.length > 0) {
                impactBookingList.innerHTML = data.service_dates.map((row) => {
                    const id = row.booking_id ?? row.bookingId ?? '—';
                    const s = row.start_date ?? '';
                    const e = row.end_date ?? s;
                    const range = (e && e !== s) ? `${s} → ${e}` : s;
                    return `<li>Booking #${id}${range ? ` — ${range}` : ''}</li>`;
                }).join('');
                impactBookingList.hidden = false;
            } else {
                impactBookingList.innerHTML = '';
                impactBookingList.hidden = true;
            }
        }

        if (Array.isArray(data.booking_ids) && data.booking_ids.length > 0 && impactIds && impactIdsLine) {
            impactIds.textContent = data.booking_ids.map((id) => `#${id}`).join(', ');
            impactIdsLine.hidden = false;
        } else if (impactIdsLine) {
            impactIdsLine.hidden = true;
        }
    }

    async function fetchImpactPreview() {
        if (!previewConfig.impactUrl || !startInput.value) {
            hideImpactPreview();
            return;
        }

        if (activeImpactController) {
            activeImpactController.abort();
        }

        activeImpactController = new AbortController();

        const body = new URLSearchParams({
            start_date: startInput.value,
            end_date: endInput.value
        });
        if (previewConfig.leaveId) {
            body.append('leave_id', String(previewConfig.leaveId));
        }

        try {
            const response = await fetch(previewConfig.impactUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: body.toString(),
                signal: activeImpactController.signal
            });

            if (!response.ok) {
                hideImpactPreview();
                return;
            }

            const payload = await response.json();
            applyServerWindow(payload);
            syncDateHints();
            renderImpactPreview(payload);
        } catch (error) {
            if (error.name !== 'AbortError') {
                applyServerWindow(null);
                hideImpactPreview();
            }
        }
    }

    function queueImpactPreview() {
        if (impactDebounceTimer) {
            clearTimeout(impactDebounceTimer);
        }

        impactDebounceTimer = setTimeout(() => {
            fetchImpactPreview();
        }, 250);
    }

    function syncFormState() {
        if (startInput.value !== lastPreviewStartDate) {
            lastPreviewStartDate = startInput.value;
            applyServerWindow(null);
        }
        syncDateHints();
        updateDurationBadge();
        const errors = collectClientErrors();
        renderInlineErrors(errors);

        if (errors.length > 0) {
            hideImpactPreview();
            return;
        }

        queueImpactPreview();
    }

    leaveTypeInput.addEventListener('change', syncFormState);
    startInput.addEventListener('change', syncFormState);
    endInput.addEventListener('change', syncFormState);
    startInput.addEventListener('input', syncFormState);
    endInput.addEventListener('input', syncFormState);

    form.addEventListener('submit', (event) => {
        const errors = collectClientErrors();
        renderInlineErrors(errors);
        if (errors.length > 0) {
            event.preventDefault();
        }
    });

    syncFormState();
});
