-- Migration: Create recurring_payments table
-- Description: Tracks recurring payment obligations for monthly/yearly bookings
-- Date: 2026-03-07

USE smartcare;

-- Create recurring_payments table
CREATE TABLE IF NOT EXISTS `recurring_payments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `booking_id` INT NOT NULL,
  `client_id` INT NOT NULL,
  `caretaker_id` INT NOT NULL,
  `cycle_number` INT NOT NULL COMMENT 'Payment cycle number (1, 2, 3...)',
  `cycle_type` ENUM('monthly', '15_day', 'daily') NOT NULL DEFAULT 'monthly' COMMENT 'Type of billing cycle',
  `due_date` DATE NOT NULL COMMENT 'Payment due date',
  `amount` DECIMAL(10,2) NOT NULL COMMENT 'Amount due for this cycle',
  `status` ENUM('pending', 'paid', 'overdue', 'cancelled') NOT NULL DEFAULT 'pending',
  `paid_at` DATETIME NULL COMMENT 'When payment was completed',
  `payment_id` INT NULL COMMENT 'Reference to payments table when paid',
  `reminder_7_days_sent` TINYINT(1) DEFAULT 0 COMMENT '7 day reminder sent',
  `reminder_3_days_sent` TINYINT(1) DEFAULT 0 COMMENT '3 day reminder sent',
  `reminder_due_date_sent` TINYINT(1) DEFAULT 0 COMMENT 'Due date reminder sent',
  `grace_period_end` DATE NULL COMMENT 'End of 3-day grace period',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_recurring_booking` (`booking_id`),
  KEY `idx_recurring_client` (`client_id`),
  KEY `idx_recurring_status` (`status`),
  KEY `idx_recurring_due_date` (`due_date`),
  KEY `idx_recurring_cycle` (`booking_id`, `cycle_number`),
  CONSTRAINT `fk_recurring_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_recurring_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_recurring_caretaker` FOREIGN KEY (`caretaker_id`) REFERENCES `caretakers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add comment to table
ALTER TABLE `recurring_payments` COMMENT = 'Tracks recurring payment schedules and reminders for bookings';
