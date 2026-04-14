-- Migration: Add payment tracking fields to bookings table
-- Description: Adds fields for service start dates, advance payment tracking, and billing cycles
-- Date: 2026-03-07

USE smartcare;

-- Add new columns to bookings table
ALTER TABLE `bookings`
ADD COLUMN `service_start_date` DATE NULL COMMENT 'Agreed service start date' AFTER `booking_date`,
ADD COLUMN `advance_paid_date` DATETIME NULL COMMENT 'When advance payment was completed' AFTER `created_at`,
ADD COLUMN `advance_months` INT DEFAULT 0 COMMENT 'Number of months covered by advance payment' AFTER `advance_paid_date`,
ADD COLUMN `total_months` INT DEFAULT 0 COMMENT 'Total billing months (years converted to months)' AFTER `advance_months`,
ADD COLUMN `advance_balance` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Monetary value of prepaid service period' AFTER `total_months`;

-- Add index for service_start_date for efficient querying
CREATE INDEX `idx_bookings_service_start` ON `bookings` (`service_start_date`);

-- Add index for advance_paid_date for payment tracking
CREATE INDEX `idx_bookings_advance_paid` ON `bookings` (`advance_paid_date`);
