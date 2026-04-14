/**
 * Admin announcements list: add/edit modals, deep-link open, body scroll lock.
 */
(function () {
    'use strict';

    var main = document.querySelector('.announcement-admin-page');
    if (!main) {
        return;
    }

    var urlRoot = (main.getAttribute('data-urlroot') || '').replace(/\/$/, '');

    var addModal = document.getElementById('annAddModal');
    var editModal = document.getElementById('annEditModal');
    var addForm = document.getElementById('annAddForm');
    var editForm = document.getElementById('annEditForm');

    var openAddBtn = document.getElementById('annOpenAddModal');
    var deepLinkScript = document.getElementById('annDeepLinkPayload');
    var hadDeepLinkEl = !!deepLinkScript;

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

    function anyAnnModalOpen() {
        return (addModal && addModal.classList.contains('show')) || (editModal && editModal.classList.contains('show'));
    }

    function closeModal(el) {
        if (!el) {
            return;
        }
        el.classList.remove('show');
        el.setAttribute('aria-hidden', 'true');
        if (el === editModal) {
            var sub = document.getElementById('annEditSubmit');
            if (sub) {
                sub.disabled = true;
            }
        }
        if (!anyAnnModalOpen()) {
            setBodyScroll(false);
        }
    }

    function closeAllAnnModals() {
        [addModal, editModal].forEach(function (m) {
            if (m) {
                m.classList.remove('show');
                m.setAttribute('aria-hidden', 'true');
            }
        });
        setBodyScroll(false);
    }

    function b64ToJson(s) {
        try {
            var bin = atob(s);
            var bytes = new Uint8Array(bin.length);
            for (var i = 0; i < bin.length; i++) {
                bytes[i] = bin.charCodeAt(i);
            }
            var text = new TextDecoder('utf-8').decode(bytes);
            return JSON.parse(text);
        } catch (e) {
            return null;
        }
    }

    function clearFieldErrors(form) {
        if (!form) {
            return;
        }
        form.querySelectorAll('.is-invalid').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
        form.querySelectorAll('.field-error').forEach(function (el) {
            el.textContent = '';
            el.style.display = 'none';
        });
    }

    function resetAddForm() {
        if (!addForm) {
            return;
        }
        addForm.reset();
        clearFieldErrors(addForm);
    }

    function fillEditForm(row) {
        if (!editForm || !row) {
            return;
        }
        var id = parseInt(row.id, 10);
        if (!id || id < 1) {
            return;
        }
        editForm.action = urlRoot + '/AnnouncementCRUD/edit/' + id;
        var t = editForm.querySelector('#ann-edit-title');
        var m = editForm.querySelector('#ann-edit-message');
        var s = editForm.querySelector('#ann-edit-target');
        if (t) {
            t.value = row.title != null ? String(row.title) : '';
        }
        if (m) {
            m.value = row.message != null ? String(row.message) : '';
        }
        if (s) {
            s.value = row.target_role != null ? String(row.target_role) : 'All';
        }
        clearFieldErrors(editForm);
        var sub = document.getElementById('annEditSubmit');
        if (sub) {
            sub.disabled = false;
        }
    }

    function openEditFromPayload(row) {
        fillEditForm(row);
        openModal(editModal);
    }

    document.querySelectorAll('[data-ann-modal-close]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var m = btn.closest('.modal');
            closeModal(m);
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

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && anyAnnModalOpen()) {
            closeAllAnnModals();
        }
    });

    if (openAddBtn) {
        openAddBtn.addEventListener('click', function () {
            resetAddForm();
            openModal(addModal);
        });
    }

    document.querySelectorAll('[data-ann-b64]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var raw = btn.getAttribute('data-ann-b64') || '';
            var row = b64ToJson(raw);
            if (row) {
                openEditFromPayload(row);
            }
        });
    });

    /* Deep link: server injects JSON script */
    if (deepLinkScript && deepLinkScript.textContent) {
        try {
            var payload = JSON.parse(deepLinkScript.textContent);
            if (payload && payload.open === 'add') {
                resetAddForm();
                openModal(addModal);
            } else if (payload && payload.open === 'edit' && payload.row) {
                openEditFromPayload(payload.row);
            }
        } catch (err) {
            /* ignore */
        }
        deepLinkScript.remove();
    }

    /* Strip open= / edit_id= after a server-driven deep link (cleaner address bar) */
    if (hadDeepLinkEl && window.history && window.history.replaceState) {
        var u = new URL(window.location.href);
        if (u.searchParams.get('open') || u.searchParams.get('edit_id')) {
            u.searchParams.delete('open');
            u.searchParams.delete('edit_id');
            var q = u.searchParams.toString();
            window.history.replaceState({}, '', u.pathname + (q ? '?' + q : '') + u.hash);
        }
    }
})();
