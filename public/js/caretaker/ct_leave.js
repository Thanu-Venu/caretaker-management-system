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

    if (!form || !startInput || !endInput) {
        return;
    }

    const policy = window.leavePolicy || {
        advanceNoticeDays: 3,
        maxPerRequest: 7,
        monthlyLimit: 5
    };
    const previewConfig = window.leavePreview || { impactUrl: '' };
    let impactDebounceTimer = null;
    let activeImpactController = null;

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
        const minAdvanceIso = minAdvance.toISOString().slice(0, 10);
        
        const leaveType = leaveTypeInput ? leaveTypeInput.value : '';
        if (leaveType === 'Sick Leave') {
            startHint.textContent = `Sick leave can be requested starting from today.`;
            startInput.min = new Date().toISOString().slice(0, 10);
        } else {
            startHint.textContent = `Leave must be requested at least ${policy.advanceNoticeDays} days in advance. Earliest start: ${formatDate(minAdvanceIso)}.`;
            startInput.min = minAdvanceIso;
        }

        // Auto-correct invalid start dates
        if (startInput.value && startInput.value < startInput.min && document.activeElement !== startInput) {
            startInput.value = startInput.min;
        }

        if (startInput.value) {
            const startDateObj = parseDate(startInput.value);
            
            // Check if start month is current month
            const isCurrentMonth = startDateObj.getMonth() === new Date().getMonth() && startDateObj.getFullYear() === new Date().getFullYear();
            
            let allowedDays = 5; 
            if (isCurrentMonth && typeof policy.remainingThisMonth !== 'undefined') {
                 allowedDays = Math.min(5, policy.remainingThisMonth);
            }
            if (allowedDays < 1) allowedDays = 0;

            const maxEndDateObj = new Date(startDateObj);
            if (allowedDays > 0) {
                maxEndDateObj.setDate(maxEndDateObj.getDate() + (allowedDays - 1));
            }
            const maxEndIso = maxEndDateObj.toISOString().slice(0, 10);
            
            endInput.min = startInput.value;
            endInput.max = maxEndIso;
            endHint.textContent = allowedDays > 0 ? `End date must be between ${formatDate(startInput.value)} and ${formatDate(maxEndIso)} (Max ${allowedDays} days remaining).` : 'You have no leave days remaining this month.';
            
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
        const todayIso = new Date().toISOString().slice(0, 10);
        const minAdvanceIso = getMinAdvanceDate().toISOString().slice(0, 10);
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
            if (startInput.value) {
                const startObj = parseDate(startInput.value);
                const isCurrentMonth = startObj.getMonth() === new Date().getMonth() && startObj.getFullYear() === new Date().getFullYear();
                if (isCurrentMonth && typeof policy.remainingThisMonth !== 'undefined') {
                    if (days > policy.remainingThisMonth) {
                        errors.push(`You only have ${policy.remainingThisMonth} remaining leave days but requested ${days}.`);
                    }
                }
            }

            if (leaveType === 'Sick Leave') {
                if (days > 5) {
                    errors.push('Sick leave cannot exceed 5 days.');
                }
            } else {
                if (days > 5) {
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
        impactMessage.textContent = data.message || 'Warning: active bookings detected in this leave period.';

        if (Array.isArray(data.booking_ids) && data.booking_ids.length > 0 && impactIds && impactIdsLine) {
            impactIds.textContent = data.booking_ids.join(', ');
            impactIdsLine.hidden = false;
        } else if (impactIdsLine) {
            impactIdsLine.hidden = true;
        }
    }

    async function fetchImpactPreview() {
        if (!previewConfig.impactUrl || !startInput.value || !endInput.value || endInput.value < startInput.value) {
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
            renderImpactPreview(payload);
        } catch (error) {
            if (error.name !== 'AbortError') {
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
