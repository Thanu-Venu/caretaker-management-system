# Database Migration Instructions

## Run Refunds Table Migration

### Option 1: Using phpMyAdmin (Recommended for WAMP)

1. Open phpMyAdmin (usually at `http://localhost/phpmyadmin`)
2. Select the `smartcare` database from the left sidebar
3. Click on the "SQL" tab at the top
4. Copy and paste the contents from:
   ```
   database/migrations/05_create_refunds_table.sql
   ```
5. Click "Go" to execute the SQL

### Option 2: Using MySQL Command Line

#### Windows WAMP:
```bash
# Open Command Prompt or PowerShell
cd C:\wamp64\www\CMA

# Run migration (enter root password when prompted)
C:\wamp64\bin\mysql\mysql9.1.0\bin\mysql.exe -u root -p smartcare < database/migrations/05_create_refunds_table.sql
```

#### Alternative:
```bash
# Open MySQL shell
C:\wamp64\bin\mysql\mysql9.1.0\bin\mysql.exe -u root -p

# In MySQL shell:
USE smartcare;
SOURCE c:/wamp64/www/CMA/database/migrations/05_create_refunds_table.sql;
```

### Option 3: Manual Table Creation

If automated methods fail, run these SQL commands manually:

```sql
USE smartcare;

-- Create refunds table
CREATE TABLE IF NOT EXISTS `refunds` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `booking_id` INT NOT NULL,
  `client_id` INT NOT NULL,
  `cancellation_type` ENUM('before_service_start', 'after_service_start', 'during_recurring', 'daily_service', 'auto_nonpayment') NOT NULL,
  `total_paid` DECIMAL(10,2) NOT NULL,
  `service_used_amount` DECIMAL(10,2) DEFAULT 0.00,
  `cancellation_fee` DECIMAL(10,2) DEFAULT 0.00,
  `refund_amount` DECIMAL(10,2) NOT NULL,
  `refund_calculation` TEXT,
  `status` ENUM('pending', 'approved', 'declined', 'processed', 'completed') NOT NULL DEFAULT 'pending',
  `approved_by` INT NULL,
  `approved_at` DATETIME NULL,
  `processed_at` DATETIME NULL,
  `refund_method` VARCHAR(100) NULL,
  `refund_reference` VARCHAR(255) NULL,
  `admin_notes` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_refund_booking` (`booking_id`),
  KEY `idx_refund_client` (`client_id`),
  KEY `idx_refund_status` (`status`),
  KEY `idx_refund_created` (`created_at`),
  CONSTRAINT `fk_refund_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_refund_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add columns to bookings table
ALTER TABLE `bookings`
  ADD COLUMN `refund_status` ENUM('none', 'pending', 'approved', 'declined', 'completed') DEFAULT 'none' COMMENT 'Refund processing status',
  ADD COLUMN `advance_amount` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Advance payment amount received',
  ADD COLUMN `service_days_used` INT DEFAULT 0 COMMENT 'Number of days/months of service used before cancellation';

-- Create index
CREATE INDEX `idx_bookings_refund_status` ON `bookings` (`refund_status`);
```

## Verify Installation

After running the migration, verify tables were created:

```sql
-- Check refunds table exists
SHOW TABLES LIKE 'refunds';

-- Check refunds table structure
DESCRIBE refunds;

-- Check new columns in bookings table
DESCRIBE bookings;

-- Check for new columns
SHOW COLUMNS FROM bookings LIKE '%refund%';
SHOW COLUMNS FROM bookings LIKE '%advance_amount%';
```

Expected output should show:
- `refunds` table with 18 columns
- `bookings` table with new columns: `refund_status`, `advance_amount`, `service_days_used`

## Troubleshooting

### Error: Table already exists
This is safe to ignore. The migration uses `IF NOT EXISTS` clauses.

### Error: Column already exists
If you get errors about columns already existing, it means they were added previously. This is safe to ignore.

### Error: Foreign key constraint fails
Ensure that:
1. The `bookings` table exists
2. The `clients` table exists
3. The referenced columns (`id`) have the correct type and exist

### Error: Access denied
Make sure you're using a MySQL user with sufficient privileges:
```sql
GRANT ALL PRIVILEGES ON smartcare.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

## Migration Status Check

Run this query to check if migration was successful:

```sql
SELECT
  COUNT(*) as refunds_table_exists
FROM information_schema.tables
WHERE table_schema = 'smartcare'
  AND table_name = 'refunds';

-- Should return 1 if table exists
```

## Rollback (If Needed)

To rollback the changes:

```sql
-- Drop refunds table
DROP TABLE IF EXISTS `refunds`;

-- Remove columns from bookings table
ALTER TABLE `bookings`
  DROP COLUMN IF EXISTS `refund_status`,
  DROP COLUMN IF EXISTS `advance_amount`,
  DROP COLUMN IF EXISTS `service_days_used`;

-- Drop index
DROP INDEX IF EXISTS `idx_bookings_refund_status` ON `bookings`;
```

---

## Post-Installation Steps

1. **Test Cancellation Flow**
   - Create a test booking
   - Make advance payment
   - Cancel the booking
   - Verify refund record created

2. **Test HR Workflow**
   - Login as HR
   - Navigate to Refunds page
   - Approve a test refund
   - Mark as completed

3. **Check Notifications**
   - Verify client receives cancellation notification
   - Verify client receives refund status notifications
   - Verify HR receives refund approval requests

---

## Need Help?

If you encounter issues during migration:

1. Check MySQL error logs: `C:\wamp64\logs\mysql.log`
2. Check PHP error logs: `C:\wamp64\logs\php_error.log`
3. Verify database connection in `app/config/config.php`
4. Ensure WAMP services are running

---

**Note:** Always backup your database before running migrations!

```bash
# Backup command
mysqldump -u root -p smartcare > backup_before_refund_migration.sql
```
