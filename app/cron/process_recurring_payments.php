<?php

/**
 * Daily Payment Processing Cron Job
 *
 * This script should be run daily (preferably at midnight) via cron job
 * to handle:
 * 1. Send payment reminders (7, 3, 0 days before due date)
 * 2. Mark overdue payments
 * 3. Auto-cancel bookings past grace period
 *
 * Setup cron job (Linux/Unix):
 * 0 0 * * * /usr/bin/php /path/to/CMA/app/cron/process_recurring_payments.php
 *
 * Windows Task Scheduler:
 * Program: php.exe
 * Arguments: C:\wamp64\www\CMA\app\cron\process_recurring_payments.php
 * Start in: C:\wamp64\www\CMA\app\cron
 * Trigger: Daily at 12:00 AM
 */

// Prevent browser access
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from command line.");
}

// Include required files
define('APPROOT', dirname(__DIR__));
require_once APPROOT . '/core/Database.php';
require_once APPROOT . '/core/RecurringPaymentService.php';

// Log file
$logFile = APPROOT . '/../logs/payment_cron_' . date('Y-m-d') . '.log';
$logDir = dirname($logFile);
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

function logMessage($message)
{
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[{$timestamp}] {$message}\n", FILE_APPEND);
    echo "[{$timestamp}] {$message}\n";
}

logMessage("=== Starting Daily Payment Processing ===");

try {
    // Initialize database connection
    $db = new Database();
    $conn = $db->conn;

    logMessage("Database connection established");

    // Initialize RecurringPaymentService
    $recurringService = new RecurringPaymentService($conn);

    // Step 1: Send payment reminders
    logMessage("Step 1: Sending payment reminders...");
    $remindersSent = $recurringService->sendPaymentReminders();
    logMessage("  - 7-day reminders sent: {$remindersSent['7_days']}");
    logMessage("  - 3-day reminders sent: {$remindersSent['3_days']}");
    logMessage("  - Due date reminders sent: {$remindersSent['due_date']}");

    // Step 2: Mark overdue payments
    logMessage("Step 2: Marking overdue payments...");
    $overdueCount = $recurringService->markOverduePayments();
    logMessage("  - Payments marked as overdue: {$overdueCount}");

    // Step 3: Auto-cancel bookings past grace period
    logMessage("Step 3: Auto-cancelling unpaid bookings...");
    $cancelledBookings = $recurringService->autoCancelUnpaidBookings();
    $cancelCount = count($cancelledBookings);
    logMessage("  - Bookings auto-cancelled: {$cancelCount}");

    if ($cancelCount > 0) {
        foreach ($cancelledBookings as $booking) {
            logMessage("    - Booking #{$booking['booking_id']}: {$booking['client_name']} - {$booking['service_type']}");
        }
    }

    logMessage("=== Payment Processing Completed Successfully ===");
    exit(0);
} catch (Exception $e) {
    logMessage("ERROR: " . $e->getMessage());
    logMessage("Stack trace: " . $e->getTraceAsString());
    logMessage("=== Payment Processing Failed ===");
    exit(1);
}
