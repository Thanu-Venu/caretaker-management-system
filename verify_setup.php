<?php
chdir(__DIR__ . '/public');
require_once "../app/init.php";

$db = new Database();

// Check if attendance table exists
$tableCheck = $db->conn->query("SELECT 1 FROM information_schema.tables WHERE table_schema='smartcare' AND table_name='attendance'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    echo "✓ Attendance table exists\n";
} else {
    echo "Creating attendance table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS `attendance` (
      `id` int NOT NULL AUTO_INCREMENT,
      `caretaker_id` int NOT NULL,
      `attendance_date` date NOT NULL,
      `status` enum('Present','Absent','Leave','Late') DEFAULT 'Present',
      PRIMARY KEY (`id`),
      UNIQUE KEY `uq_attendance_date` (`caretaker_id`, `attendance_date`),
      CONSTRAINT `fk_attendance_caretaker` FOREIGN KEY (`caretaker_id`) REFERENCES `caretakers` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB";
    $db->conn->query($sql);
    echo "✓ Table created\n";
}

// Check attendance records
$result = $db->conn->query("SELECT COUNT(*) as count FROM attendance");
$row = $result->fetch_assoc();
echo "Attendance records: " . $row['count'] . "\n";

// Check caretaker ratings
$result = $db->conn->query("SELECT COUNT(CASE WHEN rating IS NOT NULL THEN 1 END) as rated FROM caretakers WHERE status='Active'");
$row = $result->fetch_assoc();
echo "Caretakers with ratings: " . $row['rated'] . "\n";
?>
