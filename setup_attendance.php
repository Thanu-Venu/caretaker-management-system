<?php
// Setup script for attendance data
chdir(__DIR__ . '/public');
require_once "../app/init.php";

echo "=== Setting up Attendance Table ===\n\n";

$db = new Database();

// 1. Create attendance table if it doesn't exist
$createTableSql = "CREATE TABLE IF NOT EXISTS `attendance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `caretaker_id` int NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('Present','Absent','Leave','Late') DEFAULT 'Present',
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_attendance_date` (`caretaker_id`, `attendance_date`),
  KEY `idx_caretaker_date` (`caretaker_id`, `attendance_date`),
  CONSTRAINT `fk_attendance_caretaker` FOREIGN KEY (`caretaker_id`) REFERENCES `caretakers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($db->conn->query($createTableSql) === TRUE) {
    echo "✓ Attendance table created/verified\n";
} else {
    echo "✗ Error creating attendance table: " . $db->conn->error . "\n";
    die();
}

// 2. Add sample attendance data for the last 30 days
echo "\n=== Adding Sample Attendance Data ===\n";

// Get all active caretakers
$caretakersResult = $db->conn->query("SELECT id, name FROM caretakers WHERE status = 'Active' LIMIT 15");
$caretakers = [];
while ($row = $caretakersResult->fetch_assoc()) {
    $caretakers[] = $row;
}

echo "Found " . count($caretakers) . " active caretakers\n";

// Generate attendance for the last 30 days
$insertedCount = 0;
$skippedCount = 0;
$today = new DateTime();
$today->modify('-30 days');

for ($day = 0; $day < 30; $day++) {
    $currentDate = $today->format('Y-m-d');
    
    // Skip weekends (Saturday = 6, Sunday = 0)
    $dayOfWeek = $today->format('w');
    if ($dayOfWeek != 0 && $dayOfWeek != 6) {
        foreach ($caretakers as $caretaker) {
            $caretakerId = $caretaker['id'];
            
            // Randomly decide status (90% Present, 5% Late, 3% Absent, 2% Leave)
            $rand = rand(0, 100);
            if ($rand < 90) {
                $status = 'Present';
                $checkIn = date('H:i:s', strtotime('08:00:00 +' . rand(0, 30) . ' minutes'));
                $checkOut = date('H:i:s', strtotime('17:00:00 -' . rand(0, 30) . ' minutes'));
            } elseif ($rand < 95) {
                $status = 'Late';
                $checkIn = date('H:i:s', strtotime('08:45:00 +' . rand(0, 45) . ' minutes'));
                $checkOut = date('H:i:s', strtotime('17:00:00'));
            } elseif ($rand < 98) {
                $status = 'Absent';
                $checkIn = null;
                $checkOut = null;
            } else {
                $status = 'Leave';
                $checkIn = null;
                $checkOut = null;
            }
            
            // Check if record already exists
            $checkSql = "SELECT id FROM attendance WHERE caretaker_id = $caretakerId AND attendance_date = '$currentDate'";
            $checkResult = $db->conn->query($checkSql);
            
            if ($checkResult && $checkResult->num_rows == 0) {
                $checkInVal = $checkIn ? "'$checkIn'" : "NULL";
                $checkOutVal = $checkOut ? "'$checkOut'" : "NULL";
                
                $sql = "INSERT INTO attendance (caretaker_id, attendance_date, status, check_in_time, check_out_time, notes)
                        VALUES ($caretakerId, '$currentDate', '$status', $checkInVal, $checkOutVal, 'System generated test data')";
                
                if ($db->conn->query($sql) === TRUE) {
                    $insertedCount++;
                } else {
                    // Silently skip errors to avoid too much output
                }
            } else {
                $skippedCount++;
            }
        }
    }
    
    $today->modify('+1 day');
}

echo "✓ Inserted " . $insertedCount . " attendance records\n";
echo "✓ Skipped " . $skippedCount . " existing records\n";

// 3. Update caretaker ratings if not set
echo "\n=== Updating Caretaker Ratings ===\n";

$ratings = [4.8, 4.5, 4.2, 3.9, 3.5, 4.7, 4.3, 3.8, 4.1, 4.6, 4.0, 3.7, 4.4, 4.9, 3.6];
$caretakerIds = array_column($caretakers, 'id');

foreach ($caretakerIds as $idx => $caretakerId) {
    $rating = $ratings[$idx % count($ratings)];
    
    $updateSql = "UPDATE caretakers SET rating = $rating WHERE id = $caretakerId AND rating IS NULL";
    $db->conn->query($updateSql);
}

echo "✓ Updated caretaker ratings\n";

echo "\n=== Setup Complete ===\n";
echo "✓ Charts now have real data from the database\n";
?>
