<?php

/**
 * PaymentCalculationService
 *
 * Implements SmartCare payment calculation logic for all service bases:
 * - Hourly: 100% advance
 * - Daily: 100% for <15 days, 10 days advance for 15-30 days
 * - Monthly: 1 month advance (<6 months), 3 months advance (>=6 months)
 * - Yearly: 4 months advance (1 year), 6 months advance (>1 year)
 */
class PaymentCalculationService
{
    /**
     * Validate daily booking duration (max 30 days)
     *
     * @param string $basis Service basis
     * @param int $duration Duration in days
     * @return array ['valid' => bool, 'message' => string]
     */
    public static function validateDailyBooking($basis, $duration)
    {
        if (strtolower($basis) === 'daily') {
            if ($duration < 1) {
                return [
                    'valid' => false,
                    'message' => 'Daily bookings require a minimum of 1 day.'
                ];
            }

            if ($duration > 30) {
                return [
                    'valid' => false,
                    'message' => 'Daily bookings are limited to 30 days. Please choose Monthly Service.'
                ];
            }
        }

        return ['valid' => true, 'message' => 'Valid'];
    }

    /**
     * Calculate advance payment and related details
     *
     * @param array $booking ['basis', 'duration', 'total_payment', 'service_start_date']
     * @return array Calculation details
     */
    public static function calculateAdvancePayment($booking)
    {
        $basis = strtolower(trim($booking['basis']));
        $duration = (int)($booking['duration'] ?? 0);
        $totalPayment = (float)($booking['total_payment'] ?? 0);
        $serviceStartDate = $booking['service_start_date'] ?? date('Y-m-d');

        $result = [
            'advance_amount' => 0.00,
            'advance_months' => 0,
            'total_months' => 0,
            'advance_balance' => 0.00,
            'remaining_balance' => 0.00,
            'needs_recurring' => false,
            'cycle_type' => null,
            'next_payment_due' => null,
            'description' => ''
        ];

        switch ($basis) {
            case 'hourly':
                $result = self::calculateHourlyAdvance($totalPayment);
                break;

            case 'daily':
                $result = self::calculateDailyAdvance($duration, $totalPayment, $serviceStartDate);
                break;

            case 'monthly':
                $result = self::calculateMonthlyAdvance($duration, $totalPayment, $serviceStartDate);
                break;

            case 'yearly':
                $result = self::calculateYearlyAdvance($duration, $totalPayment, $serviceStartDate);
                break;

            default:
                $result['description'] = 'Unknown basis type';
                break;
        }

        return $result;
    }

    /**
     * Hourly service calculation
     * Rule: 100% payment in advance
     */
    private static function calculateHourlyAdvance($totalPayment)
    {
        return [
            'advance_amount' => round($totalPayment, 2),
            'advance_months' => 0,
            'total_months' => 0,
            'advance_balance' => 0.00,
            'remaining_balance' => 0.00,
            'needs_recurring' => false,
            'cycle_type' => null,
            'next_payment_due' => null,
            'description' => 'Hourly service: 100% payment required in advance'
        ];
    }

    /**
     * Daily service calculation
     * Rules:
     * - < 15 days: 100% advance
        * - 15-30 days: 10 days advance, remaining paid before booking end
     */
    private static function calculateDailyAdvance($duration, $totalPayment, $serviceStartDate)
    {
        $result = [
            'advance_months' => 0,
            'total_months' => 0,
            'advance_balance' => 0.00,
            'needs_recurring' => false,
            'cycle_type' => null,
            'next_payment_due' => null
        ];

        if ($duration < 15) {
            // Full payment required
            $result['advance_amount'] = round($totalPayment, 2);
            $result['remaining_balance'] = 0.00;
            $result['description'] = "Daily service ({$duration} days): 100% payment required in advance";
        } else {
            // 15-30 days: 10 days advance
            $dailyRate = $totalPayment / $duration;
            $advanceDays = 10;
            $advanceAmount = $dailyRate * $advanceDays;
            $remainingAmount = $totalPayment - $advanceAmount;

            $result['advance_amount'] = round($advanceAmount, 2);
            $result['advance_balance'] = round($advanceAmount, 2);
            $result['remaining_balance'] = round($remainingAmount, 2);
            $result['needs_recurring'] = true;
            $result['cycle_type'] = '15_day';

            // Remaining payment due one day before booking end date.
            $startDate = new DateTime($serviceStartDate);
            $dueDate = clone $startDate;
            $dueDate->modify('+' . max($duration - 2, 0) . ' days');
            $result['next_payment_due'] = $dueDate->format('Y-m-d');

            $result['description'] = "Daily service ({$duration} days): {$advanceDays} days advance (Rs. " .
                number_format($advanceAmount, 2) . "), remaining Rs. " .
                number_format($remainingAmount, 2) . " due before booking end";
        }

        return $result;
    }

    /**
     * Monthly service calculation
     * Rules:
     * - < 6 months: 1 month advance
     * - >= 6 months: 3 months advance
     */
    private static function calculateMonthlyAdvance($duration, $totalPayment, $serviceStartDate)
    {
        $advanceMonths = ($duration < 6) ? 1 : 3;
        $monthlyRate = $totalPayment / $duration;
        $advanceAmount = $monthlyRate * $advanceMonths;
        $remainingAmount = $totalPayment - $advanceAmount;

        // Calculate next payment due date (after advance period)
        $startDate = new DateTime($serviceStartDate);
        $startDate->modify("+{$advanceMonths} months");
        $nextPaymentDue = $startDate->format('Y-m-d');

        return [
            'advance_amount' => round($advanceAmount, 2),
            'advance_months' => $advanceMonths,
            'total_months' => $duration,
            'advance_balance' => round($advanceAmount, 2),
            'remaining_balance' => round($remainingAmount, 2),
            'needs_recurring' => ($duration > $advanceMonths),
            'cycle_type' => 'monthly',
            'next_payment_due' => ($duration > $advanceMonths) ? $nextPaymentDue : null,
            'monthly_rate' => round($monthlyRate, 2),
            'description' => "Monthly service ({$duration} months): {$advanceMonths} month(s) advance = Rs. " .
                number_format($advanceAmount, 2)
        ];
    }

    /**
     * Yearly service calculation
     * Rules:
     * - Convert years to months (total_months = duration × 12)
     * - 1 year: 4 months advance
     * - > 1 year: 6 months advance
     */
    private static function calculateYearlyAdvance($duration, $totalPayment, $serviceStartDate)
    {
        $totalMonths = $duration * 12;
        $advanceMonths = ($duration == 1) ? 4 : 6;

        $monthlyRate = $totalPayment / $totalMonths;
        $advanceAmount = $monthlyRate * $advanceMonths;
        $remainingAmount = $totalPayment - $advanceAmount;

        // Calculate next payment due date (after advance period)
        $startDate = new DateTime($serviceStartDate);
        $startDate->modify("+{$advanceMonths} months");
        $nextPaymentDue = $startDate->format('Y-m-d');

        return [
            'advance_amount' => round($advanceAmount, 2),
            'advance_months' => $advanceMonths,
            'total_months' => $totalMonths,
            'advance_balance' => round($advanceAmount, 2),
            'remaining_balance' => round($remainingAmount, 2),
            'needs_recurring' => true,
            'cycle_type' => 'monthly',
            'next_payment_due' => $nextPaymentDue,
            'monthly_rate' => round($monthlyRate, 2),
            'description' => "Yearly service ({$duration} year(s) = {$totalMonths} months): {$advanceMonths} month(s) advance = Rs. " .
                number_format($advanceAmount, 2)
        ];
    }

    /**
     * Get recurring payment schedule for a booking
     *
     * @param int $bookingId
     * @param array $bookingData Must include: service_start_date, advance_months, total_months, total_payment
     * @return array List of payment cycles
     */
    public static function generateRecurringPaymentSchedule($bookingId, $bookingData)
    {
        $serviceStartDate = $bookingData['service_start_date']
            ?? $bookingData['booking_date']
            ?? date('Y-m-d');
        $advanceMonths = (int)$bookingData['advance_months'];
        $totalMonths = (int)$bookingData['total_months'];
        $totalPayment = (float)$bookingData['total_payment'];
        $cycleType = $bookingData['cycle_type'] ?? 'monthly';

        if ($totalMonths <= 0 && $cycleType === 'monthly') {
            return [];
        }

        if ($totalMonths <= $advanceMonths && $cycleType === 'monthly') {
            return []; // No recurring payments needed
        }

        $monthlyRate = $cycleType === 'monthly' ? ($totalPayment / $totalMonths) : 0;
        $schedule = [];

        if ($cycleType === 'monthly') {
            // Generate monthly payment schedule.
            // First recurring due date is at the start of the first month after advance coverage.
            $startDate = new DateTime($serviceStartDate);
            $recurringMonths = $totalMonths - $advanceMonths;

            for ($cycleNumber = 1; $cycleNumber <= $recurringMonths; $cycleNumber++) {
                $monthsOffset = $advanceMonths + ($cycleNumber - 1);
                $dueDate = clone $startDate;
                $dueDate->modify("+{$monthsOffset} months");

                $schedule[] = [
                    'cycle_number' => $cycleNumber,
                    'cycle_type' => 'monthly',
                    'due_date' => $dueDate->format('Y-m-d'),
                    'amount' => round($monthlyRate, 2),
                    'status' => 'pending'
                ];
            }
        } elseif ($cycleType === '15_day') {
            // For daily bookings 15-30 days: one remaining payment due before booking end.
            if (!empty($bookingData['next_payment_due'])) {
                $dueDate = new DateTime((string) $bookingData['next_payment_due']);
            } else {
                $durationDays = (int) ($bookingData['duration'] ?? 0);
                $startDate = new DateTime($serviceStartDate);
                $dueDate = clone $startDate;
                $dueDate->modify('+' . max($durationDays - 2, 0) . ' days');
            }

            $schedule[] = [
                'cycle_number' => 1,
                'cycle_type' => '15_day',
                'due_date' => $dueDate->format('Y-m-d'),
                'amount' => round($bookingData['remaining_balance'], 2),
                'status' => 'pending'
            ];
        }

        return $schedule;
    }
}
