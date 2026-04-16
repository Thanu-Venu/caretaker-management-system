/**
 * Admin caregivers: detail modal (incl. qualifications in dl), add/edit modals.
 */
(function () {
    'use strict';

    var body = document.body;
    var urlRoot = (body.getAttribute('data-urlroot') || '').replace(/\/$/, '');
    var caretakerCrudBase = (
        body.getAttribute('data-caretaker-crud-base') || urlRoot + '/CaretakerCRUD'
    ).replace(/\/$/, '');
    var autoOpen = body.getAttribute('data-auto-open') || '';

    var detailModal = document.getElementById('caretakerDetailModal');
    var detailDl = document.getElementById('caretakerDetailDl');

    var addModal = document.getElementById('caretakerAddModal');
    var editModal = document.getElementById('caretakerEditModal');
    var editForm = document.getElementById('caretakerEditForm');

    var LABELS = [
        ['name', 'Name'],
        ['email', 'Email'],
        ['phone', 'Phone'],
        ['service_type', 'Service type'],
        ['experience', 'Experience'],
        ['location', 'Location'],
        ['qualifications', 'Qualifications'],
        ['status', 'Status'],
        ['created_at', 'Member since'],
    ];

    function parsePayload(el) {
        var raw = el.getAttribute('data-caretaker') || '{}';
        try {
            return JSON.parse(raw);
        } catch (e) {
            return {};
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

    function anyCaretakerModalOpen() {
        return (
            (detailModal && detailModal.classList.contains('show')) ||
            (addModal && addModal.classList.contains('show')) ||
            (editModal && editModal.classList.contains('show'))
        );
    }

    function closeModal(el) {
        if (!el) {
            return;
        }
        el.classList.remove('show');
        el.setAttribute('aria-hidden', 'true');
        if (!anyCaretakerModalOpen()) {
            setBodyScroll(false);
        }
    }

    function closeAllCaretakerModals() {
        [detailModal, addModal, editModal].forEach(function (m) {
            if (m) {
                m.classList.remove('show');
                m.setAttribute('aria-hidden', 'true');
            }
        });
        setBodyScroll(false);
    }

    function fillDetailModal(d) {
        if (!detailDl) {
            return;
        }
        detailDl.innerHTML = '';

        LABELS.forEach(function (pair) {
            var key = pair[0];
            var label = pair[1];
            var val = d[key];
            if (val === undefined || val === null || val === '') {
                val = '—';
            }
            var dt = document.createElement('dt');
            dt.textContent = label;
            var dd = document.createElement('dd');
            dd.textContent = String(val);
            if (key === 'qualifications') {
                dd.className = 'ddBlock';
            }
            detailDl.appendChild(dt);
            detailDl.appendChild(dd);
        });

        var dtImg = document.createElement('dt');
        dtImg.textContent = 'Profile image';
        var ddImg = document.createElement('dd');
        var fn = d.profile_image ? String(d.profile_image) : '';
        if (fn && fn !== 'default.jpg') {
            var img = document.createElement('img');
            img.src = urlRoot + '/public/uploads/' + encodeURIComponent(fn);
            img.alt = '';
            img.className = 'caretaker-detail-thumb';
            ddImg.appendChild(img);
        } else {
            ddImg.textContent = '—';
        }
        detailDl.appendChild(dtImg);
        detailDl.appendChild(ddImg);

        var titleEl = document.getElementById('caretakerDetailTitle');
        if (titleEl) {
            titleEl.textContent = d.name ? 'Caregiver — ' + String(d.name) : 'Caregiver details';
        }
    }

    function populateEditForm(d) {
        if (!editForm || !d || !d.id) {
            return;
        }
        editForm.action = caretakerCrudBase + '/edit/' + encodeURIComponent(String(d.id));
        var set = function (name, value) {
            var field = editForm.querySelector('[name="' + name + '"]');
            if (!field) {
                return;
            }
            if (field.tagName === 'TEXTAREA' || (field.tagName === 'INPUT' && field.type !== 'file')) {
                field.value = value != null ? String(value) : '';
            }
        };
        set('name', d.name);
        set('email', d.email);
        set('phone', d.phone);
        set('experience', d.experience);
        set('location', d.location);
        set('qualifications', d.qualifications);
        set('service_type', d.service_type);
        set('status', d.status);
        var fileInput = editForm.querySelector('input[type="file"][name="profile_image"]');
        if (fileInput) {
            fileInput.value = '';
        }
        var np = editForm.querySelector('input[name="new_password"]');
        if (np) {
            np.value = '';
            np.type = 'password';
            var tw = np.closest('.password-input-wrap');
            var tbtn = tw && tw.querySelector('.password-toggle');
            if (tbtn) {
                tbtn.setAttribute('aria-label', 'Show password');
                var ic = tbtn.querySelector('i');
                if (ic) {
                    ic.classList.remove('bx-show');
                    ic.classList.add('bx-hide');
                }
            }
        }
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

    function bindDetailButtons() {
        document.querySelectorAll('.cgView').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                closeAllCaretakerModals();
                fillDetailModal(parsePayload(btn));
                openModal(detailModal);
            });
        });
    }

    function bindEditButtons() {
        document.querySelectorAll('.cgEdit').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                closeAllCaretakerModals();
                populateEditForm(parsePayload(btn));
                openModal(editModal);
            });
        });
    }

    function bindCloseTargets() {
        document.querySelectorAll('[data-close-caretaker-modal]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                closeAllCaretakerModals();
            });
        });
        [detailModal, addModal, editModal].forEach(function (m) {
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
        if (
            (detailModal && detailModal.classList.contains('show')) ||
            (addModal && addModal.classList.contains('show')) ||
            (editModal && editModal.classList.contains('show'))
        ) {
            closeAllCaretakerModals();
        }
    });

    var openAddBtn = document.getElementById('caretakerOpenAddModal');
    if (openAddBtn && addModal) {
        openAddBtn.addEventListener('click', function () {
            closeAllCaretakerModals();
            openModal(addModal);
        });
    }

    if (autoOpen === 'add' && addModal) {
        openModal(addModal);
    }
    if (autoOpen === 'edit' && editModal) {
        openModal(editModal);
    }

    bindDetailButtons();
    bindEditButtons();
    bindCloseTargets();

    var deleteId = null;
var confirmDeleteModal = document.getElementById('confirmDeleteModal');
var confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

function openDeleteModal(id) {
    deleteId = id;
    confirmDeleteModal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    confirmDeleteModal.classList.remove('show');
    deleteId = null;
    document.body.style.overflow = '';
}

// open modal
document.querySelectorAll('.deleteCaretaker').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        openDeleteModal(this.getAttribute('data-id'));
    });
});

// confirm delete
confirmDeleteBtn.addEventListener('click', function () {
    if (!deleteId) return;

    window.location.href =
        (document.body.getAttribute('data-caretaker-crud-base') || '') +
        '/delete/' + deleteId;
});

// close modal
document.querySelectorAll('[data-close-delete-modal]').forEach(function (btn) {
    btn.addEventListener('click', closeDeleteModal);
});

// click outside modal
confirmDeleteModal.addEventListener('click', function (e) {
    if (e.target === confirmDeleteModal) {
        closeDeleteModal();
    }
});

    bindPasswordToggles(document);
})();
