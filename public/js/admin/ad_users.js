/**
 * Staff / access control: add & edit in modals (no separate pages).
 */
(function () {
    'use strict';

    var body = document.body;
    var urlRoot = (body.getAttribute('data-urlroot') || '').replace(/\/$/, '');
    var autoOpen = body.getAttribute('data-auto-open') || '';

    var addModal = document.getElementById('staffAddModal');
    var editModal = document.getElementById('staffEditModal');
    var editForm = document.getElementById('staffEditForm');

    function setBodyScroll(lock) {
        document.body.style.overflow = lock ? 'hidden' : '';
    }

    function anyStaffModalOpen() {
        return (
            (addModal && addModal.classList.contains('show')) ||
            (editModal && editModal.classList.contains('show'))
        );
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
        if (!anyStaffModalOpen()) {
            setBodyScroll(false);
        }
    }

    function closeAllStaffModals() {
        if (addModal) {
            addModal.classList.remove('show');
            addModal.setAttribute('aria-hidden', 'true');
        }
        if (editModal) {
            editModal.classList.remove('show');
            editModal.setAttribute('aria-hidden', 'true');
        }
        setBodyScroll(false);
    }

    function parsePayload(el) {
        var raw = el.getAttribute('data-staff-user') || '{}';
        try {
            return JSON.parse(raw);
        } catch (e) {
            return {};
        }
    }

    function populateEditForm(d) {
        if (!editForm || !d || !d.id) {
            return;
        }
        editForm.action = urlRoot + '/userCRUD/edit/' + encodeURIComponent(String(d.id));
        var set = function (name, value) {
            var field = editForm.querySelector('[name="' + name + '"]');
            if (!field) {
                return;
            }
            if (field.tagName === 'INPUT' && field.type !== 'file') {
                field.value = value != null ? String(value) : '';
            }
            if (field.tagName === 'SELECT') {
                var v = value != null ? String(value) : '';
                field.value = v;
                if (field.value !== v && name === 'role') {
                    var lower = v.toLowerCase();
                    for (var i = 0; i < field.options.length; i++) {
                        if (field.options[i].value.toLowerCase() === lower) {
                            field.selectedIndex = i;
                            break;
                        }
                    }
                }
            }
        };
        set('username', d.username);
        set('email', d.email);
        set('phone', d.phone);
        set('role', d.role);
        set('status', d.status);
    }

    function bindPasswordToggles(root) {
        var scope = root || document;
        scope.querySelectorAll('.password-input-wrap .password-toggle').forEach(function (btn) {
            if (btn.getAttribute('data-pw-toggle-bound') === '1') {
                return;
            }
            btn.setAttribute('data-pw-toggle-bound', '1');
            btn.addEventListener('click', function () {
                var wrap = btn.closest('.password-input-wrap');
                var inp = wrap && wrap.querySelector('input');
                var icon = btn.querySelector('i');
                if (!inp) {
                    return;
                }
                var plain = inp.type === 'text';
                if (plain) {
                    inp.type = 'password';
                    btn.setAttribute('aria-label', 'Show password');
                    if (icon) {
                        icon.classList.remove('bx-show');
                        icon.classList.add('bx-hide');
                    }
                } else {
                    inp.type = 'text';
                    btn.setAttribute('aria-label', 'Hide password');
                    if (icon) {
                        icon.classList.remove('bx-hide');
                        icon.classList.add('bx-show');
                    }
                }
            });
        });
    }

    function bindEditButtons() {
        document.querySelectorAll('.js-staff-user-edit').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                closeAllStaffModals();
                populateEditForm(parsePayload(btn));
                openModal(editModal);
            });
        });
    }

    function bindCloseTargets() {
        document.querySelectorAll('[data-close-staff-modal]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                closeAllStaffModals();
            });
        });
        [addModal, editModal].forEach(function (m) {
            if (!m) {
                return;
            }
            m.addEventListener('click', function (e) {
                if (e.target === m) {
                    closeModal(m);
                }
            });
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') {
            return;
        }
        if (anyStaffModalOpen()) {
            closeAllStaffModals();
        }
    });

    var openAddBtn = document.getElementById('staffOpenAddModal');
    if (openAddBtn && addModal) {
        openAddBtn.addEventListener('click', function () {
            closeAllStaffModals();
            openModal(addModal);
        });
    }

    if (autoOpen === 'add' && addModal) {
        openModal(addModal);
    }
    if (autoOpen === 'edit' && editModal) {
        openModal(editModal);
    }

    bindEditButtons();
    bindCloseTargets();
    bindPasswordToggles(document);
})();
