-- Migration: Add hr_note column to reschedule_requests table
-- Purpose: Store HR's notes/reasons when approving or rejecting reschedule requests
-- Date: 2024

-- Add hr_note column after reason column
ALTER TABLE `reschedule_requests`
ADD COLUMN `hr_note` TEXT NULL AFTER `reason`;

-- Verify the change
-- SELECT * FROM reschedule_requests LIMIT 1;
