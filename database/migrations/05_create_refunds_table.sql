-- Migration: Create refunds table
-- Description: Tracks refund transactions for cancelled bookings
-- Date: 2026-03-07

USE smartcare;

-- Create refunds table
CREATE TABLE IF NOT EXISTS `refunds` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `booking_id` INT NOT NULL,
  `client_id` INT NOT NULL,
  `cancellation_type` ENUM('before_service_start', 'after_service_start', 'during_recurring', 'daily_service', 'auto_nonpayment') NOT NULL COMMENT 'Type of cancellation',
  `total_paid` DECIMAL(10,2) NOT NULL COMMENT 'Total amount paid by client',
  `service_used_amount` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Value of service already used',
  `cancellation_fee` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Cancellation fee deducted',
  `refund_amount` DECIMAL(10,2) NOT NULL COMMENT 'Final refund amount',
  `refund_calculation` TEXT COMMENT 'JSON string with calculation breakdown',
  `status` ENUM('pending', 'approved', 'declined', 'processed', 'completed') NOT NULL DEFAULT 'pending',
  `approved_by` INT NULL COMMENT 'HR/Admin user who approved',
  `approved_at` DATETIME NULL,
  `processed_at` DATETIME NULL COMMENT 'When refund was actually processed',
  `refund_method` VARCHAR(100) NULL COMMENT 'Bank transfer, cash, etc.',
  `refund_reference` VARCHAR(255) NULL COMMENT 'Transaction reference number',
  `admin_notes` TEXT NULL COMMENT 'Internal notes from HR/Admin',
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

ALTER TABLE `refunds` COMMENT = 'Tracks refund calculations and processing for cancelled bookings';

-- Add refund tracking columns to bookings table if they don't exist
ALTER TABLE `bookings`
  ADD COLUMN IF NOT EXISTS `refund_status` ENUM('none', 'pending', 'approved', 'declined', 'completed') DEFAULT 'none' COMMENT 'Refund processing status',
  ADD COLUMN IF NOT EXISTS `advance_amount` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Advance payment amount received',
  ADD COLUMN IF NOT EXISTS `service_days_used` INT DEFAULT 0 COMMENT 'Number of days/months of service used before cancellation';

-- Create index on refund_status
CREATE INDEX IF NOT EXISTS `idx_bookings_refund_status` ON `bookings` (`refund_status`);
