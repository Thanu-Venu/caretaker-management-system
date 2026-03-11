<?php
class PaymentController extends Controller
{
    /**
     * Calculate advance and remaining payment according to rules.
     *
     * @param array $booking  Booking array with keys: 'basis','duration','total_payment','booking_date' (optional)
     * @param string|null $today YYYY-MM-DD (optional; defaults to today)
     * @return array ['advance'=>float,'remaining'=>float,'advance_months'=>int|null,'notes'=>string]
     */
   
public static function calculateAdvanceFromBooking(array $booking, ?string $today = null): array
    {
        $basis = strtolower(trim($booking['basis'] ?? ''));
        $duration = $booking['duration'] ?? 0;
        $total = floatval($booking['total_payment'] ?? 0);
        $bookingDate = $booking['booking_date'] ?? null;
        $today = $today ?: date('Y-m-d');

        $advance = 0.0;
        $advanceMonths = null;
        $notes = '';

        if ($basis === 'hourly') {
            // Full payment for hourly bookings
            $advance = $total;
            $notes = 'Hourly booking: full payment required.';
        } elseif ($basis === 'daily') {
            // Rule: if duration is 15+ days -> 50% advance, otherwise use lead time rule
            $leadDays = PHP_INT_MAX;
            if ($bookingDate) {
                $leadDays = (int) floor((strtotime($bookingDate) - strtotime($today)) / 86400);
            }
            $days = max(1, (int) $duration);
            if ($days >= 15) {
                $advance = $total * 0.5;
                $notes = "Daily booking for {$days} days: 50% advance payment allowed.";
            } elseif ($leadDays <= 15) {
                $advance = $total;
                $notes = "Daily booking with {$leadDays} days lead: full payment required.";
            } else {
                $advance = $total * 0.5;
                $notes = "After 15 days booking, you can pay half payment (50% advance).";
            }
        } elseif ($basis === 'monthly') {
            // Monthly: duration <5 => 1 month, else 3 months
            $months = max(1, intval($duration));
            $advanceMonths = ($months < 6) ? 1 : 3;
            $advance = $total * ($advanceMonths / max(1, $months));
            $notes = "Monthly booking: pay {$advanceMonths} month(s) advance.";
        } elseif ($basis === 'yearly') {
            // Yearly: duration in years -> if <1 year => 3 months, else 6 months
            $years = max(0.0, floatval($duration));
            $totalMonths = max(1, intval(round($years * 12)));
            $advanceMonths = ($years < 1.0) ? 3 : 6;
            $advance = $total * ($advanceMonths / max(1, $totalMonths));
            $notes = "Yearly booking: pay {$advanceMonths} month(s) advance.";
        } else {
            // fallback 50%
            $advance = $total * 0.5;
            $notes = 'Unknown basis: default 50% advance applied.';
        }

        $advance = round($advance, 2);
        $remaining = round(max(0, $total - $advance), 2);

        return [
            'advance' => $advance,
            'remaining' => $remaining,
            'advance_months' => $advanceMonths,
            'notes' => $notes,
        ];
    }




    
}