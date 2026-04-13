/**
 * Read-only booking detail modal (clone from <template id="booking-detail-template-{id}">).
 */
(function () {
    'use strict';

    function close() {
        var m = document.getElementById('bookingDetailModal');
        var body = document.getElementById('bookingDetailBody');
        if (body) {
            body.innerHTML = '';
        }
        if (m) {
            m.classList.remove('show');
        }
    }

    function open(bookingId) {
        var id = parseInt(String(bookingId), 10);
        if (!id) {
            return;
        }
        var tpl = document.getElementById('booking-detail-template-' + id);
        var body = document.getElementById('bookingDetailBody');
        var m = document.getElementById('bookingDetailModal');
        if (!tpl || !body || !m) {
            return;
        }
        body.innerHTML = '';
        body.appendChild(tpl.content.cloneNode(true));
        m.classList.add('show');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') {
            return;
        }
        var m = document.getElementById('bookingDetailModal');
        if (m && m.classList.contains('show')) {
            close();
        }
    });

    window.SmartCareBookingDetail = { open: open, close: close };
})();
