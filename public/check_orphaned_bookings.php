<?php
// Temporary diagnostic script to check for bookings with invalid caretaker_id
require_once '../app/init.php';

$db = new Database();
$conn = $db->conn;

echo "<h2>Checking for Orphaned Bookings</h2>";

// Check bookings without matching caretaker
$sql = "SELECT
    b.id AS booking_id,
    b.client_id,
    b.caretaker_id,
    b.service_type,
    b.booking_date,
    b.status,
    b.created_at,
    CASE
        WHEN c.id IS NULL THEN 'ORPHANED - Caretaker does not exist'
        ELSE 'OK'
    END AS validity_status,
    c.name AS caretaker_name
FROM bookings b
LEFT JOIN caretakers c ON b.caretaker_id = c.id
WHERE b.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY b.created_at DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Booking ID</th><th>Client ID</th><th>Caretaker ID</th><th>Service</th><th>Date</th><th>Status</th><th>Created</th><th>Validity</th><th>Caretaker Name</th></tr>";

    while ($row = $result->fetch_assoc()) {
        $color = ($row['validity_status'] === 'OK') ? '#d4edda' : '#f8d7da';
        echo "<tr style='background-color: $color'>";
        echo "<td>{$row['booking_id']}</td>";
        echo "<td>{$row['client_id']}</td>";
        echo "<td>{$row['caretaker_id']}</td>";
        echo "<td>{$row['service_type']}</td>";
        echo "<td>{$row['booking_date']}</td>";
        echo "<td>{$row['status']}</td>";
        echo "<td>{$row['created_at']}</td>";
        echo "<td><strong>{$row['validity_status']}</strong></td>";
        echo "<td>" . ($row['caretaker_name'] ?? '-') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No recent bookings found.</p>";
}

// Check if any caretakers exist
$ctCheck = $conn->query("SELECT COUNT(*) as ct_count FROM caretakers");
$ctCount = $ctCheck->fetch_assoc()['ct_count'];
echo "<h3>Total Caretakers in Database: $ctCount</h3>";

// Show recent caretakers
echo "<h3>Recent Caretakers:</h3>";
$ctList = $conn->query("SELECT id, name, service_type, location, status FROM caretakers ORDER BY id DESC LIMIT 10");
if ($ctList && $ctList->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Service</th><th>Location</th><th>Status</th></tr>";
    while ($ct = $ctList->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$ct['id']}</td>";
        echo "<td>{$ct['name']}</td>";
        echo "<td>{$ct['service_type']}</td>";
        echo "<td>{$ct['location']}</td>";
        echo "<td>{$ct['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

$conn->close();
