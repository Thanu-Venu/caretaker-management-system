<?php
/**
 * CSS class for client booking status pills (tables, modals).
 * Uses admin design tokens via client-post-admin.css.
 */
if (!function_exists('client_booking_status_class')) {
    function client_booking_status_class($status)
    {
        $s = strtolower((string) preg_replace('/[\s\-]+/', '_', (string) $status));

        if ($s === 'completed') {
            return 'completed';
        }
        if ($s === 'cancelled') {
            return 'cancelled';
        }
        if ($s === 'rejected') {
            return 'rejected';
        }
        if ($s === 'requested') {
            return 'pbs-requested';
        }
        if ($s === 'payment_requested') {
            return 'pbs-payment-requested';
        }
        if ($s === 'advance_paid') {
            return 'pbs-advance-paid';
        }
        if ($s === 'accepted') {
            return 'ongoing';
        }
        if ($s === 'reschedule_requested') {
            return 'pbs-reschedule-requested';
        }
        if ($s === 'change_requested') {
            return 'pbs-change-requested';
        }

        return 'pending';
    }
}
