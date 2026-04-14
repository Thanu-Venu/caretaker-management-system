<?php

/**
 * Notification System Test Script
 * This script tests the recurring payment reminder system
 */

require_once 'app/core/Database.php';

$db = new Database();
$conn = $db->conn;

echo "\n";
echo str_repeat("=", 80) . "\n";
echo "RECURRING PAYMENT NOTIFICATION SYSTEM - TEST VERIFICATION\n";
echo str_repeat("=", 80) . "\n\n";

// Current date
$currentDate = date('Y-m-d');
echo "Current System Date: " . date('Y-m-d H:i:s') . "\n\n";

// Step 1: Show recurring payments for booking #17
echo str_repeat("-", 80) . "\n";
echo "STEP 1: RECURRING PAYMENTS FOR BOOKING #17\n";
echo str_repeat("-", 80) . "\n";

$query = "
    SELECT
        rp.id,
        rp.cycle_number,
        rp.due_date,
        rp.amount,
        rp.status,
        rp.reminder_7_days_sent,
        rp.reminder_3_days_sent,
        rp.reminder_due_date_sent,
        DATEDIFF(rp.due_date, CURDATE()) as days_until_due
    FROM recurring_payments rp
    WHERE rp.booking_id = 17
    ORDER BY rp.cycle_number
";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    echo "❌ No recurring payments found!\n";
    exit;
}

echo "\nFound " . $result->num_rows . " recurring payment(s):\n\n";
while ($payment = $result->fetch_assoc()) {
    echo "  Cycle #{$payment['cycle_number']} | Due: {$payment['due_date']} | ";
    echo "Amount: Rs. {$payment['amount']} | Status: {$payment['status']} | ";
    echo "Days until due: {$payment['days_until_due']}\n";
    echo "    Reminders: 7-day=" . ($payment['reminder_7_days_sent'] ? '✓' : '✗');
    echo " | 3-day=" . ($payment['reminder_3_days_sent'] ? '✓' : '✗');
    echo " | Due-date=" . ($payment['reminder_due_date_sent'] ? '✓' : '✗') . "\n\n";
}

// Step 2: Check which reminders WOULD be sent today
echo str_repeat("-", 80) . "\n";
echo "STEP 2: REMINDERS THAT WOULD BE SENT TODAY (March 7, 2026)\n";
echo str_repeat("-", 80) . "\n\n";

// 7-day reminders (due in 7 days)
$query = "
    SELECT COUNT(*) as count
    FROM recurring_payments
    WHERE booking_id = 17
    AND status = 'pending'
    AND reminder_7_days_sent = 0
    AND due_date = DATE_ADD(CURDATE(), INTERVAL 7 DAY)
";
$result = $conn->query($query);
$count = $result->fetch_assoc()['count'];
echo "7-day reminders (due on " . date('Y-m-d', strtotime('+7 days')) . "): {$count}\n";

// 3-day reminders (due in 3 days)
$query = "
    SELECT COUNT(*) as count
    FROM recurring_payments
    WHERE booking_id = 17
    AND status = 'pending'
    AND reminder_3_days_sent = 0
    AND due_date = DATE_ADD(CURDATE(), INTERVAL 3 DAY)
";
$result = $conn->query($query);
$count = $result->fetch_assoc()['count'];
echo "3-day reminders (due on " . date('Y-m-d', strtotime('+3 days')) . "): {$count}\n";

// Due date reminders (due today)
$query = "
    SELECT COUNT(*) as count
    FROM recurring_payments
    WHERE booking_id = 17
    AND status = 'pending'
    AND reminder_due_date_sent = 0
    AND due_date = CURDATE()
";
$result = $conn->query($query);
$count = $result->fetch_assoc()['count'];
echo "Due date reminders (due today): {$count}\n\n";

echo "--- No reminders would be sent today (earliest due date is June 7, 2026) ---\n\n";

// Step 3: Simulate June 25, 2026
echo str_repeat("-", 80) . "\n";
echo "STEP 3: SIMULATION - WHAT WOULD HAPPEN ON JUNE 25, 2026?\n";
echo str_repeat("-", 80) . "\n\n";

$simulatedDate = '2026-06-25';
echo "Simulated Date: {$simulatedDate}\n\n";

// Check 7-day reminders (due July 2, 2026)
$query = "
    SELECT
        id, cycle_number, due_date, amount,
        DATEDIFF(due_date, '{$simulatedDate}') as days_until_due
    FROM recurring_payments
    WHERE booking_id = 17
    AND status = 'pending'
    AND due_date = DATE_ADD('{$simulatedDate}', INTERVAL 7 DAY)
";
$result = $conn->query($query);
echo "7-day reminders (due on July 2, 2026):\n";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "  ✓ Cycle #{$row['cycle_number']} - Due: {$row['due_date']} - Rs. {$row['amount']}\n";
    }
} else {
    echo "  None\n";
}
echo "\n";

// Check 3-day reminders
$query = "
    SELECT
        id, cycle_number, due_date, amount
    FROM recurring_payments
    WHERE booking_id = 17
    AND status = 'pending'
    AND due_date = DATE_ADD('{$simulatedDate}', INTERVAL 3 DAY)
";
$result = $conn->query($query);
echo "3-day reminders (due on June 28, 2026):\n";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "  ✓ Cycle #{$row['cycle_number']} - Due: {$row['due_date']} - Rs. {$row['amount']}\n";
    }
} else {
    echo "  None\n";
}
echo "\n";

// Check due date reminders
$query = "
    SELECT
        id, cycle_number, due_date, amount
    FROM recurring_payments
    WHERE booking_id = 17
    AND status = 'pending'
    AND due_date = '{$simulatedDate}'
";
$result = $conn->query($query);
echo "Due date reminders (due today June 25, 2026):\n";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "  ✓ Cycle #{$row['cycle_number']} - Due: {$row['due_date']} - Rs. {$row['amount']}\n";
    }
} else {
    echo "  None\n";
}
echo "\n";

// Step 4: Send a TEST notification
echo str_repeat("-", 80) . "\n";
echo "STEP 4: SEND TEST NOTIFICATION\n";
echo str_repeat("-", 80) . "\n\n";

// Get booking and client details
$query = "
    SELECT
        b.id as booking_id,
        b.client_id,
        c.name as client_name
    FROM bookings b
    JOIN clients c ON b.client_id = c.id
    WHERE b.id = 17
";
$result = $conn->query($query);
$booking = $result->fetch_assoc();

if ($booking) {
    // Insert test notification
    $title = "Recurring Payment Reminder - TEST";
    $message = "TEST: Your recurring payment of Rs. 45,000 for Elder Care service is due on July 1, 2026 (in 7 days). Please ensure timely payment.";
    $link = "http://localhost/CMA/client/c_payment";
    $query = "
        INSERT INTO notifications (user_id, user_role, title, message, link, created_at)
        VALUES (?, 'client', ?, ?, ?, NOW())
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('isss', $booking['client_id'], $title, $message, $link);

    if ($stmt->execute()) {
        $notificationId = $conn->insert_id;
        echo "✓ Test notification created successfully!\n";
        echo "  Notification ID: {$notificationId}\n";
        echo "  Client ID: {$booking['client_id']}\n";
        echo "  Client Name: {$booking['client_name']}\n";
        echo "  Message: {$message}\n\n";
    } else {
        echo "❌ Failed to create notification: " . $stmt->error . "\n\n";
    }
}

// Step 5: Check notifications
echo str_repeat("-", 80) . "\n";
echo "STEP 5: RECENT NOTIFICATIONS FOR BOOKING #17 CLIENT\n";
echo str_repeat("-", 80) . "\n\n";

$query = "
    SELECT
        id,
        title,
        message,
        is_read,
        created_at
    FROM notifications
    WHERE user_id = {$booking['client_id']}
    AND user_role = 'client'
    AND (title LIKE '%payment%' OR title LIKE '%Payment%')
    ORDER BY created_at DESC
    LIMIT 5
";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "Found " . $result->num_rows . " payment notification(s):\n\n";
    while ($notif = $result->fetch_assoc()) {
        echo "  ID: {$notif['id']} | ";
        echo "Read: " . ($notif['is_read'] ? 'Yes' : 'No') . "\n";
        echo "  Title: {$notif['title']}\n";
        echo "  Message: " . substr($notif['message'], 0, 100) . "...\n";
        echo "  Created: {$notif['created_at']}\n\n";
    }
} else {
    echo "No payment notifications found.\n\n";
}

// Step 6: Instructions
echo str_repeat("=", 80) . "\n";
echo "HOW TO TEST WITH ACTUAL DATE CHANGE\n";
echo str_repeat("=", 80) . "\n\n";

echo "To test with actual system date change:\n\n";
echo "1. Change Windows system date to June 25, 2026:\n";
echo "   - Right-click clock in taskbar\n";
echo "   - Click 'Adjust date and time'\n";
echo "   - Turn OFF 'Set time automatically'\n";
echo "   - Click 'Change' and set to June 25, 2026\n\n";

echo "2. Restart MySQL/Apache in WAMP (to pick up system date)\n\n";

echo "3. Run the cron job:\n";
echo "   C:\\wamp64\\bin\\php\\php8.3.14\\php.exe c:\\wamp64\\www\\CMA\\app\\cron\\process_recurring_payments.php\n\n";

echo "4. Check the log file:\n";
echo "   Look for: logs\\payment_cron_2026-06-25.log\n\n";

echo "5. Check notifications in the database or client dashboard\n\n";

echo "6. IMPORTANT: Change system date back to correct date after testing!\n\n";

echo str_repeat("=", 80) . "\n";
echo "TEST COMPLETED\n";
echo str_repeat("=", 80) . "\n\n";
