/**
 * HR approve leave — open/close impact & usage modal.
 */
(function () {
    'use strict';

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

    document.addEventListener('DOMContentLoaded', function () {
        var modal = document.getElementById('leaveApproveContextModal');
        var openBtn = document.getElementById('leaveApproveOpenContext');
        if (!modal) {
            return;
        }

        if (openBtn) {
            openBtn.addEventListener('click', function () {
                openModal(modal);
            });
        }

        document.querySelectorAll('[data-leave-approve-context-close]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                closeModal(modal);
            });
        });

        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal(modal);
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape' || !modal.classList.contains('show')) {
                return;
            }
            closeModal(modal);
        });
    });
})();
