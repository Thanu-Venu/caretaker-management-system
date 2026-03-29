# HR Dashboard Charts - Database Connection Implementation

## Changes Made

### 1. **Database Setup**
- Created migration file: `database/migrations/add_attendance_table.sql`
- New table: `attendance` with fields for tracking caretaker attendance
  - attendance_date, status (Present/Absent/Leave/Late)
  - check_in_time, check_out_time
  - Unique constraint on caretaker_id + attendance_date

### 2. **Backend - HRDashboardModel** (`app/models/HRDashboardModel.php`)
Added three new methods:

#### `getAttendanceData($limit = 10)`
- Returns attendance summary for last 30 days
- Groups by caretaker, counts present days
- Returns: caretaker name, days_present, days_worked, total_days_tracked

#### `getPerformanceRatings()`
- Categorizes caretakers by rating (Excellent/Good/Average/Poor)
- Based on caretaker rating field (Excellent: >= 4.5, Good: >= 4.0, etc.)
- Groups by category with count of caretakers

#### `getRatingStats()`
- Comprehensive statistics about caretaker ratings
- Returns: total_caretakers, excellent, good, average, poor, avg_rating

### 3. **Backend - HrController Update** (`app/controllers/HrController.php`)
Modified `hr_dashboard()` method to:
- Call new dashboard model methods
- Prepare data for both charts
- Format labels and data arrays as JSON
- Pass colors array for performance chart

Data passed to view:
- `attendanceLabels`: JSON array of caretaker names
- `attendanceDays`: JSON array of days present
- `performanceLabels`: JSON array of rating categories
- `performanceCounts`: JSON array of caretaker counts per category
- `performanceColors`: JSON array of chart colors

### 4. **Frontend - Views** (`app/views/hr/hr_dashboard.php`)

#### Changed chart subtitles:
- Attendance: "Demo chart" → "Last 30 days data"
- Performance: "Demo chart" → "Active caretakers"

#### Updated JavaScript:
- Replaced hardcoded demo data with PHP-generated JSON data
- Attendance chart now shows top 10 caretakers by attendance
- Performance chart shows distribution of rating categories
- Enhanced tooltips and legends

---

## How to Use

### 1. **Create the Attendance Table**
Run in database client:
```sql
CREATE TABLE IF NOT EXISTS `attendance` (
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
  CONSTRAINT `fk_attendance_caretaker` FOREIGN KEY (`caretaker_id`) REFERENCES `caretakers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;
```

### 2. **Add Sample Data**
Insert attendance records:
```sql
INSERT INTO attendance (caretaker_id, attendance_date, status, check_in_time, check_out_time)
VALUES 
(2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Present', '08:15:00', '17:00:00'),
(2, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'Present', '08:20:00', '16:45:00'),
-- ... more records
```

Or run the setup script:
```bash
cd c:\xampp\htdocs\CMA
c:\xampp\php\php.exe setup_attendance.php
```

### 3. **Update Caretaker Ratings** (if needed)
The performance chart uses the `rating` field from the `caretakers` table.
Update ratings for demo:
```sql
UPDATE caretakers SET rating = 4.8 WHERE id = 2;
UPDATE caretakers SET rating = 4.5 WHERE id = 4;
UPDATE caretakers SET rating = 3.9 WHERE id = 5;
```

---

## Chart Data Sources

### Attendance Summary (Bar Chart)
- **Data**: Last 30 days of attendance records
- **X-axis**: Caretaker names (top 10 by attendance)
- **Y-axis**: Days present count
- **Source**: attendance table with JOIN to caretakers

### Performance Ratings (Pie Chart)
- **Data**: Caretaker rating distribution
- **Categories**: Excellent (≥4.5), Good (≥4.0), Average (≥3.0), Poor (<3.0)
- **Values**: Count of caretakers in each category
- **Source**: caretakers table with rating field

---

## Testing

1. **Log in as HR/Manager**
2. **Navigate to HR Dashboard**: `index.php?url=hr/hr_dashboard`
3. **Verify Charts Display**:
   - Attendance chart shows caretaker names and present days
   - Performance chart shows rating distribution
   - Both charts have real data from the database

---

## Future Enhancements

1. Add date range filters for attendance chart
2. Add caretaker-wise rating breakdown
3. Add export functionality for reports
4. Track attendance via attendance tracking system
5. Integration with booking/service completion data
