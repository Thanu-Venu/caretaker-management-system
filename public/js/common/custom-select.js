/**
 * SmartCare custom select
 * Replaces native single-select popup with a DOM dropdown to avoid
 * Chromium/Windows native popup paint glitches in modal overlays.
 */
(function () {
    'use strict';

    var ROOT_CLASS = 'sc-select';
    var ENHANCED_ATTR = 'data-sc-enhanced';

    function isEnhanceable(select) {
        if (!select || select.tagName !== 'SELECT') return false;
        if (select.hasAttribute(ENHANCED_ATTR)) return false;
        if (select.dataset.nativeSelect === 'true') return false;
        if (select.multiple) return false;
        if ((select.size || 0) > 1) return false;
        return true;
    }

    function closeAll(except) {
        document.querySelectorAll('.' + ROOT_CLASS + '.is-open').forEach(function (root) {
            if (except && root === except) return;
            root.classList.remove('is-open');
            var trigger = root.querySelector('.sc-select-trigger');
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
        });
    }

    function bindRequiredValidation(form) {
        if (!form || form.dataset.scSelectValidationBound === '1') return;
        form.dataset.scSelectValidationBound = '1';

        form.addEventListener(
            'submit',
            function (e) {
                var invalid = null;
                form.querySelectorAll('select.sc-select-native[data-sc-required="1"]').forEach(function (sel) {
                    var root = sel.closest('.' + ROOT_CLASS);
                    if (root) root.classList.remove('is-invalid');
                    if (!invalid && String(sel.value || '').trim() === '') {
                        invalid = sel;
                    }
                });

                if (invalid) {
                    e.preventDefault();
                    var badRoot = invalid.closest('.' + ROOT_CLASS);
                    if (badRoot) {
                        badRoot.classList.add('is-invalid');
                        var trg = badRoot.querySelector('.sc-select-trigger');
                        if (trg) trg.focus();
                    }
                }
            },
            true
        );
    }

    function enhanceOne(select) {
        select.setAttribute(ENHANCED_ATTR, '1');
        select.classList.add('sc-select-native');

        if (select.required) {
            select.dataset.scRequired = '1';
            select.required = false;
        }

        var root = document.createElement('div');
        root.className = ROOT_CLASS;
        if (select.disabled) root.classList.add('is-disabled');

        var trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'sc-select-trigger';
        trigger.setAttribute('aria-haspopup', 'listbox');
        trigger.setAttribute('aria-expanded', 'false');

        var label = document.createElement('span');
        label.className = 'sc-select-label';
        trigger.appendChild(label);

        var arrow = document.createElement('span');
        arrow.className = 'sc-select-arrow';
        arrow.innerHTML = '&#9662;';
        trigger.appendChild(arrow);

        var menu = document.createElement('div');
        menu.className = 'sc-select-menu';
        menu.setAttribute('role', 'listbox');

        var parent = select.parentNode;
        parent.insertBefore(root, select);
        root.appendChild(select);
        root.appendChild(trigger);
        root.appendChild(menu);

        function rebuild() {
            menu.innerHTML = '';
            Array.prototype.forEach.call(select.options, function (opt, idx) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'sc-select-option';
                btn.setAttribute('role', 'option');
                btn.dataset.value = opt.value;
                btn.dataset.index = String(idx);
                btn.disabled = !!opt.disabled;
                btn.textContent = opt.textContent || '';
                if (opt.selected) btn.classList.add('is-selected');
                if (opt.disabled) btn.classList.add('is-disabled');

                btn.addEventListener('click', function () {
                    if (opt.disabled || select.disabled) return;
                    select.value = opt.value;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    root.classList.remove('is-invalid');
                    closeAll();
                    trigger.focus();
                });
                menu.appendChild(btn);
            });
        }

        function syncFromNative() {
            var selectedOption = select.options[select.selectedIndex] || null;
            label.textContent = selectedOption ? selectedOption.textContent : '';
            root.classList.toggle('is-disabled', !!select.disabled);
            menu.querySelectorAll('.sc-select-option').forEach(function (btn) {
                btn.classList.toggle('is-selected', btn.dataset.value === String(select.value));
            });
        }

        function openMenu() {
            if (select.disabled) return;
            closeAll(root);
            root.classList.add('is-open');
            trigger.setAttribute('aria-expanded', 'true');
            var selectedBtn = menu.querySelector('.sc-select-option.is-selected');
            if (selectedBtn) selectedBtn.scrollIntoView({ block: 'nearest' });
        }

        function toggleMenu() {
            if (root.classList.contains('is-open')) {
                closeAll();
            } else {
                openMenu();
            }
        }

        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            toggleMenu();
        });

        trigger.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openMenu();
            }
            if (e.key === 'Escape') {
                closeAll();
            }
        });

        select.addEventListener('change', syncFromNative);

        if (typeof MutationObserver !== 'undefined') {
            var observer = new MutationObserver(function (mutations) {
                var shouldRebuild = false;
                var shouldSync = false;

                mutations.forEach(function (mutation) {
                    if (mutation.type === 'childList') {
                        shouldRebuild = true;
                    } else if (mutation.type === 'attributes') {
                        if (mutation.target === select) {
                            shouldSync = true;
                        } else if (mutation.target && mutation.target.tagName === 'OPTION') {
                            shouldRebuild = true;
                        }
                    }
                });

                if (shouldRebuild) {
                    rebuild();
                    syncFromNative();
                } else if (shouldSync) {
                    syncFromNative();
                }
            });

            observer.observe(select, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['disabled', 'selected', 'label', 'value']
            });
        }

        if (select.form) {
            bindRequiredValidation(select.form);
            select.form.addEventListener('reset', function () {
                setTimeout(function () {
                    syncFromNative();
                }, 0);
            });
        }

        rebuild();
        syncFromNative();
    }

    function init(root) {
        var scope = root || document;
        scope.querySelectorAll('select').forEach(function (sel) {
            if (isEnhanceable(sel)) enhanceOne(sel);
        });
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.' + ROOT_CLASS)) {
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
    window.SmartCare.CustomSelect = {
        init: init,
        closeAll: closeAll,
    };
})();
