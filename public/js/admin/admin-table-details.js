/**
 * Wide admin tables (>6 columns): show first 4 data columns + Actions,
 * hide middle columns, add an eye icon to open a modal with the full row (all columns).
 * Tables with ≤6 columns (e.g. 5 data + Actions) are left as-is.
 * Opt out: table.no-table-collapse or data-table-collapse="off"
 */
(function () {
    'use strict';

    /** Total th count above this triggers collapse (6 = five data + Actions, unchanged). */
    var MAX_TOTAL_COLS = 6;
    var VISIBLE_DATA_SLOTS = 4;

    function isActionHeader(text) {
        return /^(action|actions)$/i.test(String(text || '').trim());
    }

    function findActionIndex(ths) {
        for (var i = 0; i < ths.length; i++) {
            if (isActionHeader(ths[i].textContent)) {
                return i;
            }
        }
        return ths.length - 1;
    }

    function buildVisibleSet(n, actionIdx) {
        var indices = [];
        for (var i = 0; i < n; i++) {
            if (i !== actionIdx) {
                indices.push(i);
            }
        }
        var visible = new Set();
        for (var j = 0; j < Math.min(VISIBLE_DATA_SLOTS, indices.length); j++) {
            visible.add(indices[j]);
        }
        visible.add(actionIdx);
        var hidden = [];
        for (var k = 0; k < n; k++) {
            if (!visible.has(k)) {
                hidden.push(k);
            }
        }
        return { visible: visible, hidden: hidden };
    }

    function closeModal(modal) {
        if (!modal) {
            return;
        }
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }

    function cellDetailText(td) {
        if (!td) {
            return '';
        }
        var clone = td.cloneNode(true);
        var strip = clone.querySelectorAll('.action-view-btn');
        for (var s = 0; s < strip.length; s++) {
            strip[s].parentNode.removeChild(strip[s]);
        }
        return clone.innerText.replace(/\s+/g, ' ').trim();
    }

    function openModal(modal, ths, tds, n) {
        var dl = modal.querySelector('.admin-row-detail-modal__dl');
        if (!dl) {
            return;
        }
        dl.innerHTML = '';
        for (var i = 0; i < n; i++) {
            var label = ths[i] ? ths[i].textContent.trim() : 'Column ' + (i + 1);
            if (!label) {
                label = 'Column ' + (i + 1);
            }
            var dt = document.createElement('dt');
            dt.textContent = label;
            var dd = document.createElement('dd');
            dd.textContent = cellDetailText(tds[i]);
            dl.appendChild(dt);
            dl.appendChild(dd);
        }
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function ensureModal() {
        var modal = document.getElementById('adminRowDetailModal');
        if (modal) {
            return modal;
        }
        modal = document.createElement('div');
        modal.id = 'adminRowDetailModal';
        modal.className = 'modal admin-row-detail-modal';
        modal.setAttribute('aria-hidden', 'true');
        modal.innerHTML =
            '<div class="modal-content admin-row-detail-modal__content" role="dialog" aria-modal="true" aria-labelledby="adminRowDetailTitle">' +
            '<button type="button" class="modal-close admin-row-detail-modal__close" aria-label="Close"><i class="bx bx-x" aria-hidden="true"></i></button>' +
            '<header class="admin-row-detail-modal__header">' +
            '<span class="admin-row-detail-modal__header-icon" aria-hidden="true"><i class="bx bx-show"></i></span>' +
            '<h3 id="adminRowDetailTitle" class="admin-row-detail-modal__title">Row details</h3>' +
            '</header>' +
            '<dl class="admin-row-detail-modal__dl"></dl>' +
            '</div>';
        document.body.appendChild(modal);
        modal.querySelector('.admin-row-detail-modal__close').addEventListener('click', function () {
            closeModal(modal);
        });
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal(modal);
            }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.classList.contains('show')) {
                closeModal(modal);
            }
        });
        return modal;
    }

    function enhanceTable(table) {
        if (table.dataset.adminTableCollapse === '1') {
            return;
        }
        if (table.classList.contains('no-table-collapse')) {
            return;
        }
        if (table.getAttribute('data-table-collapse') === 'off') {
            return;
        }
        if (table.closest('.no-table-collapse')) {
            return;
        }

        var theadRow = table.querySelector('thead tr');
        if (!theadRow) {
            return;
        }
        var ths = theadRow.querySelectorAll('th');
        if (!ths.length || ths.length <= MAX_TOTAL_COLS) {
            return;
        }

        var thList = Array.prototype.slice.call(ths);
        var actionIdx = findActionIndex(thList);
        var n = thList.length;
        var plan = buildVisibleSet(n, actionIdx);
        if (!plan.hidden.length) {
            return;
        }

        table.dataset.adminTableCollapse = '1';
        table.classList.add('admin-table--collapsed');

        thList.forEach(function (th, i) {
            if (!plan.visible.has(i)) {
                th.classList.add('admin-table-col--hidden');
            }
        });

        var rows = table.querySelectorAll('tbody tr');
        var modal = null;

        rows.forEach(function (tr) {
            var tds = tr.querySelectorAll('td');
            if (tds.length !== n) {
                return;
            }
            var tdArr = Array.prototype.slice.call(tds);
            plan.hidden.forEach(function (i) {
                if (tdArr[i]) {
                    tdArr[i].classList.add('admin-table-col--hidden');
                }
            });

            var actionTd = tdArr[actionIdx];
            if (!actionTd) {
                return;
            }

            var viewBtn = document.createElement('button');
            viewBtn.type = 'button';
            viewBtn.className = 'btn secondary btn-sm action-view-btn action-view-btn--icon';
            viewBtn.innerHTML = '<i class="bx bx-show" aria-hidden="true"></i>';
            viewBtn.setAttribute('aria-label', 'View full row details');
            viewBtn.setAttribute('title', 'View details');
            viewBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (!modal) {
                    modal = ensureModal();
                }
                openModal(modal, thList, tdArr, n);
            });
            actionTd.insertBefore(viewBtn, actionTd.firstChild);
        });
    }

    function run() {
        var sel =
            '.main-content table, main.main-content table, .content table, .payments-page table, .reports-container table';
        document.querySelectorAll(sel).forEach(function (table) {
            if (table.closest('#adminRowDetailModal')) {
                return;
            }
            enhanceTable(table);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
})();
