<?php

/**
 * PaymentHelper
 *
 * Helper functions for displaying payment information in views
 */
class PaymentHelper
{
    /**
     * Get formatted payment description based on booking details
     *
     * @param array $booking
     * @return string HTML formatted payment description
     */
    public static function getPaymentDescription($booking)
    {
        $basis = strtolower($booking['basis']);
        $duration = (int)$booking['duration'];
        $advanceMonths = (int)($booking['advance_months'] ?? 0);
        $totalPayment = (float)$booking['total_payment'];

        $html = '<div class="payment-info-box">';

        switch ($basis) {
            case 'hourly':
                $html .= '<h4>Payment: Hourly Service</h4>';
                $html .= '<p><strong>Full payment required:</strong> Rs. ' . number_format($totalPayment, 2) . '</p>';
                $html .= '<p class="text-muted">No recurring payments needed.</p>';
                break;

            case 'daily':
                $html .= '<h4>Payment: Daily Service</h4>';
                if ($duration < 15) {
                    $html .= '<p><strong>Full payment required:</strong> Rs. ' . number_format($totalPayment, 2) . '</p>';
                    $html .= '<p class="text-muted">No recurring payments needed.</p>';
                } else {
                    $dailyRate = $totalPayment / $duration;
                    $advance = $dailyRate * 10;
                    $remaining = $totalPayment - $advance;

                    $html .= '<p><strong>Advance (10 days):</strong> Rs. ' . number_format($advance, 2) . '</p>';
                    $html .= '<p><strong>Remaining:</strong> Rs. ' . number_format($remaining, 2) . ' due before booking end date</p>';
                    $html .= '<p class="text-warning">⚠️ Remaining payment must be completed before the booking is finished.</p>';
                }
                break;

            case 'monthly':
                $monthlyRate = $totalPayment / $duration;
                $advance = $monthlyRate * $advanceMonths;
                $remaining = $totalPayment - $advance;

                $html .= '<h4>Payment: Monthly Service</h4>';
                $html .= '<p><strong>Advance ({$advanceMonths} month(s)):</strong> Rs. ' . number_format($advance, 2) . '</p>';
                $html .= '<p><strong>Monthly Rate:</strong> Rs. ' . number_format($monthlyRate, 2) . '</p>';

                if ($duration > $advanceMonths) {
                    $recurringMonths = $duration - $advanceMonths;
                    $html .= '<p><strong>Remaining:</strong> ' . $recurringMonths . ' monthly payments of Rs. ' . number_format($monthlyRate, 2) . '</p>';
                    $html .= '<p class="text-info">📅 Monthly payments will be due starting from month ' . ($advanceMonths + 1) . '</p>';
                } else {
                    $html .= '<p class="text-success">✓ No additional payments needed - fully covered by advance.</p>';
                }
                break;

            case 'yearly':
                $totalMonths = (int)($booking['total_months'] ?? 0);
                $monthlyRate = $totalMonths > 0 ? $totalPayment / $totalMonths : 0;
                $advance = $monthlyRate * $advanceMonths;

                $html .= '<h4>Payment: Yearly Service</h4>';
                $html .= '<p><strong>Duration:</strong> ' . $duration . ' year(s) = ' . $totalMonths . ' months</p>';
                $html .= '<p><strong>Advance ({$advanceMonths} months):</strong> Rs. ' . number_format($advance, 2) . '</p>';
                $html .= '<p><strong>Monthly Rate:</strong> Rs. ' . number_format($monthlyRate, 2) . '</p>';

                $recurringMonths = $totalMonths - $advanceMonths;
                if ($recurringMonths > 0) {
                    $html .= '<p><strong>Remaining:</strong> ' . $recurringMonths . ' monthly payments of Rs. ' . number_format($monthlyRate, 2) . '</p>';
                    $html .= '<p class="text-info">📅 Monthly payments will be due starting from month ' . ($advanceMonths + 1) . '</p>';
                }
                break;
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Get recurring payment schedule for display
     *
     * @param array $recurringPayments Array of recurring_payments records
     * @return string HTML table
     */
    public static function displayPaymentSchedule($recurringPayments)
    {
        if (empty($recurringPayments)) {
            return '<p class="text-muted">No recurring payments scheduled.</p>';
        }

        $html = '<div class="payment-schedule-table">';
        $html .= '<h4>Payment Schedule</h4>';
        $html .= '<table class="table table-bordered">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Cycle</th>';
        $html .= '<th>Due Date</th>';
        $html .= '<th>Amount</th>';
        $html .= '<th>Status</th>';
        $html .= '<th>Action</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($recurringPayments as $payment) {
            $status = $payment['status'];
            $statusClass = self::getStatusClass($status);
            $statusLabel = self::getStatusLabel($status);

            $html .= '<tr>';
            $html .= '<td>' . $payment['cycle_number'] . '</td>';
            $html .= '<td>' . date('M j, Y', strtotime($payment['due_date'])) . '</td>';
            $html .= '<td>Rs. ' . number_format($payment['amount'], 2) . '</td>';
            $html .= '<td><span class="badge ' . $statusClass . '">' . $statusLabel . '</span></td>';

            // Action button
            if ($status === 'pending' || $status === 'overdue') {
                $html .= '<td><a href="' . URLROOT . '/client/payRecurring?payment_id=' . $payment['id'] . '" class="btn btn-sm btn-primary">Pay Now</a></td>';
            } elseif ($status === 'paid') {
                $html .= '<td><span class="text-success">✓ Paid</span></td>';
            } else {
                $html .= '<td>-</td>';
            }

            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Get status badge class
     */
    private static function getStatusClass($status)
    {
        switch ($status) {
            case 'pending':
                return 'badge-warning';
            case 'paid':
                return 'badge-success';
            case 'overdue':
                return 'badge-danger';
            case 'cancelled':
                return 'badge-secondary';
            default:
                return 'badge-light';
        }
    }

    /**
     * Get status label
     */
    private static function getStatusLabel($status)
    {
        switch ($status) {
            case 'pending':
                return 'Pending';
            case 'paid':
                return 'Paid';
            case 'overdue':
                return 'Overdue';
            case 'cancelled':
                return 'Cancelled';
            default:
                return ucfirst($status);
        }
    }

    /**
     * Get next payment due information
     *
     * @param array $nextPayment Next recurring_payment record
     * @return string HTML alert box
     */
    public static function getNextPaymentAlert($nextPayment)
    {
        if (empty($nextPayment)) {
            return '<div class="alert alert-success">No upcoming payments. You\'re all paid up! ✓</div>';
        }

        $dueDate = $nextPayment['due_date'];
        $amount = number_format($nextPayment['amount'], 2);
        $status = $nextPayment['status'];

        $daysUntilDue = (strtotime($dueDate) - time()) / 86400;
        $daysUntilDue = floor($daysUntilDue);

        if ($status === 'overdue') {
            $gracePeriodEnd = $nextPayment['grace_period_end'] ?? null;
            $daysInGrace = $gracePeriodEnd ? floor((strtotime($gracePeriodEnd) - time()) / 86400) : 0;

            if ($daysInGrace > 0) {
                return '<div class="alert alert-danger">' .
                    '<strong>⚠️ Payment Overdue!</strong><br>' .
                    'Amount: Rs. ' . $amount . '<br>' .
                    'Grace period ends in ' . $daysInGrace . ' day(s). Service will be cancelled if not paid.<br>' .
                    '<a href="' . URLROOT . '/client/payRecurring?payment_id=' . $nextPayment['id'] . '" class="btn btn-danger btn-sm mt-2">Pay Now</a>' .
                    '</div>';
            } else {
                return '<div class="alert alert-danger">' .
                    '<strong>⚠️ Payment Overdue - Grace Period Expired!</strong><br>' .
                    'This booking may be cancelled automatically.<br>' .
                    '<a href="' . URLROOT . '/client/payRecurring?payment_id=' . $nextPayment['id'] . '" class="btn btn-danger btn-sm mt-2">Pay Immediately</a>' .
                    '</div>';
            }
        } elseif ($daysUntilDue <= 0) {
            return '<div class="alert alert-warning">' .
                '<strong>Payment Due Today!</strong><br>' .
                'Amount: Rs. ' . $amount . '<br>' .
                '<a href="' . URLROOT . '/client/payRecurring?payment_id=' . $nextPayment['id'] . '" class="btn btn-warning btn-sm mt-2">Pay Now</a>' .
                '</div>';
        } elseif ($daysUntilDue <= 7) {
            return '<div class="alert alert-info">' .
                '<strong>Upcoming Payment</strong><br>' .
                'Amount: Rs. ' . $amount . '<br>' .
                'Due in ' . $daysUntilDue . ' day(s) - ' . date('M j, Y', strtotime($dueDate)) . '<br>' .
                '<a href="' . URLROOT . '/client/payRecurring?payment_id=' . $nextPayment['id'] . '" class="btn btn-info btn-sm mt-2">Pay Early</a>' .
                '</div>';
        } else {
            return '<div class="alert alert-secondary">' .
                'Next payment: Rs. ' . $amount . ' due on ' . date('M j, Y', strtotime($dueDate)) .
                '</div>';
        }
    }
}
