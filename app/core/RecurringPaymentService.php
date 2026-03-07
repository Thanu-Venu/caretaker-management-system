<?php

require_once __DIR__ . '/../models/NotificationModel.php';

/**
 * RecurringPaymentService
 *
 * Handles recurring payment operations:
 * - Creating recurring payment records
 * - Sending reminders (7, 3, 0 days before due)
 * - Managing grace period (3 days after due date)
 * - Auto-cancellation for non-payment
 */
class RecurringPaymentService
{
    private $conn;
    private $notificationModel;

    public function __construct($dbConnection = null)
    {
        if ($dbConnection) {
            $this->conn = $dbConnection;
        } else {
            require_once __DIR__ . '/Database.php';
            $db = new Database();
            $this->conn = $db->conn;
        }

        $this->notificationModel = new NotificationModel();
    }

    /**
     * Create recurring payment records for a booking
     *
     * @param int $bookingId
     * @param array $bookingData
     * @param array $schedule Payment schedule from PaymentCalculationService
     * @return bool Success status
     */
    public function createRecurringPayments($bookingId, $bookingData, $schedule)
    {
        if (empty($schedule)) {
            return true; // No recurring payments needed
        }

        // Idempotency guard: do not create a second schedule for the same booking.
        $existsStmt = $this->conn->prepare("SELECT COUNT(*) AS cnt FROM recurring_payments WHERE booking_id = ?");
        $existsStmt->bind_param("i", $bookingId);
        $existsStmt->execute();
        $existsRow = $existsStmt->get_result()->fetch_assoc();
        $existsStmt->close();

        if (!empty($existsRow['cnt']) && (int)$existsRow['cnt'] > 0) {
            return true;
        }

        $clientId = $bookingData['client_id'];
        $caretakerId = $bookingData['caretaker_id'];

        $sql = "INSERT INTO recurring_payments
                (booking_id, client_id, caretaker_id, cycle_number, cycle_type, due_date, amount, status, grace_period_end)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', DATE_ADD(?, INTERVAL 3 DAY))";

        $stmt = $this->conn->prepare($sql);

        foreach ($schedule as $payment) {
            $stmt->bind_param(
                "iiiissds",
                $bookingId,
                $clientId,
                $caretakerId,
                $payment['cycle_number'],
                $payment['cycle_type'],
                $payment['due_date'],
                $payment['amount'],
                $payment['due_date']
            );

            if (!$stmt->execute()) {
                error_log("Failed to create recurring payment: " . $stmt->error);
                return false;
            }
        }

        $stmt->close();
        return true;
    }

    /**
     * Send payment reminders for upcoming due dates
     * Run this via cron job daily
     *
     * @return array Summary of reminders sent
     */
    public function sendPaymentReminders()
    {
        $today = date('Y-m-d');
        $remindersSent = [
            '7_days' => 0,
            '3_days' => 0,
            'due_date' => 0
        ];

        // 7 days before reminder
        $date7Days = date('Y-m-d', strtotime('+7 days'));
        $remindersSent['7_days'] = $this->sendReminderForDate($date7Days, '7_days', 7);

        // 3 days before reminder
        $date3Days = date('Y-m-d', strtotime('+3 days'));
        $remindersSent['3_days'] = $this->sendReminderForDate($date3Days, '3_days', 3);

        // Due date reminder
        $remindersSent['due_date'] = $this->sendReminderForDate($today, 'due_date', 0);

        return $remindersSent;
    }

    /**
     * Send reminder for a specific date
     */
    private function sendReminderForDate($dueDate, $reminderType, $daysAhead)
    {
        $reminderColumn = "reminder_{$reminderType}_sent";

        $sql = "SELECT rp.*, b.service_type, b.basis, b.booking_date, b.preferred_time,
                       c.name as client_name, c.email as client_email,
                       ct.name as caretaker_name
                FROM recurring_payments rp
                JOIN bookings b ON rp.booking_id = b.id
                JOIN clients c ON rp.client_id = c.id
                JOIN caretakers ct ON rp.caretaker_id = ct.id
                WHERE rp.due_date = ?
                  AND rp.status = 'pending'
                  AND rp.{$reminderColumn} = 0";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $dueDate);
        $stmt->execute();
        $result = $stmt->get_result();

        $count = 0;
        while ($payment = $result->fetch_assoc()) {
            $this->sendPaymentReminderNotification($payment, $daysAhead);

            // Mark reminder as sent
            $updateSql = "UPDATE recurring_payments SET {$reminderColumn} = 1 WHERE id = ?";
            $updateStmt = $this->conn->prepare($updateSql);
            $updateStmt->bind_param("i", $payment['id']);
            $updateStmt->execute();
            $updateStmt->close();

            $count++;
        }

        $stmt->close();
        return $count;
    }

    /**
     * Send payment reminder notification
     */
    private function sendPaymentReminderNotification($payment, $daysAhead)
    {
        $clientId = $payment['client_id'];
        $bookingId = $payment['booking_id'];
        $amount = number_format($payment['amount'], 2);
        $dueDate = date('F j, Y', strtotime($payment['due_date']));

        if ($daysAhead == 0) {
            $title = 'Payment Due Today';
            $message = "Payment of Rs. {$amount} is due today for your booking #{$bookingId}.\n" .
                "Service: {$payment['service_type']}\n" .
                "Caretaker: {$payment['caretaker_name']}\n\n" .
                "Please complete the payment to avoid service interruption.";
        } else {
            $title = "Payment Reminder - {$daysAhead} Days";
            $message = "Your payment of Rs. {$amount} is due in {$daysAhead} days ({$dueDate}).\n" .
                "Booking #{$bookingId} | {$payment['service_type']}\n" .
                "Caretaker: {$payment['caretaker_name']}\n\n" .
                "Please ensure timely payment to continue the service.";
        }

        $this->notificationModel->addNotification(
            $clientId,
            'client',
            $title,
            $message,
            "http://localhost/CMA/client/paymentDetails/{$bookingId}"
        );

        // Also notify HR
        $hrMessage = "Upcoming payment due for Booking #{$bookingId}\n" .
            "Client: {$payment['client_name']}\n" .
            "Amount: Rs. {$amount}\n" .
            "Due: {$dueDate}";

        $this->notificationModel->addNotification(
            5, // HR Manager ID
            'Manager',
            'Upcoming Payment Due',
            $hrMessage,
            "http://localhost/CMA/hr/pendingPayments"
        );
    }

    /**
     * Check for overdue payments and mark them
     * Run this via cron job daily
     *
     * @return int Number of payments marked overdue
     */
    public function markOverduePayments()
    {
        $today = date('Y-m-d');

        $sql = "UPDATE recurring_payments
                SET status = 'overdue'
                WHERE due_date < ?
                  AND status = 'pending'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $affectedRows;
    }

    /**
     * Auto-cancel bookings with payments past grace period
     * Run this via cron job daily
     *
     * Grace period = 3 days after due date
     *
     * @return array Bookings cancelled
     */
    public function autoCancelUnpaidBookings()
    {
        $today = date('Y-m-d');

        // Find payments past grace period
        $sql = "SELECT rp.*, b.status as booking_status, b.service_type,
                       c.name as client_name, c.email as client_email,
                       ct.name as caretaker_name, ct.email as caretaker_email
                FROM recurring_payments rp
                JOIN bookings b ON rp.booking_id = b.id
                JOIN clients c ON rp.client_id = c.id
                JOIN caretakers ct ON rp.caretaker_id = ct.id
                WHERE rp.status = 'overdue'
                  AND rp.grace_period_end < ?
                  AND b.status IN ('Accepted', 'Advance_Paid')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();

        $cancelledBookings = [];

        while ($payment = $result->fetch_assoc()) {
            $bookingId = $payment['booking_id'];

            // Cancel the booking
            $cancelSql = "UPDATE bookings
                         SET status = 'Cancelled',
                             cancellation_reason = 'Auto-cancelled due to non-payment',
                             cancelled_at = NOW()
                         WHERE id = ? AND status IN ('Accepted', 'Advance_Paid')";

            $cancelStmt = $this->conn->prepare($cancelSql);
            $cancelStmt->bind_param("i", $bookingId);

            if ($cancelStmt->execute() && $cancelStmt->affected_rows > 0) {
                $cancelStmt->close();

                // Cancel all pending recurring payments for this booking
                $cancelPaymentsSql = "UPDATE recurring_payments
                                     SET status = 'cancelled'
                                     WHERE booking_id = ? AND status IN ('pending', 'overdue')";
                $cancelPaymentsStmt = $this->conn->prepare($cancelPaymentsSql);
                $cancelPaymentsStmt->bind_param("i", $bookingId);
                $cancelPaymentsStmt->execute();
                $cancelPaymentsStmt->close();

                // Send notifications
                $this->sendCancellationNotifications($payment);

                $cancelledBookings[] = [
                    'booking_id' => $bookingId,
                    'client_name' => $payment['client_name'],
                    'service_type' => $payment['service_type']
                ];
            } else {
                $cancelStmt->close();
            }
        }

        $stmt->close();
        return $cancelledBookings;
    }

    /**
     * Send cancellation notifications to all parties
     */
    private function sendCancellationNotifications($payment)
    {
        $bookingId = $payment['booking_id'];

        // Notify client
        $clientMessage = "Your booking #{$bookingId} has been automatically cancelled due to non-payment.\n\n" .
            "Service: {$payment['service_type']}\n" .
            "Caretaker: {$payment['caretaker_name']}\n\n" .
            "Payment was due on {$payment['due_date']} with a 3-day grace period.\n" .
            "Please contact us if you wish to rebook this service.";

        $this->notificationModel->addNotification(
            $payment['client_id'],
            'client',
            'Booking Cancelled - Non-Payment',
            $clientMessage,
            "http://localhost/CMA/client/c_cancelledBookings"
        );

        // Notify HR
        $hrMessage = "Booking #{$bookingId} auto-cancelled due to non-payment.\n\n" .
            "Client: {$payment['client_name']} (ID: {$payment['client_id']})\n" .
            "Service: {$payment['service_type']}\n" .
            "Caretaker: {$payment['caretaker_name']}\n" .
            "Payment was due: {$payment['due_date']}";

        $this->notificationModel->addNotification(
            5, // HR Manager ID
            'Manager',
            'Booking Auto-Cancelled',
            $hrMessage,
            "http://localhost/CMA/hr/cancelledBookings"
        );

        // Notify caretaker
        $caretakerMessage = "Booking #{$bookingId} has been cancelled due to client non-payment.\n\n" .
            "Client: {$payment['client_name']}\n" .
            "Service: {$payment['service_type']}\n\n" .
            "You are now available for new bookings.";

        $this->notificationModel->addNotification(
            $payment['caretaker_id'],
            'caretaker',
            'Booking Cancelled',
            $caretakerMessage,
            "http://localhost/CMA/caretaker/ct_booking"
        );
    }

    /**
     * Mark recurring payment as paid
     *
     * @param int $recurringPaymentId
     * @param int $paymentId Reference to payments table
     * @return bool
     */
    public function markRecurringPaymentAsPaid($recurringPaymentId, $paymentId)
    {
        $sql = "UPDATE recurring_payments
                SET status = 'paid',
                    paid_at = NOW(),
                    payment_id = ?
                WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $paymentId, $recurringPaymentId);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    /**
     * Get recurring payment by id for a specific client and booking.
     */
    public function getRecurringPaymentByIdForClient($recurringPaymentId, $clientId, $bookingId)
    {
        $sql = "SELECT * FROM recurring_payments
                WHERE id = ? AND client_id = ? AND booking_id = ?
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $recurringPaymentId, $clientId, $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();
        $payment = $result->fetch_assoc();
        $stmt->close();

        return $payment ?: null;
    }

    /**
     * Mark one matching pending/overdue recurring payment as paid.
     */
    public function markRecurringPaymentAsPaidByDetails($bookingId, $dueDate, $amount, $paymentId)
    {
        $sql = "SELECT id FROM recurring_payments
                WHERE booking_id = ?
                  AND due_date = ?
                  AND amount = ?
                  AND status IN ('pending', 'overdue')
                ORDER BY cycle_number ASC
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isd", $bookingId, $dueDate, $amount);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if (!$row || empty($row['id'])) {
            return false;
        }

        return $this->markRecurringPaymentAsPaid((int)$row['id'], (int)$paymentId);
    }

    /**
     * Get pending recurring payments for a booking
     *
     * @param int $bookingId
     * @return array
     */
    public function getPendingPayments($bookingId)
    {
        $sql = "SELECT * FROM recurring_payments
                WHERE booking_id = ?
                  AND status IN ('pending', 'overdue')
                ORDER BY cycle_number ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();
        $payments = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $payments;
    }

    /**
     * Get next due payment for a booking
     *
     * @param int $bookingId
     * @return array|null
     */
    public function getNextDuePayment($bookingId)
    {
        $sql = "SELECT * FROM recurring_payments
                WHERE booking_id = ?
                  AND status IN ('pending', 'overdue')
                ORDER BY due_date ASC, cycle_number ASC
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();
        $payment = $result->fetch_assoc();
        $stmt->close();

        return $payment;
    }
}
