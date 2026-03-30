-- Migration: Create booking_reassignments table
-- Description: Tracks caretaker reassignments when leaves are approved with replacements
-- Date: 2026-03-07
-- Note: Foreign key constraints commented out until bookings table is created

USE smartcare;

-- Create booking_reassignments table
CREATE TABLE IF NOT EXISTS `booking_reassignments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `booking_id` INT NOT NULL COMMENT 'Booking being reassigned',
  `old_caretaker_id` INT NOT NULL COMMENT 'Original caretaker on leave',
  `new_caretaker_id` INT NOT NULL COMMENT 'Replacement caretaker',
  `start_date` DATE NOT NULL COMMENT 'Reassignment period start',
  `end_date` DATE NOT NULL COMMENT 'Reassignment period end',
  `reassigned_by` INT NOT NULL COMMENT 'HR user who approved the reassignment',
  `reassigned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When reassignment was created',
  `note` TEXT NULL COMMENT 'HR note about the reassignment',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reassign_booking` (`booking_id`),
  KEY `idx_reassign_old_caretaker` (`old_caretaker_id`),
  KEY `idx_reassign_new_caretaker` (`new_caretaker_id`),
  KEY `idx_reassign_dates` (`start_date`, `end_date`),
  KEY `idx_reassign_replacement_period` (`new_caretaker_id`, `start_date`, `end_date`)
  -- Foreign key constraints will be added after bookings table is created:
  -- CONSTRAINT `fk_reassign_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  -- CONSTRAINT `fk_reassign_old_caretaker` FOREIGN KEY (`old_caretaker_id`) REFERENCES `caretakers` (`id`) ON DELETE CASCADE,
  -- CONSTRAINT `fk_reassign_new_caretaker` FOREIGN KEY (`new_caretaker_id`) REFERENCES `caretakers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
