/**
 * SmartCare custom date picker
 * Replaces native date inputs with a DOM calendar to avoid
 * Chromium/Windows popup paint glitches in overlays and modals.
 * Time fields use the browser native picker.
 */
(function () {
    'use strict';

    var ENHANCED_ATTR = 'data-sc-dt-enhanced';
    var DATE_ROOT_CLASS = 'sc-date';

    function ensureStyles() {
        if (document.getElementById('sc-custom-datetime-styles')) return;
        var style = document.createElement('style');
        style.id = 'sc-custom-datetime-styles';
        style.textContent =
            '.sc-date{position:relative;width:100%;min-width:0}' +
            '.sc-dt-native{position:absolute!important;inset:0!important;opacity:0!important;pointer-events:none!important;margin:0!important;width:100%!important;height:100%!important}' +
            '.sc-date-trigger{width:100%;min-height:40px;padding:8px 36px 8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;color:#0f172a;font:inherit;text-align:left;display:flex;align-items:center;justify-content:space-between;gap:10px;cursor:pointer}' +
            '.sc-date.is-open .sc-date-trigger{border-color:#1e88e5;box-shadow:0 0 0 3px rgba(30,136,229,.22)}' +
            '.sc-dt-label{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}' +
            '.sc-dt-arrow{position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#64748b;pointer-events:none;font-size:12px}' +
            '.sc-date-panel{position:absolute;left:0;top:calc(100% + 4px);z-index:3200;background:#fff;border:1px solid #cbd5e1;border-radius:8px;box-shadow:0 10px 24px rgba(15,23,42,.14);display:none;width:min(320px,100%)}' +
            '.sc-date.is-open .sc-date-panel{display:block}' +
            '.sc-date-head{display:flex;align-items:center;justify-content:space-between;padding:6px 8px;border-bottom:1px solid #e2e8f0}' +
            '.sc-date-nav{border:1px solid #e2e8f0;background:#fff;border-radius:6px;min-width:26px;height:26px;line-height:1;cursor:pointer;color:#334155}' +
            '.sc-date-month{font-weight:600;color:#0f172a;font-size:12px}' +
            '.sc-date-grid{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:3px;padding:6px}' +
            '.sc-date-dow{font-size:10px;font-weight:700;color:#64748b;text-align:center;padding:2px 0}' +
            '.sc-date-day{border:1px solid transparent;background:#fff;border-radius:6px;height:28px;font:inherit;font-size:12px;cursor:pointer;color:#0f172a}' +
            '.sc-date-day:hover{background:rgba(30,136,229,.09)}' +
            '.sc-date-day.is-out{color:#94a3b8}' +
            '.sc-date-day.is-selected{background:#1e88e5;color:#fff}' +
            '.sc-date-day:disabled{color:#94a3b8;background:#f8fafc;cursor:not-allowed}' +
            '.sc-date.is-disabled .sc-date-trigger{opacity:.7;cursor:not-allowed}' +
            '.sc-date.is-invalid .sc-date-trigger{border-color:#e63946!important;box-shadow:0 0 0 1px rgba(230,57,70,.25)}';
        document.head.appendChild(style);
    }

    function isEnhanceable(input) {
        if (!input || input.tagName !== 'INPUT') return false;
        if (input.hasAttribute(ENHANCED_ATTR)) return false;
        if (input.dataset.nativePicker === 'true') return false;
        if (input.readOnly) return false;
        return input.type === 'date';
    }

    function toIsoDate(dateObj) {
        var y = dateObj.getFullYear();
        var m = String(dateObj.getMonth() + 1).padStart(2, '0');
        var d = String(dateObj.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
    }

    function parseIsoDate(value) {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(String(value || ''))) return null;
        var parts = value.split('-');
        var y = Number(parts[0]);
        var m = Number(parts[1]);
        var d = Number(parts[2]);
        var dt = new Date(y, m - 1, d);
        if (dt.getFullYear() !== y || dt.getMonth() !== m - 1 || dt.getDate() !== d) return null;
        return dt;
    }

    function formatDateLabel(value) {
        var dt = parseIsoDate(value);
        if (!dt) return '';
        return dt.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: '2-digit' });
    }

    function closeAll(exceptRoot) {
        document.querySelectorAll('.' + DATE_ROOT_CLASS + '.is-open').forEach(function (root) {
            if (exceptRoot && root === exceptRoot) return;
            root.classList.remove('is-open');
            var trigger = root.querySelector('.sc-date-trigger');
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
        });
    }

    function bindRequiredValidation(form) {
        if (!form || form.dataset.scDateTimeValidationBound === '1') return;
        form.dataset.scDateTimeValidationBound = '1';

        form.addEventListener(
            'submit',
            function (e) {
                var invalid = null;
                form.querySelectorAll('input.sc-dt-native[data-sc-required="1"]').forEach(function (inp) {
                    var root = inp.closest('.' + DATE_ROOT_CLASS);
                    if (root) root.classList.remove('is-invalid');
                    if (!invalid && String(inp.value || '').trim() === '') invalid = inp;
                });
                if (!invalid) return;

                e.preventDefault();
                var badRoot = invalid.closest('.' + DATE_ROOT_CLASS);
                if (badRoot) {
                    badRoot.classList.add('is-invalid');
                    var trg = badRoot.querySelector('.sc-date-trigger');
                    if (trg) trg.focus();
                }
            },
            true
        );
    }

    function enhanceDateInput(input) {
        input.setAttribute(ENHANCED_ATTR, '1');
        input.classList.add('sc-dt-native');

        if (input.required) {
            input.dataset.scRequired = '1';
            input.required = false;
        }

        var root = document.createElement('div');
        root.className = DATE_ROOT_CLASS;
        if (input.disabled) root.classList.add('is-disabled');

        var trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'sc-date-trigger';
        trigger.setAttribute('aria-haspopup', 'dialog');
        trigger.setAttribute('aria-expanded', 'false');

        var label = document.createElement('span');
        label.className = 'sc-dt-label';
        trigger.appendChild(label);

        var arrow = document.createElement('span');
        arrow.className = 'sc-dt-arrow';
        arrow.innerHTML = '&#9662;';
        trigger.appendChild(arrow);

        var panel = document.createElement('div');
        panel.className = 'sc-date-panel';

        var head = document.createElement('div');
        head.className = 'sc-date-head';
        var prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.className = 'sc-date-nav';
        prevBtn.textContent = '<';
        var monthLabel = document.createElement('span');
        monthLabel.className = 'sc-date-month';
        var nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.className = 'sc-date-nav';
        nextBtn.textContent = '>';
        head.appendChild(prevBtn);
        head.appendChild(monthLabel);
        head.appendChild(nextBtn);

        var grid = document.createElement('div');
        grid.className = 'sc-date-grid';

        panel.appendChild(head);
        panel.appendChild(grid);

        var parent = input.parentNode;
        parent.insertBefore(root, input);
        root.appendChild(input);
        root.appendChild(trigger);
        root.appendChild(panel);

        var viewMonth = parseIsoDate(input.value) || new Date();
        viewMonth = new Date(viewMonth.getFullYear(), viewMonth.getMonth(), 1);

        function inRange(dateObj) {
            var min = parseIsoDate(input.min);
            var max = parseIsoDate(input.max);
            var t = new Date(dateObj.getFullYear(), dateObj.getMonth(), dateObj.getDate()).getTime();
            if (min && t < min.getTime()) return false;
            if (max && t > max.getTime()) return false;
            return true;
        }

        function rebuildCalendar() {
            grid.innerHTML = '';
            var dows = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
            dows.forEach(function (dow) {
                var el = document.createElement('div');
                el.className = 'sc-date-dow';
                el.textContent = dow;
                grid.appendChild(el);
            });

            monthLabel.textContent = viewMonth.toLocaleDateString(undefined, { month: 'long', year: 'numeric' });

            var first = new Date(viewMonth.getFullYear(), viewMonth.getMonth(), 1);
            var start = new Date(first);
            start.setDate(1 - first.getDay());
            var selectedIso = String(input.value || '');

            for (var i = 0; i < 42; i += 1) {
                var current = new Date(start);
                current.setDate(start.getDate() + i);
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'sc-date-day';
                if (current.getMonth() !== viewMonth.getMonth()) btn.classList.add('is-out');
                var iso = toIsoDate(current);
                btn.textContent = String(current.getDate());
                btn.dataset.value = iso;
                if (iso === selectedIso) btn.classList.add('is-selected');

                var enabled = inRange(current) && !input.disabled;
                btn.disabled = !enabled;

                btn.addEventListener('click', function (e) {
                    var value = e.currentTarget.dataset.value || '';
                    input.value = value;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    root.classList.remove('is-invalid');
                    closeAll();
                    trigger.focus();
                });
                grid.appendChild(btn);
            }
        }

        function syncFromNative() {
            var formatted = formatDateLabel(input.value);
            var fallback = input.getAttribute('placeholder') || 'Select date';
            label.textContent = formatted || fallback;
            root.classList.toggle('is-disabled', !!input.disabled);
            if (input.value) {
                var selectedDate = parseIsoDate(input.value);
                if (selectedDate) viewMonth = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);
            }
            rebuildCalendar();
        }

        function openPanel() {
            if (input.disabled) return;
            closeAll(root);
            root.classList.add('is-open');
            trigger.setAttribute('aria-expanded', 'true');
        }

        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            if (root.classList.contains('is-open')) {
                closeAll();
            } else {
                openPanel();
            }
        });

        trigger.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openPanel();
            }
            if (e.key === 'Escape') closeAll();
        });

        prevBtn.addEventListener('click', function () {
            viewMonth = new Date(viewMonth.getFullYear(), viewMonth.getMonth() - 1, 1);
            rebuildCalendar();
        });

        nextBtn.addEventListener('click', function () {
            viewMonth = new Date(viewMonth.getFullYear(), viewMonth.getMonth() + 1, 1);
            rebuildCalendar();
        });

        input.addEventListener('change', syncFromNative);
        input.addEventListener('input', syncFromNative);

        if (typeof MutationObserver !== 'undefined') {
            var observer = new MutationObserver(function () {
                syncFromNative();
            });
            observer.observe(input, {
                attributes: true,
                attributeFilter: ['value', 'min', 'max', 'disabled', 'readonly', 'placeholder']
            });
        }

        if (input.form) {
            bindRequiredValidation(input.form);
            input.form.addEventListener('reset', function () {
                setTimeout(syncFromNative, 0);
            });
        }

        syncFromNative();
    }

    function init(scopeRoot) {
        ensureStyles();
        var scope = scopeRoot || document;
        scope.querySelectorAll('input').forEach(function (input) {
            if (!isEnhanceable(input)) return;
            enhanceDateInput(input);
        });
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.' + DATE_ROOT_CLASS)) {
            closeAll();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeAll();
    });

    document.addEventListener('DOMContentLoaded', function () {
        init(document);
    });

    window.SmartCare = window.SmartCare || {};
    window.SmartCare.CustomDateTime = {
        init: init,
        closeAll: closeAll
    };
})();
