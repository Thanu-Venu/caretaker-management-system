<?php

require_once __DIR__ . '/../core/PaymentCalculationService.php';
require_once __DIR__ . '/../core/RecurringPaymentService.php';

class PaymentController extends Controller
{
    /**
     * Calculate advance and remaining payment according to SmartCare business rules.
     * DEPRECATED: Use PaymentCalculationService::calculateAdvancePayment() instead
     *
     * @param array $booking  Booking array with keys: 'basis','duration','total_payment','booking_date' (optional)
     * @param string|null $today YYYY-MM-DD (optional; defaults to today)
     * @return array ['advance'=>float,'remaining'=>float,'advance_months'=>int|null,'notes'=>string]
     * @deprecated Use PaymentCalculationService instead
     */

    public static function calculateAdvanceFromBooking(array $booking, ?string $today = null): array
    {
        // Use new PaymentCalculationService
        $result = PaymentCalculationService::calculateAdvancePayment($booking);

        return [
            'advance' => $result['advance_amount'],
            'remaining' => $result['remaining_balance'],
            'advance_months' => $result['advance_months'],
            'notes' => $result['description'],
        ];
    }

    /**
     * Validate booking before creation
     *
     * @param array $bookingData
     * @return array ['valid' => bool, 'message' => string]
     */
    public static function validateBooking($bookingData)
    {
        $basis = $bookingData['basis'] ?? '';
        $duration = (int)($bookingData['duration'] ?? 0);

        // Validate daily booking duration
        return PaymentCalculationService::validateDailyBooking($basis, $duration);
    }

    /**
     * Calculate payment details for a booking
     *
     * @param array $booking Must include: basis, duration, total_payment, service_start_date
     * @return array Complete payment calculation
     */
    public static function calculatePaymentDetails($booking)
    {
        return PaymentCalculationService::calculateAdvancePayment($booking);
    }

    /**
     * Create recurring payment schedule after advance payment is made
     *
     * @param int $bookingId
     * @param array $bookingData
     * @return bool
     */
    public static function createRecurringPayments($bookingId, $bookingData)
    {
        // Ensure date anchor exists for recurring schedule generation.
        if (empty($bookingData['service_start_date']) && !empty($bookingData['booking_date'])) {
            $bookingData['service_start_date'] = $bookingData['booking_date'];
        }

        $paymentCalc = PaymentCalculationService::calculateAdvancePayment($bookingData);

        if (!$paymentCalc['needs_recurring']) {
            return true; // No recurring payments needed
        }

        $schedule = PaymentCalculationService::generateRecurringPaymentSchedule($bookingId, array_merge($bookingData, $paymentCalc));

        $recurringService = new RecurringPaymentService();
        return $recurringService->createRecurringPayments($bookingId, $bookingData, $schedule);
    }
}