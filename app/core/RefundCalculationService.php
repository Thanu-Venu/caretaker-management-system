<?php

/**
 * RefundCalculationService
 *
 * Implements SmartCare cancellation and refund policy
 * Calculates refunds based on service usage, cancellation timing, and payment status
 */
class RefundCalculationService
{
    private $conn;
    private const CANCELLATION_FEE_FIXED = 5000; // LKR 5,000
    private const CANCELLATION_FEE_PERCENTAGE = 0.05; // 5% of advance payment
    private const GRACE_PERIOD_DAYS = 3;

    public function __construct($databaseConnection = null)
    {
        if ($databaseConnection) {
            $this->conn = $databaseConnection;
        } else {
            require_once APPROOT . '/core/Database.php';
            $db = new Database();
            $this->conn = $db->conn;
        }
    }

    /**
     * Calculate refund amount for a cancelled booking
     *
     * @param int $bookingId Booking ID to calculate refund for
     * @param string $cancellationReason Reason for cancellation
     * @param bool $isAutoCancellation Whether this is an automatic cancellation
     * @return array Refund calculation details
     */
    public function calculateRefund($bookingId, $cancellationReason = '', $isAutoCancellation = false)
    {
        // Get booking details with payment information
        $booking = $this->getBookingWithPaymentDetails($bookingId);

        if (!$booking) {
            return [
                'success' => false,
                'message' => 'Booking not found'
            ];
        }

        // Get payment breakdown (approved vs pending)
        $paymentBreakdown = $this->getPaymentBreakdown($bookingId);
        $booking['payment_breakdown'] = $paymentBreakdown;

        // Determine cancellation type
        $cancellationType = $this->determineCancellationType($booking, $isAutoCancellation);

        // Calculate refund based on cancellation type
        switch ($cancellationType) {
            case 'before_service_start':
                $refundData = $this->calculateRefundBeforeServiceStart($booking);
                break;

            case 'after_service_start':
                $refundData = $this->calculateRefundAfterServiceStart($booking);
                break;

            case 'during_recurring':
                $refundData = $this->calculateRefundDuringRecurring($booking);
                break;

            case 'daily_service':
                $refundData = $this->calculateDailyServiceRefund($booking);
                break;

            case 'auto_nonpayment':
                $refundData = $this->calculateRefundForNonPayment($booking);
                break;

            default:
                return [
                    'success' => false,
                    'message' => 'Unknown cancellation type'
                ];
        }

        // Add common fields
        $refundData['booking_id'] = $bookingId;
        $refundData['client_id'] = $booking['client_id'];
        $refundData['cancellation_type'] = $cancellationType;
        $refundData['cancellation_reason'] = $cancellationReason;
        $refundData['success'] = true;

        return $refundData;
    }

    /**
     * Get booking details with payment information
     */
    private function getBookingWithPaymentDetails($bookingId)
    {
        $sql = "SELECT b.*,
                    COALESCE(
                        (
                            SELECT SUM(p.amount)
                            FROM payments p
                            WHERE p.booking_id = b.id
                              AND p.payment_type = 'advance'
                              AND p.status IN ('approved', 'pending')
                        ),
                        b.advance_amount,
                        b.advance_balance,
                        0
                    ) as advance_paid,
                    COALESCE(
                        (
                            SELECT SUM(p.amount)
                            FROM payments p
                            WHERE p.booking_id = b.id
                              AND p.status IN ('approved', 'pending')
                        ),
                        b.advance_amount,
                        b.advance_balance,
                        0
                    ) as total_paid,
                    DATEDIFF(CURDATE(), b.service_start_date) as days_since_start,
                    DATEDIFF(CURDATE(), b.booking_date) as days_since_booking,
                    (SELECT COUNT(*) FROM recurring_payments WHERE booking_id = b.id AND status = 'paid') as recurring_paid_count,
                    (SELECT SUM(amount) FROM recurring_payments WHERE booking_id = b.id AND status = 'paid') as recurring_paid_total
                FROM bookings b
                WHERE b.id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Get payment status breakdown for a booking
     * Returns information about approved vs pending payments
     */
    private function getPaymentBreakdown($bookingId)
    {
        $sql = "SELECT
                    SUM(CASE WHEN status = 'approved' THEN amount ELSE 0 END) as approved_amount,
                    SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as pending_amount,
                    COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_count,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count
                FROM payments
                WHERE booking_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Determine the type of cancellation
     */
    private function determineCancellationType($booking, $isAutoCancellation)
    {
        if ($isAutoCancellation) {
            return 'auto_nonpayment';
        }

        $basis = strtolower($booking['basis']);
        $serviceStartDate = $booking['service_start_date'] ?? $booking['booking_date'];
        $today = date('Y-m-d');

        // Check if service has started
        $serviceStarted = (strtotime($today) >= strtotime($serviceStartDate));

        // Daily service
        if ($basis === 'daily') {
            return 'daily_service';
        }

        // Before service start
        if (!$serviceStarted) {
            return 'before_service_start';
        }

        // Check if in recurring payment stage
        $advanceMonths = (int)$booking['advance_months'];
        $monthsSinceStart = $this->calculateMonthsSinceStart($serviceStartDate);

        if ($monthsSinceStart > $advanceMonths && $advanceMonths > 0) {
            return 'during_recurring';
        }

        // After service start but within advance period
        return 'after_service_start';
    }

    /**
     * Calculate months elapsed since service start
     */
    private function calculateMonthsSinceStart($serviceStartDate)
    {
        $start = new DateTime($serviceStartDate);
        $today = new DateTime();
        $interval = $start->diff($today);

        return ($interval->y * 12) + $interval->m;
    }

    /**
     * Calculate days elapsed since service start
     */
    private function calculateDaysSinceStart($serviceStartDate)
    {
        $start = new DateTime($serviceStartDate);
        $today = new DateTime();
        $interval = $start->diff($today);

        return $interval->days;
    }

    /**
     * Count started months for refund deduction.
     * Once a month has started, it is treated as used.
     */
    private function calculateStartedMonthsForRefund($serviceStartDate)
    {
        $start = new DateTime($serviceStartDate);
        $today = new DateTime();

        if ($today < $start) {
            return 0;
        }

        $interval = $start->diff($today);
        $months = ($interval->y * 12) + $interval->m;

        // If there are extra days, current month has started and is non-refundable.
        if ($interval->d > 0) {
            $months += 1;
        }

        return max(1, $months);
    }

    /**
     * Calculate cancellation fee
     */
    private function calculateCancellationFee($advanceAmount)
    {
        // Use whichever is larger: fixed fee or percentage
        $percentageFee = $advanceAmount * self::CANCELLATION_FEE_PERCENTAGE;
        return max(self::CANCELLATION_FEE_FIXED, $percentageFee);
    }

    /**
     * Refund calculation: Before service start
     * Rule: Refund = Advance Paid - Cancellation Fee
     */
    private function calculateRefundBeforeServiceStart($booking)
    {
        $advancePaid = (float)($booking['advance_paid'] ?? 0);
        $cancellationFee = $this->calculateCancellationFee($advancePaid);
        $refundAmount = max(0, $advancePaid - $cancellationFee);

        // Check for pending payments
        $paymentBreakdown = $booking['payment_breakdown'] ?? [];
        $pendingAmount = (float)($paymentBreakdown['pending_amount'] ?? 0);
        $approvedAmount = (float)($paymentBreakdown['approved_amount'] ?? 0);

        $message = 'Service has not started yet. Refund processed after deducting cancellation fee.';
        if ($pendingAmount > 0) {
            $message .= " NOTE: This refund includes LKR " . number_format($pendingAmount, 2) . " from pending (unapproved) payments.";
        }

        return [
            'total_paid' => $advancePaid,
            'service_used_amount' => 0.00,
            'cancellation_fee' => $cancellationFee,
            'refund_amount' => $refundAmount,
            'refund_calculation' => json_encode([
                'scenario' => 'Cancellation before service start',
                'advance_paid' => $advancePaid,
                'total_paid' => $advancePaid,
                'approved_payments' => $approvedAmount,
                'pending_payments' => $pendingAmount,
                'cancellation_fee' => $cancellationFee,
                'calculation' => "{$advancePaid} - {$cancellationFee} = {$refundAmount}",
                'message' => $message
            ])
        ];
    }

    /**
     * Refund calculation: After service has started
     * Rule: Refund = Total Paid - Used Service Value - Cancellation Fee
     */
    private function calculateRefundAfterServiceStart($booking)
    {
        $basis = strtolower($booking['basis']);
        $totalPaid = (float)($booking['total_paid'] ?? 0);
        $advancePaid = (float)($booking['advance_paid'] ?? 0);

        $usedAmount = 0;

        if ($basis === 'monthly' || $basis === 'yearly') {
            // Calculate months used
            $monthsUsed = $this->calculateStartedMonthsForRefund($booking['service_start_date']);
            $totalMonths = (int)$booking['total_months'];

            if ($totalMonths > 0) {
                $monthlyRate = (float)$booking['total_payment'] / $totalMonths;
                $usedAmount = $monthlyRate * min($monthsUsed, $totalMonths);
            }
        } elseif ($basis === 'hourly') {
            // Hourly services are typically paid in full upfront
            $usedAmount = $totalPaid; // Consider all as used if service started
        }

        $cancellationFee = $this->calculateCancellationFee($advancePaid);
        $refundAmount = max(0, $totalPaid - $usedAmount - $cancellationFee);

        // Check for pending payments
        $paymentBreakdown = $booking['payment_breakdown'] ?? [];
        $pendingAmount = (float)($paymentBreakdown['pending_amount'] ?? 0);
        $approvedAmount = (float)($paymentBreakdown['approved_amount'] ?? 0);

        $message = 'Charges for used service deducted from refund.';
        if ($pendingAmount > 0) {
            $message .= " NOTE: This refund includes LKR " . number_format($pendingAmount, 2) . " from pending (unapproved) payments.";
        }

        return [
            'total_paid' => $totalPaid,
            'service_used_amount' => $usedAmount,
            'cancellation_fee' => $cancellationFee,
            'refund_amount' => $refundAmount,
            'refund_calculation' => json_encode([
                'scenario' => 'Cancellation after service started',
                'advance_paid' => $advancePaid,
                'total_paid' => $totalPaid,
                'approved_payments' => $approvedAmount,
                'pending_payments' => $pendingAmount,
                'basis' => $basis,
                'months_used' => $monthsUsed ?? null,
                'monthly_rate' => isset($monthlyRate) ? $monthlyRate : null,
                'service_used_amount' => $usedAmount,
                'cancellation_fee' => $cancellationFee,
                'calculation' => "{$totalPaid} - {$usedAmount} - {$cancellationFee} = {$refundAmount}",
                'message' => $message
            ])
        ];
    }

    /**
     * Refund calculation: During recurring payment stage
     * Rule: Current cycle non-refundable, future prepaid cycles refundable
     */
    private function calculateRefundDuringRecurring($booking)
    {
        $totalPaid = (float)($booking['total_paid'] ?? 0);
        $advancePaid = (float)($booking['advance_paid'] ?? 0);

        $monthsUsed = $this->calculateStartedMonthsForRefund($booking['service_start_date']);
        $totalMonths = (int)$booking['total_months'];
        $monthlyRate = (float)$booking['total_payment'] / $totalMonths;

        // Current month is non-refundable once started.
        $monthsUsedForCalculation = $monthsUsed;

        $usedAmount = $monthlyRate * $monthsUsedForCalculation;
        $cancellationFee = $this->calculateCancellationFee($advancePaid);
        $refundAmount = max(0, $totalPaid - $usedAmount - $cancellationFee);

        // Check for pending payments
        $paymentBreakdown = $booking['payment_breakdown'] ?? [];
        $pendingAmount = (float)($paymentBreakdown['pending_amount'] ?? 0);
        $approvedAmount = (float)($paymentBreakdown['approved_amount'] ?? 0);

        $message = 'Current billing cycle is non-refundable. Future prepaid cycles refunded.';
        if ($pendingAmount > 0) {
            $message .= " NOTE: This refund includes LKR " . number_format($pendingAmount, 2) . " from pending (unapproved) payments.";
        }

        return [
            'total_paid' => $totalPaid,
            'service_used_amount' => $usedAmount,
            'cancellation_fee' => $cancellationFee,
            'refund_amount' => $refundAmount,
            'refund_calculation' => json_encode([
                'scenario' => 'Cancellation during recurring payment stage',
                'advance_paid' => $advancePaid,
                'total_paid' => $totalPaid,
                'approved_payments' => $approvedAmount,
                'pending_payments' => $pendingAmount,
                'months_used' => $monthsUsedForCalculation,
                'monthly_rate' => $monthlyRate,
                'service_used_amount' => $usedAmount,
                'cancellation_fee' => $cancellationFee,
                'calculation' => "{$totalPaid} - {$usedAmount} - {$cancellationFee} = {$refundAmount}",
                'message' => $message
            ])
        ];
    }

    /**
     * Refund calculation: Daily service
     * Rule: Used days deducted, unused prepaid days refunded minus cancellation fee
     */
    private function calculateDailyServiceRefund($booking)
    {
        $totalPaid = (float)($booking['total_paid'] ?? 0);
        $advancePaid = (float)($booking['advance_paid'] ?? 0);
        $duration = (int)$booking['duration'];
        $dailyRate = (float)$booking['total_payment'] / $duration;

        $serviceStartDate = $booking['service_start_date'] ?? $booking['booking_date'];
        $daysUsed = 0;

        if (strtotime(date('Y-m-d')) >= strtotime($serviceStartDate)) {
            $daysUsed = min(max(1, $this->calculateDaysSinceStart($serviceStartDate)), $duration);
        }

        $usedAmount = $dailyRate * $daysUsed;
        $cancellationFee = $this->calculateCancellationFee($advancePaid);
        $refundAmount = max(0, $totalPaid - $usedAmount - $cancellationFee);

        // Check for pending payments
        $paymentBreakdown = $booking['payment_breakdown'] ?? [];
        $pendingAmount = (float)($paymentBreakdown['pending_amount'] ?? 0);
        $approvedAmount = (float)($paymentBreakdown['approved_amount'] ?? 0);

        $message = 'Used service days deducted. Unused prepaid days refunded.';
        if ($pendingAmount > 0) {
            $message .= " NOTE: This refund includes LKR " . number_format($pendingAmount, 2) . " from pending (unapproved) payments.";
        }

        return [
            'total_paid' => $totalPaid,
            'service_used_amount' => $usedAmount,
            'cancellation_fee' => $cancellationFee,
            'refund_amount' => $refundAmount,
            'refund_calculation' => json_encode([
                'scenario' => 'Daily service cancellation',
                'advance_paid' => $advancePaid,
                'total_paid' => $totalPaid,
                'approved_payments' => $approvedAmount,
                'pending_payments' => $pendingAmount,
                'total_days' => $duration,
                'days_used' => $daysUsed,
                'daily_rate' => $dailyRate,
                'service_used_amount' => $usedAmount,
                'cancellation_fee' => $cancellationFee,
                'calculation' => "{$totalPaid} - ({$daysUsed} days × {$dailyRate}) - {$cancellationFee} = {$refundAmount}",
                'message' => $message
            ])
        ];
    }

    /**
     * Refund calculation: Auto-cancellation due to non-payment
     * Rule: No refund - current unpaid cycle is non-refundable
     */
    private function calculateRefundForNonPayment($booking)
    {
        return [
            'total_paid' => 0.00,
            'service_used_amount' => 0.00,
            'cancellation_fee' => 0.00,
            'refund_amount' => 0.00,
            'refund_calculation' => json_encode([
                'scenario' => 'Auto-cancellation due to non-payment',
                'message' => 'No refund applicable. Service cancelled due to payment failure after grace period.',
                'note' => 'The current unpaid billing cycle is non-refundable.'
            ])
        ];
    }

    /**
     * Create refund record in database
     */
    public function createRefundRecord($refundData)
    {
        $sql = "INSERT INTO refunds
                (booking_id, client_id, cancellation_type, total_paid, service_used_amount,
                 cancellation_fee, refund_amount, refund_calculation, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "iisdddds",
            $refundData['booking_id'],
            $refundData['client_id'],
            $refundData['cancellation_type'],
            $refundData['total_paid'],
            $refundData['service_used_amount'],
            $refundData['cancellation_fee'],
            $refundData['refund_amount'],
            $refundData['refund_calculation']
        );

        $result = $stmt->execute();

        if ($result) {
            $refundId = $stmt->insert_id;

            // Update booking refund status
            $updateSql = "UPDATE bookings SET refund_status = 'pending' WHERE id = ?";
            $updateStmt = $this->conn->prepare($updateSql);
            $updateStmt->bind_param("i", $refundData['booking_id']);
            $updateStmt->execute();
            $updateStmt->close();

            return [
                'success' => true,
                'refund_id' => $refundId,
                'message' => 'Refund record created successfully'
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to create refund record'
        ];
    }

    /**
     * Get refund details for a booking
     */
    public function getRefundByBookingId($bookingId)
    {
        $sql = "SELECT * FROM refunds WHERE booking_id = ? ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Update refund status (for HR/Admin approval)
     */
    public function updateRefundStatus($refundId, $status, $userId, $notes = null, $refundMethod = null, $refundReference = null)
    {
        $sql = "UPDATE refunds
                SET status = ?,
                    approved_by = ?,
                    approved_at = NOW(),
                    admin_notes = COALESCE(?, admin_notes),
                    refund_method = COALESCE(?, refund_method),
                    refund_reference = COALESCE(?, refund_reference)";

        if ($status === 'processed' || $status === 'completed') {
            $sql .= ", processed_at = NOW()";
        }

        $sql .= " WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sisssi", $status, $userId, $notes, $refundMethod, $refundReference, $refundId);
        $result = $stmt->execute();

        if ($result && $stmt->affected_rows > 0) {
            // Update booking refund status
            $refund = $this->getRefundById($refundId);
            if ($refund) {
                $updateBookingSql = "UPDATE bookings SET refund_status = ? WHERE id = ?";
                $updateStmt = $this->conn->prepare($updateBookingSql);
                $updateStmt->bind_param("si", $status, $refund['booking_id']);
                $updateStmt->execute();
                $updateStmt->close();
            }

            return true;
        }

        return false;
    }

    /**
     * Get refund by ID
     */
    public function getRefundById($refundId)
    {
        $sql = "SELECT r.*, b.booking_date, b.service_type, b.basis, b.duration,
                       c.name as client_name, c.email as client_email
                FROM refunds r
                JOIN bookings b ON r.booking_id = b.id
                JOIN clients c ON r.client_id = c.id
                WHERE r.id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $refundId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Get all pending refunds (for HR/Admin)
     */
    public function getAllPendingRefunds()
    {
        $sql = "SELECT r.*, b.booking_date, b.service_type, b.basis, b.duration,
                       c.name as client_name, c.email as client_email,
                       ct.name as caretaker_name
                FROM refunds r
                JOIN bookings b ON r.booking_id = b.id
                JOIN clients c ON r.client_id = c.id
                JOIN caretakers ct ON b.caretaker_id = ct.id
                WHERE r.status = 'pending'
                ORDER BY r.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all refunds with filters
     */
    public function getAllRefunds($status = null, $limit = 50)
    {
        $sql = "SELECT r.*, b.booking_date, b.service_type, b.basis, b.duration,
                       c.name as client_name, c.email as client_email,
                      ct.name as caretaker_name,
                      u.username as approved_by_name
                FROM refunds r
                JOIN bookings b ON r.booking_id = b.id
                JOIN clients c ON r.client_id = c.id
                JOIN caretakers ct ON b.caretaker_id = ct.id
                LEFT JOIN users u ON r.approved_by = u.id
                WHERE 1=1";

        if ($status) {
            $sql .= " AND r.status = ?";
        }

        $sql .= " ORDER BY r.created_at DESC LIMIT ?";

        $stmt = $this->conn->prepare($sql);

        if ($status) {
            $stmt->bind_param("si", $status, $limit);
        } else {
            $stmt->bind_param("i", $limit);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}