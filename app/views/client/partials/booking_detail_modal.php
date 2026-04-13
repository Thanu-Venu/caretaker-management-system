<div id="bookingDetailModal" class="modal admin-row-detail-modal booking-detail-modal-root" role="dialog" aria-modal="true" aria-labelledby="bookingDetailModalTitle"
     onclick="if (event.target === this && window.SmartCareBookingDetail) SmartCareBookingDetail.close();">
    <div class="modal-content admin-row-detail-modal__content booking-detail-modal__content" role="document" onclick="event.stopPropagation();">
        <button type="button" class="modal-close admin-row-detail-modal__close" onclick="window.SmartCareBookingDetail && SmartCareBookingDetail.close();" aria-label="Close">
            <i class="bx bx-x" aria-hidden="true"></i>
        </button>
        <header class="admin-row-detail-modal__header">
            <h3 id="bookingDetailModalTitle" class="admin-row-detail-modal__title">Booking details</h3>
        </header>
        <div id="bookingDetailBody" class="booking-detail-body"></div>
    </div>
</div>
