<?php
require_once 'app/core/Database.php';

$db = new Database();
$conn = $db->conn;

echo "Checking recurring payments for booking #17:\n";
echo str_repeat("=", 80) . "\n\n";

$query = "
    SELECT
        id,
        booking_id,
        cycle_number,
        amount,
        due_date,
        status,
        reminder_7_days_sent,
        reminder_3_days_sent,
        reminder_due_date_sent,
        created_at
    FROM recurring_payments
    WHERE booking_id = 17
    ORDER BY cycle_number
";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error . "\n");
}

if ($result->num_rows === 0) {
    echo "No recurring payments found for booking #17\n";
    echo "This means HR approval did not trigger payment creation.\n";
} else {
    echo "Found " . $result->num_rows . " recurring payment(s):\n\n";
    while ($payment = $result->fetch_assoc()) {
        echo "Cycle #{$payment['cycle_number']}:\n";
        echo "  ID: {$payment['id']}\n";
        echo "  Amount: Rs. {$payment['amount']}\n";
        echo "  Due Date: {$payment['due_date']}\n";
        echo "  Status: {$payment['status']}\n";
        echo "  7-day reminder sent: " . ($payment['reminder_7_days_sent'] ? 'Yes' : 'No') . "\n";
        echo "  3-day reminder sent: " . ($payment['reminder_3_days_sent'] ? 'Yes' : 'No') . "\n";
        echo "  Due date reminder sent: " . ($payment['reminder_due_date_sent'] ? 'Yes' : 'No') . "\n";
        echo "  Created: {$payment['created_at']}\n";
        echo "\n";
    }
}

// Check current system date
echo str_repeat("=", 80) . "\n";
echo "Current PHP date: " . date('Y-m-d H:i:s') . "\n";
echo "MySQL date: ";
$result = $conn->query("SELECT NOW() as current_time");
$row = $result->fetch_assoc();
echo $row['current_time'] . "\n";
